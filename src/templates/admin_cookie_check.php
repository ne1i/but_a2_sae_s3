<?php

if (isset($_COOKIE["session"])) {
    $session_id = $_COOKIE["session"];
    if ($db->is_correct_session_id($session_id)) {
        header('Location: /login');
        die();
    }
} else {
    header('Location: /login');
    die();
}
