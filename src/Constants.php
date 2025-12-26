<?php

namespace ButA2SaeS3;


class Constants
{
    private static $DEBUG = true;

    public static function is_debug()
    {
        if (isset($_ENV["DEBUG"])) {
            $_ENV["DEBUG"] === "true";
        }
        return self::$DEBUG;
    }
}
