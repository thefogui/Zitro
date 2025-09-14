<?php

namespace modules\user\repository;

use core\BaseRepository;
use modules\user\dto\CompanyPositionDTO;
use modules\user\dto\CompanyPositionOutputDTO;
use PDO;

/**
 * Repository to manage company positions in the database.
 * Handles creation, retrieval, update, deletion, and listing of positions.
 * 
 * @package modules\user\repository
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class CompanyPositionRepository extends BaseRepository {

    /**
     * Create a new company position.
     *
     * @param CompanyPositionDTO $dto
     * @param int $timemodified Timestamp of creation
     * @return CompanyPositionOutputDTO
     */
    public function create(CompanyPositionDTO $dto, int $timemodified): CompanyPositionOutputDTO {
        $stmt = $this->db->prepare(
            "INSERT INTO company_position (name, timemodified, modifiedby, deleted)
             VALUES (:name, :timemodified, :modifiedby, 0)"
        );

        $stmt->execute([
            ':name' => $dto->name,
            ':timemodified' => $timemodified,
            ':modifiedby' => $dto->modifiedBy
        ]);

        return $this->getById((int) $this->db->lastInsertId());
    }

    /**
     * Update an existing company position.
     *
     * @param int $id
     * @param CompanyPositionDTO $dto
     * @param int $timemodified
     * @return CompanyPositionOutputDTO|null Updated object or null if no fields changed
     */
    public function update(int $id, CompanyPositionDTO $dto, int $timemodified): ?CompanyPositionOutputDTO {
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

        $sql = "UPDATE company_position SET " . implode(', ', $fields) . " WHERE id = :id AND deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $this->getById($id);
    }

    /**
     * Get a company position by ID.
     *
     * @param int $id
     * @return CompanyPositionOutputDTO|null
     */
    public function getById(int $id): ?CompanyPositionOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM company_position WHERE id = :id AND deleted = 0");
        $stmt->execute([':id' => $id]);
        $position = $stmt->fetch(PDO::FETCH_ASSOC);

        return $position ? new CompanyPositionOutputDTO($position) : null;
    }

    /**
     * Get a company position by name.
     *
     * @param string $name
     * @return CompanyPositionOutputDTO|null
     */
    public function getByName(string $name): ?CompanyPositionOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM company_position WHERE name = ? AND deleted = 0");
        $stmt->execute([$name]);
        $position = $stmt->fetch(PDO::FETCH_ASSOC);

        return $position ? new CompanyPositionOutputDTO($position) : null;
    }

    /**
     * Soft-delete a company position and unset related user assignments.
     *
     * @param int $id
     * @param int|null $modifiedBy
     * @return int Number of rows affected
     */
    public function delete(int $id, ?int $modifiedBy): int {
        $now = time();

        $stmt = $this->db->prepare(
            "UPDATE company_position 
             SET deleted = 1, timemodified = :timemodified, modifiedby = :modifiedby 
             WHERE id = :id"
        );

        $stmt->execute([
            ':timemodified' => $now,
            ':modifiedby' => $modifiedBy,
            ':id' => $id
        ]);

        $affected = $stmt->rowCount();

        if ($affected > 0) {
            // Remove position assignment from users
            $stmt2 = $this->db->prepare(
                "UPDATE user_company_position
                 SET companypositionid = NULL,
                     timemodified = :timemodified,
                     modifiedby = :modifiedby
                 WHERE companypositionid = :id"
            );

            $stmt2->execute([
                ':timemodified' => $now,
                ':modifiedby' => $modifiedBy,
                ':id' => $id
            ]);
        }

        return $affected;
    }

    /**
     * List all active company positions.
     *
     * @return CompanyPositionOutputDTO[]
     */
    public function listAll(): array {
        $stmt = $this->db->query("SELECT * FROM company_position WHERE deleted = 0");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new CompanyPositionOutputDTO($row), $rows);
    }
}
