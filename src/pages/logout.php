<?php

use ButA2SaeS3\services\SessionService;
use ButA2SaeS3\utils\HttpUtils;

SessionService::logout();
HttpUtils::redirect("/login");
