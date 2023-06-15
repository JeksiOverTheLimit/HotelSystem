<?php

declare(strict_types=1);


class RoomTypesRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(string $name): RoomTypes
    {
        $query = "INSERT INTO room_types (name) 
        VALUES (:name)";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':name', $name);
        $statement->execute();

        $id = $connection->lastInsertId();

        return $this->findById(intval($id));
    }

    public function update(int $id, string $name): void
    {
        $query = "UPDATE room_types SET name = :name WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':name', $name);
        $statement->execute();
    }

    public function delete(int $id) : void
    {
        $query = "DELETE from room_types Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(int $id): ?RoomTypes
    {
        $query = "SELECT id as id, name as name From room_types Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $roomTypes = $statement->fetchObject(RoomTypes::class) ?: null;

        return $roomTypes;
    }

    
    public function getAllTypes() : array
    {
        $query = "SELECT id as id, name as name  FROM room_types";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "RoomTypes");

        return $result;
    }
}

