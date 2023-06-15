<?php
declare(strict_types=1);

class RoomExtrasRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(string $name): RoomExtras
    {
        $query = "INSERT INTO room_extras (name) 
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
        $query = "UPDATE room_extras SET name = :name WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':name', $name);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from room_extras Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(?int $id): ?RoomExtras
    {
        $query = "SELECT id as id, name as name From room_extras Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $countries = $statement->fetchObject(RoomExtras::class) ?: null;

        return $countries;
    }

    public function getAllExtras(): array
    {
        $query = "SELECT id as id, name as name  FROM room_extras";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "RoomExtras");

        return $result;
    }

}
