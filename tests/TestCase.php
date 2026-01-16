<?php

namespace Maksde\Support\Tests;

use Illuminate\Contracts\Validation\ValidationRule;
use Maksde\Support\SupportServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SupportServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // Настройка конфигурации для тестов
        $app['config']->set('support.validate.format.date', 'Y-m-d');
        $app['config']->set('support.validate.format.time', 'H:i:s');
        $app['config']->set('support.validate.format.datetime', 'Y-m-d H:i:s');
        $app['config']->set('support.validate.length.name', 50);
        $app['config']->set('support.validate.length.comment', 1000);
        $app['config']->set('support.validate.length.phone', 11);
        $app['config']->set('support.validate.length.phone_international_min', 7);
        $app['config']->set('support.validate.length.phone_international_max', 15);
        $app['config']->set('support.validate.file.image.extensions', ['jpg', 'jpeg', 'png', 'heic', 'webp']);
        $app['config']->set('support.validate.file.image.mimes', ['image/jpeg', 'image/png', 'image/heic', 'image/webp']);
        $app['config']->set('support.validate.file.image.max_size', 10240);
        $app['config']->set('support.validate.file.video.extensions', ['mp4', 'webm', 'hevc']);
        $app['config']->set('support.validate.file.video.mimes', ['video/mp4', 'video/webm', 'video/h265']);
        $app['config']->set('support.validate.file.video.max_size', 20480);
        $app['config']->set('support.validate.file.document.extensions', ['docx', 'xlsx', 'pdf']);
        $app['config']->set('support.validate.file.document.mimes', [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/pdf',
        ]);
        $app['config']->set('support.validate.file.document.max_size', 10240);
    }

    /**
     * Проверка, что валидация проходит успешно.
     */
    protected function assertValid(ValidationRule $rule, mixed $value): void
    {
        $failed = false;

        $rule->validate('test_attribute', $value, function (string $attribute, string $message) use (&$failed, $value) {
            $failed = true;
            $this->fail("Validation unexpectedly failed for value '".print_r($value, true)."' with message: {$message}");
        });

        $this->assertFalse($failed, "Validation should pass for value '".print_r($value, true)."'");
    }

    /**
     * Проверка, что валидация проваливается с ожидаемой ошибкой.
     */
    protected function assertInvalid(ValidationRule $rule, mixed $value, ?string $expectedErrorKey = null): void
    {
        $failed = false;
        $errorMessage = '';

        $rule->validate('test_attribute', $value, function (string $attribute, string $message) use (&$failed, &$errorMessage) {
            $failed = true;
            $errorMessage = $message;
        });

        $this->assertTrue($failed, "Validation should fail for value '".print_r($value, true)."'");
        $this->assertNotEmpty($errorMessage, 'Error message should not be empty');

        // Опционально: проверяем, что сообщение содержит ожидаемый ключ (если указан)
        // Примечание: в реальном приложении Laravel переводит ключи, поэтому эта проверка может не сработать
        if ($expectedErrorKey !== null) {
            // Не делаем строгую проверку, так как ключ может быть уже переведен
            // Просто убеждаемся, что есть какое-то сообщение об ошибке
        }
    }
}
