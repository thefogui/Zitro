<?php

namespace modules\app\controller;

use modules\app\service\AppService;
use modules\app\dto\AppDTO;
use core\BaseController;
use Exception;
use core\HttpStatus;

/**
 * Controller to handle requests against the App model.
 * Provides endpoints for CRUD operations on apps.
 *
 * @package modules\app\controller
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class AppController extends BaseController {
    private AppService $service;

    /**
     * AppController constructor.
     * Initializes the service instance.
     */
    public function __construct() {
        parent::__construct();
        $this->service = new AppService();
    }

    /**
     * Retrieve a list of all apps saved in the database.
     *
     * GET /api/app/app/list
     *
     * @param array $requestData Optional request data (not used here)
     * @return array List of apps
     * @throws Exception If an error occurs while fetching apps
     */
    public function list(array $requestData): array {
        return $this->service->listAllApps();
    }

    /**
     * Retrieve a single app by its ID.
     *
     * GET /api/app/get/{id}
     *
     * @param array $requestData Must include 'param' key with the app ID
     * @return array App data
     * @throws Exception If 'param' is missing or app not found
     */
    public function get(array $requestData) {
        if (empty($requestData['param'])) {
            throw new Exception('The id is required to fetch an app', HttpStatus::REQUIRED_FIELD);
        }

        $id = (int) $requestData['param'];

        return $this->service->getApp($id);
    }

    /**
     * Create a new app.
     *
     * POST /api/app/create
     *
     * @param array $requestData Must include 'name', 'url', and 'active' keys
     * @return array Created app data
     * @throws Exception If token validation fails or creation fails
     */
    public function create(array $requestData) {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        $dto = new AppDTO(
            null,
            $requestData['name'] ?? '',
            $requestData['url'] ?? '',
            (int) filter_var($requestData['active'], FILTER_VALIDATE_BOOLEAN),
            $currentUserId
        );

        return $this->service->createApp($dto);
    }

    /**
     * Update an existing app by ID.
     *
     * POST /api/app/update/{id}
     *
     * @param array $requestData Must include 'param' with app ID, and optionally 'name', 'url', 'active'
     * @return array Updated app data
     * @throws Exception If 'param' is missing or update fails
     */
    public function update(array $requestData) {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        $id = (int) $requestData['param'];

        $dto = new AppDTO(
            $id,
            $requestData['name'] ?? '',
            $requestData['url'] ?? '',
            (int) filter_var($requestData['active'], FILTER_VALIDATE_BOOLEAN),
            $currentUserId
        );

        return $this->service->updateApp($dto);
    }

    /**
     * Delete an app by ID.
     *
     * DELETE /api/app/delete/{id}
     *
     * @param array $requestData Must include 'param' key with the app ID
     * @return array Result of deletion
     * @throws Exception If 'param' is missing or deletion fails
     */
    public function delete(array $requestData) {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['param'])) {
            throw new Exception('The id is required', HttpStatus::REQUIRED_FIELD);
        }

        $id = (int) $requestData['param'];

        return $this->service->deleteApp($id, $currentUserId);
    }
}
