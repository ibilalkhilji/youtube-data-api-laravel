<?php


namespace Khaleejinfotech\YoutubeDataApi\Facade;


class Thumbnails
{

    /**
     * Thumbnails constructor.
     */
    public function __construct($array)
    {
        foreach ($array as $value) {
            $name = $value[0];
            $this->$name = new \stdClass();
            $this->$name->url = $value[1];
            $this->$name->width = $value[2];
            $this->$name->height = $value[3];
        }

    }
}
