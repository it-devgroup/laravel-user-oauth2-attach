<?php

namespace ItDevgroup\LaravelUserOAuth2Attach;

use Exception;
use Illuminate\Database\Eloquent\Collection as CollectionEloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use ItDevgroup\LaravelUserOAuth2Attach\Data\UserData;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachModelDuplicateException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachModelNotFoundException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachNoServiceException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachUserServiceExistsException;
use ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2;
use ItDevgroup\LaravelUserOAuth2Attach\Services\ServiceInterface;

/**
 * Class UserOAuth2AttachService
 * @package ItDevgroup\LaravelUserOAuth2Attach
 */
class UserOAuth2AttachService implements UserOAuth2AttachServiceInterface
{
    /**
     * @var string|null
     */
    private ?string $modelName = null;
    /**
     * @var string|null
     */
    private ?string $modelUser = null;
    /**
     * @var string|null
     */
    private ?string $modelUserFieldEmail = null;
    /**
     * @var ServiceInterface|null
     */
    private ?ServiceInterface $service = null;
    /**
     * @var string|null
     */
    private ?string $serviceName = null;

    /**
     * UserOAuth2AttachService constructor.
     */
    public function __construct()
    {
        $this->modelName = Config::get('user_oauth2_attach.model');
        $this->modelUser = Config::get('user_oauth2_attach.user_model.class');
        $this->modelUserFieldEmail = Config::get('user_oauth2_attach.user_model.field_email');
    }

    /**
     * @param string $service
     * @throws UserOAuth2AttachNoServiceException
     */
    public function loadService(string $service): void
    {
        $list = $this->getServices();
        if (!$service || !$list->get($service)) {
            throw UserOAuth2AttachNoServiceException::message($service);
        }

        $this->service = app($list->get($service)['class']);
        $this->serviceName = $service;
    }

    /**
     * @return string
     */
    public function getLoginLink(): string
    {
        return $this->service->getLoginUrl();
    }

    /**
     * @param Collection $data
     * @return UserData
     */
    public function authorize(Collection $data): UserData
    {
        return $this->service->authorize($data);
    }

    /**
     * @return array
     */
    public function getServiceList(): array
    {
        return Collection::make($this->getServices())->keys()->toArray();
    }

    /**
     * @param UserData $userData
     * @param Model $newUser
     * @return UserOAuth2
     * @throws UserOAuth2AttachUserServiceExistsException
     */
    public function getOrCreateModelFromData(UserData $userData, Model $newUser): UserOAuth2
    {
        try {
            return $this->getModelByServiceAndExternalId(
                $this->serviceName,
                $userData->getId()
            );
        } catch (UserOAuth2AttachModelNotFoundException $e) {
        }

        if (method_exists($this->modelUser, 'userOAuth2Find')) {
            $user = (new $this->modelUser())->userOAuth2Find($userData);
        } else {
            $field = $this->modelUser::query()->raw(sprintf('LOWER(%s)', $this->modelUserFieldEmail));
            $user = $this->modelUser::query()
                ->where($field, '=', Str::lower($userData->getEmail()))
                ->first();
        }

        if (!$user) {
            $newUser->save();
            $user = $newUser;
        }

        try {
            $userOauth2 = $this->getModelByUserAndService($user->id, $this->serviceName);
            if ($userOauth2 && $userOauth2->external_id != $userData->getId()) {
                throw UserOAuth2AttachUserServiceExistsException::message();
            }
        } catch (UserOAuth2AttachModelNotFoundException $e) {
        }

        $userOAuth2 = UserOAuth2::register(
            $user,
            $this->serviceName,
            $userData->getId(),
            $userData->getEmail(),
            $userData->getPhone(),
            $userData->getFirstName(),
            $userData->getLastName(),
            $userData->getGender(),
            $userData->getLink(),
            $userData->getProperties()
        );
        $userOAuth2->save();

        return $userOAuth2;
    }

    /**
     * @param int $userId
     * @return CollectionEloquent
     */
    public function getModelListByUser(int $userId): CollectionEloquent
    {
        $builder = $this->modelName::query()
            ->where('user_id', '=', $userId);

        return $builder->get();
    }

    /**
     * @param int $id
     * @return UserOAuth2
     * @throws UserOAuth2AttachModelNotFoundException
     */
    public function getModelById(int $id): UserOAuth2
    {
        $builder = $this->modelName::query()
            ->where('id', '=', $id);

        $res = $builder->first();

        if (!$res) {
            throw UserOAuth2AttachModelNotFoundException::message();
        }

        return $res;
    }

    /**
     * @param int $userId
     * @param string $service
     * @return UserOAuth2
     * @throws UserOAuth2AttachModelNotFoundException
     */
    public function getModelByUserAndService(int $userId, string $service): UserOAuth2
    {
        $builder = $this->modelName::query()
            ->where('user_id', '=', $userId)
            ->where('service', '=', $service);

        $res = $builder->first();

        if (!$res) {
            throw UserOAuth2AttachModelNotFoundException::message();
        }

        return $res;
    }

    /**
     * @param string $service
     * @param string $externalId
     * @return UserOAuth2
     * @throws UserOAuth2AttachModelNotFoundException
     */
    public function getModelByServiceAndExternalId(string $service, string $externalId): UserOAuth2
    {
        $builder = $this->modelName::query()
            ->where('service', '=', $service)
            ->where('external_id', '=', $externalId);

        $res = $builder->first();

        if (!$res) {
            throw UserOAuth2AttachModelNotFoundException::message();
        }

        return $res;
    }

    /**
     * @param UserOAuth2 $model
     * @return bool
     * @throws UserOAuth2AttachModelDuplicateException
     */
    public function modelCreate(UserOAuth2 $model): bool
    {
        $builder = $this->modelName::query()
            ->where('service', '=', $model->service)
            ->where('external_id', '=', $model->external_id);

        if ($builder->first()) {
            throw UserOAuth2AttachModelDuplicateException::message();
        }

        return $model->save();
    }

    /**
     * @param UserOAuth2 $model
     * @return bool
     */
    public function modelUpdate(UserOAuth2 $model): bool
    {
        return $model->save();
    }

    /**
     * @param UserOAuth2 $model
     * @return bool
     * @throws Exception
     */
    public function modelDelete(UserOAuth2 $model): bool
    {
        return $model->delete();
    }

    /**
     * @return Collection
     */
    private function getServices(): Collection
    {
        $services = Collection::make();

        foreach (Config::get('user_oauth2_attach.services') as $key => $options) {
            $options = Collection::make($options);
            if (!$options->get('enabled')) {
                continue;
            }

            $services->put($key, $options);
        }

        return $services;
    }
}
