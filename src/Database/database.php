<?php

declare(strict_types=1);

class Database
{
    private const SERVER_NAME = "localhost";
    private const SERVER_USERNAME = "root";
    private const SERVER_PASSWORD = "";

    public function getConnection(): PDO
    {
        $connection = null;
        try {
            $connectionString = sprintf("mysql:host=%s;dbname=hotelsystem", self::SERVER_NAME);
            $connection = new PDO($connectionString, self::SERVER_USERNAME, self::SERVER_PASSWORD);

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            die();
        }
       
        return $connection;
    }
}
