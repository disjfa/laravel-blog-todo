<?php

namespace App\Services\AssetDrivers;

use Exception;
use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;

class FtpDriver implements AssetDriverInterface
{
    public function testConnection(array $credentials): bool
    {
        try {
            $filesystem = $this->createFilesystem($credentials);
            $filesystem->listContents('/')->toArray();

            return true;
        } catch (Exception $e) {
            throw new Exception('FTP connection failed: '.$e->getMessage());
        }
    }

    public function upload(string $path, $contents, array $credentials): array
    {
        try {
            $filesystem = $this->createFilesystem($credentials);
            $filesystem->write($path, $contents);

            $publicUrl = $this->buildPublicUrl($credentials, $path);

            return [
                'public_url' => $publicUrl,
            ];
        } catch (Exception $e) {
            throw new Exception('FTP upload failed: '.$e->getMessage());
        }
    }

    public function delete($identifier, array $credentials): bool
    {
        try {
            $filesystem = $this->createFilesystem($credentials);
            $filesystem->delete($identifier);

            return true;
        } catch (Exception $e) {
            throw new Exception('FTP deletion failed: '.$e->getMessage());
        }
    }

    public function getPublicUrl($identifier, array $credentials): string
    {
        return $this->buildPublicUrl($credentials, $identifier);
    }

    private function createFilesystem(array $credentials): Filesystem
    {
        $connectionOptions = FtpConnectionOptions::fromArray([
            'host' => $credentials['host'],
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'root' => $credentials['root'],
            'ssl' => false,
            'timeout' => 10,
        ]);

        $adapter = new FtpAdapter($connectionOptions);

        return new Filesystem($adapter);
    }

    private function buildPublicUrl(array $credentials, string $path): string
    {
        $baseUrl = rtrim($credentials['url'] ?? '', '/');

        return $baseUrl.'/'.ltrim($path, '/');
    }
}
