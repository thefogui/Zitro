<?php

namespace modules\user\dto;

use modules\user\dto\DepartmentOutputDTO;
use modules\user\dto\CompanyPositionOutputDTO;

/**
 * Data Transfer Object for output representation of a User.
 * Encapsulates all fields returned by the user service, including metadata and assignments.
 * 
 * @package modules\user\dto
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserOutputDTO {
    /** @var int The unique identifier of the user */
    public int $id;

    /** @var string The username of the user */
    public string $username;

    /** @var string The email of the user */
    public string $email;

    /** @var string The first name of the user */
    public string $firstname;

    /** @var string|null The last name of the user */
    public ?string $lastname;

    /** @var string|null The password of the user (optional, usually hashed) */
    public ?string $password;

    /** @var int The start date of the user (timestamp) */
    public int $startdate;

    /** @var int Timestamp of the last modification */
    public int $timemodified;

    /** @var int|null ID of the user who last modified this user */
    public ?int $modifiedBy;

    /** @var bool Indicates whether the user has been soft-deleted */
    public bool $deleted;

    /** @var DepartmentOutputDTO|null The department assigned to the user */
    public ?DepartmentOutputDTO $department = null;

    /** @var CompanyPositionOutputDTO|null The company position assigned to the user */
    public ?CompanyPositionOutputDTO $position = null;

    /**
     * UserOutputDTO constructor.
     *
     * @param array $data Associative array containing user data.
     * Expected keys: 'id', 'username', 'email', 'firstname', 'lastname', 'password',
     * 'startdate', 'timemodified', 'modifiedby', 'deleted'
     */
    public function __construct(array $data) {
        $this->id = (int) $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'] ?? null;
        $this->startdate = (int) $data['startdate'];
        $this->timemodified = (int) $data['timemodified'];
        $this->modifiedBy = isset($data['modifiedby']) ? (int) $data['modifiedby'] : null;
        $this->deleted = (bool) $data['deleted'];
        $this->password = $data['password'] ?? null;
    }

    /**
     * Set the department assigned to the user.
     *
     * @param DepartmentOutputDTO|null $dept The department DTO or null
     */
    public function setDepartment(?DepartmentOutputDTO $dept): void {
        $this->department = $dept;
    }

    /**
     * Set the company position assigned to the user.
     *
     * @param CompanyPositionOutputDTO|null $pos The position DTO or null
     */
    public function setPosition(?CompanyPositionOutputDTO $pos): void {
        $this->position = $pos;
    }
}
