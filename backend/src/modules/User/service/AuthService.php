<?php

namespace modules\user\service;

use modules\user\repository\UserRepository;
use modules\user\repository\SessionRepository;
use Firebase\JWT\JWT;
use core\HttpStatus;
use Exception;

/**
 * Service class to handle authentication and session management.
 * Provides methods for login, session creation, retrieval, and deletion.
 *
 * @package modules\user\service
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class AuthService {
    /** @var UserRepository Repository for user-related database operations */
    private UserRepository $userRepository;

    /** @var SessionRepository Repository for session-related database operations */
    private SessionRepository $sessionRepository;

    /** @var string Secret key used to encode JWT tokens */
    private string $jwtKey = 'super_secret_key'; // TODO: Use env or config

    /**
     * AuthService constructor.
     * Initializes UserRepository and SessionRepository instances.
     */
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->sessionRepository = new SessionRepository();
    }

    /**
     * Authenticate a user and generate a JWT token.
     *
     * @param string $username The username of the user
     * @param string $password The user's password
     * @return array Array containing 'token', 'expires', and 'user' data
     * @throws Exception If the user does not exist or credentials are invalid
     */
    public function login(string $username, string $password): array {
        $user = $this->userRepository->getByUsername($username);

        if (!$user) {
            throw new Exception('User not found', HttpStatus::NOT_FOUND);
        }

        if (!password_verify($password, $user->password)) {
            throw new Exception('Invalid credentials', HttpStatus::FORBIDDEN);
        }

        $issuedAt = time();
        $expiresAt = $issuedAt + 3600;
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => $issuedAt,
            'exp' => $expiresAt
        ];

        $jwt = JWT::encode($payload, $this->jwtKey, 'HS256');

        $this->sessionRepository->createSession($user->id, $jwt, $issuedAt, $expiresAt);

        return [
            'token' => $jwt,
            'expires' => $expiresAt,
            'user' => $user,
        ];
    }

    /**
     * Delete a session by its JWT token.
     *
     * @param string $jwt The JWT token of the session to delete
     */
    public function deleteSession(string $jwt): void {
        $this->sessionRepository->deleteSession($jwt);
    }

    /**
     * Retrieve session data by JWT token.
     *
     * @param string $jwt The JWT token of the session
     * @return array|null Session data array or null if not found
     */
    public function getSession(string $jwt): ?array {
        return $this->sessionRepository->getSession($jwt);
    }
}
