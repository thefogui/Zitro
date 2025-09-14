<?php
namespace core;

use Firebase\JWT\JWT;
use Firebase\JWT\KEY;
use modules\user\service\AuthService;
use core\HttpStatus;

/**
 * Class that set a base for controllers in the application
 *
 * @author Vitor Carvalho vitorcarvalhodso@gamil.com
 */
class BaseController {
    private $jwtKey = 'super_secret_key'; // TODO
    private AuthService $service;

    public function __construct() {
        $this->service = new AuthService();
    }

    /**
     * Funtion to validate the token and return the user assocaited to the token
     *
     * @param array $requestData arary with the request information
     * @return int the id of the user that sent the token
     */
    public function validateTokenAndGetCurrentUser(array $requestData): int {
        try {
            $jwt = $requestData['token'] ?? null;

            if (empty($jwt)) {
                throw new \Exception('Authentication token is required', HttpStatus::UNAUTHORIZED);
            }

            if (empty($jwt)) {
                throw new \Exception('Authentication token is required', HttpStatus::UNAUTHORIZED);
            }

            $decoded = JWT::decode($jwt, new Key($this->jwtKey, 'HS256'));
            $session = $this->service->getSession($jwt);

            if (!$session || $session['expiresat'] < time()) {
                throw new \Exception('Your session has expired', HttpStatus::UNAUTHORIZED);
            }

            return $decoded->sub;
        } catch (\Exception $e) {
            throw new \Exception('Invalid token', HttpStatus::UNAUTHORIZED);
        }
    }
}
