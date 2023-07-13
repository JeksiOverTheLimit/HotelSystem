<?php

declare(strict_types=1);

include_once "../Database/Repositories/PaymentRepository.php";
include_once "../Models/Payment.php";

class PaymentValidationService
{
    private PaymentRepository $paymentRepository;

    public function __construct()
    {
        $this->paymentRepository = new PaymentRepository();
    }

    public function validateReservationId($reservationId): void
    {
        $idExsistCheck = $this->paymentRepository->checkForAvaliblePayment($reservationId);
        
        if($idExsistCheck === true) {
            throw new Exception("Вече е извършено плащане по тази резервация");
        }

        if ($reservationId == null) {
            throw new Exception("Трябва да изберете резервация");
        }
    }

    public function validateCurrencyId($currencyId): void
    {
        if ($currencyId == null) {
            throw new Exception("Трябва да изберете валута ");
        }
    }

    public function validatePrice($price): void
    {
        if ($price == null) {
            throw new Exception("Трябва да въведете цена");
        }

        if (!ctype_digit($price)) {
            throw new Exception("Цената трябва да бъде в цифри");
        }
    }

    public function validatePaymentDate($paymentDate): void
    {

        if ($paymentDate == '') {
            throw new Exception("Трябва да въведете дата на плащането");
        }

        $paymentDateTime = new DateTime($paymentDate);
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        if ($paymentDateTime < $today) {
            throw new Exception("Датата на плащането не може да бъде по-ранна от днес");
        }
    }
}
