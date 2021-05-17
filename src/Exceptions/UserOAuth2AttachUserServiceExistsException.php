<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Exceptions;

use Exception;

/**
 * Class UserOAuth2AttachUserServiceExistsException
 * @package ItDevgroup\LaravelUserOAuth2Attach\Exceptions
 */
class UserOAuth2AttachUserServiceExistsException extends Exception
{
    /**
     * @var int
     */
    protected $code = 422;

    /**
     * @return self
     */
    public static function message(): self
    {
        return new self('The social network is already registered for this user, but with a different ID');
    }
}
