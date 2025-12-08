<?php
$session_id = $_COOKIE["session"];

if (isset($session_id)) {
    if ($db->is_correct_session_id($session_id)) {
        header('Location: /login');
        die();
    }
} else {
    header('Location: /login');
}
