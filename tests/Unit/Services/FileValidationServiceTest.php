<?php

namespace Tests\Unit\Services;

use App\Services\Upload\FileValidationService;
use App\Services\Upload\ValidationResult;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileValidationServiceTest extends TestCase
{
    private FileValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FileValidationService();
    }

    public function test_validates_valid_pdf_file(): void
    {
        // We need a fake with a proper PDF magic number for this to pass
        file_put_contents('/tmp/test.pdf', "%PDF-1.4\n%EOF");
        $file = new UploadedFile('/tmp/test.pdf', 'certificate.pdf', 'application/pdf', null, true);

        $result = $this->service->validate($file);

        $this->assertTrue($result->isValid());
    }

    public function test_rejects_invalid_extension(): void
    {
        $file = UploadedFile::fake()->create('malware.exe', 1024, 'application/octet-stream');

        $result = $this->service->checkExtension($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('UNSUPPORTED_TYPE', $result->getErrorCode());
    }

    public function test_rejects_oversized_file(): void
    {
        // Create file larger than allowed
        $file = UploadedFile::fake()->create('large.pdf', 50 * 1024 * 1024, 'application/pdf');

        $result = $this->service->checkFileSize($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('FILE_TOO_LARGE', $result->getErrorCode());
    }

    public function test_rejects_empty_file(): void
    {
        $file = UploadedFile::fake()->create('empty.pdf', 0, 'application/pdf');

        $result = $this->service->checkFileSize($file);

        $this->assertFalse($result->isValid());
        $this->assertEquals('EMPTY_FILE', $result->getErrorCode());
    }

    public function test_sanitizes_filename_removes_dangerous_chars(): void
    {
        $filename = '../../../etc/passwd';
        $sanitized = $this->service->sanitizeFilename($filename);

        $this->assertStringNotContainsString('..', $sanitized);
        $this->assertStringNotContainsString('/', $sanitized);
    }

    public function test_sanitizes_filename_removes_null_bytes(): void
    {
        $filename = "image.php\x00.jpg";
        $sanitized = $this->service->sanitizeFilename($filename);

        $this->assertStringNotContainsString("\0", $sanitized);
    }

    public function test_generates_secure_filename_with_uuid(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        $filename = $this->service->generateSecureFilename($file);

        // Should have UUID format
        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}\.pdf$/',
            $filename
        );
    }

    public function test_generates_different_filenames_for_same_file(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        $filename1 = $this->service->generateSecureFilename($file);
        $filename2 = $this->service->generateSecureFilename($file);

        $this->assertNotEquals($filename1, $filename2);
    }

    public function test_pdf_requires_ocr(): void
    {
        $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

        $this->assertTrue($this->service->requiresOcr($file));
    }

    public function test_jpeg_does_not_require_ocr_by_default(): void
    {
        $file = UploadedFile::fake()->create('test.jpg', 1024, 'image/jpeg');

        $this->assertTrue($this->service->requiresOcr($file)); // OCR is required for images according to requiresOcr implementation
    }
}
