<?php

namespace ItDevgroup\LaravelUserOAuth2Attach\Providers;

use Illuminate\Support\ServiceProvider;
use ItDevgroup\LaravelUserOAuth2Attach\Console\Commands\UserOAuth2AttachPublishCommand;
use ItDevgroup\LaravelUserOAuth2Attach\UserOAuth2AttachService;
use ItDevgroup\LaravelUserOAuth2Attach\UserOAuth2AttachServiceInterface;

/**
 * Class UserOAuth2AttachServiceProvider
 * @package ItDevgroup\LaravelUserOAuth2Attach\Providers
 */
class UserOAuth2AttachServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->loadCustomCommands();
        $this->loadCustomConfig();
        $this->loadCustomPublished();
        $this->loadCustomClasses();
    }

    /**
     * @return void
     */
    private function loadCustomCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    UserOauth2AttachPublishCommand::class,
                ]
            );
        }
    }

    /**
     * @return void
     */
    private function loadCustomConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/user_oauth2_attach.php', 'user_oauth2_attach');
    }

    /**
     * @return void
     */
    private function loadCustomPublished()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . '/../../config' => base_path('config')
                ],
                'config'
            );
        }
    }

    /**
     * @return void
     */
    private function loadCustomClasses()
    {
        $this->app->singleton(UserOauth2AttachServiceInterface::class, UserOauth2AttachService::class);
    }
}
