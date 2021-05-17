<?php

namespace ItDevgroup\LaravelUserOAuth2Attach;

use Illuminate\Database\Eloquent\Collection as CollectionEloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ItDevgroup\LaravelUserOAuth2Attach\Data\UserData;
use ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2;

/**
 * Interface UserOAuth2AttachServiceInterface
 * @package ItDevgroup\LaravelUserOAuth2Attach
 */
interface UserOAuth2AttachServiceInterface
{
    /**
     * @param string $service
     */
    public function loadService(string $service): void;

    /**
     * @return string
     */
    public function getLoginLink(): string;

    /**
     * @param Collection $data
     * @return UserData
     */
    public function authorize(Collection $data): UserData;

    /**
     * @return array
     */
    public function getServiceList(): array;

    /**
     * @param UserData $userData
     * @param Model $newUser
     * @return UserOAuth2
     */
    public function getOrCreateModelFromData(UserData $userData, Model $newUser): UserOAuth2;

    /**
     * @param int $userId
     * @return CollectionEloquent|UserOAuth2[]
     */
    public function getModelListByUser(int $userId): CollectionEloquent;

    /**
     * @param int $id
     * @return UserOAuth2
     */
    public function getModelById(int $id): UserOAuth2;

    /**
     * @param int $userId
     * @param string $service
     * @return UserOAuth2
     */
    public function getModelByUserAndService(int $userId, string $service): UserOAuth2;

    /**
     * @param string $service
     * @param string $externalId
     * @return UserOAuth2
     */
    public function getModelByServiceAndExternalId(string $service, string $externalId): UserOAuth2;

    /**
     * @param UserOAuth2 $model
     * @return bool
     */
    public function modelCreate(UserOAuth2 $model): bool;

    /**
     * @param UserOAuth2 $model
     * @return bool
     */
    public function modelUpdate(UserOAuth2 $model): bool;

    /**
     * @param UserOAuth2 $model
     * @return bool
     */
    public function modelDelete(UserOAuth2 $model): bool;
}
