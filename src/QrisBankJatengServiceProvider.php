<?php

namespace Mdigi\QrisBankJateng;

use Mdigi\QrisBankJateng\Console\Commands\GenerateApiKeyCommand;
use Illuminate\Support\ServiceProvider;
use Mdigi\QrisBankJateng\Services\QrisService;
use Mdigi\QrisBankJateng\Services\QrisServiceImpl;

class QrisBankJatengServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('qris.php'),
            ], 'config-qris');

            $this->commands([
                GenerateApiKeyCommand::class
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'qris');

        $this->app->bind(QrisService::class, fn() => new QrisServiceImpl(
            config('qris.base_url'),
            config('qris.username'),
            config('qris.password'),
            config('qris.api_key'),
        ));
    }
}
