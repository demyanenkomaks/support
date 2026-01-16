<?php

namespace Maksde\Support\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Maksde\Support\Contracts\Validation\VideoValidate;
use Maksde\Support\Tests\TestCase;

class VideoValidateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Mock the config for testing purposes
        $this->app['config']->set('support.validate.file.video.extensions', ['mp4', 'webm', 'hevc']);
        $this->app['config']->set('support.validate.file.video.mimes', ['video/mp4', 'video/webm', 'video/h265']);
        $this->app['config']->set('support.validate.file.video.max_size', 20480); // 20 MB in KB
    }

    /**
     * @test
     *
     * @dataProvider validVideosProvider
     */
    public function test_valid_videos(string $extension, string $mimeType, int $size): void
    {
        $file = UploadedFile::fake()->create('video.'.$extension, $size, $mimeType);
        $this->assertValid(new VideoValidate, $file);
    }

    /**
     * @test
     *
     * @dataProvider invalidExtensionsProvider
     */
    public function test_invalid_extensions(string $extension): void
    {
        $file = UploadedFile::fake()->create('video.'.$extension, 1024, 'video/mp4');
        $this->assertInvalid(new VideoValidate, $file, 'video.extension');
    }

    /**
     * @test
     *
     * @dataProvider invalidMimesProvider
     */
    public function test_invalid_mimes(string $mimeType): void
    {
        $file = UploadedFile::fake()->create('video.mp4', 1024, $mimeType);
        $this->assertInvalid(new VideoValidate, $file, 'video.mime');
    }

    /**
     * @test
     */
    public function test_exceeds_max_size(): void
    {
        // Create a file larger than 20 MB (20480 KB)
        $file = UploadedFile::fake()->create('video.mp4', 21000, 'video/mp4');
        $this->assertInvalid(new VideoValidate, $file, 'video.size');
    }

    /**
     * @test
     */
    public function test_custom_parameters(): void
    {
        // Only allow MP4 files up to 50 MB
        $validator = new VideoValidate(
            extensions: ['mp4'],
            mimes: ['video/mp4'],
            maxSize: 51200
        );

        // Valid MP4
        $validFile = UploadedFile::fake()->create('video.mp4', 40960, 'video/mp4');
        $this->assertValid($validator, $validFile);

        // Invalid WEBM (not in allowed extensions)
        $invalidFile = UploadedFile::fake()->create('video.webm', 10240, 'video/webm');
        $this->assertInvalid($validator, $invalidFile, 'video.extension');

        // Exceeds custom max size
        $largeFile = UploadedFile::fake()->create('video.mp4', 52000, 'video/mp4');
        $this->assertInvalid($validator, $largeFile, 'video.size');
    }

    /**
     * @test
     */
    public function test_non_file_value_is_skipped(): void
    {
        // If value is not an UploadedFile, validation should pass
        $this->assertValid(new VideoValidate, 'not-a-file');
        $this->assertValid(new VideoValidate, null);
        $this->assertValid(new VideoValidate, 123);
    }

    public static function validVideosProvider(): array
    {
        return [
            'mp4' => ['mp4', 'video/mp4', 10240],
            'webm' => ['webm', 'video/webm', 15360],
            'hevc' => ['hevc', 'video/h265', 20480],
            'max size' => ['mp4', 'video/mp4', 20480],
        ];
    }

    public static function invalidExtensionsProvider(): array
    {
        return [
            'avi' => ['avi'],
            'mov' => ['mov'],
            'flv' => ['flv'],
            'mkv' => ['mkv'],
            'wmv' => ['wmv'],
        ];
    }

    public static function invalidMimesProvider(): array
    {
        return [
            'avi' => ['video/x-msvideo'],
            'quicktime' => ['video/quicktime'],
            'image' => ['image/jpeg'],
            'pdf' => ['application/pdf'],
            'text' => ['text/plain'],
        ];
    }
}
