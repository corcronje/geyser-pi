<?php

class Database
{
    var $host = 'localhost';
    var $username = 'pi';
    var $password = 'raspberry';
    var $database = 'geyser_pi';

    var $conn;

    public function open()
    {
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->database);
    }

    public function close()
    {
        $this->conn->close();
    }

    public function query($sql)
    {
        $this->open();
        $result = $this->conn->query($sql);
        $this->close();
        return $result;
    }
}