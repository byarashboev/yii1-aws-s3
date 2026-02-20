<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Yii1 stubs for unit tests (no full app boot needed)
if (!class_exists('CException')) {
    class CException extends RuntimeException {}
}

if (!class_exists('CApplicationComponent')) {
    class CApplicationComponent
    {
        public function init(): void {}
    }
}

if (!class_exists('Yii')) {
    class Yii
    {
        public static function app() { return null; }
    }
}

if (!class_exists('CUploadedFile')) {
    class CUploadedFile
    {
        private string $name;
        private string $tempName;
        private string $type;
        private string $extensionName;

        public function __construct(string $name, string $tempName = '', string $type = '', string $ext = '')
        {
            $this->name = $name;
            $this->tempName = $tempName;
            $this->type = $type;
            $this->extensionName = $ext ?: pathinfo($name, PATHINFO_EXTENSION);
        }

        public function getName(): string { return $this->name; }
        public function getTempName(): string { return $this->tempName; }
        public function getType(): string { return $this->type; }
        public function getExtensionName(): string { return $this->extensionName; }
    }
}
