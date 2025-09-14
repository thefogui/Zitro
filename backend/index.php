<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-type: application/json');

require_once(__DIR__ . '/Rest.php');

use App\Rest;

require_once __DIR__ . '/vendor/autoload.php';

if (isset($_SERVER['REQUEST_URI'])) {
    Rest::open($_SERVER);
}
