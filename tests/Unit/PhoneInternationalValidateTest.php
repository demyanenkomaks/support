<?php

namespace Maksde\Support\Tests\Unit;

use Maksde\Support\Contracts\Validation\PhoneInternationalValidate;
use Maksde\Support\Tests\TestCase;

class PhoneInternationalValidateTest extends TestCase
{
    private PhoneInternationalValidate $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new PhoneInternationalValidate;
    }

    /**
     * @dataProvider validPhoneProvider
     */
    public function test_valid_international_phones(string $phone): void
    {
        $fails = false;
        $this->validator->validate('phone', $phone, function () use (&$fails) {
            $fails = true;
        });

        $this->assertFalse($fails, "Phone '{$phone}' should be valid");
    }

    /**
     * @dataProvider invalidPhoneProvider
     */
    public function test_invalid_international_phones(string $phone): void
    {
        $fails = false;
        $this->validator->validate('phone', $phone, function () use (&$fails) {
            $fails = true;
        });

        $this->assertTrue($fails, "Phone '{$phone}' should be invalid");
    }

    public static function validPhoneProvider(): array
    {
        return [
            'minimum length' => ['+1234567'],
            'maximum length' => ['+123456789012345'],
            'usa format' => ['+12025551234'],
            'uk format' => ['+442071234567'],
            'russia format' => ['+71234567890'],
            'with spaces around' => [' +12025551234 '],
        ];
    }

    public static function invalidPhoneProvider(): array
    {
        return [
            'without plus' => ['1234567890'],
            'too short' => ['+123456'],
            'too long' => ['+1234567890123456'],
            'with spaces inside' => ['+1 202 555 1234'],
            'with dashes' => ['+1-202-555-1234'],
            'with brackets' => ['+1(202)555-1234'],
            'letters' => ['+1202555123a'],
        ];
    }
}
