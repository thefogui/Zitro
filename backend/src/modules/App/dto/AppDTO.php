<?php

namespace modules\app\dto;

/**
 * Data Transfer Object for App entities.
 * Encapsulates the properties of an app for transfer between layers.
 * 
 * @package modules\app\dto
 */
class AppDTO {
    /** @var int|null The unique identifier of the app */
    public ?int $id;

    /** @var string The name of the app */
    public string $name;

    /** @var string The URL associated with the app */
    public string $url;

    /** @var int|null Indicates whether the app is active (1 for active, 0 for inactive) */
    public ?int $active;

    /** @var int|null ID of the user who last modified the app */
    public ?int $modifiedBy;

    /**
     * AppDTO constructor.
     *
     * @param int|null $id The unique identifier of the app
     * @param string $name The name of the app
     * @param string $url The URL associated with the app
     * @param int|null $active Whether the app is active (default: 1)
     * @param int|null $modifiedBy ID of the user who last modified the app (optional)
     */
    public function __construct(
        ?int $id,
        string $name,
        string $url,
        ?int $active = 1,
        ?int $modifiedBy = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->active = $active;
        $this->modifiedBy = $modifiedBy;
    }
}
