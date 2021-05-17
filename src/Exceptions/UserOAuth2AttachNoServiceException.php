<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Exceptions;

use Exception;

/**
 * Class UserOAuth2AttachNoServiceException
 * @package ItDevgroup\LaravelUserOAuth2Attach\Exceptions
 */
class UserOAuth2AttachNoServiceException extends Exception
{
    /**
     * @var int
     */
    protected $code = 422;

    /**
     * @param string|null $service
     * @return self
     */
    public static function message(?string $service): self
    {
        return new self(sprintf('Service %s not found', $service));
    }
}
