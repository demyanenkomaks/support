<?php

declare(strict_types=1);

namespace Maksde\Support\Contracts\Validation;

use Closure;
use DateTime;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

class TimeValidate implements ValidationRule
{
    /**
     * @param  string|null  $timeConstraint  Ограничение по времени: 'future' (будущее), 'past' (прошлое), null (любое время)
     * @param  string|null  $referenceTime  Опорное время для сравнения (формат H:i:s), если null - используется текущее время
     */
    public function __construct(
        protected ?string $timeConstraint = null,
        protected ?string $referenceTime = null
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
        $format = Config::get('support.validate.format.time', 'H:i:s');

        // Обрезаем пробелы по краям
        $trimmedValue = trim($value);

        // Проверка: должно быть валидным временем в указанном формате
        $time = DateTime::createFromFormat($format, $trimmedValue);
        if (! $time || $time->format($format) !== $trimmedValue) {
            $fail($attribute, __('support::support.validate.errors.time.format', ['format' => $format]));

            return;
        }

        // Проверка ограничения по времени
        if ($this->timeConstraint !== null) {
            // Определяем опорное время
            if ($this->referenceTime !== null) {
                $referenceDateTime = DateTime::createFromFormat($format, $this->referenceTime);
                if (! $referenceDateTime) {
                    throw new InvalidArgumentException("Invalid reference time format. Expected: {$format}");
                }
            } else {
                $now = new DateTime;
                $referenceDateTime = DateTime::createFromFormat($format, $now->format($format));
            }

            if ($this->timeConstraint === 'future' && $time <= $referenceDateTime) {
                $fail($attribute, __('support::support.validate.errors.time.future'));

                return;
            }

            if ($this->timeConstraint === 'past' && $time >= $referenceDateTime) {
                $fail($attribute, __('support::support.validate.errors.time.past'));

                return;
            }
        }
    }
}
