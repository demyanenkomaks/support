<?php

declare(strict_types=1);

namespace Maksde\Support\Tests\Unit;

use Illuminate\Translation\PotentiallyTranslatedString;
use Maksde\Support\Contracts\Validation\PhoneValidate;
use Maksde\Support\Tests\TestCase;

class PhoneValidateTest extends TestCase
{
    private PhoneValidate $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new PhoneValidate;
    }

    /**
     * @dataProvider validPhoneProvider
     */
    public function test_valid_phones(string $phone): void
    {
        $fails = false;
        $this->validator->validate('phone', $phone, function (string $attribute, ?string $message = null) use (&$fails): PotentiallyTranslatedString {
            $fails = true;

            return new PotentiallyTranslatedString($message ?? '', $this->app['translator']);
        });

        $this->assertFalse($fails, "Phone '{$phone}' should be valid");
    }

    /**
     * @dataProvider invalidPhoneProvider
     */
    public function test_invalid_phones(string $phone): void
    {
        $fails = false;
        $this->validator->validate('phone', $phone, function (string $attribute, ?string $message = null) use (&$fails): PotentiallyTranslatedString {
            $fails = true;

            return new PotentiallyTranslatedString($message ?? '', $this->app['translator']);
        });

        $this->assertTrue($fails, "Phone '{$phone}' should be invalid");
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validPhoneProvider(): array
    {
        return [
            'standard format' => ['+71234567890'],
            'with spaces around' => [' +71234567890 '],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidPhoneProvider(): array
    {
        return [
            'without plus' => ['71234567890'],
            'without 7' => ['+81234567890'],
            'too short' => ['+7123456789'],
            'too long' => ['+712345678901'],
            'with spaces inside' => ['+7 123 456 78 90'],
            'with dashes' => ['+7-123-456-78-90'],
            'with brackets' => ['+7(123)456-78-90'],
            'letters' => ['+7123456789a'],
        ];
    }
}
