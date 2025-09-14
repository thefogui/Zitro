<?php

namespace modules\user\repository;

use core\BaseRepository;
use modules\user\dto\DepartmentDTO;
use modules\user\dto\DepartmentOutputDTO;
use PDO;

/**
 * Repository for managing departments in the database.
 * Handles CRUD operations and user assignments cleanup.
 * 
 * @package modules\user\repository
 * @author Vitor 
 * Carvalho <vitorcarvalhodso@gmail.com>
 */
class DepartmentRepository extends BaseRepository {

    /**
     * Create a new department.
     *
     * @param DepartmentDTO $dto
     * @param int $timemodified
     * @return DepartmentOutputDTO
     */
    public function create(DepartmentDTO $dto, int $timemodified): DepartmentOutputDTO {
        $stmt = $this->db->prepare(
            "INSERT INTO department (name, timemodified, modifiedby, deleted)
             VALUES (:name, :timemodified, :modifiedby, 0)"
        );

        $stmt->execute([
            ':name' => $dto->name,
            ':timemodified'=> $timemodified,
            ':modifiedby' => $dto->modifiedBy
        ]);

        return $this->getById((int) $this->db->lastInsertId());
    }

    /**
     * Update a department.
     *
     * @param int $id
     * @param DepartmentDTO $dto
     * @param int $timemodified
     * @return DepartmentOutputDTO|null
     */
    public function update(int $id, DepartmentDTO $dto, int $timemodified): ?DepartmentOutputDTO {
        $fields = [];
        $params = [':id' => $id];

        if (!empty($dto->name)) {
            $fields[] = "name = :name";
            $params[':name'] = $dto->name;
        }

        if (empty($fields)) {
            return null;
        }

        $fields[] = "timemodified = :timemodified";
        $fields[] = "modifiedby = :modifiedby";
        $params[':timemodified'] = $timemodified;
        $params[':modifiedby'] = $dto->modifiedBy;

        $sql = "UPDATE department SET " . implode(', ', $fields) . " WHERE id = :id AND deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $this->getById($id);
    }

    /**
     * Get a department by ID.
     *
     * @param int $id
     * @return DepartmentOutputDTO|null
     */
    public function getById(int $id): ?DepartmentOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM department WHERE id = :id AND deleted = 0");
        $stmt->execute([':id' => $id]);
        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        return $department ? new DepartmentOutputDTO($department) : null;
    }

    /**
     * Get a department by name.
     *
     * @param string $name
     * @return DepartmentOutputDTO|null
     */
    public function getByName(string $name): ?DepartmentOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM department WHERE name = ? AND deleted = 0");
        $stmt->execute([$name]);
        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        return $department ? new DepartmentOutputDTO($department) : null;
    }

    /**
     * Soft-delete a department and clear related user assignments.
     *
     * @param int $id
     * @param int|null $modifiedBy
     * @return int Number of rows affected
     * @throws \Exception
     */
    public function delete(int $id, ?int $modifiedBy): int {
        $this->db->beginTransaction();

        try {
            $this->clearUserAssignments($id, $modifiedBy);
            $affected = $this->markAsDeleted($id, $modifiedBy);

            $this->db->commit();
            return $affected;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Remove department assignment from all users.
     *
     * @param int $departmentId
     * @param int|null $modifiedBy
     */
    private function clearUserAssignments(int $departmentId, ?int $modifiedBy): void {
        $stmt = $this->db->prepare("
            UPDATE user_company_position
            SET departmentid = NULL,
                timemodified = :timemodified,
                modifiedby = :modifiedby
            WHERE departmentid = :id
            AND deleted = 0
        ");

        $stmt->execute([
            ':timemodified' => time(),
            ':modifiedby' => $modifiedBy,
            ':id' => $departmentId
        ]);
    }

    /**
     * Mark department as deleted.
     *
     * @param int $id
     * @param int|null $modifiedBy
     * @return int Rows affected
     */
    private function markAsDeleted(int $id, ?int $modifiedBy): int {
        $stmt = $this->db->prepare("
            UPDATE department 
            SET deleted = 1,
                timemodified = :timemodified,
                modifiedby = :modifiedby
            WHERE id = :id
        ");

        $stmt->execute([
            ':timemodified' => time(),
            ':modifiedby' => $modifiedBy,
            ':id' => $id
        ]);

        return $stmt->rowCount();
    }

    /**
     * List all active departments.
     *
     * @return DepartmentOutputDTO[]
     */
    public function listAll(): array {
        $stmt = $this->db->query("SELECT * FROM department WHERE deleted = 0");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new DepartmentOutputDTO($row), $rows);
    }
}
