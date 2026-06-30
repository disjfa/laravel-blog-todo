<?php

namespace App\Services\AssetDrivers;

use Aws\S3\S3Client;
use Exception;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

class S3Driver implements AssetDriverInterface
{
    public function testConnection(array $credentials): bool
    {
        try {
            $filesystem = $this->createFilesystem($credentials);
            $filesystem->listContents('/')->toArray();

            return true;
        } catch (Exception $e) {
            throw new Exception('S3 connection failed: '.$e->getMessage());
        }
    }

    public function upload(string $path, $contents, array $credentials): array
    {
        try {
            $filesystem = $this->createFilesystem($credentials);
            $filesystem->write($path, $contents);

            $url = $this->buildUrl($credentials, $path);

            return [
                'public_url' => $url,
            ];
        } catch (Exception $e) {
            throw new Exception('S3 upload failed: '.$e->getMessage());
        }
    }

    public function delete($identifier, array $credentials): bool
    {
        try {
            $filesystem = $this->createFilesystem($credentials);
            $filesystem->delete($identifier);

            return true;
        } catch (Exception $e) {
            throw new Exception('S3 deletion failed: '.$e->getMessage());
        }
    }

    public function getPublicUrl($identifier, array $credentials): string
    {
        return $this->buildUrl($credentials, $identifier);
    }

    private function createFilesystem(array $credentials): Filesystem
    {
        $s3Client = $this->createS3Client($credentials);
        $adapter = new AwsS3V3Adapter($s3Client, $credentials['bucket']);

        return new Filesystem($adapter);
    }

    private function createS3Client(array $credentials): S3Client
    {
        $config = [
            'version' => 'latest',
            'region' => $credentials['region'],
            'credentials' => [
                'key' => $credentials['access_key'],
                'secret' => $credentials['secret_key'],
            ],
        ];

        if (! empty($credentials['endpoint'])) {
            $config['endpoint'] = $credentials['endpoint'];
            $config['use_path_style_endpoint'] = true;
        }

        return new S3Client($config);
    }

    private function buildUrl(array $credentials, string $path): string
    {
        $bucket = $credentials['bucket'];
        $region = $credentials['region'];

        if (! empty($credentials['endpoint'])) {
            // S3-compatible service
            $endpoint = rtrim($credentials['endpoint'], '/');

            return "{$endpoint}/{$bucket}/{$path}";
        }

        // AWS S3
        return "https://{$bucket}.s3.{$region}.amazonaws.com/{$path}";
    }
}
