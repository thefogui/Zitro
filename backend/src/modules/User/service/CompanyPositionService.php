<?php
namespace modules\user\service;

use modules\user\repository\CompanyPositionRepository;
use modules\user\dto\CompanyPositionDTO;
use modules\user\dto\CompanyPositionOutputDTO;
use core\HttpStatus;
use Exception;

/**
 * Service class to manage company positions.
 * Provides methods to create, retrieve, update, delete, and list company positions.
 *
 * @package modules\user\service
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class CompanyPositionService {
    /** @var CompanyPositionRepository Repository instance for company position database operations */
    private CompanyPositionRepository $repository;

    /**
     * CompanyPositionService constructor.
     * Initializes the repository instance.
     */
    public function __construct() {
        $this->repository = new CompanyPositionRepository();
    }

    /**
     * Create a new company position.
     *
     * @param CompanyPositionDTO $dto DTO containing company position data
     * @return CompanyPositionOutputDTO The created company position output DTO
     * @throws Exception If required fields are missing or a position with the same name already exists
     */
    public function createPosition(CompanyPositionDTO $dto): CompanyPositionOutputDTO {
        $this->checkRequiredFields($dto);

        $existing = $this->repository->getByName($dto->name);
        if ($existing) {
            throw new Exception("A company position with name '{$dto->name}' already exists", HttpStatus::REQUIRED_FIELD);
        }

        $timemodified = time();
        return $this->repository->create($dto, $timemodified);
    }

    /**
     * Retrieve a company position by ID.
     *
     * @param int $id The position ID
     * @return CompanyPositionOutputDTO The company position output DTO
     * @throws Exception If the position is not found
     */
    public function getPosition(int $id): CompanyPositionOutputDTO {
        $position = $this->repository->getById($id);
        if (empty($position)) {
            throw new Exception('Company position not found', HttpStatus::NOT_FOUND);
        }

        return $position;
    }

    /**
     * Update an existing company position.
     *
     * @param CompanyPositionDTO $dto DTO containing updated data
     * @return CompanyPositionOutputDTO The updated company position output DTO
     * @throws Exception If the ID is missing, position not found, name conflicts, or no changes made
     */
    public function updatePosition(CompanyPositionDTO $dto): CompanyPositionOutputDTO {
        if (empty($dto->id)) {
            throw new Exception("The field 'id' is required", HttpStatus::REQUIRED_FIELD);
        }

        $position = $this->repository->getById($dto->id);
        if (empty($position)) {
            throw new Exception('Company position not found', HttpStatus::NOT_FOUND);
        }

        $this->checkRequiredFields($dto);

        if (!empty($dto->name)) {
            $existing = $this->repository->getByName($dto->name);
            if ($existing && $existing->id !== $dto->id) {
                throw new Exception("Another company position with name '{$dto->name}' already exists", HttpStatus::REQUIRED_FIELD);
            }
        }

        $timemodified = time();
        $updated = $this->repository->update($dto->id, $dto, $timemodified);

        if (empty($updated)) {
            throw new Exception('No changes were made', HttpStatus::REQUIRED_FIELD);
        }

        return $updated;
    }

    /**
     * Delete a company position by ID.
     *
     * @param int $id The position ID
     * @param int|null $modifiedBy ID of the user performing the deletion
     * @return bool True if deletion was successful, false otherwise
     * @throws Exception If the position is not found
     */
    public function deletePosition(int $id, ?int $modifiedBy): bool {
        $position = $this->repository->getById($id);
        if (empty($position)) {
            throw new Exception('Company position not found', HttpStatus::NOT_FOUND);
        }

        return $this->repository->delete($id, $modifiedBy) > 0;
    }

    /**
     * List all company positions.
     *
     * @return array Array of CompanyPositionOutputDTO objects
     */
    public function listAllPositions(): array {
        return $this->repository->listAll();
    }

    /**
     * Validate required fields for a company position DTO.
     *
     * @param CompanyPositionDTO $dto The DTO to validate
     * @throws Exception If the 'name' field is missing
     */
    private function checkRequiredFields(CompanyPositionDTO $dto) {
        if (empty($dto->name)) {
            throw new Exception("The field 'name' is required", HttpStatus::REQUIRED_FIELD);
        }
    }
}
