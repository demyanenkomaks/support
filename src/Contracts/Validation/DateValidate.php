<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use DateTime;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class DateValidate implements ValidationRule
{
    /**
     * @param  string|null  $timeConstraint  Ограничение по времени: 'future' (будущее), 'past' (прошлое), null (любая дата)
     * @param  string|null  $referenceDate  Опорная дата для сравнения (формат Y-m-d), если null - используется текущая дата
     */
    public function __construct(
        protected ?string $timeConstraint = null,
        protected ?string $referenceDate = null
    ) {
        // Валидация параметра timeConstraint
        if ($this->timeConstraint !== null && ! in_array($this->timeConstraint, ['future', 'past'], true)) {
            throw new InvalidArgumentException('Time constraint must be "future", "past", or null');
        }
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $format = Config::get('support.validate.format.date', 'Y-m-d');

        // Обрезаем пробелы по краям
        $trimmedValue = trim($value);

        // Проверка: должна быть валидной датой в указанном формате
        $date = DateTime::createFromFormat($format, $trimmedValue);
        if (! $date || $date->format($format) !== $trimmedValue) {
            $fail($attribute, __('support::support.validate.errors.date.format', ['format' => $format]));

            return;
        }

        // Проверка ограничения по времени
        if ($this->timeConstraint !== null) {
            // Определяем опорную дату
            if ($this->referenceDate !== null) {
                $referenceDateTime = DateTime::createFromFormat($format, $this->referenceDate);
                if (! $referenceDateTime) {
                    throw new InvalidArgumentException("Invalid reference date format. Expected: {$format}");
                }

                $referenceDateTime->setTime(0, 0, 0);
            } else {
                $referenceDateTime = new DateTime('today');
            }

            $date->setTime(0, 0, 0); // Обнуляем время для корректного сравнения дат

            if ($this->timeConstraint === 'future' && $date <= $referenceDateTime) {
                $fail($attribute, __('support::support.validate.errors.date.future'));

                return;
            }

            if ($this->timeConstraint === 'past' && $date >= $referenceDateTime) {
                $fail($attribute, __('support::support.validate.errors.date.past'));

                return;
            }
        }
    }
}
