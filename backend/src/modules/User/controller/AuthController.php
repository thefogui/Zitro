<?php

namespace modules\user\controller;

use modules\user\service\AuthService;
use modules\user\service\UserService;
use modules\user\dto\UserDTO;
use modules\user\service\UserCompanyPositionAssignmentService;
use core\HttpStatus;
use Exception;

/**
 * Controller responsible for user authentication and registration.
 * Provides endpoints for registering, logging in, and logging out users.
 *
 * @package modules\user\controller
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class AuthController {
    private AuthService $authService;
    private UserService $userService;
    private UserCompanyPositionAssignmentService $userCompanyPositionAssignmentService;

    /**
     * AuthController constructor.
     * Initializes the service instances.
     */
    public function __construct() {
        $this->authService = new AuthService();
        $this->userService = new UserService();
        $this->userCompanyPositionAssignmentService = new UserCompanyPositionAssignmentService();
    }

    /**
     * Register a new user.
     *
     * POST /api/auth/register
     *
     * @param array $requestData Must include 'username', 'email', 'firstname', 'password'. 
     * Optionally 'lastname', 'department', 'position'.
     * @return array Login data after registration
     * @throws Exception If required fields are missing
     */
    public function register(array $requestData): array {
        $required = ['username', 'email', 'firstname', 'password'];
        foreach ($required as $field) {
            if (empty($requestData[$field])) {
                throw new Exception("The field '$field' is required", HttpStatus::REQUIRED_FIELD);
            }
        }

        $dto = new UserDTO(
            null,
            $requestData['username'],
            $requestData['email'],
            $requestData['firstname'],
            $requestData['lastname'] ?? null,
            $requestData['password'],
            null
        );

        $userOutput = $this->userService->createUser($dto);

        $this->userCompanyPositionAssignmentService->assign(
            $userOutput->id,
            $requestData['department'] ?? null,
            $requestData['position'] ?? null,
            $userOutput->id
        );

        $loginData = $this->authService->login(
            $userOutput->username,
            $requestData['password']
        );

        return $loginData;
    }

    /**
     * Login a user with username and password.
     * 
     * POST /api/auth/login
     *
     * @param array $requestData Must include 'username' and 'password'
     * @return array Login session data
     * @throws Exception If username or password is missing
     */
    public function login(array $requestData): array {
        if (empty($requestData['username']) || empty($requestData['password'])) {
            throw new Exception('Username and password required', HttpStatus::REQUIRED_FIELD);
        }

        return $this->authService->login(
            $requestData['username'],
            $requestData['password']
        );
    }

    /**
     * Logout a user by deleting their session.
     * 
     * POST /api/auth/logout
     *
     * @param string $jwt JWT token of the session to delete
     * @return bool True if logout successful
     * @throws Exception If session not found
     */
    public function logout(string $jwt): bool {
        $session = $this->authService->getSession($jwt);
        if (empty($session)) {
            throw new Exception('Session not found', HttpStatus::NOT_FOUND);
        }

        $this->authService->deleteSession($jwt);
        return true;
    }
}
