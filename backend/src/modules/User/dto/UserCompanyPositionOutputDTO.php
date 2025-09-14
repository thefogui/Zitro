<?php

namespace modules\user\dto;

/**
 * Data Transfer Object for output representation of a User's assignment to a department and company position.
 * Encapsulates all fields returned by the user-company-position assignment service, including metadata.
 *
 * @package modules\user\dto
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserCompanyPositionOutputDTO {
    /** @var int The unique identifier of the assignment */
    public int $id;

    /** @var int The ID of the user */
    public int $userId;

    /** @var int The ID of the department */
    public int $departmentId;

    /** @var int The ID of the company position */
    public int $companyPositionId;

    /** @var int Timestamp of the last modification */
    public int $timemodified;

    /** @var int|null ID of the user who last modified the assignment */
    public ?int $modifiedBy;

    /** @var bool Indicates whether the assignment has been soft-deleted */
    public bool $deleted;

    /**
     * UserCompanyPositionOutputDTO constructor.
     *
     * @param array $data Associative array containing assignment data.
     * Expected keys: 'id', 'userid', 'departmentid', 'companypositionid', 'timemodified', 'modifiedby', 'deleted'
     */
    public function __construct(array $data) {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['userid'];
        $this->departmentId = (int) $data['departmentid'];
        $this->companyPositionId = (int) $data['companypositionid'];
        $this->timemodified = (int) $data['timemodified'];
        $this->modifiedBy = isset($data['modifiedby']) ? (int) $data['modifiedby'] : null;
        $this->deleted = (bool) $data['deleted'];
    }
}
