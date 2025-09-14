<?php

namespace modules\user\dto;

/**
 * Data Transfer Object for a Department.
 * Encapsulates the properties of a department for transfer between layers.
 * 
 * @package modules\user\dto
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class DepartmentDTO {
    /** @var int|null The unique identifier of the department */
    public ?int $id;

    /** @var string The name of the department */
    public string $name;

    /** @var int|null ID of the user who last modified the department */
    public ?int $modifiedBy;

    /**
     * DepartmentDTO constructor.
     *
     * @param int|null $id The unique identifier of the department
     * @param string $name The name of the department
     * @param int|null $modifiedBy ID of the user who last modified the department (optional)
     */
    public function __construct(
        ?int $id,
        string $name,
        ?int $modifiedBy = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->modifiedBy = $modifiedBy;
    }
}
