<?php

namespace modules\user\controller;

use modules\user\service\UserService;
use modules\user\dto\UserDTO;
use modules\user\dto\UserOutputDTO;
use modules\user\service\UserCompanyPositionAssignmentService;
use core\BaseController;
use core\HttpStatus;
use Exception;

/**
 * Controller to manage users.
 * Provides endpoints to list, get, create, update, and delete users.
 * Also manages user assignments to departments and company positions.
 *
 * @package modules\user\controller
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserController extends BaseController {
    private UserService $service;
    private UserCompanyPositionAssignmentService $userCompanyPositionAssignmentService;

    /**
     * UserController constructor.
     * Initializes service instances.
     */
    public function __construct() {
        parent::__construct();
        $this->service = new UserService();
        $this->userCompanyPositionAssignmentService = new UserCompanyPositionAssignmentService();
    }

    /**
     * List all users.
     *
     * GET /api/user/user/list
     *
     * @param array $requestData Optional request data
     * @return array List of users
     */
    public function list(array $requestData): array {
        return $this->service->listAllUsers();
    }

    /**
     * Retrieve a user by ID.
     *
     * GET /api/user/user/get/{id}
     *
     * @param array $requestData Must include 'param' key with user ID
     * @return UserOutputDTO User data
     * @throws Exception If 'param' is missing
     */
    public function get(array $requestData): UserOutputDTO {
        if (empty($requestData['param'])) {
            throw new Exception('The id is required to fetch an user', HttpStatus::REQUIRED_FIELD);
        }

        $id = (int) $requestData['param'];
        return $this->service->getUser($id);
    }

    /**
     * Create a new user.
     *
     * POST /api/user/user/create
     *
     * @param array $requestData Must include 'username', 'email', 'firstname', 'password', 'department', 'position'
     * @return UserOutputDTO Created user data
     * @throws Exception If token validation fails or required fields are missing
     */
    public function create(array $requestData): UserOutputDTO {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        $dto = new UserDTO(
            null,
            $requestData['username'] ?? '',
            $requestData['email'] ?? '',
            $requestData['firstname'] ?? '',
            $requestData['lastname'] ?? null,
            $requestData['password'] ?? '',
            $currentUserId
        );

        $userOutput = $this->service->createUser($dto);

        $this->userCompanyPositionAssignmentService->assign(
            $userOutput->id,
            $requestData['department'],
            $requestData['position'],
            $currentUserId
        );

        return $userOutput;
    }

    /**
     * Update an existing user by ID.
     *
     * POST /api/user/user/update/{id}
     *
     * @param array $requestData Must include 'param' with user ID and optionally other fields
     * @return UserOutputDTO Updated user data
     * @throws Exception If 'param' is missing or token validation fails
     */
    public function update(array $requestData): UserOutputDTO {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        $id = (int) $requestData['param'];

        $user = new UserDTO(
            $id,
            $requestData['username'] ?? '',
            $requestData['email'] ?? '',
            $requestData['firstname'] ?? '',
            $requestData['lastname'] ?? null,
            $requestData['password'] ?? null,
            $currentUserId
        );

        $this->userCompanyPositionAssignmentService->assign(
            $user->id,
            $requestData['department'],
            $requestData['position'],
            $currentUserId
        );

        return $this->service->updateUser($user);
    }

    /**
     * Delete a user by ID.
     *
     * DELETE /api/user/user/delete/{id}
     *
     * @param array $requestData Must include 'param' key with user ID
     * @return bool Result of deletion
     * @throws Exception If 'param' is missing or token validation fails
     */
    public function delete(array $requestData): bool {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        $id = (int) $requestData['param'];
        return $this->service->deleteUser($id, $currentUserId);
    }
}
