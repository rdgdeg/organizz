<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Accepte E.164 (+32..., +33...) ou formats locaux BE/FR courants.
 */
class PhoneBeFr implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $v = preg_replace('/\s+/', '', (string) $value);

        if (preg_match('/^\+[1-9]\d{7,14}$/', $v)) {
            return;
        }

        if (preg_match('/^0[1-9]\d{8}$/', $v)) {
            return;
        }

        if (preg_match('/^0[1-9]\d{7}$/', $v)) {
            return;
        }

        $fail(__('Le numéro de téléphone doit être valide (format belge, français ou E.164).'));
    }
}
