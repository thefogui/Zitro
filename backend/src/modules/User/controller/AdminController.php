<?php

namespace modules\user\controller;

use modules\user\service\AdminService;
use core\BaseController;
use core\HttpStatus;
use Exception;

/**
 * Controller to manage admin users.
 * Provides endpoints for adding and removing admins, as well as configuration checks.
 *
 * @package modules\user\controller
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class AdminController extends BaseController {
    private AdminService $adminService;

    /**
     * AdminController constructor.
     * Initializes the AdminService instance.
     */
    public function __construct() {
        parent::__construct();
        $this->adminService = new AdminService();
    }

    /**
     * Check if the admin configuration is needed.
     * Returns true if no admin is set.
     *
     * GET /api/admin/configure
     *
     * @return bool True if admin needs to be configured, false otherwise
     */
    public function configure(): bool {
        return !$this->adminService->isAdminSet();
    }

    /**
     * Add a new admin by username.
     *
     * POST /api/admin/add
     *
     * @param array $requestData Must include 'username'
     * @return array Details of the newly added admin
     * @throws Exception If username is missing, current user is not admin, or target user is already admin
     */
    public function add(array $requestData): array {
        $currentUserId = $this->validateTokenAndGetCurrentUser($requestData);

        if (empty($requestData['username'])) {
            throw new Exception("The field 'username' is required for this operation", HttpStatus::REQUIRED_FIELD);
        }

        $currentUser = $this->adminService->getUserById($currentUserId);

        if ($this->adminService->isAdminSet() && !$this->adminService->isThisUserAdmin($currentUser['username'])) {
            throw new Exception('Your user is not an admin', HttpStatus::UNAUTHORIZED);
        }

        if ($this->adminService->isThisUserAdmin($requestData['username'])) {
            throw new Exception('This user is already an admin', HttpStatus::REQUIRED_FIELD);
        }

        $adminId = $this->adminService->addAdminByUsername($requestData['username'], $currentUserId);

        return [
            'admin_id' => $adminId,
            'username' => $requestData['username'],
            'active' => 1
        ];
    }
}
