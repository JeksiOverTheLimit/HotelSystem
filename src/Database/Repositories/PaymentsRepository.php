<?php

declare(strict_types=1);


class PaymentsRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(int $reservationId, int $currencyId, float $price, string $paymentDate): Payments
    {
        $query = "INSERT INTO payments (reservation_id, currency_id, price, payment_date) 
        VALUES (:reservationId, :currencyId, :price, :paymentDate)";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':reservationId', $reservationId);
        $statement->bindParam(':currencyId', $currencyId);
        $statement->bindParam(':price', $price);
        $statement->bindParam(':paymentDate', $paymentDate);
        $statement->execute();

        $id = $connection->lastInsertId();

        return $this->findById(intval($id));
    }

    public function update(int $id, int $reservationId, int $currencyId, float $price, string $paymentDate): void
    {
        $query = "UPDATE payments SET reservation_id = :reservationId, currency_id = :currencyId, price = :price, payment_date = :paymentDate WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':reservationId', $reservationId);
        $statement->bindParam(':currencyId', $currencyId);
        $statement->bindParam(':price', $price);
        $statement->bindParam(':paymentDate', $paymentDate);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from payments Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(int $id): ?Payments
    {
        $query = "SELECT reservation_id as reservationId, currency_id as currencyId, price as price, payment_date as paymentDate From guests Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $payments = $statement->fetchObject(Payments::class) ?: null;

        return $payments;
    }

    public function getAllPayments() : array
    {
        $query = "SELECT reservation_id as reservationid, currency_id  as currencyId, price as price, payment_date as paymentDate FROM payments";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Payments");

        return $result;
    }
}