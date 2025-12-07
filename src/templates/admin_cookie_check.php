<?php
if (isset($_COOKIE["session"])) {
    if ($_COOKIE["session"] != "mon_secret") {
        header('Location: /login');
        die();
    }
} else {
    header('Location: /login');
}
