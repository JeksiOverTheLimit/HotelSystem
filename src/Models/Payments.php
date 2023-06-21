<?php

declare(strict_types=1);

include_once "BaseId.php";

class Payments 
{
    private int $reservationId;
    private int $currencyId;
    private float $price;
    private string $paymentDate;

    public function getReservationId() : int {
        return $this->reservationId;
    }

    public function setReservationId(int $value): void {
        $this->reservationId = $value;
    }

    public function getCurrencyId() : int {
        return $this->currencyId;
    }

    public function setCurrencyId(int $value) : void {
        $this->currencyId = $value;
    }

    public function getPrice() : float {
        return $this->price;
    }

    public function setPrice(float $value) : void {
        $this->price = $value;
    }

    public function getPaymentDate() : string {
        return $this->paymentDate;
    }

    public function setPaymentDate(string $value) : void {
        $this->paymentDate = $value;
    } 
}
