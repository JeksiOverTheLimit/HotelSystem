<?php

declare(strict_types=1);

class RoomsRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(int $number, int $typeId, float $price): Rooms
    {
        $query = "INSERT INTO rooms (number, type_id, price) 
        VALUES (:number, :typeId, :price)";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':number', $number);
        $statement->bindParam(':typeId', $typeId);
        $statement->bindParam(':price', $price);
        $statement->execute();

        $id = $connection->lastInsertId();

        return $this->findById(intval($id));
    }

    public function update(int $id, int $number, int $typeId, float $price): void
    {
        $query = "UPDATE rooms SET number = :number, type_id = :typeId, price = :price WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':number', $number);
        $statement->bindParam(':typeId', $typeId);
        $statement->bindParam(':price', $price);
        $statement->execute();
    }

    public function delete(int $id) : void
    {
        $query = "DELETE from rooms Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(?int $id): ?Rooms
    {
        $query = "SELECT id as id, number as number, type_id as typeId, price as price From rooms Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $rooms = $statement->fetchObject(Rooms::class) ?: null;

        return $rooms;
    }

    
    public function getAllRooms() : array
    {
        $query = "SELECT id as id, number as number, type_id as typeId, price as price FROM rooms";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Rooms");

        return $result;
    }

    public function getAllRoomsById(int $roomId) : ?array
    {
        $query = "SELECT id as id, number as number, type_id as typeId, price as price FROM rooms Where id = :roomId";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->bindParam(":roomId", $roomId);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Rooms");

        return $result;
    }
}


