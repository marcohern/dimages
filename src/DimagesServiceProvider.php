<?php

namespace Marcohern\Dimages;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManagerStatic as IImage;

class DimagesServiceProvider extends ServiceProvider {

    public function boot() {

        //Adding a stupid comment just to change the version
        $basePath = dirname(__DIR__);

        //set the default string length for migrations (I guess)
        Schema::defaultStringLength(191);

        IImage::configure(['driver' => 'gd']);

        //Load the routes for this package
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/image.php');

        //Load the views for this package
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dimages');
    }

    public function register() {
        $this->registerPublishables();
    }

    private function registerPublishables() {
        $basePath = dirname(__DIR__);

        $publishables = [
            'public' => [
                "$basePath/publishables/public/marcohern/dimages" => public_path('marcohern/dimages')
            ],
            'migrations' => [
                "$basePath/publishables/database/migrations" => database_path('migrations')
            ],
            'config' => [
                "$basePath/publishables/config/dimages.php" => config_path('dimages.php')
            ]
        ];

        foreach($publishables as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }
}