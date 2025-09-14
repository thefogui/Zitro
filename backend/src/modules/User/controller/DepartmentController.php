<?php

namespace modules\user\controller;

use modules\user\service\DepartmentService;
use modules\user\dto\DepartmentDTO;
use modules\user\dto\DepartmentOutputDTO;
use core\BaseController;
use core\HttpStatus;
use Exception;


/**
 * Controller to manage company departments.
 * Provides endpoints to list, get, create, update, and delete departments.
 *
 * @package modules\user\controller
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class DepartmentController extends BaseController {
    private DepartmentService $service;

    /**
     * DepartmentController constructor.
     * Initializes the DepartmentService instance.
     */
    public function __construct() {
        parent::__construct();
        $this->service = new DepartmentService();
    }

    /**
     * List all departments.
     *
     * GET /api/department/list
     *
     * @param array $requestData Optional request data
     * @return array List of all departments
     */
    public function list(array $requestData): array {
        return $this->service->listAllDepartments();
    }

    /**
     * Retrieve a department by its ID.
     *
     * GET /api/department/get/{id}
     *
     * @param array $requestData Must include 'param' key with department ID
     * @return DepartmentOutputDTO Department data
     * @throws Exception If 'param' is missing
     */
    public function get(array $requestData): DepartmentOutputDTO {
        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        return $this->service->getDepartment((int)$requestData['param']);
    }

    /**
     * Create a new department.
     *
     * POST /api/department/create
     *
     * @param array $requestData Must include 'name' of the department
     * @return DepartmentOutputDTO Created department data
     * @throws Exception If token validation fails
     */
    public function create(array $requestData): DepartmentOutputDTO {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        $dto = new DepartmentDTO(
            null,
            $requestData['name'] ?? '',
            $currentUserId
        );

        return $this->service->createDepartment($dto);
    }

    /**
     * Update an existing department by ID.
     *
     * POST /api/department/update/{id}
     *
     * @param array $requestData Must include 'param' with department ID and optionally 'name'
     * @return DepartmentOutputDTO Updated department data
     * @throws Exception If 'param' is missing or token validation fails
     */
    public function update(array $requestData): DepartmentOutputDTO {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        $dto = new DepartmentDTO(
            (int) $requestData['param'],
            $requestData['name'] ?? '',
            $currentUserId
        );

        return $this->service->updateDepartment($dto);
    }

    /**
     * Delete a department by ID.
     *
     * DELETE /api/department/delete/{id}
     *
     * @param array $requestData Must include 'param' key with department ID
     * @return bool Result of deletion
     * @throws Exception If 'param' is missing or token validation fails
     */
    public function delete(array $requestData): bool {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        return $this->service->deleteDepartment((int)$requestData['param'], $currentUserId);
    }
}
