<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests;

use PhpMyAdmin\File;
use PhpMyAdmin\Import\ImportSettings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;

use function bin2hex;
use function file_get_contents;

#[CoversClass(File::class)]
class FileTest extends AbstractTestCase
{
    /**
     * Setup function for test cases
     */
    protected function setUp(): void
    {
        parent::setUp();

        ImportSettings::$charsetConversion = false;
    }

    /**
     * Test for File::getCompression
     *
     * @param string $file file string
     * @param string $mime expected mime
     */
    #[DataProvider('compressedFiles')]
    public function testMIME(string $file, string $mime): void
    {
        $arr = new File($file);
        self::assertSame($mime, $arr->getCompression());
    }

    /**
     * Test for File::getContent
     *
     * @param string $file file string
     */
    #[DataProvider('compressedFiles')]
    public function testBinaryContent(string $file): void
    {
        $data = '0x' . bin2hex((string) file_get_contents($file));
        $file = new File($file);
        self::assertSame($data, $file->getContent());
    }

    /**
     * Test for File::read
     *
     * @param string $file file string
     */
    #[DataProvider('compressedFiles')]
    #[RequiresPhpExtension('bz2')]
    #[RequiresPhpExtension('zip')]
    public function testReadCompressed(string $file): void
    {
        $file = new File($file);
        $file->setDecompressContent(true);
        $file->open();
        self::assertSame("TEST FILE\n", $file->read(100));
        $file->close();
    }

    /** @return array<array{string, string}> */
    public static function compressedFiles(): array
    {
        return [
            ['./tests/test_data/test.gz', 'application/gzip'],
            ['./tests/test_data/test.bz2', 'application/bzip2'],
            ['./tests/test_data/test.zip', 'application/zip'],
        ];
    }
}
