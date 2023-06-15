<?php
declare(strict_types=1);

class CountriesRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(string $name): Countries
    {
        $query = "INSERT INTO countries (name) 
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
        $query = "UPDATE countries SET name = :name WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':name', $name);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from countries Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(int $id): ?Countries
    {
        $query = "SELECT id as id, name as name From countries Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $countries = $statement->fetchObject(Countries::class) ?: null;

        return $countries;
    }

    public function getAllCountries(): array
    {
        $query = "SELECT id as id, name as name  FROM countries";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Countries");

        return $result;
    }

}
