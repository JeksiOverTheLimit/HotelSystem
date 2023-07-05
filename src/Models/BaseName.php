<?php

declare(strict_types=1);

include_once "BaseModel.php";

class BaseName extends BaseModel
{
    protected string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $value): void
    {
        $this->name = $value;
    }
}
