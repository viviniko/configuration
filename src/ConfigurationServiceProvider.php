<?php

namespace Viviniko\Configuration;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Viviniko\Configuration\Console\Commands\ConfigurationTableCommand;

class ConfigurationServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../config/configuration.php' => config_path('configuration.php'),
        ]);

        // Register commands
        $this->commands('command.configuration.table');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/configuration.php', 'configuration');

        $this->registerRepositories();

        $this->registerConfigableService();

        $this->registerVariableService();

        $this->registerCommands();
    }

    public function registerRepositories()
    {
        $this->app->singleton(
            \Viviniko\Configuration\Repositories\Configable\ConfigableRepository::class,
            \Viviniko\Configuration\Repositories\Configable\EloquentConfigable::class
        );

        $this->app->singleton(
            \Viviniko\Configuration\Repositories\Variable\VariableRepository::class,
            \Viviniko\Configuration\Repositories\Variable\EloquentVariable::class
        );
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->singleton('command.configuration.table', function ($app) {
            return new ConfigurationTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the configuration service provider.
     *
     * @return void
     */
    protected function registerConfigableService()
    {
        $this->app->singleton('configable', \Viviniko\Configuration\Services\Configable\ConfigableServiceImpl::class);

        $this->app->alias('configable', \Viviniko\Configuration\Contracts\ConfigableService::class);
    }

    /**
     * Register the configuration service provider.
     *
     * @return void
     */
    protected function registerVariableService()
    {
        $this->app->singleton('variable', \Viviniko\Configuration\Services\Variable\VariableServiceImpl::class);

        $this->app->alias('variable', \Viviniko\Configuration\Contracts\VariableService::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'configable',
            'variable',
            \Viviniko\Configuration\Contracts\ConfigableService::class,
            \Viviniko\Configuration\Contracts\VariableService::class,
        ];
    }
}