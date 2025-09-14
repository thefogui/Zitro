<?php

namespace modules\user\repository;

use core\BaseRepository;
use PDO;

/**
 * Repository to manage admin records in the database.
 * Handles checking, adding, and removing admin users.
 * 
 * @package modules\user\repository
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class AdminRepository extends BaseRepository {

    /**
     * Check if there is at least one active admin in the database.
     * 
     * @return bool True if at least one admin exists, false otherwise
     */
    public function thereAreAdminsSavedInTheDatabase(): bool {
        $sql = "SELECT EXISTS(
            SELECT 1 
            FROM admin 
            WHERE deleted = 0 AND active = 1
        ) AS admin_exists";

        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (bool) $result['admin_exists'];
    }

    /**
     * Check if a given user ID corresponds to an active admin.
     * 
     * @param int $id User ID
     * @return bool True if the user is an admin, false otherwise
     */
    public function isUserAdmin(int $id): bool {
        $sql = "SELECT EXISTS(
            SELECT 1
            FROM admin
            WHERE userid = :userid AND active = 1 AND deleted = 0
        ) AS is_admin";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userid' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (bool) $result['is_admin'];
    }

    /**
     * Add a new admin record for a user.
     * 
     * @param int $id User ID
     * @param int|null $modifiedBy User ID performing the operation
     * @return int ID of the newly created admin record
     */
    public function add(int $id, ?int $modifiedBy): int {
        $sql = "INSERT INTO admin (userid, active, timemodified, modifiedby, deleted)
                VALUES (:userid, 1, :timemodified, :modifiedby, 0)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':userid' => $id,
            ':timemodified' => time(),
            ':modifiedby' => $modifiedBy
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Soft-delete an admin by setting deleted = 1 and active = 0.
     * 
     * @param int $id Admin record ID
     * @param int|null $modifiedBy User ID performing the operation
     * @return int Number of affected rows
     */
    public function remove(int $id, ?int $modifiedBy): int {
        $stmt = $this->db->prepare(
            "UPDATE admin 
             SET deleted = 1, active = 0, timemodified = :timemodified, modifiedby = :modifiedby 
             WHERE id = :id"
        );

        $stmt->execute([
            ':timemodified' => time(),
            ':modifiedby' => $modifiedBy,
            ':id' => $id
        ]);

        return $stmt->rowCount();
    }
}
