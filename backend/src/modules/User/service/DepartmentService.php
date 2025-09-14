<?php
namespace modules\user\service;

use modules\user\repository\DepartmentRepository;
use modules\user\dto\DepartmentDTO;
use modules\user\dto\DepartmentOutputDTO;
use core\HttpStatus;
use Exception;

/**
 * Service class to manage departments.
 * Provides methods to create, retrieve, update, delete, and list departments.
 * 
 * @package modules\user\service
 * @author Vitor
 * Carvalho <vitorcarvalhodso@gmail.com>
 */
class DepartmentService {
    /** @var DepartmentRepository Repository instance for department database operations */
    private DepartmentRepository $repository;

    /**
     * DepartmentService constructor.
     * Initializes the repository instance.
     */
    public function __construct() {
        $this->repository = new DepartmentRepository();
    }

    /**
     * Create a new department.
     *
     * @param DepartmentDTO $dto DTO containing department data
     * @return DepartmentOutputDTO The created department output DTO
     * @throws Exception If required fields are missing or a department with the same name already exists
     */
    public function createDepartment(DepartmentDTO $dto): DepartmentOutputDTO {
        $this->checkRequiredFields($dto);

        $existing = $this->repository->getByName($dto->name);
        if ($existing) {
            throw new Exception("A department with name '{$dto->name}' already exists", HttpStatus::REQUIRED_FIELD);
        }

        $timemodified = time();
        return $this->repository->create($dto, $timemodified);
    }

    /**
     * Retrieve a department by ID.
     *
     * @param int $id The department ID
     * @return DepartmentOutputDTO The department output DTO
     * @throws Exception If the department is not found
     */
    public function getDepartment(int $id): DepartmentOutputDTO {
        $department = $this->repository->getById($id);
        if (!$department) {
            throw new Exception('Department not found', HttpStatus::NOT_FOUND);
        }
        return $department;
    }

    /**
     * Update an existing department.
     *
     * @param DepartmentDTO $dto DTO containing updated department data
     * @return DepartmentOutputDTO The updated department output DTO
     * @throws Exception If ID is missing, department not found, name conflicts, or no changes made
     */
    public function updateDepartment(DepartmentDTO $dto): DepartmentOutputDTO {
        if (!$dto->id) {
            throw new Exception("The field 'id' is required", HttpStatus::REQUIRED_FIELD);
        }

        $department = $this->repository->getById($dto->id);
        if (!$department) {
            throw new Exception('Department not found', HttpStatus::NOT_FOUND);
        }

        $this->checkRequiredFields($dto);

        if (!empty($dto->name)) {
            $existing = $this->repository->getByName($dto->name);
            if ($existing && $existing->id !== $dto->id) {
                throw new Exception("Another department with name '{$dto->name}' already exists", HttpStatus::REQUIRED_FIELD);
            }
        }

        $timemodified = time();
        $updated = $this->repository->update($dto->id, $dto, $timemodified);

        if (!$updated) {
            throw new Exception('No changes were made', HttpStatus::REQUIRED_FIELD);
        }

        return $updated;
    }

    /**
     * Delete a department by ID.
     *
     * @param int $id The department ID
     * @param int|null $modifiedBy ID of the user performing the deletion
     * @return bool True if deletion was successful, false otherwise
     * @throws Exception If the department is not found
     */
    public function deleteDepartment(int $id, ?int $modifiedBy): bool {
        $department = $this->repository->getById($id);
        if (!$department) {
            throw new Exception('Department not found', HttpStatus::NOT_FOUND);
        }

        return $this->repository->delete($id, $modifiedBy) > 0;
    }

    /**
     * List all departments.
     *
     * @return array Array of DepartmentOutputDTO objects
     */
    public function listAllDepartments(): array {
        return $this->repository->listAll();
    }

    /**
     * Validate required fields for a DepartmentDTO.
     *
     * @param DepartmentDTO $dto The DTO to validate
     * @throws Exception If the 'name' field is missing
     */
    private function checkRequiredFields(DepartmentDTO $dto) {
        if (empty($dto->name)) {
            throw new Exception("The field 'name' is required", HttpStatus::REQUIRED_FIELD);
        }
    }
}
