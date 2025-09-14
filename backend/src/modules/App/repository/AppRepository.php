<?php

namespace modules\app\repository;

use core\BaseRepository;
use modules\app\dto\AppDTO;
use modules\app\dto\AppOutputDTO;
use PDO;

/**
 * Repository to interact with the "app" database table.
 * Handles CRUD operations for App entities.
 * 
 * @package modules\app\repository
 * @author Vitor 
 * Carvalho <vitorcarvalhodso@gmail.com>
 */
class AppRepository extends BaseRepository {

    /**
     * Create a new app in the database.
     * 
     * @param AppDTO $dto App data transfer object
     * @param int $timemodified Timestamp of creation/modification
     * @return AppOutputDTO The created app
     */
    public function create(AppDTO $dto, int $timemodified): AppOutputDTO {
        $stmt = $this->db->prepare(
            "INSERT INTO app (name, url, active, timemodified, modifiedby, deleted)
             VALUES (:name, :url, :active, :timemodified, :modifiedby, 0)"
        );

        $stmt->execute([
            ':name' => $dto->name,
            ':url' => $dto->url,
            ':active' => $dto->active ?? 1,
            ':timemodified'=> $timemodified,
            ':modifiedby'  => $dto->modifiedBy
        ]);

        return $this->getById((int) $this->db->lastInsertId());
    }

    /**
     * Update an existing app in the database.
     * Only non-null fields in $dto are updated.
     * 
     * @param int $id App ID
     * @param AppDTO $dto Updated app data
     * @param int $timemodified Timestamp of modification
     * @return AppOutputDTO|null Updated app, or null if no fields were updated
     */
    public function update(int $id, AppDTO $dto, int $timemodified): ?AppOutputDTO {
        $fields = [];
        $params = [':id' => $id];

        foreach (['name','url','active'] as $key) {
            if (isset($dto->$key)) {
                $fields[] = "$key = :$key";
                $params[":$key"] = $dto->$key;
            }
        }

        if (empty($fields)) {
            return null;
        }

        $fields[] = "timemodified = :timemodified";
        $fields[] = "modifiedby = :modifiedby";
        $params[':timemodified'] = $timemodified;
        $params[':modifiedby'] = $dto->modifiedBy;

        $sql = "UPDATE app SET " . implode(', ', $fields) . " WHERE id = :id AND deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $this->getById($id);
    }

    /**
     * Get an app by ID.
     * 
     * @param int $id App ID
     * @return AppOutputDTO|null App or null if not found
     */
    public function getById(int $id): ?AppOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM app WHERE id = :id AND deleted = 0");
        $stmt->execute([':id' => $id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);

        return $app ? new AppOutputDTO($app) : null;
    }

    /**
     * Get an app by its name.
     * 
     * @param string $name App name
     * @return AppOutputDTO|null App or null if not found
     */
    public function getByName(string $name): ?AppOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM app WHERE name = ? AND deleted = 0");
        $stmt->execute([$name]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);

        return $app ? new AppOutputDTO($app) : null;
    }

    /**
     * Soft-delete an app by setting deleted = 1 and active = 0.
     * 
     * @param int $id App ID
     * @param int|null $modifiedBy User ID performing the deletion
     * @return int Number of affected rows
     */
    public function delete(int $id, ?int $modifiedBy): int {
        $stmt = $this->db->prepare(
            "UPDATE app SET deleted = 1, active = 0, timemodified = :timemodified, modifiedby = :modifiedby 
             WHERE id = :id"
        );

        $stmt->execute([
            ':timemodified' => time(),
            ':modifiedby' => $modifiedBy,
            ':id' => $id
        ]);

        return $stmt->rowCount();
    }

    /**
     * List all non-deleted apps.
     * 
     * @return AppOutputDTO[] Array of AppOutputDTO objects
     */
    public function listAll(): array {
        $stmt = $this->db->query("SELECT * FROM app WHERE deleted = 0");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new AppOutputDTO($row), $rows);
    }
}
