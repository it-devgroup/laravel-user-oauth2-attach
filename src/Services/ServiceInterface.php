<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Services;

use Illuminate\Support\Collection;
use ItDevgroup\LaravelUserOAuth2Attach\Data\UserData;

/**
 * Interface ServiceInterface
 * @package ItDevgroup\LaravelUserOAuth2Attach\Services
 */
interface ServiceInterface
{
    /**
     * @return string
     */
    public function getLoginUrl(): string;

    /**
     * @param Collection $data
     * @return UserData
     */
    public function authorize(Collection $data): UserData;
}
