<?php

namespace Database;

use mysqli;

class Database
{
    /** @var string  */
    private $servername = 'localhost';
    /** @var string  */
    private $username = 'root';
    /** @var string  */
    private $password = '';
    /** @var string  */
    private $database = 'phone_book';

    /** @return mysqli */
    public function connect()
    {
        $connection = new mysqli($this->servername, $this->username, $this->password, $this->database);
        if ($connection->connect_error) {
            die('Connection failed: ' . $connection->connect_error);
        }
        return $connection;
    }
}
