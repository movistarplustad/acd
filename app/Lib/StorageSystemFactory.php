<?php
namespace Acd\Lib;
use League\Flysystem\Filesystem;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;



class StorageSystemFactory
{
    const availableAdapters = ['disk', 'awss3', 'disksubdirprefix'];
    public static function getAvailableAdapters()
    {
        return self::availableAdapters;
    }
    private static function getAdapter(string $type) {
        if (!in_array($type, self::getAvailableAdapters())) {
            throw new FlySystemException("Type of adapter ($type) not supported", 1);
        }
        switch ($type) {
            case 'disk':
                $rootPath = getenv('ACD_DATA_CONTENT_PATH');
                return new LocalFilesystemAdapter($rootPath);
            case 'awss3':
                $minioConfig = [
                    'credentials' => [
                        'key'    => getenv('ACD_DATA_CONTENT_STORAGE_AWSS3_CREDENTIALS_KEY'),
                        'secret' => getenv('ACD_DATA_CONTENT_STORAGE_AWSS3_CREDENTIALS_SECRET'),
                    ],
                    'region'  => getenv('ACD_DATA_CONTENT_STORAGE_AWSS3_CREDENTIALS_REGION'),
                    'version' => getenv('ACD_DATA_CONTENT_STORAGE_AWSS3_CREDENTIALS_VERSION'),
                    'endpoint' => getenv('ACD_DATA_CONTENT_STORAGE_AWSS3_CREDENTIALS_ENDPOINT'),
                    'use_path_style_endpoint' => filter_var(getenv('ACD_DATA_CONTENT_STORAGE_AWSS3_CREDENTIALS_USE_PATH_STYLE_ENDPOINT'), FILTER_VALIDATE_BOOLEAN)
                ];
                $s3Client = new S3Client($minioConfig);
                $bucketName = 'yomvi-storage';
                $adapter = new AwsS3V3Adapter(
                    $s3Client,
                    $bucketName
                );

                return $adapter;
            case 'disksubdirprefix':
                    $rootPath = getenv('ACD_DATA_CONTENT_PATH');
                    return new PrefixSubdirectoryAdapter($rootPath);
        }
    }

    public static function getFileSystem(string $storageBinaryAdapter)
    {
        try {
            $adapter = self::getAdapter($storageBinaryAdapter);
        } catch (FlySystemException $exception) {
            throw $exception;
        }

        return new Filesystem($adapter);
    }
}