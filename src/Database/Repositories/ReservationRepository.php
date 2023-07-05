<?php

declare(strict_types=1);


class ReservationRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(int $employeeId, int $roomId, string $startingDate, string $finalDate, int $statusId): Reservation
    {
        $query = "INSERT INTO reservations (employee_id, room_id, starting_date, final_date, status_id) 
        VALUES (:employeeId, :roomId, :startingDate, :finalDate, :statusId)";

        $reservation = new Reservation();
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

        $reservation = new Reservation();
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

    public function deleteByEmployeeId(int $employeeId) {
        $query = "DELETE from reservations Where employee_id = :employeeId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':employeeId', $employeeId);
        $statement->execute();
    }

    public function deleteByRoomId(int $roomId) {
        $query = "DELETE from reservations Where room_id = :roomId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->execute();
    }

    public function findByEmployeeId(int $employeeId) :array{
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId From reservations Where employee_id = :employeeId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':employeeId', $employeeId);
        $statement->execute();

        $reservations = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $reservations;
    }

    public function findByRoomId(int $roomId) :array{
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId From reservations Where room_id = :roomId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->execute();

        $reservations = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $reservations;
    }

    public function findById(int $id): ?Reservation
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId From reservations Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $reservations = $statement->fetchObject(Reservation::class) ?: null;

        return $reservations;
    }

    public function getAllReservations(): array
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId FROM reservations";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $result;
    }

    public function getAllReservationsByPeriod(string $startingDate, string $finalDate): array
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId FROM reservations WHERE starting_date BETWEEN :startingDate AND :finalDate";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->bindParam(':startingDate', $startingDate);
        $statement->bindParam(':finalDate', $finalDate);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $result;
    }

    public function getAllReservationByRoom(int $roomId) : array {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId FROM reservations WHERE room_id = :roomId";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $result;
    }

    public function getAllReservationById(int $id) : array {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId FROM reservations WHERE id = :Id";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->bindParam(':Id', $id);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $result;
    }

    public function getAllReservationByRoomAndPeriod(string $startingDate, string $finalDate, int $roomId) : array {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId FROM reservations WHERE starting_date BETWEEN :startingDate AND :finalDate And room_id = :roomId";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->bindParam(':startingDate', $startingDate);
        $statement->bindParam(':finalDate', $finalDate);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $result;
    }

}
