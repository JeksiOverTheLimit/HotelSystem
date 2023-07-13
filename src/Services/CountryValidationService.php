<?php

declare(strict_types=1);

class CountryValidationService
{

    public function validateCountryName($name): void
    {
        if ($name == '') {
            throw new Exception('Името на държавата няма как да е празно');
        }
    }
}
