<?php
if ($_COOKIE["session"] != "mon_secret") {
    header('Location: /login');
    die();
}

?>
Bonjour