<?php

# See: https://getcomposer.org/doc/01-basic-usage.md
# And: https://www.php-fig.org/psr/psr-4/
# Summary: allows the autoloading of classes/functions using `use <Namespace\To\Load\Classname>`
require __DIR__ . '/../../vendor/autoload.php';

use Uri\Rfc3986\Uri;

$uri = new Uri($_SERVER['REQUEST_URI']);
$page_path = __DIR__ . "/../pages/{$uri->getPath()}.php";

if (file_exists($page_path)) {
    require $page_path;
} else {
    require __DIR__ .  "/../pages/index.php";
}
