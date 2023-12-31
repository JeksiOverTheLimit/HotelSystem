<?php

declare(strict_types=1);

include_once "BaseModel.php";

class Guest extends BaseModel
{
    private string $firstName;
    private string $lastName;
    private string $egn;
    private string $phoneNumber;
    private int $countryId;
    private int $cityId;

    public function __construct()
    {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $value): void
    {
        if (empty($value)) {
            throw new Exception("Не може да бъде празно полето  FirstName");
        }
        $this->firstName = $value;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $value): void
    {
        if (empty($value)) {
            throw new Exception("Не може да бъде празно полето LastName");
        }
        $this->lastName = $value;
    }

    public function getEgn(): string
    {
        return $this->egn;
    }

    public function setEgn(string $value): void
    {
        if (strlen($value) !== 10) {
            throw new Exception('Невалидна дължина на ЕГН');
        }
       
        $this->egn = $value;
    }



    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $value): void
    {
        $this->phoneNumber = $value;
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }

    public function setCountryId(int $value): void
    {
        $this->countryId = $value;
    }

    public function getCityId(): int
    {
        return $this->cityId;
    }

    public function setCityId(int $value): void
    {
        $this->cityId = $value;
    }
}
