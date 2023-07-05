<?php

declare(strict_types=1);

class EmployeeRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(string $firstName, string $lastName, string $egn, string $phoneNumber, int $countryId, int $cityId): Employee
    {
        $query = "INSERT INTO employees (first_name, last_name, egn, phone_number, country_id, city_id) 
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
        $query = "UPDATE employees SET first_name = :firstName, last_name = :lastName, egn = :egn, phone_number = :phoneNumber, country_id = :countryId, city_id = :cityId WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':firstName', $firstName);
        $statement->bindParam(':lastName', $lastName);
        $statement->bindParam(':egn', $egn);
        $statement->bindParam(':phoneNumber', $phoneNumber);
        $statement->bindParam(':countryId', $countryId);
        $statement->bindParam(':cityId', $cityId);
        $statement->execute();
    }

    public function delete(int $id)
    {
        $query = "DELETE from employees Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function deleteByCountryId(int $countryId) : void {
        $query = "DELETE from employees Where country_id = :countryId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':countryId', $CountryId);
        $statement->execute();
    }

    public function findById(?int $id): ?Employee
    {
        $query = "SELECT id as id, first_name as firstName, last_name as lastName, egn as egn, phone_number as phoneNumber, country_id as countryId, city_id as cityId From employees Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $employee = $statement->fetchObject(Employee::class) ?: null;

        return $employee;
    }

    public function findByCountryId(?int $countryId): array
    {
        $query = "SELECT id as id, first_name as firstName, last_name as lastName, egn as egn, phone_number as phoneNumber, country_id as countryId, city_id as cityId From employees Where country_id = :countryId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':countryId', $countryId);
        $statement->execute();

        $employees = $statement->fetchAll(PDO::FETCH_CLASS, "Employee");

        return $employees;
    }

    public function getAllEmployees(): array
    {
        $query = "SELECT id, first_name as firstName, last_name as lastName, egn, phone_number as phoneNumber, country_id as countryId, city_id as cityId   FROM employees";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Employee");

    
        return $result;
    }

}
