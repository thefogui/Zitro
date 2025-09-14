<?php

namespace modules\user\repository;

use core\BaseRepository;
use modules\user\dto\UserDTO;
use modules\user\dto\UserOutputDTO;
use PDO;

/**
 * Repository for managing users.
 * Handles creation, update, retrieval, deletion, and listing of users.
 * 
 * @package modules\user\repository
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserRepository extends BaseRepository {

    /**
     * Create a new user.
     *
     * @param UserDTO $dto
     * @param int $startdate Timestamp for the start date
     * @param int $timemodified Timestamp for last modification
     * @return UserOutputDTO
     */
    public function create(UserDTO $dto, int $startdate, int $timemodified): UserOutputDTO {
        $stmt = $this->db->prepare(
            "INSERT INTO user (username, email, firstname, lastname, password, startdate, timemodified, deleted, modifiedby)
             VALUES (:username, :email, :firstname, :lastname, :password, :startdate, :timemodified, 0, :modifiedby)"
        );

        $stmt->execute([
            ':username' => $dto->username,
            ':email' => $dto->email,
            ':firstname' => $dto->firstname,
            ':lastname' => $dto->lastname ?? '',
            ':password' => $dto->password,
            ':startdate' => $startdate,
            ':timemodified' => $timemodified,
            ':modifiedby' => $dto->modifiedBy
        ]);

        $id = (int) $this->db->lastInsertId();
        return $this->getById($id);
    }

    /**
     * Update an existing user.
     *
     * @param int $id
     * @param UserDTO $dto
     * @param int $timemodified
     * @return UserOutputDTO|null Returns the updated user or null if no changes
     */
    public function update(int $id, UserDTO $dto, int $timemodified): ?UserOutputDTO {
        $fields = [];
        $params = [':id' => $id];

        foreach (['username','email','firstname','lastname','password'] as $key) {
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

        $sql = "UPDATE user SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $this->getById($id);
    }

    /**
     * Get a user by ID.
     *
     * @param int $id
     * @return UserOutputDTO|null
     */
    public function getById(int $id): ?UserOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id = :id AND deleted = 0");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? new UserOutputDTO($user) : null;
    }

    /**
     * Get a user by username.
     *
     * @param string $username
     * @return UserOutputDTO|null
     */
    public function getByUsername(string $username): ?UserOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE username = ? AND deleted = 0");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? new UserOutputDTO($user) : null;
    }

    /**
     * Get a user by email.
     *
     * @param string $email
     * @return UserOutputDTO|null
     */
    public function getByEmail(string $email): ?UserOutputDTO {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = ? AND deleted = 0");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? new UserOutputDTO($user) : null;
    }

    /**
     * Soft delete a user.
     *
     * @param int $id
     * @param int|null $modifiedBy
     * @return int Returns the number of affected rows
     */
    public function delete(int $id, ?int $modifiedBy): int {
        $stmt = $this->db->prepare(
            "UPDATE user SET deleted = 1, timemodified = :timemodified, modifiedby = :modifiedby WHERE id = :id"
        );

        $stmt->execute([
            ':timemodified' => time(),
            ':modifiedby' => $modifiedBy,
            ':id' => $id
        ]);

        return $stmt->rowCount();
    }

    /**
     * List all users.
     *
     * @return UserOutputDTO[]
     */
    public function listAll(): array {
        $stmt = $this->db->query("SELECT * FROM user WHERE deleted = 0");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new UserOutputDTO($row), $rows);
    }
}
