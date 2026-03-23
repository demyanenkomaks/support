<?php

declare(strict_types=1);

namespace Maksde\Support\Tests\Unit;

use Maksde\Support\Contracts\Validation\QuantityValidate;
use Maksde\Support\Tests\TestCase;

class QuantityValidateTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validQuantitiesProvider
     */
    public function test_valid_quantities(int|string $quantity): void
    {
        $this->assertValid(new QuantityValidate, $quantity);
    }

    /**
     * @test
     *
     * @dataProvider invalidQuantitiesProvider
     */
    public function test_invalid_quantities(int|string|float $quantity, string $expectedErrorKey): void
    {
        $this->assertInvalid(new QuantityValidate, $quantity, $expectedErrorKey);
    }

    /**
     * @test
     */
    public function test_custom_min_constraint(): void
    {
        $validator = new QuantityValidate(min: 10);

        // Значения >= 10 должны пройти
        $this->assertValid($validator, 10);
        $this->assertValid($validator, 15);
        $this->assertValid($validator, 100);

        // Значения < 10 должны провалиться
        $this->assertInvalid($validator, 0, 'quantity.min');
        $this->assertInvalid($validator, 5, 'quantity.min');
        $this->assertInvalid($validator, 9, 'quantity.min');
    }

    /**
     * @test
     */
    public function test_custom_max_constraint(): void
    {
        $validator = new QuantityValidate(max: 100);

        // Значения <= 100 должны пройти
        $this->assertValid($validator, 0);
        $this->assertValid($validator, 50);
        $this->assertValid($validator, 100);

        // Значения > 100 должны провалиться
        $this->assertInvalid($validator, 101, 'quantity.max');
        $this->assertInvalid($validator, 200, 'quantity.max');
        $this->assertInvalid($validator, 999999, 'quantity.max');
    }

    /**
     * @test
     */
    public function test_custom_min_and_max_constraints(): void
    {
        $validator = new QuantityValidate(min: 10, max: 100);

        // Значения в диапазоне [10, 100] должны пройти
        $this->assertValid($validator, 10);
        $this->assertValid($validator, 50);
        $this->assertValid($validator, 100);

        // Значения < 10 должны провалиться
        $this->assertInvalid($validator, 0, 'quantity.min');
        $this->assertInvalid($validator, 9, 'quantity.min');

        // Значения > 100 должны провалиться
        $this->assertInvalid($validator, 101, 'quantity.max');
        $this->assertInvalid($validator, 999, 'quantity.max');
    }

    /**
     * @test
     */
    public function test_no_min_constraint(): void
    {
        $validator = new QuantityValidate(min: null, max: 100);

        // Отрицательные значения должны пройти (нет минимального ограничения)
        $this->assertValid($validator, -10);
        $this->assertValid($validator, -1);
        $this->assertValid($validator, 0);
    }

    /**
     * @test
     */
    public function test_invalid_min_max_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new QuantityValidate(min: 100, max: 10);
    }

    /**
     * @return array<string, array{0: int|string}>
     */
    public static function validQuantitiesProvider(): array
    {
        return [
            'zero' => [0],
            'positive integer' => [10],
            'string zero' => ['0'],
            'string positive' => ['100'],
            'large number' => [999999],
        ];
    }

    /**
     * @return array<string, array{0: int|string|float, 1: string}>
     */
    public static function invalidQuantitiesProvider(): array
    {
        return [
            'negative' => [-1, 'quantity.min'],
            'negative string' => ['-5', 'quantity.forbidden_characters'],
            'float' => [10.5, 'quantity.not_integer'],
            'float string' => ['10.5', 'quantity.not_integer'],
            'with e' => ['1e5', 'quantity.forbidden_characters'],
            'with E' => ['1E5', 'quantity.forbidden_characters'],
            'with plus' => ['+10', 'quantity.forbidden_characters'],
            'with minus' => ['-10', 'quantity.forbidden_characters'],
            'letters' => ['abc', 'quantity.not_integer'],
            'mixed' => ['10abc', 'quantity.not_integer'],
        ];
    }
}
