<?php

declare(strict_types=1);

namespace MyVendor\MyProject\InputQuery\Admin;

use Koriym\FileUpload\ErrorFileUpload;
use Koriym\FileUpload\FileUpload;
use Ray\InputQuery\Attribute\Input;
use Ray\InputQuery\Attribute\InputFile;

readonly class UploadDemoInput
{
    /** @psalm-suppress UndefinedAttributeClass */
    public function __construct(
        #[InputFile(
            maxSize: 1024 * 1024, // 1MB
            allowedTypes: ['image/jpeg', 'image/png', 'image/svg+xml'],
            allowedExtensions: ['jpg', 'jpeg', 'png', 'svg'],
            required: false, // ファイルアップロードをオプショナルにする
        )]
        public FileUpload|ErrorFileUpload|null $file = null,
        #[Input]
        public string|null $submit = null,
    ) {
    }
}
