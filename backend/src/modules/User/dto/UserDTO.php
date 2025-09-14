<?php

namespace modules\user\dto;

/**
 * Data Transfer Object for a User.
 * Encapsulates the properties of a user for transfer between layers.
 *
 * @package modules\user\dto
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserDTO {
    /** @var int|null The unique identifier of the user */
    public ?int $id;

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

    /** @var int|null ID of the user who last modified this user */
    public ?int $modifiedBy;

    /**
     * UserDTO constructor.
     *
     * @param int|null $id The unique identifier of the user
     * @param string $username The username of the user
     * @param string $email The email of the user
     * @param string $firstname The first name of the user
     * @param string|null $lastname The last name of the user (optional)
     * @param string|null $password The password of the user (optional)
     * @param int|null $modifiedBy ID of the user who last modified this user (optional)
     */
    public function __construct(
        ?int $id,
        string $username,
        string $email,
        string $firstname,
        ?string $lastname = null,
        ?string $password = null,
        ?int $modifiedBy = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
        $this->modifiedBy = $modifiedBy;
    }
}
