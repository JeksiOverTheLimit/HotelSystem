<?php

declare(strict_types=1);

class GuestRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(string $firstName, string $lastName, string $egn, string $phoneNumber, int $countryId, int $cityId): Guest
    {
        $query = "INSERT INTO guests (first_name, last_name, egn, phone_number,  country_id, city_id) 
     VALUES (:firstName, :lastName, :egn, :phoneNumber, :countryId, :cityId)";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':firstName', $firstName);
        $statement->bindParam(':lastName', $lastName);
        $statement->bindParam(':egn', $egn);
        $statement->bindParam(':phoneNumber', $phoneNumber);
        $statement->bindParam(':countryId', $countryId);
        $statement->bindParam(':cityId', $cityId);
        $statement->execute();

        $id = $connection->lastInsertId();

        return $this->findById(intval($id));
    }


    public function update(int $id, string $firstName, string $lastName, string $egn, string $phoneNumber, int $countryId, int $cityId): void
    {
        $query = "UPDATE guests SET first_name = :firstName, last_name = :lastName, egn = :egn, phone_number = :phoneNumber, country_id = :countryId, city_id = :cityId WHERE id = :id";

        $guests = new Guest();
        $guests->setFirstName($firstName);
        $guests->setLastName($lastName);
        $guests->setEgn($egn);
        $guests->setPhoneNumber($phoneNumber);
        $guests->setCountryId($countryId);
        $guests->setCityId($cityId);

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':firstName', $guests->getFirstName());
        $statement->bindParam(':lastName', $guests->getLastName());
        $statement->bindParam(':egn', $guests->getEgn());
        $statement->bindParam(':phoneNumber', $guests->getPhoneNumber());
        $statement->bindParam(':countryId', $guests->getCountryId());
        $statement->bindParam(':cityId', $guests->getCityId());
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from guests Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(?int $id): ?Guest
    {
        $query = "SELECT id as id, first_name as firstName, last_name as lastName, egn as egn, phone_number as phoneNumber, country_id as countryId, city_id as cityId From guests Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $guests = $statement->fetchObject(Guest::class) ?: null;

        return $guests;
    }

    public function findByCountryId(int $countryId): array
    {
        $query = "SELECT id as id, first_name as firstName, last_name as lastName, egn as egn, phone_number as phoneNumber, country_id as countryId, city_id as cityId From guests Where country_id = :countryId";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->bindParam(':countryId', $countryId);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Guest");

        return $result;
    }

    public function findByEGN(string $egn): ?Guest
    {
        $query = "SELECT id as id, first_name as firstName, last_name as lastName, egn as egn, phone_number as phoneNumber, country_id as countryId, city_id as cityId From guests Where egn = :egn";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':egn', $egn);
        $statement->execute();

        $guests = $statement->fetchObject(Guest::class) ?: null;

        return $guests;
    }
}
