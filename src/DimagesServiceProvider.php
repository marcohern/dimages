<?php

namespace Marcohern\Dimages;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManagerStatic as IImage;
use Marcohern\Dimages\Lib\Dimages\Dimage;

class DimagesServiceProvider extends ServiceProvider {

    public function boot() {

        //Adding a stupid comment just to change the version
        $basePath = dirname(__DIR__);

        //set the default string length for migrations (I guess)
        Schema::defaultStringLength(191);

        IImage::configure(['driver' => 'gd']);

        //Load the routes for this package
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/image.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        //Load the views for this package
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dimages');

        Dimage::boot();
    }

    public function register() {
        $this->registerPublishables();
    }

    private function registerPublishables() {
        $basePath = dirname(__DIR__);

        $publishables = [
            'public' => [
                "$basePath/publishables/public/mhn/dimages" => public_path('mhn/dimages')
            ],
            'migrations' => [
                "$basePath/publishables/database/migrations" => database_path('migrations')
            ],
            'config' => [
                "$basePath/publishables/config/dimages.php" => config_path('dimages.php')
            ]
        ];

        $views = [
            "$basePath/resources/views" => resource_path('views/vendor/dimages')
        ];

        foreach($publishables as $group => $paths) {
            $this->publishes($paths, $group);
        }

        $this->publishes($views);
    }
}