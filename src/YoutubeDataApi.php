<?php

namespace Khaleejinfotech\YoutubeDataApi;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\JsonResponse;
use Khaleejinfotech\YoutubeDataApi\Facade\Thumbnails;
use Khaleejinfotech\YoutubeDataApi\Facade\VideoData;

class YoutubeDataApi
{
    private $api = null;
    private $response = null;
    private $videoID = null;

    /**
     * Sets api key if defined in config file when booted
     */
    public function __construct()
    {
        $this->api = config('youtube_data_api.key');
    }

    /**
     * Parse metadata information to VideoData object
     *
     * @return VideoData
     * @throws Exception
     */
    protected function parseResponse(): VideoData
    {
        if ($this->response == null)
            throw new Exception("Failed to parse response, fetch method didn't triggered.");

        $videoData = new VideoData();
        $videoData->videoId = trim($this->response->items[0]->id);
        $videoData->title = $this->response->items[0]->snippet->title;
        $videoData->description = trim($this->response->items[0]->snippet->description);
        $videoData->channelTitle = $this->response->items[0]->snippet->channelTitle;
        $videoData->publishedAt = $this->response->items[0]->snippet->publishedAt;
        $videoData->thumbnails = new Thumbnails([
            [
                "default",
                $this->response->items[0]->snippet->thumbnails->default->url,
                $this->response->items[0]->snippet->thumbnails->default->width,
                $this->response->items[0]->snippet->thumbnails->default->height
            ], [
                "medium",
                $this->response->items[0]->snippet->thumbnails->medium->url,
                $this->response->items[0]->snippet->thumbnails->medium->width,
                $this->response->items[0]->snippet->thumbnails->medium->height
            ], [
                "high",
                $this->response->items[0]->snippet->thumbnails->high->url,
                $this->response->items[0]->snippet->thumbnails->high->width,
                $this->response->items[0]->snippet->thumbnails->high->height
            ],
        ]);
        return $videoData;
    }

    /**
     * Extract videoId from youtube url
     * supported methods: watch|shorts|youtu.be
     *
     * @param string $videoURL
     * @return string
     */
    protected function parseVideoID(string $videoURL): string
    {
        $patterns = [
            '/(?:http[s]?:\/\/)?(?:\w+\.)?youtube.com\/watch\?v=([\w_-]+)(?:&.*)?/i',
            '/(?:http[s]?:\/\/)?(?:\w+\.)?youtube.com\/shorts\/([\w_-]+)(?:&.*)?/i',
            '/(?:http[s]?:\/\/)?youtu.be\/([\w_-]+)(?:\?.*)?/i'
        ];
        foreach ($patterns as $pattern) {
            preg_match($pattern, $videoURL, $output_array);
            if (count($output_array) > 0)
                if (isset($output_array[1]))
                    return $output_array[1];
        }
        return "invalid url";
    }

    /**
     * Checks whether the given string is valid videoID or not
     *
     * @param string $string
     * @return bool
     */
    protected function isValidVideoID(string $string): bool
    {
        $pattern = "/[a-zA-Z0-9]+/i";
        preg_match_all($pattern, $string, $array);
        if (count($array[0]) > 1) return false;
        return true;
    }

    /**
     * Returns api key
     *
     * @return string
     * @throws Exception
     */
    protected function getApiKey(): string
    {
        if ($this->api == null || $this->api == '')
            throw new Exception("API key not found...");
        return $this->api;
    }

    /**
     * Sets the youtube video ID
     *
     * @param string $videoID
     * @return YoutubeDataApi
     * @throws Exception
     */
    public function setVideoID(string $videoID): YoutubeDataApi
    {
        if (!$this->isValidVideoID($videoID))
            throw new Exception("Video ID seems to be invalid");
        $this->videoID = $videoID;
        return $this;
    }

    /**
     * Sets the youtube video url, video ID parsed later on
     *
     * @param string $videoURL
     * @return YoutubeDataApi
     */
    public function setVideoURL(string $videoURL): YoutubeDataApi
    {
        $this->videoID = $this->parseVideoID($videoURL);
        return $this;
    }

    /**
     * Sets the api key
     *
     * @param string $apiKey
     * @return YoutubeDataApi
     */
    public function setApiKey(string $apiKey): YoutubeDataApi
    {
        $this->api = $apiKey;
        return $this;
    }

    /**
     * Fetch information for the given video ID
     *
     * @return JsonResponse|VideoData
     * @throws Exception
     */
    public function fetch()
    {
        if ($this->videoID == null || $this->videoID == '')
            throw new Exception("Video ID not specified.");

        $client = new Client([
            'base_uri' => 'https://www.googleapis.com/youtube/v3/videos',
            RequestOptions::VERIFY => false
        ]);
        try {
            $res = $client->request('GET', "?part=id,+snippet&key={$this->getApiKey()}&id={$this->videoID}");
            $this->response = json_decode($res->getBody()->getContents());
            return $this->parseResponse();
        } catch (GuzzleException $exception) {
            return response()->json([
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Returns the title of the video
     * @return string
     * @throws Exception
     */
    public function getTitle(): string
    {
        if ($this->response == null) throw new Exception("Method fetch not fired..");
        return $this->response->items[0]->snippet->title;
    }

    /**
     * Returns the description of the video
     * @return string
     * @throws Exception
     */
    public function getDescription(): string
    {
        if ($this->response == null) throw new Exception("Method fetch not fired..");
        return $this->response->items[0]->snippet->description;
    }

    /**
     * Returns the channel title of the video
     * @return string
     * @throws Exception
     */
    public function getChannelTitle(): string
    {
        if ($this->response == null) throw new Exception("Method fetch not fired..");
        return $this->response->items[0]->snippet->channelTitle;
    }

    /**
     * Returns the channel title of the video
     * @return string
     * @throws Exception
     */
    public function getPublishedAt(): string
    {
        if ($this->response == null) throw new Exception("Method fetch not fired..");
        return $this->response->items[0]->snippet->publishedAt;
    }
}
