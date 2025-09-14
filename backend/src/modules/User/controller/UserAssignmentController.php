<?php

namespace modules\user\controller;

use modules\user\service\UserCompanyPositionAssignmentService;
use modules\user\dto\UserCompanyPositionOutputDTO;
use core\BaseController;
use core\HttpStatus;
use Exception;

/**
 * Controller to manage user assignments to departments and company positions.
 * Provides endpoints to list, assign, revoke, and check user assignments.
 * 
 * @package modules\user\controller
 * @author Vitor 
 * Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserAssignmentController extends BaseController {
    private UserCompanyPositionAssignmentService $service;

    /**
     * UserAssignmentController constructor.
     * Initializes the UserCompanyPositionAssignmentService instance.
     */
    public function __construct() {
        parent::__construct();
        $this->service = new UserCompanyPositionAssignmentService();
    }

    /**
     * List all assignments for a given user.
     * 
     * GET /api/user/assignment/list/{userId}
     *
     * @param array $requestData Must include 'param' key with user ID
     * @return UserCompanyPositionOutputDTO List of assignments
     * @throws Exception If 'param' is missing
     */
    public function list(array $requestData): ?UserCompanyPositionOutputDTO {
        if (empty($requestData['param'])) {
            throw new Exception('The userId is required', HttpStatus::REQUIRED_FIELD);
        }

        $currentUserId = (int) $requestData['param'];
        return $this->service->getAssignmentForUser($currentUserId);
    }

    /**
     * Assign a user to a department and company position.
     * 
     * POST /api/user/assignment/assign
     *
     * @param array $requestData Must include 'userId', 'departmentId', 'companyPositionId'
     * @return UserCompanyPositionOutputDTO Details of the assignment
     * @throws Exception If any required field is missing
     */
    public function assign(array $requestData): UserCompanyPositionOutputDTO {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        $userId = (int)($requestData['userId'] ?? 0);
        $departmentId = (int)($requestData['departmentId'] ?? 0);
        $companyPositionId = (int)($requestData['companyPositionId'] ?? 0);

        if (!$userId || !$departmentId || !$companyPositionId) {
            throw new Exception('userId, departmentId and companyPositionId are required', HttpStatus::REQUIRED_FIELD);
        }

        return $this->service->assign($userId, $departmentId, $companyPositionId, $currentUserId);
    }

    /**
     * Revoke a user assignment by assignment ID.
     *
     * DELETE /api/user/assignment/revoke/{assignmentId}
     *
     * @param array $requestData Must include 'param' key with assignment ID
     * @return array Result of the revocation
     * @throws Exception If 'param' is missing
     */
    public function revoke(array $requestData): bool {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The assignmentId is required', HttpStatus::REQUIRED_FIELD);
        }

        $assignmentId = (int) $requestData['param'];
        return $this->service->revoke($assignmentId, $currentUserId);
    }

    /**
     * Check if a user has a specific assignment.
     *
     * GET /api/user/assignment/check
     *
     * @param array $requestData Must include 'userId', 'departmentId', 'companyPositionId'
     * @return bool True if the user has the assignment, false otherwise
     * @throws Exception If any required field is missing
     */
    public function check(array $requestData): bool {
        $userId = (int) ($requestData['userId'] ?? 0);
        $departmentId = (int) ($requestData['departmentId'] ?? 0);
        $companyPositionId = (int) ($requestData['companyPositionId'] ?? 0);

        if (!$userId || !$departmentId || !$companyPositionId) {
            throw new Exception('userId, departmentId and companyPositionId are required', HttpStatus::REQUIRED_FIELD);
        }

        return $this->service->userHasAssignment($userId, $departmentId, $companyPositionId);
    }
}
