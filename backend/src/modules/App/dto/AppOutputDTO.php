<?php

namespace modules\app\dto;

/**
 * Data Transfer Object for output representation of an App.
 * Encapsulates all fields returned by the app service, including metadata.
 * 
 * @package modules\app\dto
 */
class AppOutputDTO {
    /** @var int The unique identifier of the app */
    public int $id;

    /** @var string The name of the app */
    public string $name;

    /** @var string The URL associated with the app */
    public string $url;

    /** @var int Indicates whether the app is active (1 for active, 0 for inactive) */
    public int $active;

    /** @var int Timestamp of the last modification */
    public int $timemodified;

    /** @var int|null ID of the user who last modified the app */
    public ?int $modifiedBy;

    /** @var bool Indicates whether the app has been soft-deleted */
    public bool $deleted;

    /**
     * AppOutputDTO constructor.
     *
     * @param array $data Associative array containing app data
     * Expected keys: 'id', 'name', 'url', 'active', 'timemodified', 'modifiedby', 'deleted'
     */
    public function __construct(array $data) {
        $this->id = (int) $data['id'];
        $this->name = $data['name'];
        $this->url = $data['url'];
        $this->active = (int) ($data['active'] ?? 1);
        $this->timemodified = (int) $data['timemodified'];
        $this->modifiedBy = isset($data['modifiedby']) ? (int) $data['modifiedby'] : null;
        $this->deleted = (bool) $data['deleted'];
    }
}
