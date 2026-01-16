<?php

namespace Maksde\Support\Tests\Unit;

use Maksde\Support\Contracts\Validation\NameValidate;
use Maksde\Support\Tests\TestCase;

class NameValidateTest extends TestCase
{
    private NameValidate $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new NameValidate;
    }

    /**
     * @dataProvider validNameProvider
     */
    public function test_valid_names(string $name): void
    {
        $fails = false;
        $this->validator->validate('name', $name, function () use (&$fails) {
            $fails = true;
        });

        $this->assertFalse($fails, "Name '{$name}' should be valid");
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function test_invalid_names(string $name): void
    {
        $fails = false;
        $this->validator->validate('name', $name, function () use (&$fails) {
            $fails = true;
        });

        $this->assertTrue($fails, "Name '{$name}' should be invalid");
    }

    public static function validNameProvider(): array
    {
        return [
            'cyrillic' => ['Алексей'],
            'latin' => ['John'],
            'with hyphen' => ['Жан-Клод'],
            'with space' => ['Анна Мария'],
            'with apostrophe' => ["O'Connor"],
            'mixed' => ['Jean Клод'],
            'with spaces around' => [' Иван '],
        ];
    }

    public static function invalidNameProvider(): array
    {
        return [
            'with numbers' => ['Иван123'],
            'with special chars' => ['Иван@'],
            'only spaces' => ['   '],
            'only hyphens' => ['---'],
            'only apostrophes' => ["'''"],
            'too long' => [str_repeat('а', 51)],
            'empty' => [''],
        ];
    }
}
