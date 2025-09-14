<?php
namespace modules\user\service;

use modules\user\repository\UserCompanyPositionRepository;
use modules\user\dto\UserCompanyPositionOutputDTO;
use modules\user\repository\DepartmentRepository;
use modules\user\repository\CompanyPositionRepository;
use modules\user\dto\DepartmentDTO;
use modules\user\dto\CompanyPositionDTO;
use core\HttpStatus;
use Exception;

/**
 * Service class to manage assignments of users to departments and company positions.
 * Provides methods to assign, revoke, retrieve, and check assignments.
 *
 * @package modules\user\service
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserCompanyPositionAssignmentService {
    private UserCompanyPositionRepository $repository;
    private DepartmentRepository $departmentRepository;
    private CompanyPositionRepository $companyPositionRepository;

    public function __construct() {
        $this->repository = new UserCompanyPositionRepository();
        $this->departmentRepository = new DepartmentRepository();
        $this->companyPositionRepository = new CompanyPositionRepository();
    }

    /**
     * Assign a user to a department and/or a position.
     * If the department or position is a string and does not exist, it will be created.
     * If the user already has an assignment, it will be revoked first.
     *
     * @param int $userId User ID
     * @param int|string|null $department Department ID, name, or null
     * @param int|string|null $position Company position ID, name, or null
     * @param int|null $modifiedBy ID of the user performing the assignment
     * @return UserCompanyPositionOutputDTO The created assignment
     */
    public function assign(int $userId, $department = null, $position = null, ?int $modifiedBy = null): UserCompanyPositionOutputDTO {
        $departmentId = $this->resolveDepartment($department, $modifiedBy);
        $positionId = $this->resolvePosition($position, $modifiedBy);

        // Revoke existing assignment if exists
        $existingAssignment = $this->getAssignmentForUser($userId);
        if ($existingAssignment) {
            $this->revoke($existingAssignment->id, $modifiedBy);
        }

        $this->repository->assign($userId, $departmentId, $positionId, $modifiedBy);

        return $this->repository->getAssignmentForUser($userId);
    }

    /**
     * Revoke an assignment by its ID.
     *
     * @param int $assignmentId Assignment ID
     * @param int|null $modifiedBy ID of the user performing the revocation
     * @return bool True if the assignment was revoked successfully
     */
    public function revoke(int $assignmentId, ?int $modifiedBy = null): bool {
        return $this->repository->revoke($assignmentId, $modifiedBy) > 0;
    }

    /**
     * Get the assignment of a user.
     *
     * @param int $userId User ID
     * @return UserCompanyPositionOutputDTO|null Assignment DTO or null if no assignment exists
     */
    public function getAssignmentForUser(int $userId): ?UserCompanyPositionOutputDTO {
        return $this->repository->getAssignmentForUser($userId);
    }

    /**
     * Check if a user has an assignment.
     *
     * @param int $userId User ID
     * @param int $departmentId Department ID
     * @param int $companyPositionId Company position ID
     * @return bool True if the user has any assignment
     */
    public function userHasAssignment(int $userId, int $departmentId, int $companyPositionId): bool {
        return !empty($this->getAssignmentForUser($userId));
    }

    /**
     * Resolve a department ID from an ID or a name.
     * Returns null if input is null.
     *
     * @param int|string|null $department Department ID, name, or null
     * @param int|null $modifiedBy ID of the user performing the action
     * @return int|null Department ID or null
     */
    private function resolveDepartment($department, ?int $modifiedBy): ?int {
        if (is_null($department)) {
            return null;
        }

        if (is_int($department)) {
            return $department;
        }

        if (!empty($department)) {
            $existing = $this->departmentRepository->getByName($department);
            if ($existing) {
                return $existing->id;
            }
            $dto = new DepartmentDTO(null, $department, $modifiedBy);
            $created = $this->departmentRepository->create($dto, time());
            return $created->id;
        }

        return null;
    }

    /**
     * Resolve a position ID from an ID or a name.
     * Returns null if input is null.
     *
     * @param int|string|null $position Position ID, name, or null
     * @param int|null $modifiedBy ID of the user performing the action
     * @return int|null Position ID or null
     */
    private function resolvePosition($position, ?int $modifiedBy): ?int {
        if (is_null($position)) {
            return null;
        }

        if (is_int($position)) {
            return $position;
        }

        if (!empty($position)) {
            $existing = $this->companyPositionRepository->getByName($position);
            if ($existing) {
                return $existing->id;
            }
            $dto = new CompanyPositionDTO(null, $position, $modifiedBy);
            $created = $this->companyPositionRepository->create($dto, time());
            return $created->id;
        }

        return null;
    }
}
