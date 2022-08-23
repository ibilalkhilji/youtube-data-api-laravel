# YoutubeDataApi Laravel Package

YouTube Metadata normal grabs singular details about a video and its uploader.

*Note: This extension works in Laravel 8 and Laravel 9.*

## INSTALLATION

Run the command: `composer require khaleejinfotech/youtube-data-api-laravel` to download the package into the Laravel platform.

After you have installed the package, open your Laravel config file config/app.php and add the following lines.

In the $providers array add the service providers for this package.

```
Khaleejinfotech\YoutubeDataApiLaravel\YoutubeDataApiServiceProvider::class,
```

Publish the config file with

```
php artisan vendor:publish --tag="youtube_data_api"
```

Open the **config/youtube_data_api.php** in any text editor and add your api key obtained from google developer console.

```php
<?php

return [
    'key' => env('YOUTUBE_DATA_API')
];

```

## USAGE

Create a **TestController** in Laravel using the below command line

```
php artisan make:controller TestController
```

Open the **app/Http/Controllers/TestController.php** in any text editor. To use IP2Location, add the below lines into the controller file.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Khaleejinfotech\YoutubeDataApi\YoutubeDataApi;

class TestController extends Controller
{
	//Create a fetch function for display
    public function fetch(){

        // Try query the video metadata by video Id
        $youtubeDataApi = new YoutubeDataApi();
        $youtubeDataApi->setVideoID("cT1Df9lpYw8");
        $videoData= $youtubeDataApi->fetch();
		
        echo 'VideoID           : ' . $videoData->videoId . "<br>";
        echo 'Title             : ' . $videoData->title . "<br>";
        echo 'Description       : ' . $videoData->description . "<br>";
        echo 'Channel Title     : ' . $videoData->channelTitle . "<br>";		
        echo 'Published At      : ' . $videoData->publishedAt ;
        
        $thumbnails = $videoData->thumbnails; // Returns array of different thumbnail sizes. 
        
        $defaultImageUrl = $thumbnails->default->url;
        $defaultImageWidth = $thumbnails->default->width;
        $defaultImageHeight = $thumbnails->default->height;
        
        $mediumImageUrl = $thumbnails->medium->url;
        $mediumImageWidth = $thumbnails->medium->width;
        $mediumImageHeight = $thumbnails->medium->height;
        
        $highImageUrl = $thumbnails->high->url;
        $highImageWidth = $thumbnails->high->width;
        $highImageHeight = $thumbnails->high->height;
        
    }
}
```

Add the following line into the *routes/web.php* file.

```
Route::get('test', [TestController::class,'fetch');
```

Enter the URL localhost:8000/test and run. You should see the metadata of videoID **cT1Df9lpYw8**.
