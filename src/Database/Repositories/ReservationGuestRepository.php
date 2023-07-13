<?php

declare(strict_types=1);

class ReservationGuestRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(int $reservationId, int $guestId): ReservationGuest
    {
        $query = "INSERT INTO reservations_guests_map (reservation_id, guest_id) 
        VALUES (:reservationId, :guestId)";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':reservationId', $reservationId);
        $statement->bindParam(':guestId', $guestId);
        $statement->execute();

        $id = $connection->lastInsertId();

        return $this->findById(intval($id));
    }

    public function update(int $id, int $reservationId, int $guestId): void
    {
        $query = "UPDATE reservations_guests_map SET reservation_id = :reservationId, guest_id = :guestId  WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':reservationId', $reservationId);
        $statement->bindParam(':guestId', $guestId);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from reservations_guests_map Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function deleteByReservationId(int $reservationId): void
    {
        $query = "DELETE from reservations_guests_map Where reservation_id = :reservationId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':reservationId', $reservationId);
        $statement->execute();
    }

    public function deleteByGuestId(int $guestId): void
    {
        $query = "DELETE from reservations_guests_map Where guest_id = :guestId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':guestId', $guestId);
        $statement->execute();
    }

    public function findById(?int $id): ?ReservationGuest
    {
        $query = "SELECT id as id, reservation_id as reservationId, guest_id as guestId From reservations_guests_map Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $guests = $statement->fetchObject(ReservationGuest::class) ?: null;

        return $guests;
    }

    public function findByReservationId(int $reservationId): ?ReservationGuest
    {
        $query = "SELECT id as id, reservation_id as reservationId, guest_id as guestId From reservations_guests_map Where reservation_id = :reservationId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':reservationId', $reservationId);
        $statement->execute();

        $guests = $statement->fetchObject(ReservationGuest::class) ?: null;

        return $guests;
    }

    public function findByGuestId(int $guestId): ?ReservationGuest
    {
        $query = "SELECT id as id, reservation_id as reservationId, guest_id as guestId From reservations_guests_map Where guest_id = :guestId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':guestId', $guestId);
        $statement->execute();

        $guests = $statement->fetchObject(ReservationGuest::class) ?: null;

        return $guests;
    }

    public function getAllReservationGuestsByGuestId(int $guestId): ?array
    {
        $query = "SELECT id as id, reservation_id as reservationId, guest_id as guestId From reservations_guests_map Where guest_id = :guestId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':guestId', $guestId);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "ReservationGuest");

        return $result;
    }

    
}
