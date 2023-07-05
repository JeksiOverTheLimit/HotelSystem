<?php

declare(strict_types=1);

class CityRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(string $name): City
    {
        $query = "INSERT INTO cities (name) 
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
        $query = "UPDATE cities SET name = :name WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':name', $name);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from cities Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(int $id): ?City
    {
        $query = "SELECT id as id , name as name From cities Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $cities = $statement->fetchObject(City::class) ?: null;

        return $cities;
    }

    public function findByCountryId(int $countryId): array
    {
        $query = "SELECT id as id , name as name From cities Where country_id = :countryId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':countryId', $countryId);
        $statement->execute();

        $cities = $statement->fetchAll(PDO::FETCH_CLASS, "City");

        return $cities;
    }

    public function getAllCities(): array
    {
        $query = "SELECT id as id, name as name  FROM cities";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "City");

        return $result;
    }

    public function getCitiesByCountryId(?int $countryId): ?array
    {
        $query = "SELECT id as id, name as name, country_id as countryId  FROM cities Where country_id = :countryId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':countryId', $countryId);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "City");

        return $result;
    }
}
