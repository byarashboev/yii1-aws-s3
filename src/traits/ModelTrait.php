<?php

namespace byarashboev\aws\s3\traits;

use CUploadedFile;

/**
 * Trait ModelTrait for CActiveRecord / CModel
 *
 * @package byarashboev\aws\s3\traits
 */
trait ModelTrait
{
    /**
     * @return \byarashboev\aws\s3\Service
     */
    public function getS3Component()
    {
        return \Yii::app()->getComponent('s3');
    }

    /**
     * List the paths on S3 to each model file attribute.
     * Key-Value array: Key = attribute name, Value = base path in S3.
     *
     * @return array
     */
    protected function attributePaths()
    {
        return [];
    }

    /**
     * Save CUploadedFile to S3.
     *
     * @param CUploadedFile $file      Uploaded file to save
     * @param string        $attribute Attribute name where the filename will be stored
     * @param string        $fileName  Name to save the file as. If empty, uses $file->getName()
     * @param bool          $autoExtension Automatically append/replace extension. Default true
     *
     * @return string|false Uploaded filename on success, false on failure
     */
    public function saveUploadedFile(CUploadedFile $file, $attribute, $fileName = '', $autoExtension = true)
    {
        if ($this->hasErrors()) {
            return false;
        }

        if (empty($fileName)) {
            $fileName = $file->getName();
        }
        if ($autoExtension) {
            $_file = (string) pathinfo($fileName, PATHINFO_FILENAME);
            $fileName = $_file . '.' . $file->getExtensionName();
        }
        $filePath = rtrim($this->getAttributePath($attribute), '/') . '/' . $fileName;

        /** @var \Aws\ResultInterface $result */
        $result = $this->getS3Component()
            ->commands()
            ->upload(
                $filePath,
                $file->getTempName()
            )
            ->withContentType($file->getType())
            ->execute();

        if ($this->isSuccessResponseStatus($result)) {
            $this->{$attribute} = $fileName;

            return $fileName;
        }

        return false;
    }

    /**
     * Delete model file attribute from S3.
     *
     * @param string $attribute Attribute name which holds the filename
     *
     * @return bool true on success or if file doesn't exist
     */
    public function removeFile($attribute)
    {
        if (empty($this->{$attribute})) {
            return true;
        }

        $filePath = rtrim($this->getAttributePath($attribute), '/') . '/' . $this->{$attribute};
        $result = $this->getS3Component()
            ->commands()
            ->delete($filePath)
            ->execute();

        if ($this->isSuccessResponseStatus($result)) {
            $this->{$attribute} = null;

            return true;
        }

        return false;
    }

    /**
     * Retrieves the URL for a given model file attribute.
     *
     * @param string $attribute Attribute name which holds the filename
     *
     * @return string
     */
    public function getFileUrl($attribute)
    {
        if (empty($this->{$attribute})) {
            return '';
        }

        return $this->getS3Component()->getUrl(
            rtrim($this->getAttributePath($attribute), '/') . '/' . $this->{$attribute}
        );
    }

    /**
     * Retrieves the presigned URL for a given model file attribute.
     *
     * @param string $attribute Attribute name which holds the filename
     *
     * @return string
     */
    public function getFilePresignedUrl($attribute)
    {
        if (empty($this->{$attribute})) {
            return '';
        }

        return $this->getS3Component()->getPresignedUrl(
            rtrim($this->getAttributePath($attribute), '/') . '/' . $this->{$attribute},
            $this->getPresignedUrlDuration($attribute)
        );
    }

    /**
     * Retrieves the URL signature expiration.
     *
     * @param string $attribute Attribute name
     *
     * @return string
     */
    public function getPresignedUrlDuration($attribute)
    {
        return '+5 minutes';
    }

    /**
     * Retrieves the base path on S3 for a given attribute.
     *
     * @param string $attribute
     *
     * @return string
     */
    public function getAttributePath($attribute)
    {
        $paths = $this->attributePaths();
        if (array_key_exists($attribute, $paths)) {
            return $paths[$attribute];
        }

        return '';
    }

    /**
     * Check for valid status code from the S3 response (2xx).
     *
     * @param \Aws\ResultInterface $response
     *
     * @return bool
     */
    public function isSuccessResponseStatus($response)
    {
        return !empty($response->get('@metadata')['statusCode']) &&
            $response->get('@metadata')['statusCode'] >= 200 &&
            $response->get('@metadata')['statusCode'] < 300;
    }
}
