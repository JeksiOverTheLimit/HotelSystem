<?php

declare(strict_types=1);

class RoomExtraMapRepository
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create(int $roomId, int $extraId): RoomExtraMap
    {
        $query = "INSERT INTO rooms_extras_map (room_id, extra_id) 
        VALUES (:roomId, :extraId)";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->bindParam(':extraId', $extraId);
        $statement->execute();

        $id = $connection->lastInsertId();

        return $this->findById(intval($id));
    }

    public function deleteByRoomId(int $roomId): void
    {
        $query = "DELETE FROM rooms_extras_map WHERE room_id = :roomId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->execute();
    }

    public function update(int $id, int $roomId, int $extraId): void
    {
        $query = "UPDATE rooms_extras_map SET room_id = :roomId, extra_id = :extraId  WHERE id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':roomId', $roomId);
        $statement->bindParam(':extraId', $extraId);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = "DELETE from rooms_extras_map Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
    }

    public function findById(?int $id): ?RoomExtraMap
    {
        $query = "SELECT id as id, room_id as roomId, extra_id as extraId From rooms_extras_map Where id = :id";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();

        $guests = $statement->fetchObject(RoomExtraMap::class) ?: null;

        return $guests;
    }

    public function findByRoomId(int $roomId): ?RoomExtraMap
    {
        $query = "SELECT id as id, room_id as roomId, extra_id as extraId From rooms_extras_map Where room_id = :roomId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->execute();

        $guests = $statement->fetchObject(RoomExtraMap::class) ?: null;

        return $guests;
    }

    public function getAllExtrasForRoom(int $roomId): array
    {
        $query = "SELECT  extra_id as extraId FROM rooms_extras_map WHERE room_id = :roomId";

        $connection = $this->database->getConnection();
        $statement = $connection->prepare($query);
        $statement->bindParam(':roomId', $roomId);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getAllRoomsExtrasMap() : ?RoomExtraMap
    {
        $query = "SELECT id as id, room_id as roomId, extra_id as extraId FROM rooms_extras_map";

        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute();

        $result = $statement->fetchObject(RoomExtraMap::class) ?: null;

        return $result;
    }
}
