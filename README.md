# Yii1 AWS S3

Amazon S3 or Amazon Simple Storage Service component for Yii1.

## Installation

Install via [Composer](https://getcomposer.org):

```shell
composer require byarashboev/yii1-aws-s3 "^1.0"
```

## Dependencies

- PHP 7.3+
- [yiisoft/yii](https://github.com/yiisoft/yii) ~1.1
- [aws/aws-sdk-php](https://github.com/aws/aws-sdk-php) ~3.337.0

## Configuration

Add the component to your `config/main.php`:

```php
'components' => [
    // ...
    's3' => [
        'class' => 'byarashboev\aws\s3\Service',
        'endpoint' => 'my-endpoint',
        'usePathStyleEndpoint' => true,
        'credentials' => [
            'key' => 'my-key',
            'secret' => 'my-secret',
        ],
        'region' => 'my-region',
        'defaultBucket' => 'my-bucket',
        'defaultAcl' => 'public-read',
    ],
    // ...
],
```

## Usage

### Basic Usage

```php
/** @var \byarashboev\aws\s3\Service $s3 */
$s3 = Yii::app()->getComponent('s3');
// or
$s3 = Yii::app()->s3;

// Usage of the command factory and additional params
// ==================================================

/** @var \Aws\ResultInterface $result */
$result = $s3->commands()->get('filename.ext')->saveAs('/path/to/local/file.ext')->execute();

$result = $s3->commands()->put('filename.ext', 'body')->withContentType('text/plain')->execute();

$result = $s3->commands()->delete('filename.ext')->execute();

$result = $s3->commands()->upload('filename.ext', '/path/to/local/file.ext')->withAcl('private')->execute();

$result = $s3->commands()->restore('filename.ext', $days = 7)->execute();

$result = $s3->commands()->list('path/')->execute();

/** @var bool $exist */
$exist = $s3->commands()->exist('filename.ext')->execute();

/** @var string $url */
$url = $s3->commands()->getUrl('filename.ext')->execute();

/** @var string $signedUrl */
$signedUrl = $s3->commands()->getPresignedUrl('filename.ext', '+2 days')->execute();

// Short syntax
// ============

/** @var \Aws\ResultInterface $result */
$result = $s3->get('filename.ext');

$result = $s3->put('filename.ext', 'body');

$result = $s3->delete('filename.ext');

$result = $s3->upload('filename.ext', '/path/to/local/file.ext');

$result = $s3->restore('filename.ext', $days = 7);

$result = $s3->list('path/');

/** @var bool $exist */
$exist = $s3->exist('filename.ext');

/** @var string $url */
$url = $s3->getUrl('filename.ext');

/** @var string $signedUrl */
$signedUrl = $s3->getPresignedUrl('filename.ext', '+2 days');

// Asynchronous execution
// ======================

/** @var \GuzzleHttp\Promise\PromiseInterface $promise */
$promise = $s3->commands()->get('filename.ext')->async()->execute();

$promise = $s3->commands()->put('filename.ext', 'body')->async()->execute();

$promise = $s3->commands()->delete('filename.ext')->async()->execute();

$promise = $s3->commands()->upload('filename.ext', 'source')->async()->execute();

$promise = $s3->commands()->list('path/')->async()->execute();
```

### Advanced Usage

```php
/** @var \byarashboev\aws\s3\Service $s3 */
$s3 = Yii::app()->s3;

/** @var \byarashboev\aws\s3\commands\GetCommand $command */
$command = $s3->create(GetCommand::class);
$command->inBucket('my-another-bucket')->byFilename('filename.ext')->saveAs('/path/to/local/file.ext');

/** @var \Aws\ResultInterface $result */
$result = $s3->execute($command);

// or async
/** @var \GuzzleHttp\Promise\PromiseInterface $promise */
$promise = $s3->execute($command->async());
```

## Using Traits

### Model Trait

Attach the Trait to your `CActiveRecord` model:

```php
/**
 * @property string|null $image
 */
class MyModel extends CActiveRecord
{
    use \byarashboev\aws\s3\traits\ModelTrait;

    public function rules()
    {
        return [
            ['image', 'length', 'max' => 255],
        ];
    }

    protected function attributePaths()
    {
        return [
            'image' => 'images/'
        ];
    }
}
```

#### Using Trait Methods

```php
$image = CUploadedFile::getInstance($model, 'image');

// Save to S3, auto-detect extension
$model->saveUploadedFile($image, 'image', 'image_thumb');

// Save with forced extension
$model->saveUploadedFile($image, 'image', 'image_thumb.png', false);

// Get the URL
$model->getFileUrl('image');

// Get presigned URL (default: +5 minutes)
$model->getFilePresignedUrl('image');

// Remove file from S3
$model->removeFile('image');
```

#### Overriding Trait Methods

##### getS3Component

```php
public function getS3Component()
{
    return Yii::app()->getComponent('my_s3_component');
}
```

##### attributePaths

```php
protected function attributePaths()
{
    return [
        'logo'  => 'logos/',
        'badge' => 'images/badges/',
    ];
}

// or with dynamic paths
protected function attributePaths()
{
    return [
        'logo'  => 'thumbnail/' . $this->id . '/logos/',
        'badge' => 'thumbnail/' . $this->id . '/images/badges/',
    ];
}
```

##### getPresignedUrlDuration

```php
protected function getPresignedUrlDuration($attribute)
{
    return '+2 Hours';
}
```

## License

MIT
