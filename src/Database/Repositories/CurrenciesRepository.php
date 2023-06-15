<?php

declare(strict_types=1);

class CurrenciesRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(string $name): Currencies
    {
        $query = "INSERT INTO currencies (name) 
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
        $query = "UPDATE currencies SET name = :name WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':name', $name);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from currencies Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(int $id): ?Currencies
    {
        $query = "SELECT id as id, name as name From currencies Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $currencies = $statement->fetchObject(Currencies::class) ?: null;

        return $currencies;
    }

    public function getAllCurrencies() : array
    {
        $query = "SELECT id as id, name as name  FROM currencies";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Currencies");

        return $result;
    }
}
