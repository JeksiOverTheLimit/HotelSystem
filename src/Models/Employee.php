<?php

declare(strict_types=1);

include_once "BaseModel.php";

class Employee extends BaseModel
{
    private string $firstName;
    private string $lastName;
    private string $egn;
    private string $phoneNumber;
    private string $email;
    private int $countryId;
    private int $cityId;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $value): void
    {
        $this->firstName = $value;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $value): void
    {
        $this->lastName = $value;
    }

    public function getEgn(): string
    {
        return $this->egn;
    }

    public function setEgn(string $value): void
    {
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

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $value) {
        $this->email = $value;
    }
}
