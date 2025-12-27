<?php

use ButA2SaeS3\utils\HttpUtils;

setcookie(
    "session",
    $session_id,
    $expires_or_options = 0,
    $path = "/",
    $domain = "",
    $secure = true,
    $httpsecure = true,
);

HttpUtils::redirect("/login");
