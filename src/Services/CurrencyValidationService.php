<?php

declare(strict_types=1);

class CurrencyValidationService
{
    
    public function validateCurrencyName($name): void
    {
        if ($name == '') {
            throw new Exception('Името на валутата няма как да е празно');
        }
    }
}
