<?php
namespace modules\app\service;

use modules\app\repository\AppRepository;
use modules\app\dto\AppDTO;
use modules\app\dto\AppOutputDTO;
use core\HttpStatus;
use Exception;

/**
 * Service class to handle business logic for App entities.
 * Provides methods to create, retrieve, update, delete, and list apps.
 * 
 * @package modules\app\service
 * @author Vitor 
 * Carvalho <vitorcarvalhodso@gmail.com>
 */
class AppService {
    /** @var AppRepository Repository instance for database operations */
    private AppRepository $appRepository;

    /**
     * AppService constructor.
     * Initializes the AppRepository instance.
     */
    public function __construct() {
        $this->appRepository = new AppRepository();
    }

    /**
     * Create a new app.
     *
     * @param AppDTO $dto The app data transfer object containing app info
     * @return AppOutputDTO The created app output DTO
     * @throws Exception If required fields are missing or app name already exists
     */
    public function createApp(AppDTO $dto): AppOutputDTO {
        $this->checkRequiredFields($dto);

        // Check if app with the same name already exists
        $existingApp = $this->appRepository->getByName($dto->name);
        if ($existingApp) {
            throw new Exception("The app with name '{$dto->name}' already exists", HttpStatus::REQUIRED_FIELD);
        }

        $timemodified = time();
        return $this->appRepository->create($dto, $timemodified);
    }

    /**
     * Retrieve an app by its ID.
     *
     * @param int $id The app ID
     * @return AppOutputDTO The app output DTO
     * @throws Exception If app not found
     */
    public function getApp(int $id): AppOutputDTO {
        $app = $this->appRepository->getById($id);
        if (empty($app)) {
            throw new Exception('App not found', HttpStatus::NOT_FOUND);
        }

        return $app;
    }

    /**
     * Update an existing app.
     *
     * @param AppDTO $dto The app DTO containing updated data
     * @return AppOutputDTO The updated app output DTO
     * @throws Exception If ID is missing, app not found, or no fields updated
     */
    public function updateApp(AppDTO $dto): AppOutputDTO {
        if (empty($dto->id)) {
            throw new Exception("The field 'id' is required", HttpStatus::REQUIRED_FIELD);
        }

        $app = $this->appRepository->getById($dto->id);
        if (empty($app)) {
            throw new Exception('App not found', HttpStatus::NOT_FOUND);
        }

        $this->checkRequiredFields($dto);

        $timemodified = time();
        $updated = $this->appRepository->update($dto->id, $dto, $timemodified);

        if (empty($updated)) {
            throw new Exception("No fields were updated", HttpStatus::REQUIRED_FIELD);
        }

        return $updated;
    }

    /**
     * Delete an app by ID.
     *
     * @param int $id The app ID
     * @param int|null $modifiedBy ID of the user performing the deletion
     * @return bool True if deletion was successful, false otherwise
     * @throws Exception If app not found
     */
    public function deleteApp(int $id, ?int $modifiedBy): bool {
        $app = $this->appRepository->getById($id);
        if (empty($app)) {
            throw new Exception('App not found', HttpStatus::NOT_FOUND);
        }

        return $this->appRepository->delete($id, $modifiedBy) > 0;
    }

    /**
     * List all apps.
     *
     * @return array Array of AppOutputDTO objects
     */
    public function listAllApps(): array {
        return $this->appRepository->listAll();
    }

    /**
     * Validate required fields for an AppDTO.
     *
     * @param AppDTO $dto The app DTO to validate
     * @throws Exception If any required field is missing
     */
    private function checkRequiredFields(AppDTO $dto) {
        $fields = ['name', 'url'];
        foreach ($fields as $field) {
            if (empty($dto->$field)) {
                throw new Exception("The field '$field' is required", HttpStatus::REQUIRED_FIELD);
            }
        }
    }
}
