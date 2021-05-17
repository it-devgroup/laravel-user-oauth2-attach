<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Services;

use Exception;
use Google_Client;
use Google_Service_Oauth2;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use ItDevgroup\LaravelUserOAuth2Attach\Data\UserData;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachGetTokenException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachInitializeException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachNoUrlException;
use ItDevgroup\LaravelUserOAuth2Attach\Exceptions\UserOAuth2AttachNoUserException;

/**
 * Class GoogleService
 * @package ItDevgroup\LaravelUserOAuth2Attach\Services
 */
class GoogleService implements ServiceInterface
{
    /**
     * @var Google_Client|null
     */
    private ?Google_Client $client = null;

    /**
     * @return string
     * @throws UserOAuth2AttachInitializeException
     * @throws UserOAuth2AttachNoUrlException
     */
    public function getLoginUrl(): string
    {
        $this->initialize();

        try {
            return $this->client->createAuthUrl();
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
        $this->client->authenticate($data->get('code'));

        if (!$this->client->getAccessToken() || !isset($this->client->getAccessToken()['access_token'])) {
            throw UserOAuth2AttachGetTokenException::noToken();
        }

        $this->client->setAccessToken($this->client->getAccessToken()['access_token']);

        try {
            $user = (new Google_Service_Oauth2($this->client))->userinfo->get();
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
            $user->getGivenName(),
            $user->getFamilyName(),
            $user->getGender(),
            $user->getLink(),
            (array)$user->toSimpleObject()
        );
    }

    /**
     * @return Google_Client
     * @throws UserOAuth2AttachInitializeException
     */
    protected function initialize(): Google_Client
    {
        if ($this->client) {
            return $this->client;
        }

        try {
            $this->client = new Google_Client();
            $this->client->setAuthConfig(Config::get('user_oauth2_attach.services.google.params.credential_file'));
            $this->client->addScope([Google_Service_Oauth2::USERINFO_PROFILE, Google_Service_Oauth2::USERINFO_EMAIL]);
            $this->client->setRedirectUri(Config::get('user_oauth2_attach.services.google.redirect_url'));
            $this->client->setAccessType(Config::get('user_oauth2_attach.services.google.params.access_type'));
            $this->client->setPrompt(Config::get('user_oauth2_attach.services.google.params.prompt'));
            $this->client->setIncludeGrantedScopes(
                (bool)Config::get('user_oauth2_attach.services.google.params.include_granted_scopes')
            );
        } catch (Exception $e) {
            throw new UserOAuth2AttachInitializeException($e->getMessage());
        }

        return $this->client;
    }
}
