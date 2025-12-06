<?php
class FageDB
{
    private $db;
    private $db_path = __DIR__ . "/../data/fage.sqlite";

    function __construct()
    {
        if (file_exists($this->db_path)) {
            $this->db = new PDO("sqlite:" . $this->db_path);
        } else {
            $this->db = new PDO("sqlite:" . $this->db_path);
            $this->init_db();
        }
    }

    function init_db()
    {
        $init_script = file_get_contents(__DIR__ . "/../data/init_script.sql");
        $this->db->exec($init_script);
    }
}
