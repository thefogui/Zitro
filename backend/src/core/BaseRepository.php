<?php

namespace core;

use core\Database;
use PDO;

/**
 * Class that set a base for repositories in the application
 *
 * @author Vitor Carvalho vitorcarvalhodso@gamil.com
 */
class BaseRepository {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
}
