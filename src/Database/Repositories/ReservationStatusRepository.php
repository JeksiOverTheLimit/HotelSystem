<?php

declare(strict_types=1);

class ReservationStatusRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(string $name): ReservationStatus
    {
        $query = "INSERT INTO reservation_status (name) 
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
        $query = "UPDATE reservation_status SET name = :name WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':name', $name);
        $statement->execute();
    }

    public function delete(int $id) : void
    {
        $query = "DELETE from reservation_status Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(int $id): ?ReservationStatus
    {
        $query = "SELECT id as id, name as name  From reservation_status Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $reservationStatus = $statement->fetchObject(ReservationStatus::class) ?: null;

        return $reservationStatus;
    }

    public function getAllStatus() : array
    {
        $query = "SELECT id as id, name as name FROM reservation_status";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "ReservationStatus");

        return $result;
    }

}


