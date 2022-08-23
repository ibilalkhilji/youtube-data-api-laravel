<?php

namespace Khaleejinfotech\YoutubeDataApi;

use App\Providers\AppServiceProvider;

class YoutubeDataApiServiceProvider extends AppServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/youtube_data_api.php' => config_path('youtube_data_api.php')
        ], 'youtube_data_api');
    }

    public function register()
    {
        parent::register();
        $this->app->singleton(YoutubeDataApi::class, function () {
            return new YoutubeDataApi();
        });
    }
}
