<?php

declare(strict_types=1);

include_once "BaseId.php";

class Rooms extends BaseId
{
    private int $number;
    private int $typeId;
    private float $price;

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $value): void
    {
        $this->number = $value;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    public function setTypeId(int $value): void
    {
        $this->typeId = $value;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $value): void
    {
        $this->price = $value;
    }

}
