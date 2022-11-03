<?php

namespace Newelement\DmpKodi;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Facades\PluginFacade as Plugin;

class DmpKodiServiceProvider extends ServiceProvider
{
    private $pluginName = 'dmp-kodi';

    public function register()
    {
        $this->app->singleton($this->pluginName, function () {
            return new DmpKodi();
        });

        $this->registerConsoleCommands();
    }

    public function boot(Router $router)
    {
        $viewsDirectory = __DIR__.'/../resources/views/public';
        $publishAssetsDirectory = __DIR__.'/../publishable/assets';

        // Public views
        $this->loadViewsFrom($viewsDirectory, $this->pluginName);
        $this->publishes([$viewsDirectory => base_path('resources/views/vendor/'.$this->pluginName)], 'views');
        $this->publishes([ $publishAssetsDirectory => public_path('vendor/'.$this->pluginName) ], 'public');

        // Register routes
        $router->group([
            'prefix' => 'api',
            'middleware' => ['api']
        ], function ($router) {
            require __DIR__.'/../routes/api.php';
        });

        $router->group([
            'middleware' => ['web']
        ], function ($router) {
            require __DIR__.'/../routes/web.php';
        });

        $this->app->booted(function () {
            // Optional set a command to run on a schedule
            $schedule = $this->app->make(Schedule::class);
            //$schedule->command('dmp-kodi:sync')->dailyAt('03:00');
        });

        $this->registerPlugin();
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(Commands\DmpKodiSyncCommand::class);
    }

    private function registerPlugin()
    {
        $pluginInfo = [
            'type' => 'media_source',
            'plugin_key' => $this->pluginName,
            'name' => 'Kodi Media Sync and Now Playing',
            'description' => 'Syncs movie posters and shows now playing.',
            'assets' => [
                'scripts' => ['now_playing' => 'nowplaying.js'], // 'plugin' => 'plugin.js'
                'styles' => 'plugin.css'
            ]
        ];

        Plugin::register($pluginInfo);
    }
}
