<?php

declare(strict_types=1);


class ReservationRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(int $employeeId, int $roomId, string $startingDate, string $finalDate, int $statusId, float $price): Reservation
    {
        $query = "INSERT INTO reservations (employee_id, room_id, starting_date, final_date, status_id, price) 
        VALUES (:employeeId, :roomId, :startingDate, :finalDate, :statusId, :price)";


        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':employeeId', $employeeId);
        $statement->bindParam(':roomId', $roomId);
        $statement->bindParam(':startingDate', $startingDate);
        $statement->bindParam(':finalDate', $finalDate);
        $statement->bindParam(':statusId', $statusId);
        $statement->bindParam(':price', $price);
        $statement->execute();

        $id = $connection->lastInsertId();

        return $this->findById(intval($id));
    }

    public function update(int $id, int $employeeId, int $roomId, string $startingDate, string $finalDate, int $statusId, float $price): void
    {
        $query = "UPDATE reservations SET employee_id = :employeeId, room_id = :roomId, starting_date = :startingDate, final_date = :finalDate, status_id = :statusId, price = :price WHERE id = :id";

        

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':employeeId', $employeeId);
        $statement->bindParam(':roomId', $roomId);
        $statement->bindParam(':startingDate', $startingDate);
        $statement->bindParam(':finalDate', $finalDate);
        $statement->bindParam(':statusId', $statusId);
        $statement->bindParam(':price', $price);
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

    public function deleteByEmployeeId(int $employeeId)
    {
        $query = "DELETE from reservations Where employee_id = :employeeId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':employeeId', $employeeId);
        $statement->execute();
    }

    public function deleteByRoomId(int $roomId)
    {
        $query = "DELETE from reservations Where room_id = :roomId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->execute();
    }

    public function findByEmployeeId(int $employeeId): array
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId, price as price From reservations Where employee_id = :employeeId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':employeeId', $employeeId);
        $statement->execute();

        $reservations = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $reservations;
    }

    public function findByRoomId(int $roomId): array
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId, price as price From reservations Where room_id = :roomId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->execute();

        $reservations = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $reservations;
    }

    public function findById(int $id): ?Reservation
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId, price as price From reservations Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $reservations = $statement->fetchObject(Reservation::class) ?: null;

        return $reservations;
    }

    public function getAllReservations(): array
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId, price as price FROM reservations";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $result;
    }

    public function getAllReservationById(int $id): array
    {
        $query = "SELECT id as id, employee_id as employeeId, room_id as roomId, starting_date as startingDate, final_date as finalDate, status_id as statusId, price as price FROM reservations WHERE id = :Id";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->bindParam(':Id', $id);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");

        return $result;
    }

    public function filterReservations(string $reservationStartingDate, string $reservationFinalDate, ?int $roomId, ?int $countryId): ?array
    {
        $query = "SELECT r.id as id, r.employee_id as employeeId, r.room_id as roomId, r.starting_date as startingDate, r.final_date as finalDate, r.status_id as statusId, r.price as price 
          FROM reservations r ";
    
        $whereClauses = [];
        $parameters = [];
    
        if ($reservationStartingDate != '') {
            $whereClauses[] = "r.starting_date >= :startingDate ";
            $parameters[':startingDate'] = $reservationStartingDate;
        }
    
        if ($reservationFinalDate != '') {
            $whereClauses[] = "r.final_date <= :finalDate ";
            $parameters[':finalDate'] = $reservationFinalDate;
        }
    
        if ($roomId != null) {
            $whereClauses[] = "r.room_id = :roomId";
            $parameters[':roomId'] = $roomId;
        }
    
        if ($countryId != null) {
            $query .= "LEFT JOIN reservations_guests_map m ON r.id = m.reservation_id
            LEFT JOIN guests g ON m.guest_id = g.id";

            $whereClauses[] = "g.country_id = :countryId";
            $parameters[':countryId'] = $countryId;
        }
    
        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }
    
        $statement = $this->database->getConnection()->prepare($query);
       
        foreach($parameters as $key => $value){
          $statement->bindValue($key, $value);
        }
       
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, "Reservation");
        return $result;
    }
    
}
