<?php

namespace ButA2SaeS3\services;

use ButA2SaeS3\FageDB;

class SessionService
{
    public static function create_session(FageDB $db, $username)
    {
        $session_id = random_bytes(32);
        $expiration_date = time() + 24 * 60 * 60;
        setcookie("session", $session_id, expires_or_options: $expiration_date, path: "/", domain: "", secure: true, httponly: true);
        $db->create_session($username, $session_id, date("Y-m-d H:i:s", $expiration_date));
    }

    public static function clear_session() {}
}
