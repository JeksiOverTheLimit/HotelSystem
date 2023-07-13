<?php

declare(strict_types=1);

class UserValidationService
{
    private const EGN_REQUIRED_LENGHT = 10;
    private const PREFIX = ['359'];
    private const VALID_PREFIX = ['87', '88', '89'];
    private const PHONE_REQUIRED_LENGHT = 13;

    public function validateName(string $name): void
    {
        if (empty($name)) {
            throw new Exception('Трябва да попълните името си');
        }

        $length = strlen($name);
        for ($i = 0; $i < $length; $i += 2) {
            if (!ctype_upper($name[$i])) {
                throw new Exception('Всяка трета буква на името трябва да е главна');
            }
        }
    }

    public function validateEgn(string $egn): void
    {
        if (empty($egn)) {
            throw new Exception('ЕГН-то не може да е празно');
        }

        if (strlen($egn) !== self::EGN_REQUIRED_LENGHT) {
            throw new Exception('ЕГН-то трябва да е ' . self::EGN_REQUIRED_LENGHT . ' цифри');
        }

        if (!ctype_digit($egn)) {
            throw new Exception('ЕГН-то трябва да съдържа само цифри');
        }

        $year = substr($egn, 0, 2);
        $month = substr($egn, 3, 1);
        $checkMonth = substr($egn, 2, 2);

        if ($checkMonth === '11' || $checkMonth === '10' || $checkMonth === '12') {
            $month = $checkMonth;
        }

        $day = substr($egn, 4, 2);

        $currentYear = date('Y');
        $fullYear = ($year >= 0 && $year <= 21) ? "20$year" : "19$year";

        $fullDate = "$fullYear-$month-$day";
        $dateTime = new DateTime($fullDate);

        $currentDate = new DateTime();
        $ageInterval = $dateTime->diff($currentDate);
        $ageInYears = $ageInterval->y;

        if ($ageInYears < 18) {
            throw new Exception('Възрастта е под 18 години.');
        }
    }

    public function validatePhone(string $phone): void
    {
        $defaultPrefix = substr($phone, 0, 4);
        $phonePrefix = substr($phone, 4, 2);
        $firstDigit = substr($phone, 4, 1);
        $phoneDigit = substr($phone, 4, 13);
        $length = strlen($phone);

        if ($firstDigit == 0) {
            throw new Exception('Не може да има 0 след ' . self::PREFIX . '');
        }

        if (!ctype_digit($phoneDigit)) {
            throw new Exception('Номера трябва да съдържа само цифри');
        }

        if ($length !== self::PHONE_REQUIRED_LENGHT) {
            throw new Exception('Телефонният номер трябва да е с ' . self::PHONE_REQUIRED_LENGHT . ' символа.');
        }

        if (!in_array($defaultPrefix, self::PREFIX)) {
            throw new Exception('Телефонният номер трябва да съдържа ' . self::PREFIX . '');
        }

        if (!in_array($phonePrefix, self::VALID_PREFIX)) {
            throw new Exception('Невалиден телефонен номер трябва да започва с ' . implode(', ', self::VALID_PREFIX) . '.');
        }
    }

    public function validateCountry(int $countryId): void
    {
        if ($countryId == 0) {
            throw new Exception('Трябва да изберете Държава');
        }
    }

    public function validateEmail(string $email): void {

        if($email === ''){
            throw new Exception('Трябва да въведете имейлът си');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Невалиден имейл');
        }
    }
}
