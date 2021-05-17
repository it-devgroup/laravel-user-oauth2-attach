<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Exceptions;

use Exception;

/**
 * Class UserOAuth2AttachGetTokenException
 * @package ItDevgroup\LaravelUserOAuth2Attach\Exceptions
 */
class UserOAuth2AttachGetTokenException extends Exception
{
    /**
     * @var int
     */
    protected $code = 422;

    /**
     * @return self
     */
    public static function noToken(): self
    {
        return new self('Token not received');
    }
}
