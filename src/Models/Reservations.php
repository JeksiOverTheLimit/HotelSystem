<?php

declare(strict_types=1);

include_once "BaseId.php";

class Reservations extends BaseId
{
    private ?int $employeeId;
    private ?int $roomId;
    private string $startingDate;
    private string $finalDate;
    private int $statusId;
    
    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function setEmployeeId(int $value): void
    {
        $this->employeeId = $value;
    }

    public function getRoomId(): ?int
    {
        return $this->roomId;
    }

    public function setRoomId(int $value): void
    {
        $this->roomId = $value;
    }

    public function getStartingDate(): string
    {
        return $this->startingDate;
    }

    public function setStartingDate(string $value): void
    {
        if ($value == null){
          throw new Exception('NE MOJE Nachalnata data da e prazna');
        }
        $this->startingDate = $value;
    }

    public function getFinalDate(): string
    {
  
        return $this->finalDate;
    }

    public function setFinalDate(string $value): void
    {
        if ($value == null){
            throw new Exception('NE MOJE Krainata data da e prazna');
          }
        $this->finalDate = $value;
    }

    public function getStatusId(): int
    {
        return $this->statusId;
    }

    public function setStatusId(int $value): void
    {
        $this->statusId = $value;
    }
}
