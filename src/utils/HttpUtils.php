<?php

namespace ButA2SaeS3\utils;

class HttpUtils
{

    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    public static function redirect(string $location)
    {
        header("Location: $location");
        die();
    }
}
