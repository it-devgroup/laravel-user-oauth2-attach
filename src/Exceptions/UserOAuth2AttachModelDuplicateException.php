<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Exceptions;

use Exception;

/**
 * Class UserOAuth2AttachModelDuplicateException
 * @package ItDevgroup\LaravelUserOAuth2Attach\Exceptions
 */
class UserOAuth2AttachModelDuplicateException extends Exception
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
        return new self('User OAuth2 row duplicate');
    }
}
