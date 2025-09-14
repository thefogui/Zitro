<?php

namespace modules\user\controller;

use modules\user\service\CompanyPositionService;
use modules\user\dto\CompanyPositionDTO;
use modules\user\dto\CompanyPositionOutputDTO;
use core\BaseController;
use core\HttpStatus;
use Exception;

/**
 * Controller to manage company positions.
 * Provides endpoints to list, get, create, update, and delete company positions.
 *
 * @package modules\user\controller
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class CompanyPositionController extends BaseController {
    private CompanyPositionService $service;

    /**
     * CompanyPositionController constructor.
     * Initializes the CompanyPositionService instance.
     */
    public function __construct() {
        parent::__construct();
        $this->service = new CompanyPositionService();
    }

    /**
     * List all company positions.
     *
     * GET /api/company-position/list
     *
     * @param array $requestData Optional request data
     * @return array List of all positions
     */
    public function list(array $requestData): array {
        return $this->service->listAllPositions();
    }

    /**
     * Retrieve a company position by its ID.
     *
     * GET /api/company-position/get/{id}
     *
     * @param array $requestData Must include 'param' key with position ID
     * @return array Position data
     * @throws Exception If 'param' is missing
     */
    public function get(array $requestData): CompanyPositionOutputDTO {
        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        return $this->service->getPosition((int)$requestData['param']);
    }

    /**
     * Create a new company position.
     *
     * POST /api/company-position/create
     *
     * @param array $requestData Must include 'name' of the position
     * @return array Created position data
     * @throws Exception If token validation fails
     */
    public function create(array $requestData): CompanyPositionOutputDTO {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        $dto = new CompanyPositionDTO(
            null,
            $requestData['name'] ?? '',
            $currentUserId
        );

        return $this->service->createPosition($dto);
    }

    /**
     * Update an existing company position by ID.
     *
     * POST /api/company-position/update/{id}
     *
     * @param array $requestData Must include 'param' with position ID and optionally 'name'
     * @return CompanyPositionOutputDTO Updated position data
     * @throws Exception If 'param' is missing or token validation fails
     */
    public function update(array $requestData): CompanyPositionOutputDTO {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        $dto = new CompanyPositionDTO(
            (int)$requestData['param'],
            $requestData['name'] ?? '',
            $currentUserId
        );

        return $this->service->updatePosition($dto);
    }

    /**
     * Delete a company position by ID.
     * 
     * DELETE /api/company-position/delete/{id}
     *
     * @param array $requestData Must include 'param' key with position ID
     * @return bool Result of deletion
     * @throws Exception If 'param' is missing or token validation fails
     */
    public function delete(array $requestData): bool {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        return $this->service->deletePosition((int) $requestData['param'], $currentUserId);
    }
}
