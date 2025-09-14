<?php

namespace modules\user\service;

use modules\user\repository\AdminRepository;
use modules\user\repository\UserRepository;
use modules\user\dto\UserOutputDTO;
use core\HttpStatus;
use Exception;

/**
 * Service class to manage admin users.
 * Handles the addition, removal, and verification of admin privileges.
 *
 * @package modules\user\service
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class AdminService {
    /** @var AdminRepository Repository instance for admin-related database operations */
    private AdminRepository $adminRepository;

    /** @var UserRepository Repository instance for user-related database operations */
    private UserRepository $userRepository;

    /**
     * AdminService constructor.
     * Initializes AdminRepository and UserRepository instances.
     */
    public function __construct() {
        $this->adminRepository = new AdminRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Check if any admin users exist in the database.
     *
     * @return bool True if at least one admin exists, false otherwise
     */
    public function isAdminSet(): bool {
        return $this->adminRepository->thereAreAdminsSavedInTheDatabase();
    }

    /**
     * Add an admin user by username.
     *
     * @param string $username The username of the user to promote to admin
     * @param int|null $modifiedBy ID of the user performing the operation
     * @return int The ID of the newly added admin
     * @throws Exception If the user does not exist
     */
    public function addAdminByUsername(string $username, ?int $modifiedBy): int {
        $user = $this->userRepository->getByUsername($username);
        if (empty($user)) {
            throw new Exception("User '{$username}' not found", HttpStatus::NOT_FOUND);
        }

        return $this->adminRepository->add($user->id, $modifiedBy);
    }

    /**
     * Remove an admin user by username.
     *
     * @param string $username The username of the admin to remove
     * @param int|null $modifiedBy ID of the user performing the operation
     * @return int The number of affected rows
     * @throws Exception If the user does not exist
     */
    public function removeUserByUsername(string $username, ?int $modifiedBy): int {
        $user = $this->userRepository->getByUsername($username);
        if (empty($user)) {
            throw new Exception("User '{$username}' not found", HttpStatus::NOT_FOUND);
        }

        return $this->adminRepository->remove($user->id, $modifiedBy);
    }

    /**
     * Check if a specific user is an admin.
     *
     * @param string $username The username to check
     * @return bool True if the user is an admin, false otherwise
     * @throws Exception If the user does not exist
     */
    public function isThisUserAdmin(string $username): bool {
        $user = $this->userRepository->getByUsername($username);
        if (empty($user)) {
            throw new Exception("User '{$username}' not found", HttpStatus::NOT_FOUND);
        }

        return $this->adminRepository->isUserAdmin($user->id);
    }

    /**
     * Retrieve a user by their ID.
     *
     * @param int $userId The user ID
     * @return UserOutputDTO|null The user output DTO or null if not found
     */
    public function getUserById(int $userId): ?UserOutputDTO {
        return $this->userRepository->getById($userId);
    }
}
