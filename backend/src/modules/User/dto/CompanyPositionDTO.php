<?php

namespace modules\user\dto;

/**
 * Data Transfer Object for a company position.
 * Encapsulates the properties of a company position for transfer between layers.
 * 
 * @package modules\user\dto
 */
class CompanyPositionDTO {
    /** @var int|null The unique identifier of the company position */
    public ?int $id;

    /** @var string The name of the company position */
    public string $name;

    /** @var int|null ID of the user who last modified the position */
    public ?int $modifiedBy;

    /**
     * CompanyPositionDTO constructor.
     *
     * @param int|null $id The unique identifier of the position
     * @param string $name The name of the position
     * @param int|null $modifiedBy ID of the user who last modified the position (optional)
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
