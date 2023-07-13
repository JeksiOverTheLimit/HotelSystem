<?php

declare(strict_types=1);

class RoomValidationService
{
    private const REQUIRED_NUMBER_LENGHT = 3;

    public function validateRoomNumber($number): void
    {
        $lenght = strlen($number);

        if ($number == '') {
            throw new Exception('Не може номерът да бъде празен');
        }

        if ($lenght !== self::REQUIRED_NUMBER_LENGHT) {
            throw new Exception('Номерът на стаята трябва да бъде ' . self::REQUIRED_NUMBER_LENGHT . ' цифри');
        }

        if (!ctype_digit($number)) {
            throw new Exception("Не може номерът да съдържа букви");
        }
    }

    public function validateType($type): void
    {
        if ($type == null) {
            throw new Exception("Не може типът да е празен");
        }
    }

    public function validatePrice($price): void
    {
        if ($price == null) {
            throw new Exception("Не може цената да бъде празна");
        }
    }

    public function validateExtras($extras): void
    {
        if (empty($extras)) {
            throw new Exception("Изборът на поне една екстра е задължителен");
        }
    }
}
