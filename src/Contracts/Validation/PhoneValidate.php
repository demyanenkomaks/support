<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneValidate implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phoneLength = config('support.validate.length.phone', 11);

        // Обрезаем пробелы по краям
        $trimmedValue = trim($value);

        // Проверка: должен начинаться с +7
        if (! str_starts_with($trimmedValue, '+7')) {
            $fail($attribute, __('support::support.validate.errors.phone.start'));

            return;
        }

        // Убираем + для подсчета цифр
        $digits = str_replace('+', '', $trimmedValue);

        // Проверка: должно быть ровно указанное количество цифр
        if (strlen($digits) !== $phoneLength) {
            $fail($attribute, __('support::support.validate.errors.phone.length', ['length' => $phoneLength]));

            return;
        }

        // Проверка: все символы после + должны быть цифрами
        $digitsAfterCode = $phoneLength - 1; // -1 для кода страны (7)
        if (! preg_match('/^\+7\d{'.$digitsAfterCode.'}$/', $trimmedValue)) {
            $fail($attribute, __('support::support.validate.errors.phone.format'));

            return;
        }
    }
}
