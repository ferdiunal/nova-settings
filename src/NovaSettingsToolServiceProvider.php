<?php

namespace Ferdiunal\NovaSettings;

use Ferdiunal\NovaSettings\Console\MakeSettingResource;
use Ferdiunal\NovaSettings\Http\Middleware\Authorize;
use Ferdiunal\NovaSettings\Traits\Utils;
use Illuminate\Log\Context\Repository as ContextRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;

class NovaSettingsToolServiceProvider extends ServiceProvider
{
    use Utils;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/nova-settings.php', 'nova-settings');
        }

        $this->publishes([
            __DIR__.'/../config/nova-settings.php' => config_path('nova-settings.php'),
        ], 'nova-settings-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeSettingResource::class,
            ]);
        }

        $this->app->make(ContextRepository::class)->add(
            'nova-settings-resources',
            $this->resources()
        );

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            //
        });
    }

    public function resources(): array
    {
        $resourcePath = $this->app['config']->get('nova-settings.setting_resource_class_path', 'app/NovaSettings');
        $namespace = $this->getNamespace();
        if (! $this->app['files']->exists($resourcePath)) {
            return [];
        }

        $files = $this->app['files']->files($resourcePath);

        return array_map(
            function ($file) use (&$namespace) {
                $class = sprintf('%s\\%s', $namespace, pathinfo($file, PATHINFO_FILENAME));

                return (new $class)->serialize();
            },
            $files
        );
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authenticate::class, Authorize::class], 'nova-settings')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/nova-settings')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}
}
