<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneInternationalValidate implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $minDigits = config('support.validate.length.phone_international_min', 7);
        $maxDigits = config('support.validate.length.phone_international_max', 15);

        // Обрезаем пробелы по краям
        $trimmedValue = trim($value);

        // Проверка: должен начинаться с +
        if (! str_starts_with($trimmedValue, '+')) {
            $fail($attribute, __('support::support.validate.errors.phone_international.start'));

            return;
        }

        // Убираем + для подсчета цифр
        $digits = str_replace('+', '', $trimmedValue);

        // Проверка: должно быть от минимума до максимума цифр
        $digitCount = strlen($digits);
        if ($digitCount < $minDigits) {
            $fail($attribute, __('support::support.validate.errors.phone_international.min', ['min' => $minDigits]));

            return;
        }

        if ($digitCount > $maxDigits) {
            $fail($attribute, __('support::support.validate.errors.phone_international.max', ['max' => $maxDigits]));

            return;
        }

        // Проверка: формат должен быть +[цифры]
        if (! preg_match('/^\+\d{'.$minDigits.','.$maxDigits.'}$/', $trimmedValue)) {
            $fail($attribute, __('support::support.validate.errors.phone_international.format'));

            return;
        }
    }
}
