<?php

declare(strict_types=1);

include_once "BaseId.php";

class BaseName extends BaseId
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
