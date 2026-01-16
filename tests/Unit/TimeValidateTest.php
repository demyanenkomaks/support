<?php

namespace Maksde\Support\Tests\Unit;

use DateTime;
use Maksde\Support\Contracts\Validation\TimeValidate;
use Maksde\Support\Tests\TestCase;

class TimeValidateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Mock the config for testing purposes
        $this->app['config']->set('support.validate.format.time', 'H:i:s');
    }

    /**
     * @test
     *
     * @dataProvider validTimesProvider
     */
    public function test_valid_times(string $time): void
    {
        $this->assertValid(new TimeValidate, $time);
    }

    /**
     * @test
     *
     * @dataProvider invalidTimesProvider
     */
    public function test_invalid_times(string $time, string $expectedErrorKey): void
    {
        $this->assertInvalid(new TimeValidate, $time, $expectedErrorKey);
    }

    /**
     * @test
     */
    public function test_future_constraint(): void
    {
        $validator = new TimeValidate('future');

        // Время через 1 час - должно пройти
        $futureTime = (new DateTime('+1 hour'))->format('H:i:s');
        $this->assertValid($validator, $futureTime);

        // Время час назад - должно провалиться
        $pastTime = (new DateTime('-1 hour'))->format('H:i:s');
        $this->assertInvalid($validator, $pastTime, 'time.future');
    }

    /**
     * @test
     */
    public function test_past_constraint(): void
    {
        $validator = new TimeValidate('past');

        // Время час назад - должно пройти
        $pastTime = (new DateTime('-1 hour'))->format('H:i:s');
        $this->assertValid($validator, $pastTime);

        // Время через 1 час - должно провалиться
        $futureTime = (new DateTime('+1 hour'))->format('H:i:s');
        $this->assertInvalid($validator, $futureTime, 'time.past');
    }

    /**
     * @test
     */
    public function test_no_constraint_allows_any_time(): void
    {
        $validator = new TimeValidate;

        $pastTime = (new DateTime('-1 hour'))->format('H:i:s');
        $currentTime = (new DateTime)->format('H:i:s');
        $futureTime = (new DateTime('+1 hour'))->format('H:i:s');

        $this->assertValid($validator, $pastTime);
        $this->assertValid($validator, $currentTime);
        $this->assertValid($validator, $futureTime);
    }

    /**
     * @test
     */
    public function test_invalid_constraint_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TimeValidate('invalid');
    }

    /**
     * @test
     */
    public function test_future_constraint_with_reference_time(): void
    {
        $referenceTime = '14:30:00';
        $validator = new TimeValidate('future', $referenceTime);

        // Время после опорного (14:30:01) - должно пройти
        $this->assertValid($validator, '14:30:01');
        $this->assertValid($validator, '15:00:00');
        $this->assertValid($validator, '23:59:59');

        // Время до опорного (14:29:59) - должно провалиться
        $this->assertInvalid($validator, '14:29:59', 'time.future');
        $this->assertInvalid($validator, '10:00:00', 'time.future');
        $this->assertInvalid($validator, '00:00:00', 'time.future');

        // Опорное время (14:30:00) - должно провалиться (не строго больше)
        $this->assertInvalid($validator, '14:30:00', 'time.future');
    }

    /**
     * @test
     */
    public function test_past_constraint_with_reference_time(): void
    {
        $referenceTime = '14:30:00';
        $validator = new TimeValidate('past', $referenceTime);

        // Время до опорного (14:29:59) - должно пройти
        $this->assertValid($validator, '14:29:59');
        $this->assertValid($validator, '10:00:00');
        $this->assertValid($validator, '00:00:00');

        // Время после опорного (14:30:01) - должно провалиться
        $this->assertInvalid($validator, '14:30:01', 'time.past');
        $this->assertInvalid($validator, '15:00:00', 'time.past');
        $this->assertInvalid($validator, '23:59:59', 'time.past');

        // Опорное время (14:30:00) - должно провалиться (не строго меньше)
        $this->assertInvalid($validator, '14:30:00', 'time.past');
    }

    /**
     * @test
     */
    public function test_invalid_reference_time_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid reference time format');

        $validator = new TimeValidate('future', 'invalid-time');
        $validator->validate('test', '14:30:00', fn () => null);
    }

    public static function validTimesProvider(): array
    {
        return [
            'midnight' => ['00:00:00'],
            'noon' => ['12:00:00'],
            'end of day' => ['23:59:59'],
            'with spaces around' => [' 14:30:45 '],
            'morning' => ['08:15:30'],
        ];
    }

    public static function invalidTimesProvider(): array
    {
        return [
            'without seconds' => ['14:30', 'time.format'],
            'invalid hour' => ['24:00:00', 'time.format'],
            'invalid minute' => ['14:60:00', 'time.format'],
            'invalid second' => ['14:30:60', 'time.format'],
            'letters' => ['ab:cd:ef', 'time.format'],
            'incomplete' => ['14:30:', 'time.format'],
            'empty' => ['', 'time.format'],
        ];
    }
}
