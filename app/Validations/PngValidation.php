<?php

// app/Validation/Rules/PngValidation.php
namespace App\Validation\Rules;

use CodeIgniter\Validation\Rule;

class PngValidation extends Rule
{
    public function passes($value, $options): bool
    {
        return strpos($value, '.png') !== false;
    }

    public function error(string $field, string $error = ''): string
    {
        return 'The ' . $field . ' field must contain ".png"';
    }
}