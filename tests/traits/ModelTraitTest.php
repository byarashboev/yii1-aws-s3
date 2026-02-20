<?php

namespace byarashboev\aws\s3\tests\traits;

use Aws\ResultInterface;
use PHPUnit\Framework\TestCase;

/**
 * Minimal S3 service mock for ModelTrait tests.
 */
class FakeS3Service
{
    private string $urlPrefix;

    public function __construct(string $urlPrefix = 'https://cdn.example.com/')
    {
        $this->urlPrefix = $urlPrefix;
    }

    public function getUrl(string $path): string
    {
        return $this->urlPrefix . $path;
    }

    public function getPresignedUrl(string $path, string $duration): string
    {
        return $this->urlPrefix . $path . '?expires=' . urlencode($duration);
    }
}

/**
 * A concrete model class that uses ModelTrait for testing.
 */
class TestModel
{
    use \byarashboev\aws\s3\traits\ModelTrait;

    public bool $hasErrorsResult = false;
    public ?string $image = null;
    public $s3Mock = null;

    public function hasErrors($attribute = null): bool
    {
        return $this->hasErrorsResult;
    }

    protected function attributePaths(): array
    {
        return ['image' => 'images/'];
    }

    public function getS3Component()
    {
        return $this->s3Mock;
    }
}

class ModelTraitTest extends TestCase
{
    private TestModel $model;

    protected function setUp(): void
    {
        $this->model = new TestModel();
        $this->model->s3Mock = new FakeS3Service();
    }

    public function testGetAttributePathReturnsPathForKnownAttribute(): void
    {
        $path = $this->model->getAttributePath('image');

        $this->assertSame('images/', $path);
    }

    public function testGetAttributePathReturnsEmptyStringForUnknownAttribute(): void
    {
        $path = $this->model->getAttributePath('unknown');

        $this->assertSame('', $path);
    }

    public function testGetFileUrlCallsS3GetUrlWithCorrectPath(): void
    {
        $this->model->image = 'filename.jpg';

        $url = $this->model->getFileUrl('image');

        // Path should be 'images/filename.jpg' (no double slash)
        $this->assertSame('https://cdn.example.com/images/filename.jpg', $url);
    }

    public function testGetFileUrlDoesNotProduceDoubleSlash(): void
    {
        $this->model->image = 'filename.jpg';

        $url = $this->model->getFileUrl('image');

        $this->assertStringNotContainsString('//', str_replace('https://', '', $url));
    }

    public function testGetFileUrlReturnsEmptyStringWhenAttributeIsEmpty(): void
    {
        $this->model->image = null;

        $url = $this->model->getFileUrl('image');

        $this->assertSame('', $url);
    }

    public function testGetFileUrlReturnsEmptyStringWhenAttributeIsEmptyString(): void
    {
        $this->model->image = '';

        $url = $this->model->getFileUrl('image');

        $this->assertSame('', $url);
    }

    public function testGetFilePresignedUrlCallsCorrectPath(): void
    {
        $this->model->image = 'photo.png';

        $url = $this->model->getFilePresignedUrl('image');

        $this->assertStringContainsString('images/photo.png', $url);
    }

    public function testGetFilePresignedUrlReturnsEmptyStringWhenAttributeEmpty(): void
    {
        $this->model->image = null;

        $url = $this->model->getFilePresignedUrl('image');

        $this->assertSame('', $url);
    }

    public function testGetPresignedUrlDurationReturnsFiveMinutesByDefault(): void
    {
        $duration = $this->model->getPresignedUrlDuration('image');

        $this->assertSame('+5 minutes', $duration);
    }

    public function testGetPresignedUrlDurationReturnsSameForAnyAttribute(): void
    {
        $this->assertSame('+5 minutes', $this->model->getPresignedUrlDuration('image'));
        $this->assertSame('+5 minutes', $this->model->getPresignedUrlDuration('avatar'));
        $this->assertSame('+5 minutes', $this->model->getPresignedUrlDuration('unknown'));
    }

    public function testIsSuccessResponseStatusReturnsTrueFor200(): void
    {
        $response = $this->makeResultWithStatus(200);

        $this->assertTrue($this->model->isSuccessResponseStatus($response));
    }

    public function testIsSuccessResponseStatusReturnsTrueFor201(): void
    {
        $response = $this->makeResultWithStatus(201);

        $this->assertTrue($this->model->isSuccessResponseStatus($response));
    }

    public function testIsSuccessResponseStatusReturnsTrueFor204(): void
    {
        $response = $this->makeResultWithStatus(204);

        $this->assertTrue($this->model->isSuccessResponseStatus($response));
    }

    public function testIsSuccessResponseStatusReturnsFalseFor404(): void
    {
        $response = $this->makeResultWithStatus(404);

        $this->assertFalse($this->model->isSuccessResponseStatus($response));
    }

    public function testIsSuccessResponseStatusReturnsFalseFor500(): void
    {
        $response = $this->makeResultWithStatus(500);

        $this->assertFalse($this->model->isSuccessResponseStatus($response));
    }

    public function testIsSuccessResponseStatusReturnsFalseFor300(): void
    {
        $response = $this->makeResultWithStatus(300);

        $this->assertFalse($this->model->isSuccessResponseStatus($response));
    }

    public function testRemoveFileReturnsTrueWhenAttributeIsEmpty(): void
    {
        $this->model->image = null;

        $result = $this->model->removeFile('image');

        $this->assertTrue($result);
    }

    public function testRemoveFileReturnsTrueWhenAttributeIsEmptyString(): void
    {
        $this->model->image = '';

        $result = $this->model->removeFile('image');

        $this->assertTrue($result);
    }

    /**
     * Helper to create a mock ResultInterface with a given HTTP status code.
     */
    private function makeResultWithStatus(int $statusCode): ResultInterface
    {
        $result = $this->createMock(ResultInterface::class);
        $result->method('get')
            ->with('@metadata')
            ->willReturn(['statusCode' => $statusCode]);

        return $result;
    }
}
