<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;

class QuantityValidate implements ValidationRule
{
    /**
     * @param  int|null  $min  Минимальное значение (по умолчанию 0)
     * @param  int|null  $max  Максимальное значение (по умолчанию без ограничения)
     */
    public function __construct(
        protected ?int $min = 0,
        protected ?int $max = null
    ) {
        // Валидация параметров
        if ($this->min !== null && $this->max !== null && $this->min > $this->max) {
            throw new InvalidArgumentException('Minimum value cannot be greater than maximum value');
        }
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Проверка: не должно содержать запрещенные символы e, E, +, -
        if (is_string($value) && preg_match('/[eE\+\-]/', $value)) {
            $fail($attribute, __('support::support.validate.errors.quantity.forbidden_characters'));

            return;
        }

        // Проверка: должно быть целым числом
        if (! is_numeric($value) || ! is_int($value + 0)) {
            $fail($attribute, __('support::support.validate.errors.quantity.not_integer'));

            return;
        }

        $intValue = (int) $value;

        // Проверка минимального значения
        if ($this->min !== null && $intValue < $this->min) {
            $fail($attribute, __('support::support.validate.errors.quantity.min', ['min' => $this->min]));

            return;
        }

        // Проверка максимального значения
        if ($this->max !== null && $intValue > $this->max) {
            $fail($attribute, __('support::support.validate.errors.quantity.max', ['max' => $this->max]));

            return;
        }
    }
}
