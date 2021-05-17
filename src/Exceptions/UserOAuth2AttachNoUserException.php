<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Exceptions;

use Exception;

/**
 * Class UserOAuth2AttachNoUserException
 * @package ItDevgroup\LaravelUserOAuth2Attach\Exceptions
 */
class UserOAuth2AttachNoUserException extends Exception
{
    /**
     * @var int
     */
    protected $code = 422;

    /**
     * @return self
     */
    public static function notReceived(): self
    {
        return new self('User info not received');
    }
}
