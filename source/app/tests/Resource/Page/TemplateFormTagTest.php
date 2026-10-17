<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Resource\Page;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function assert;
use function dirname;
use function file_get_contents;
use function sprintf;
use function str_contains;

/** Forms are opened with the form() helper, which carries the CSRF token; a literal <form> tag would bypass it. */
final class TemplateFormTagTest extends TestCase
{
    public function testNoTemplateWritesFormTag(): void
    {
        $dir = dirname(__DIR__, 3) . '/var/qiq/template';
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $checked = 0;
        foreach ($files as $file) {
            assert($file instanceof SplFileInfo);
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $checked++;
            $this->assertFalse(
                str_contains((string) file_get_contents($file->getPathname()), '<form'),
                sprintf('%s opens a form with a literal tag instead of form()', $file->getPathname()),
            );
        }

        $this->assertGreaterThan(0, $checked);
    }
}
