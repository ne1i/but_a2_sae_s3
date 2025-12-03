<?php
class FageDB extends SQLite3{
    function __construct() {
        $this->open(__DIR__ . "/data/fage.db");
    }
}