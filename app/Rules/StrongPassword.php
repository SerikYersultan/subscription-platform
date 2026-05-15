<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 8) {
            $fail('The password must be at least 8 characters long.');
            return;
        }

        $score = 0;
        if (preg_match('/[A-Z]/', $value)) $score++;
        if (preg_match('/[a-z]/', $value)) $score++;
        if (preg_match('/[0-9]/', $value)) $score++;
        if (preg_match('/[^A-Za-z0-9]/', $value)) $score++;

        if ($score < 3) {
            $fail('The password must contain characters from at least 3 of these 4 categories: uppercase letters (A-Z), lowercase letters (a-z), numbers (0-9), and special symbols (!, $, #, % …).');
        }
    }
}
