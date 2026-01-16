<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NameValidate implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Обрезаем пробелы по краям
        $trimmedValue = trim($value);

        // Проверка максимальной длины из конфига
        $maxLength = config('support.validate.length.name', 50);
        $length = mb_strlen($trimmedValue);
        if ($length > $maxLength) {
            $fail($attribute, __('support::support.validate.errors.name.max_length', ['max' => $maxLength]));

            return;
        }

        // Проверка: не должно быть только пробелов, дефисов или апострофов
        if (preg_match('/^[\s\'\-]+$/', $trimmedValue)) {
            $fail($attribute, __('support::support.validate.errors.name.empty'));

            return;
        }

        // Проверка: разрешены кириллица, латиница, дефис, пробел, апостроф
        // Запрещены: цифры и другие спецсимволы
        $pattern = '/^[\p{L}\s\'\-]+$/u';
        if (! preg_match($pattern, $trimmedValue)) {
            $fail($attribute, __('support::support.validate.errors.name.invalid_characters'));

            return;
        }
    }
}
