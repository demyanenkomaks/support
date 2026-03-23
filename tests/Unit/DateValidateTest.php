<?php

declare(strict_types=1);

namespace Maksde\Support\Tests\Unit;

use DateTime;
use Illuminate\Translation\PotentiallyTranslatedString;
use Maksde\Support\Contracts\Validation\DateValidate;
use Maksde\Support\Tests\TestCase;

class DateValidateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Mock the config for testing purposes
        $this->app['config']->set('support.validate.format.date', 'Y-m-d');
    }

    /**
     * @test
     *
     * @dataProvider validDatesProvider
     */
    public function test_valid_dates(string $date): void
    {
        $this->assertValid(new DateValidate, $date);
    }

    /**
     * @test
     *
     * @dataProvider invalidDatesProvider
     */
    public function test_invalid_dates(string $date, string $expectedErrorKey): void
    {
        $this->assertInvalid(new DateValidate, $date, $expectedErrorKey);
    }

    /**
     * @test
     */
    public function test_future_constraint(): void
    {
        $validator = new DateValidate('future');

        // Завтрашняя дата - должна пройти
        $tomorrow = (new DateTime('tomorrow'))->format('Y-m-d');
        $this->assertValid($validator, $tomorrow);

        // Вчерашняя дата - должна провалиться
        $yesterday = (new DateTime('yesterday'))->format('Y-m-d');
        $this->assertInvalid($validator, $yesterday, 'date.future');

        // Сегодняшняя дата - должна провалиться (не строго больше)
        $today = (new DateTime('today'))->format('Y-m-d');
        $this->assertInvalid($validator, $today, 'date.future');
    }

    /**
     * @test
     */
    public function test_past_constraint(): void
    {
        $validator = new DateValidate('past');

        // Вчерашняя дата - должна пройти
        $yesterday = (new DateTime('yesterday'))->format('Y-m-d');
        $this->assertValid($validator, $yesterday);

        // Завтрашняя дата - должна провалиться
        $tomorrow = (new DateTime('tomorrow'))->format('Y-m-d');
        $this->assertInvalid($validator, $tomorrow, 'date.past');

        // Сегодняшняя дата - должна провалиться (не строго меньше)
        $today = (new DateTime('today'))->format('Y-m-d');
        $this->assertInvalid($validator, $today, 'date.past');
    }

    /**
     * @test
     */
    public function test_no_constraint_allows_any_date(): void
    {
        $validator = new DateValidate;

        $yesterday = (new DateTime('yesterday'))->format('Y-m-d');
        $today = (new DateTime('today'))->format('Y-m-d');
        $tomorrow = (new DateTime('tomorrow'))->format('Y-m-d');

        $this->assertValid($validator, $yesterday);
        $this->assertValid($validator, $today);
        $this->assertValid($validator, $tomorrow);
    }

    /**
     * @test
     */
    public function test_invalid_constraint_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DateValidate('invalid');
    }

    /**
     * @test
     */
    public function test_future_constraint_with_reference_date(): void
    {
        $referenceDate = '2025-06-15';
        $validator = new DateValidate('future', $referenceDate);

        // Дата после опорной (2025-06-16) - должна пройти
        $this->assertValid($validator, '2025-06-16');
        $this->assertValid($validator, '2025-12-31');

        // Дата до опорной (2025-06-14) - должна провалиться
        $this->assertInvalid($validator, '2025-06-14', 'date.future');
        $this->assertInvalid($validator, '2025-01-01', 'date.future');

        // Опорная дата (2025-06-15) - должна провалиться (не строго больше)
        $this->assertInvalid($validator, '2025-06-15', 'date.future');
    }

    /**
     * @test
     */
    public function test_past_constraint_with_reference_date(): void
    {
        $referenceDate = '2025-06-15';
        $validator = new DateValidate('past', $referenceDate);

        // Дата до опорной (2025-06-14) - должна пройти
        $this->assertValid($validator, '2025-06-14');
        $this->assertValid($validator, '2025-01-01');

        // Дата после опорной (2025-06-16) - должна провалиться
        $this->assertInvalid($validator, '2025-06-16', 'date.past');
        $this->assertInvalid($validator, '2025-12-31', 'date.past');

        // Опорная дата (2025-06-15) - должна провалиться (не строго меньше)
        $this->assertInvalid($validator, '2025-06-15', 'date.past');
    }

    /**
     * @test
     */
    public function test_invalid_reference_date_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid reference date format');

        $validator = new DateValidate('future', 'invalid-date');
        $validator->validate('test', '2025-06-15', function (string $attribute, ?string $message = null): PotentiallyTranslatedString {
            return new PotentiallyTranslatedString($message ?? '', $this->app['translator']);
        });
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validDatesProvider(): array
    {
        return [
            'standard date' => ['2025-04-16'],
            'leap year' => ['2024-02-29'],
            'with spaces around' => [' 2025-04-16 '],
            'first day of year' => ['2025-01-01'],
            'last day of year' => ['2025-12-31'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function invalidDatesProvider(): array
    {
        return [
            'wrong format d.m.Y' => ['16.04.2025', 'date.format'],
            'wrong format m/d/Y' => ['04/16/2025', 'date.format'],
            'invalid date' => ['2025-13-01', 'date.format'],
            'invalid day' => ['2025-04-32', 'date.format'],
            'non-leap year' => ['2025-02-29', 'date.format'],
            'letters' => ['abcd-ef-gh', 'date.format'],
            'incomplete' => ['2025-04', 'date.format'],
            'empty' => ['', 'date.format'],
        ];
    }
}
