<?php

namespace modules\user\repository;

use core\BaseRepository;
use PDO;

/**
 * Repository for managing user sessions (JWT) in the database.
 * Handles creation, retrieval, and deletion of session records.
 * 
 * @package modules\user\repository
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class SessionRepository extends BaseRepository {

    /**
     * Create a new session for a user.
     *
     * @param int $userId
     * @param string $jwt
     * @param int $createdAt Timestamp of session creation
     * @param int $expiresAt Timestamp when session expires
     */
    public function createSession(int $userId, string $jwt, int $createdAt, int $expiresAt): void {
        $stmt = $this->db->prepare(
            "INSERT INTO user_session (userid, jwttoken, createdat, expiresat)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $jwt, $createdAt, $expiresAt]);
    }

    /**
     * Retrieve a session by its JWT token.
     *
     * @param string $jwt
     * @return array|null Returns session data or null if not found
     */
    public function getSession(string $jwt): ?array {
        $stmt = $this->db->prepare("SELECT * FROM user_session WHERE jwttoken = ?");
        $stmt->execute([$jwt]);
        $userSession = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userSession ?: null;
    }

    /**
     * Delete a session by its JWT token.
     *
     * @param string $jwt
     */
    public function deleteSession(string $jwt): void {
        $stmt = $this->db->prepare("DELETE FROM user_session WHERE jwttoken = ?");
        $stmt->execute([$jwt]);
    }
}
