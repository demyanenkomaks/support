<?php

namespace Maksde\Support\Contracts\Validation;

use Closure;
use DateTime;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class DateTimeValidate implements ValidationRule
{
    /**
     * @param  string|null  $timeConstraint  Ограничение по времени: 'future' (будущее), 'past' (прошлое), null (любая дата и время)
     * @param  string|null  $referenceDateTime  Опорная дата-время для сравнения (формат Y-m-d H:i:s), если null - используется текущая дата-время
     */
    public function __construct(
        protected ?string $timeConstraint = null,
        protected ?string $referenceDateTime = null
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
        $format = Config::get('support.validate.format.datetime', 'Y-m-d H:i:s');

        // Обрезаем пробелы по краям
        $trimmedValue = trim($value);

        // Проверка: должна быть валидной датой и временем в указанном формате
        $datetime = DateTime::createFromFormat($format, $trimmedValue);
        if (! $datetime || $datetime->format($format) !== $trimmedValue) {
            $fail($attribute, __('support::support.validate.errors.datetime.format', ['format' => $format]));

            return;
        }

        // Проверка ограничения по времени
        if ($this->timeConstraint !== null) {
            // Определяем опорную дату-время
            if ($this->referenceDateTime !== null) {
                $referenceDateTimeObj = DateTime::createFromFormat($format, $this->referenceDateTime);
                if (! $referenceDateTimeObj) {
                    throw new InvalidArgumentException("Invalid reference datetime format. Expected: {$format}");
                }
            } else {
                $referenceDateTimeObj = new DateTime;
            }

            if ($this->timeConstraint === 'future' && $datetime <= $referenceDateTimeObj) {
                $fail($attribute, __('support::support.validate.errors.datetime.future'));

                return;
            }

            if ($this->timeConstraint === 'past' && $datetime >= $referenceDateTimeObj) {
                $fail($attribute, __('support::support.validate.errors.datetime.past'));

                return;
            }
        }
    }
}
