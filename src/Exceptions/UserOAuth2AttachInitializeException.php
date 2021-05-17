<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Exceptions;

use Exception;

/**
 * Class UserOAuth2AttachInitializeException
 * @package ItDevgroup\LaravelUserOAuth2Attach\Exceptions
 */
class UserOAuth2AttachInitializeException extends Exception
{
    /**
     * @var int
     */
    protected $code = 422;
}
