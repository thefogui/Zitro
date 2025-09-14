<?php

namespace modules\user\dto;

/**
 * Data Transfer Object for output representation of a Department.
 * Encapsulates all fields returned by the department service, including metadata.
 *
 * @package modules\user\dto
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class DepartmentOutputDTO {
    /** @var int The unique identifier of the department */
    public int $id;

    /** @var string The name of the department */
    public string $name;

    /** @var int Timestamp of the last modification */
    public int $timemodified;

    /** @var int|null ID of the user who last modified the department */
    public ?int $modifiedBy;

    /** @var bool Indicates whether the department has been soft-deleted */
    public bool $deleted;

    /**
     * DepartmentOutputDTO constructor.
     *
     * @param array $data Associative array containing department data.
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
