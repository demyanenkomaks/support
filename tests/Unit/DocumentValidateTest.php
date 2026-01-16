<?php

namespace Maksde\Support\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Maksde\Support\Contracts\Validation\DocumentValidate;
use Maksde\Support\Tests\TestCase;

class DocumentValidateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Mock the config for testing purposes
        $this->app['config']->set('support.validate.file.document.extensions', ['docx', 'xlsx', 'pdf']);
        $this->app['config']->set('support.validate.file.document.mimes', [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/pdf',
        ]);
        $this->app['config']->set('support.validate.file.document.max_size', 10240); // 10 MB in KB
    }

    /**
     * @test
     *
     * @dataProvider validDocumentsProvider
     */
    public function test_valid_documents(string $extension, string $mimeType, int $size): void
    {
        $file = UploadedFile::fake()->create('document.'.$extension, $size, $mimeType);
        $this->assertValid(new DocumentValidate, $file);
    }

    /**
     * @test
     *
     * @dataProvider invalidExtensionsProvider
     */
    public function test_invalid_extensions(string $extension): void
    {
        $file = UploadedFile::fake()->create('document.'.$extension, 1024, 'application/pdf');
        $this->assertInvalid(new DocumentValidate, $file, 'document.extension');
    }

    /**
     * @test
     *
     * @dataProvider invalidMimesProvider
     */
    public function test_invalid_mimes(string $mimeType): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 1024, $mimeType);
        $this->assertInvalid(new DocumentValidate, $file, 'document.mime');
    }

    /**
     * @test
     */
    public function test_exceeds_max_size(): void
    {
        // Create a file larger than 10 MB (10240 KB)
        $file = UploadedFile::fake()->create('document.pdf', 11000, 'application/pdf');
        $this->assertInvalid(new DocumentValidate, $file, 'document.size');
    }

    /**
     * @test
     */
    public function test_custom_parameters(): void
    {
        // Only allow PDF files up to 20 MB
        $validator = new DocumentValidate(
            extensions: ['pdf'],
            mimes: ['application/pdf'],
            maxSize: 20480
        );

        // Valid PDF
        $validFile = UploadedFile::fake()->create('document.pdf', 15360, 'application/pdf');
        $this->assertValid($validator, $validFile);

        // Invalid DOCX (not in allowed extensions)
        $invalidFile = UploadedFile::fake()->create('document.docx', 5120, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $this->assertInvalid($validator, $invalidFile, 'document.extension');

        // Exceeds custom max size
        $largeFile = UploadedFile::fake()->create('document.pdf', 21000, 'application/pdf');
        $this->assertInvalid($validator, $largeFile, 'document.size');
    }

    /**
     * @test
     */
    public function test_non_file_value_is_skipped(): void
    {
        // If value is not an UploadedFile, validation should pass
        $this->assertValid(new DocumentValidate, 'not-a-file');
        $this->assertValid(new DocumentValidate, null);
        $this->assertValid(new DocumentValidate, 123);
    }

    public static function validDocumentsProvider(): array
    {
        return [
            'docx' => ['docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 5120],
            'xlsx' => ['xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 7168],
            'pdf' => ['pdf', 'application/pdf', 10240],
            'max size' => ['pdf', 'application/pdf', 10240],
        ];
    }

    public static function invalidExtensionsProvider(): array
    {
        return [
            'doc' => ['doc'],
            'xls' => ['xls'],
            'txt' => ['txt'],
            'rtf' => ['rtf'],
            'odt' => ['odt'],
        ];
    }

    public static function invalidMimesProvider(): array
    {
        return [
            'text' => ['text/plain'],
            'image' => ['image/jpeg'],
            'video' => ['video/mp4'],
            'zip' => ['application/zip'],
            'json' => ['application/json'],
        ];
    }
}
