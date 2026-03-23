<?php

declare(strict_types=1);

namespace Maksde\Support\Tests\Unit;

use DateTime;
use Illuminate\Translation\PotentiallyTranslatedString;
use Maksde\Support\Contracts\Validation\DateTimeValidate;
use Maksde\Support\Tests\TestCase;

class DateTimeValidateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Mock the config for testing purposes
        $this->app['config']->set('support.validate.format.datetime', 'Y-m-d H:i:s');
    }

    /**
     * @test
     *
     * @dataProvider validDateTimeStringsProvider
     */
    public function test_valid_date_time_strings(string $datetime): void
    {
        $this->assertValid(new DateTimeValidate, $datetime);
    }

    /**
     * @test
     *
     * @dataProvider invalidDateTimeStringsProvider
     */
    public function test_invalid_date_time_strings(string $datetime, string $expectedErrorKey): void
    {
        $this->assertInvalid(new DateTimeValidate, $datetime, $expectedErrorKey);
    }

    /**
     * @test
     */
    public function test_future_constraint(): void
    {
        $validator = new DateTimeValidate('future');

        // Дата и время через 1 час - должно пройти
        $futureDateTime = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');
        $this->assertValid($validator, $futureDateTime);

        // Дата и время час назад - должно провалиться
        $pastDateTime = (new DateTime('-1 hour'))->format('Y-m-d H:i:s');
        $this->assertInvalid($validator, $pastDateTime, 'datetime.future');
    }

    /**
     * @test
     */
    public function test_past_constraint(): void
    {
        $validator = new DateTimeValidate('past');

        // Дата и время час назад - должно пройти
        $pastDateTime = (new DateTime('-1 hour'))->format('Y-m-d H:i:s');
        $this->assertValid($validator, $pastDateTime);

        // Дата и время через 1 час - должно провалиться
        $futureDateTime = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');
        $this->assertInvalid($validator, $futureDateTime, 'datetime.past');
    }

    /**
     * @test
     */
    public function test_no_constraint_allows_any_datetime(): void
    {
        $validator = new DateTimeValidate;

        $pastDateTime = (new DateTime('-1 hour'))->format('Y-m-d H:i:s');
        $currentDateTime = (new DateTime)->format('Y-m-d H:i:s');
        $futureDateTime = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

        $this->assertValid($validator, $pastDateTime);
        $this->assertValid($validator, $currentDateTime);
        $this->assertValid($validator, $futureDateTime);
    }

    /**
     * @test
     */
    public function test_invalid_constraint_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DateTimeValidate('invalid');
    }

    /**
     * @test
     */
    public function test_future_constraint_with_reference_datetime(): void
    {
        $referenceDateTime = '2025-06-15 14:30:00';
        $validator = new DateTimeValidate('future', $referenceDateTime);

        // Дата-время после опорной (2025-06-15 14:30:01) - должно пройти
        $this->assertValid($validator, '2025-06-15 14:30:01');
        $this->assertValid($validator, '2025-06-15 15:00:00');
        $this->assertValid($validator, '2025-06-16 10:00:00');
        $this->assertValid($validator, '2025-12-31 23:59:59');

        // Дата-время до опорной (2025-06-15 14:29:59) - должно провалиться
        $this->assertInvalid($validator, '2025-06-15 14:29:59', 'datetime.future');
        $this->assertInvalid($validator, '2025-06-15 10:00:00', 'datetime.future');
        $this->assertInvalid($validator, '2025-06-14 23:59:59', 'datetime.future');
        $this->assertInvalid($validator, '2025-01-01 00:00:00', 'datetime.future');

        // Опорная дата-время (2025-06-15 14:30:00) - должна провалиться (не строго больше)
        $this->assertInvalid($validator, '2025-06-15 14:30:00', 'datetime.future');
    }

    /**
     * @test
     */
    public function test_past_constraint_with_reference_datetime(): void
    {
        $referenceDateTime = '2025-06-15 14:30:00';
        $validator = new DateTimeValidate('past', $referenceDateTime);

        // Дата-время до опорной (2025-06-15 14:29:59) - должно пройти
        $this->assertValid($validator, '2025-06-15 14:29:59');
        $this->assertValid($validator, '2025-06-15 10:00:00');
        $this->assertValid($validator, '2025-06-14 23:59:59');
        $this->assertValid($validator, '2025-01-01 00:00:00');

        // Дата-время после опорной (2025-06-15 14:30:01) - должно провалиться
        $this->assertInvalid($validator, '2025-06-15 14:30:01', 'datetime.past');
        $this->assertInvalid($validator, '2025-06-15 15:00:00', 'datetime.past');
        $this->assertInvalid($validator, '2025-06-16 10:00:00', 'datetime.past');
        $this->assertInvalid($validator, '2025-12-31 23:59:59', 'datetime.past');

        // Опорная дата-время (2025-06-15 14:30:00) - должна провалиться (не строго меньше)
        $this->assertInvalid($validator, '2025-06-15 14:30:00', 'datetime.past');
    }

    /**
     * @test
     */
    public function test_invalid_reference_datetime_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid reference datetime format');

        $validator = new DateTimeValidate('future', 'invalid-datetime');
        $validator->validate('test', '2025-06-15 14:30:00', function (string $attribute, ?string $message = null): PotentiallyTranslatedString {
            return new PotentiallyTranslatedString($message ?? '', $this->app['translator']);
        });
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validDateTimeStringsProvider(): array
    {
        return [
            'standard datetime' => ['2025-04-16 14:30:45'],
            'midnight' => ['2025-04-16 00:00:00'],
            'end of day' => ['2025-04-16 23:59:59'],
            'with spaces around' => [' 2025-04-16 14:30:45 '],
            'leap year' => ['2024-02-29 12:00:00'],
            'first day of year' => ['2025-01-01 00:00:00'],
            'last day of year' => ['2025-12-31 23:59:59'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invalidDateTimeStringsProvider(): array
    {
        return [
            'wrong format with T' => ['2025-04-16T14:30:45', 'datetime.format'],
            'date only' => ['2025-04-16', 'datetime.format'],
            'time only' => ['14:30:45', 'datetime.format'],
            'invalid date' => ['2025-13-01 14:30:45', 'datetime.format'],
            'invalid time' => ['2025-04-16 25:30:45', 'datetime.format'],
            'letters' => ['abcd-ef-gh ij:kl:mn', 'datetime.format'],
            'incomplete' => ['2025-04-16 14:30', 'datetime.format'],
            'empty' => ['', 'datetime.format'],
        ];
    }
}
