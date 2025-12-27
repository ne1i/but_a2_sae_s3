<?php

namespace ButA2SaeS3\utils;

use ButA2SaeS3\FageDB;



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

    public static function ensure_valid_session(FageDB $db)
    {
        if (isset($_COOKIE["session"])) {
            $session_id = $_COOKIE["session"];
            $user_id = $db->get_user_id_from_session($session_id);
            if (!$user_id) {
                header('Location: /login');
                die();
            }
        } else {
            header('Location: /login');
            die();
        }
    }

    public static function redirect_if_session(FageDB $db)
    {
        if (isset($_COOKIE["session"])) {
            $session_id = $_COOKIE["session"];
            $user_id = $db->get_user_id_from_session($session_id);
            if ($user_id) {
                header('Location: /admin');
                die();
            }
        }
    }


    public static function create_cookie($name, $expires, $value) {}

    public static function get_current_user_id(FageDB $db)
    {
        if (isset($_COOKIE["session"])) {
            return $db->get_user_id_from_session($_COOKIE["session"]);
        }
        return null;
    }

    public static function get_query_params_str()
    {
        $sb = "";
        foreach ($_GET as $k => $v) {
            $sb = $sb . "$k=$v&";
        }
        return $sb;
    }
}
