<?php

declare(strict_types=1);

class BaseModel
{
    protected int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $value): void
    {
        $this->id = $value;
    }
}
