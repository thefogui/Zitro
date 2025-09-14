<?php
namespace modules\user\service;

use modules\user\repository\UserRepository;
use modules\user\dto\UserDTO;
use modules\user\dto\UserOutputDTO;
use modules\user\repository\CompanyPositionRepository;
use modules\user\repository\DepartmentRepository;
use modules\user\repository\UserCompanyPositionRepository;
use Exception;

/**
 * Service to handle user management.
 * Provides methods to create, retrieve, update, delete, and list users.
 * Also validates user email and required fields.
 * 
 * @package modules\user\service
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserService {
    private UserRepository $userRepository;
    private DepartmentRepository $departmentRepository;
    private CompanyPositionRepository $companyPositionRepository;
    private UserCompanyPositionRepository $userCompanyPositionRepository;
    private const COMPANY_DOMAIN = '@company.com';

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->companyPositionRepository = new CompanyPositionRepository;
        $this->departmentRepository = new DepartmentRepository();
        $this->userCompanyPositionRepository = new UserCompanyPositionRepository();
    }

    /**
     * Create a new user.
     * 
     * @param UserDTO $dto Data transfer object containing user information
     * @return UserOutputDTO Created user
     * @throws Exception If required fields are missing or email is invalid
     */
    public function createUser(UserDTO $dto): UserOutputDTO {
        $this->checkRequiredFields($dto, true);
        $this->validateUserEmail($dto->email);

        $dto->password = password_hash($dto->password, PASSWORD_DEFAULT);
        $startdate = time();
        $timemodified = time();

        return $this->userRepository->create($dto, $startdate, $timemodified);
    }

    /**
     * Retrieve a user by ID.
     * Includes department and position if assigned.
     * 
     * @param int $id User ID
     * @return UserOutputDTO
     * @throws Exception If user not found
     */
    public function getUser(int $id): UserOutputDTO {
        $user = $this->userRepository->getById($id);

        if (empty($user)) {
            throw new Exception('User not found', 404);
        }

        $assignment = $this->userCompanyPositionRepository->getAssignmentForUser($id);

        if (!empty($assignment)) {
            $dept = $this->departmentRepository->getById($assignment->departmentId);
            $pos = $this->companyPositionRepository->getById($assignment->companyPositionId);

            if (!empty($dept)) {
                $user->setDepartment($dept);
            }

            if (!empty($pos)) {
                $user->setPosition($pos);
            }
        }

        return $user;
    }

    /**
     * Update an existing user.
     * 
     * @param UserDTO $dto Data transfer object containing updated user information
     * @return UserOutputDTO Updated user
     * @throws Exception If required fields are missing, email is invalid, or user not found
     */
    public function updateUser(UserDTO $dto): UserOutputDTO {
        if (empty($dto->id)) {
            throw new Exception("The field 'id' is required", 422);
        }

        $user = $this->userRepository->getById($dto->id);
        if (empty($user)) {
            throw new Exception('User not found', 404);
        }

        $this->checkRequiredFields($dto, false);
        $this->validateUserEmail($dto->email);

        if (!empty($dto->password)) {
            $dto->password = password_hash($dto->password, PASSWORD_DEFAULT);
        }

        $timemodified = time();
        return $this->userRepository->update($dto->id, $dto, $timemodified);
    }

    /**
     * Delete a user by ID.
     * 
     * @param int $id User ID
     * @param int|null $modifiedBy ID of the user performing the deletion
     * @return bool True if the user was deleted
     * @throws Exception If user not found or trying to delete themselves
     */
    public function deleteUser(int $id, ?int $modifiedBy): bool {
        $user = $this->userRepository->getById($id);
        if (empty($user)) {
            throw new Exception('User not found', 404);
        }

        if ($user->id == $modifiedBy) {
            throw new Exception('You can\'t delete yourself');
        }

        $adminRepo = new \modules\user\repository\AdminRepository();
        if ($adminRepo->isUserAdmin($id)) {
            throw new \Exception('You cannot delete an admin user', 403);
        }

        return $this->userRepository->delete($id, $modifiedBy) > 0;
    }

    /**
     * List all users.
     * 
     * @return UserOutputDTO[] Array of users
     */
    public function listAllUsers(): array {
        return $this->userRepository->listAll();
    }

    /**
     * Check that required fields in a DTO are present.
     * 
     * @param UserDTO $dto
     * @param bool $checkPassword Whether to require the password field
     * @throws Exception If required fields are missing
     */
    private function checkRequiredFields(UserDTO $dto, bool $checkPassword) {
        $fields = ['username', 'email', 'firstname'];
        if ($checkPassword) {
            $fields[] = 'password';
        }

        foreach ($fields as $field) {
            if (empty($dto->$field)) {
                throw new Exception("The field '$field' is required", 422);
            }
        }
    }

    /**
     * Validate the email format and corporate domain.
     * 
     * @param string $email
     * @throws Exception If the email format is invalid or not corporate
     */
    private function validateUserEmail(string $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email has an invalid format', 422);
        }

        if (!str_ends_with($email, self::COMPANY_DOMAIN)) {
            throw new Exception('We only allow emails with corporative extension ' . self::COMPANY_DOMAIN, 422);
        }
    }
}
