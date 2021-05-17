<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Exceptions;

use Exception;

/**
 * Class UserOAuth2AttachModelNotFoundException
 * @package ItDevgroup\LaravelUserOAuth2Attach\Exceptions
 */
class UserOAuth2AttachModelNotFoundException extends Exception
{
    /**
     * @var int
     */
    protected $code = 404;

    /**
     * @return self
     */
    public static function message(): self
    {
        return new self('User OAuth2 row not found');
    }
}
