<?php

namespace Maksde\Support\Formation;

use Carbon\Carbon;

class TemporalFormat
{
    /**
     * @param  string|null  $datetime  Значение даты и времени
     * @param  string  $timezone  Временная метка
     * @return string|null Отформатированная дата и время
     */
    public static function datetime(?string $datetime, string $timezone = 'UTC'): ?string
    {
        return self::type($datetime, 'datetime', $timezone);
    }

    /**
     * @param  string|null  $date  Значение даты
     * @param  string  $timezone  Временная метка
     * @return string|null Отформатированная дата
     */
    public static function date(?string $date, string $timezone = 'UTC'): ?string
    {
        return self::type($date, 'date', $timezone);
    }

    /**
     * @param  string|null  $time  Значение времени
     * @param  string  $timezone  Временная метка
     * @return string|null Отформатированное время
     */
    public static function time(?string $time, string $timezone = 'UTC'): ?string
    {
        return self::type($time, 'time', $timezone);
    }

    /**
     * Форматирует временную метку.
     *
     * @param  string|null  $temporal  Временная метка
     * @param  string  $type  Тип временной метки
     * @param  string  $timezone  Временная зона (по умолчанию 'UTC')
     * @return string|null Отформатированное значение или null, если входные данные пустые
     */
    public static function type(?string $temporal, string $type, string $timezone = 'UTC'): ?string
    {
        if (empty($temporal)) {
            return null;
        }

        $format = config('support.return.format.'.$type);

        if (empty($format)) {
            throw new \InvalidArgumentException(sprintf("Format for type '%s' not found in configuration.", $type));
        }

        return self::format($temporal, $format, $timezone);
    }

    /**
     * Форматирует временную метку с пользовательским форматом.
     *
     * @param  string|null  $temporal  Временная метка
     * @param  string  $format  Пользовательский формат
     * @param  string  $timezone  Временная зона (по умолчанию 'UTC')
     * @return string|null Отформатированное значение или null, если входные данные пустые
     */
    public static function format(?string $temporal, string $format, string $timezone = 'UTC'): ?string
    {
        if (empty($temporal)) {
            return null;
        }

        return Carbon::parse($temporal)->setTimezone($timezone)->format($format);
    }
}
