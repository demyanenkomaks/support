<?php

namespace Maksde\Support\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Maksde\Support\Contracts\Validation\ImageValidate;
use Maksde\Support\Tests\TestCase;

class ImageValidateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Mock the config for testing purposes
        $this->app['config']->set('support.validate.file.image.extensions', ['jpg', 'jpeg', 'png', 'heic', 'webp']);
        $this->app['config']->set('support.validate.file.image.mimes', ['image/jpeg', 'image/png', 'image/heic', 'image/webp']);
        $this->app['config']->set('support.validate.file.image.max_size', 10240); // 10 MB in KB
    }

    /**
     * @test
     *
     * @dataProvider validImagesProvider
     */
    public function test_valid_images(string $extension, string $mimeType, int $size): void
    {
        $file = UploadedFile::fake()->create('image.'.$extension, $size, $mimeType);
        $this->assertValid(new ImageValidate, $file);
    }

    /**
     * @test
     *
     * @dataProvider invalidExtensionsProvider
     */
    public function test_invalid_extensions(string $extension): void
    {
        $file = UploadedFile::fake()->create('image.'.$extension, 1024, 'image/jpeg');
        $this->assertInvalid(new ImageValidate, $file, 'image.extension');
    }

    /**
     * @test
     *
     * @dataProvider invalidMimesProvider
     */
    public function test_invalid_mimes(string $mimeType): void
    {
        $file = UploadedFile::fake()->create('image.jpg', 1024, $mimeType);
        $this->assertInvalid(new ImageValidate, $file, 'image.mime');
    }

    /**
     * @test
     */
    public function test_exceeds_max_size(): void
    {
        // Create a file larger than 10 MB (10240 KB)
        $file = UploadedFile::fake()->create('image.jpg', 11000, 'image/jpeg');
        $this->assertInvalid(new ImageValidate, $file, 'image.size');
    }

    /**
     * @test
     */
    public function test_custom_parameters(): void
    {
        // Only allow PNG files up to 5 MB
        $validator = new ImageValidate(
            extensions: ['png'],
            mimes: ['image/png'],
            maxSize: 5120
        );

        // Valid PNG
        $validFile = UploadedFile::fake()->create('image.png', 4096, 'image/png');
        $this->assertValid($validator, $validFile);

        // Invalid JPG (not in allowed extensions)
        $invalidFile = UploadedFile::fake()->create('image.jpg', 4096, 'image/jpeg');
        $this->assertInvalid($validator, $invalidFile, 'image.extension');

        // Exceeds custom max size
        $largeFile = UploadedFile::fake()->create('image.png', 6000, 'image/png');
        $this->assertInvalid($validator, $largeFile, 'image.size');
    }

    /**
     * @test
     */
    public function test_non_file_value_is_skipped(): void
    {
        // If value is not an UploadedFile, validation should pass
        $this->assertValid(new ImageValidate, 'not-a-file');
        $this->assertValid(new ImageValidate, null);
        $this->assertValid(new ImageValidate, 123);
    }

    public static function validImagesProvider(): array
    {
        return [
            'jpg' => ['jpg', 'image/jpeg', 1024],
            'jpeg' => ['jpeg', 'image/jpeg', 2048],
            'png' => ['png', 'image/png', 3072],
            'heic' => ['heic', 'image/heic', 4096],
            'webp' => ['webp', 'image/webp', 5120],
            'max size' => ['jpg', 'image/jpeg', 10240],
        ];
    }

    public static function invalidExtensionsProvider(): array
    {
        return [
            'gif' => ['gif'],
            'bmp' => ['bmp'],
            'svg' => ['svg'],
            'tiff' => ['tiff'],
            'pdf' => ['pdf'],
        ];
    }

    public static function invalidMimesProvider(): array
    {
        return [
            'gif' => ['image/gif'],
            'bmp' => ['image/bmp'],
            'svg' => ['image/svg+xml'],
            'pdf' => ['application/pdf'],
            'text' => ['text/plain'],
        ];
    }
}
