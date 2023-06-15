<?php

declare(strict_types=1);


class ReservationsRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(int $employeeId, int $roomId, string $startingDate, string $finalDate, int $statusId): Reservations
    {
        $query = "INSERT INTO reservations (employee_id, room_id, starting_date, final_date, status_id) 
        VALUES (:employeeId, :roomId, :startingDate, :finalDate, :statusId)";

        $reservation = new Reservations();
        $reservation->setEmployeeId($employeeId);
        $reservation->setRoomId($roomId);
        $reservation->setStartingDate($startingDate);
        $reservation->setFinalDate($finalDate);
        $reservation->setStatusId($statusId);
    
        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':employeeId', $reservation->getEmployeeId());
        $statement->bindParam(':roomId', $reservation->getRoomId());
        $statement->bindParam(':startingDate', $reservation->getStartingDate());
        $statement->bindParam(':finalDate', $reservation->getFinalDate());
        $statement->bindParam(':statusId', $reservation->getStatusId());
        $statement->execute();

        $id = $connection->lastInsertId();

        return $this->findById(intval($id));
    }

    public function update(int $id, int $employeeId, int $roomId, string $startingDate, string $finalDate, int $statusId): void
    {
        $query = "UPDATE reservations SET employee_id = :employeeId, room_id = :roomId, starting_date = :startingDate, final_date = :finalDate, status_id = :statusId WHERE id = :id";

        $reservation = new Reservations();
        $reservation->setEmployeeId($employeeId);
        $reservation->setRoomId($roomId);
        $reservation->setStartingDate($startingDate);
        $reservation->setFinalDate($finalDate);
        $reservation->setStatusId($statusId);

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':employeeId', $reservation->getEmployeeId());
        $statement->bindParam(':roomId', $reservation->getRoomId());
        $statement->bindParam(':startingDate', $reservation->getStartingDate());
        $statement->bindParam(':finalDate', $reservation->getFinalDate());
        $statement->bindParam(':statusId', $reservation->getStatusId());
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from reservations Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(int $id): ?Reservations
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId From reservations Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $reservations = $statement->fetchObject(Reservations::class) ?: null;

        return $reservations;
    }

    public function getAllReservations(): array
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId FROM reservations";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservations");

        return $result;
    }
}
