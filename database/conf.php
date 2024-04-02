<?php

class Database
{
    private $mysql;

    public function __construct($host, $username, $password, $database)
    {
        $this->mysql = new mysqli($host, $username, $password, $database);
        //check connection 
        if ($this->mysql->connect_errno) {
            echo "Failed to connect to MySQL " . $this->mysql->connect_errno;
            exit();
        }
    }

    public function getConnection () {
        return $this->mysql;
    }

    public function closeConnection () {
        $this->mysql->close();
    }
}
global $mysql;
