<?php

namespace core;

/**
 * Class to provide named constants for HTTP protocol
 *
 * @author Vitor Carvalho vitorcarvalhodso@gamil.com
 */
class HttpStatus {
    public const OK = 200;
    public const CREATED = 201;
    public const NOT_FOUND = 404;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const INTERNAL_ERROR = 500;
    public const REQUIRED_FIELD = 422;
}
