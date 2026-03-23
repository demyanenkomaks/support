<?php

declare(strict_types=1);

namespace Maksde\Support\Formation;

use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Конвертация и форматирование даты, времени и datetime при сохранении и выводе.
 */
class TemporalFormat
{
    /**
     * Подготовка к сохранению: значение форматируется для БД.
     * Для date — только формат (timezone не применяется). Для time и datetime — из $fromTimezone в UTC.
     *
     * @param  string|null  $value  Строка даты/времени в формате, распознаваемом Carbon
     * @param  string  $type  Тип значения: 'date', 'time' или 'datetime' (для time используется якорная дата)
     * @param  string  $fromTimezone  Исходная timezone для time/datetime (по умолчанию UTC); для date не используется
     * @return string|null Отформатированная строка для хранения или null при пустом $value
     */
    public static function forStorage(?string $value, string $type, string $fromTimezone = 'UTC'): ?string
    {
        if (empty($value)) {
            return null;
        }

        $format = config('support.storage.format.'.$type);
        if (empty($format)) {
            throw new InvalidArgumentException(sprintf("Storage format for type '%s' not found in configuration.", $type));
        }

        return match ($type) {
            'date' => Carbon::parse($value)->format($format),
            'time' => Carbon::parse('2000-01-01 '.$value, $fromTimezone)->utc()->format($format),
            'datetime' => Carbon::parse($value, $fromTimezone)->utc()->format($format),
            default => throw new InvalidArgumentException(sprintf("Unknown type '%s'.", $type)),
        };
    }

    /**
     * Подготовка к выводу: значение из UTC переводится в целевую timezone и форматируется.
     *
     * @param  string|null  $value  Строка в UTC (для time — только время с якорной датой при разборе)
     * @param  string  $type  Тип значения: 'date', 'time' или 'datetime'
     * @param  string  $toTimezone  Целевая timezone (по умолчанию UTC)
     * @param  string|null  $format  Формат вывода; при null берётся из config('support.view.format.'.$type)
     * @return string|null Отформатированная строка или null при пустом $value
     */
    public static function forOutput(?string $value, string $type, string $toTimezone = 'UTC', ?string $format = null): ?string
    {
        if (empty($value)) {
            return null;
        }

        $format = $format ?? config('support.view.format.'.$type);
        if (empty($format)) {
            throw new InvalidArgumentException(sprintf("Output format for type '%s' not found in configuration.", $type));
        }

        return match ($type) {
            'date' => Carbon::parse($value)->format($format),
            'time' => Carbon::parse('2000-01-01 '.$value, 'UTC')->timezone($toTimezone)->format($format),
            'datetime' => Carbon::parse($value, 'UTC')->timezone($toTimezone)->format($format),
            default => throw new InvalidArgumentException(sprintf("Unknown type '%s'.", $type)),
        };
    }
}
