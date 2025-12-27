<?php

use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;

setcookie(
    "session",
    HttpUtils::get_current_user_id(new FageDB()),
    $expires_or_options = 0,
    $path = "/",
    $domain = "",
    $secure = true,
    $httpsecure = true,
);

HttpUtils::redirect("/login");
