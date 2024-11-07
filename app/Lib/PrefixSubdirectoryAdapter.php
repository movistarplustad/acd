<?php

declare(strict_types=1);

namespace Acd\Lib;
use League\Flysystem\Local\FallbackMimeTypeDetector;

use const DIRECTORY_SEPARATOR;



use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Config;
class PrefixSubdirectoryAdapter extends LocalFilesystemAdapter
{
    protected function pathWithSubdirectory(string $path): string
    {
        $prefix = substr($path, 0, 3);
        $pathWithSubdirectory = $prefix . DIRECTORY_SEPARATOR . $path;
        return $pathWithSubdirectory;
    }
    public function write(string $path, string $contents, Config $config): void
    {
        $pathWithSubdirectory = self::pathWithSubdirectory($path);
        parent::write($pathWithSubdirectory, $contents, $config);
    }
    public function writeStream(string $path, $contents, Config $config): void
    {
        $pathWithSubdirectory = self::pathWithSubdirectory($path);
        parent::writeStream($pathWithSubdirectory, $contents, $config);
    }
    public function read(string $path): string
    {
        $pathWithSubdirectory = self::pathWithSubdirectory($path);
        return parent::read($pathWithSubdirectory);
    }
    public function readStream(string $path)
    {
        $pathWithSubdirectory = self::pathWithSubdirectory($path);
        return parent::readStream($pathWithSubdirectory);
    }
    public function delete(string $path): void {
        $pathWithSubdirectory = self::pathWithSubdirectory($path);
        parent::delete($pathWithSubdirectory);
    }
}
