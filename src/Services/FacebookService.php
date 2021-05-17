<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Services;

use Exception;
use Facebook\Facebook;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use ItDevgroup\LaravelUserOAuth2Attach\Data\UserData;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachGetTokenException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachInitializeException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachNoUrlException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachNoUserException;

/**
 * Class Facebook
 * @package ItDevgroup\LaravelUserOAuth2Attach\Services
 */
class FacebookService implements ServiceInterface
{
    /**
     * @var Facebook|null
     */
    private ?Facebook $client = null;
    /**
     * @var string|null
     */
    private ?string $redirectUrl = null;

    /**
     * @return string
     * @throws UserOAuth2AttachInitializeException
     * @throws UserOAuth2AttachNoUrlException
     */
    public function getLoginUrl(): string
    {
        $this->initialize();

        try {
            return $this->client->getOAuth2Client()->getAuthorizationUrl(
                $this->redirectUrl,
                null,
                [
                    'email'
                ]
            );
        } catch (Exception $e) {
            throw new UserOAuth2AttachNoUrlException($e->getMessage());
        }
    }

    /**
     * @param Collection $data
     * @return UserData
     * @throws UserOAuth2AttachGetTokenException
     * @throws UserOAuth2AttachInitializeException
     * @throws UserOAuth2AttachNoUserException
     */
    public function authorize(Collection $data): UserData
    {
        $this->initialize();

        try {
            $token = $this->client->getOAuth2Client()->getAccessTokenFromCode($data->get('code'), $this->redirectUrl)
                ->getValue();
        } catch (Exception $e) {
            throw new UserOAuth2AttachGetTokenException($e->getMessage());
        }

        $this->client->setDefaultAccessToken($token);

        try {
            $response = $this->client->get('me?fields=email,first_name,last_name,gender,link,id');
            $user = $response->getGraphUser();
        } catch (Exception $e) {
            throw new UserOAuth2AttachNoUserException($e->getMessage());
        }

        if (!$user->getId()) {
            throw UserOAuth2AttachNoUserException::notReceived();
        }

        return UserData::register(
            $user->getId(),
            $user->getEmail(),
            null,
            $user->getFirstName(),
            $user->getLastName(),
            $user->getGender(),
            $user->getLink(),
            (array)$user->asArray()
        );
    }

    /**
     * @return Facebook
     * @throws UserOAuth2AttachInitializeException
     */
    protected function initialize(): Facebook
    {
        if ($this->client) {
            return $this->client;
        }

        try {
            $this->client = new Facebook(
                [
                    'app_id' => Config::get('user_oauth2_attach.services.facebook.params.app_id'),
                    'app_secret' => Config::get('user_oauth2_attach.services.facebook.params.app_secret'),
                    'default_graph_version' => Config::get('user_oauth2_attach.services.facebook.params.version'),
                ]
            );
            $this->redirectUrl = Config::get('user_oauth2_attach.services.facebook.redirect_url');
        } catch (Exception $e) {
            throw new UserOAuth2AttachInitializeException($e->getMessage());
        }

        return $this->client;
    }
}
