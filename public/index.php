<?php
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$page_path = __DIR__ . "/../src/pages/{$url}.php";

if (file_exists($page_path)) {
    require_once $page_path;
} else {
    require_once __DIR__ .  "/../src/pages/index.php";
}
