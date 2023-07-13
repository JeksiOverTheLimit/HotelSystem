<?php

declare(strict_types=1);

class ReservationValidationService
{
    private const MINIMUM_CHECKING_DAYS = 1;

    public function validateDate($startingDate, $finalDate): void
    {
        $startDate = new DateTime($startingDate);
        $endDate = new DateTime($finalDate);
        $interval = $startDate->diff($endDate);
        $differenceBetweenDates = $interval->days;


        if ($startingDate == '' && $finalDate == '') {
            throw new Exception("Трябва да посочите период на резервацията.");
        }

        if ($differenceBetweenDates <= self::MINIMUM_CHECKING_DAYS) {
            throw new Exception("Не може резервацията да бъде в период " . self::MINIMUM_CHECKING_DAYS . " ден ");
        }

        if ($startingDate > $finalDate) {
            throw new Exception("Няма как началната дата да е по голяма от крайната");
        }
    }

    public function validateEmployee($employee): void
    {
        if ($employee == null) {
            throw new Exception("Моля изберете работник");
        }
    }

    public function validateRoom($room): void
    {
        if ($room == null) {
            throw new Exception("Моля изберете стая");
        }
    }

    public function validateStatus($status): void
    {
        if ($status == null) {
            throw new Exception("Моля изберете статус");
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
}
