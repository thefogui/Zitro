<?php

namespace modules\user\dto;

/**
 * Data Transfer Object for output representation of a Company Position.
 * Encapsulates all fields returned by the company position service, including metadata.
 * 
 * @package modules\user\dto
 */
class CompanyPositionOutputDTO {
    /** @var int The unique identifier of the company position */
    public int $id;

    /** @var string The name of the company position */
    public string $name;

    /** @var int Timestamp of the last modification */
    public int $timemodified;

    /** @var int|null ID of the user who last modified the position */
    public ?int $modifiedBy;

    /** @var bool Indicates whether the position has been soft-deleted */
    public bool $deleted;

    /**
     * CompanyPositionOutputDTO constructor.
     *
     * @param array $data Associative array containing company position data.
     *  Expected keys: 'id', 'name', 'timemodified', 'modifiedby', 'deleted'
     */
    public function __construct(array $data) {
        $this->id = (int) $data['id'];
        $this->name = $data['name'];
        $this->timemodified = (int) $data['timemodified'];
        $this->modifiedBy = isset($data['modifiedby']) ? (int) $data['modifiedby'] : null;
        $this->deleted = (bool) $data['deleted'];
    }
}
