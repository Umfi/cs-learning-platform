<?php

if (!function_exists('preprocessExternalFile')) {

    /**
     * Converts a Youtube URL to an Embed URL for HTML5 Player otherwise return given url
     *
     * @param $task
     * @param $type
     */
    function preprocessExternalFile(&$task, $type)
    {
        if ($type == "INTRO") {
            $url = $task->intro;
        } else {
            $url = $task->extro;
        }

        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';
        $youtube_id = null;

        if (preg_match($longUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (!is_null($youtube_id)) {
            if ($type == "INTRO") {
                $task->intro = 'https://www.youtube.com/embed/' . $youtube_id;
            } else {
                $task->extro = 'https://www.youtube.com/embed/' . $youtube_id;
            }
        } else {
            if ($type == "INTRO") {
                $task->intro = $url;
            } else {
                $task->extro = $url;
            }
        }
    }
}
