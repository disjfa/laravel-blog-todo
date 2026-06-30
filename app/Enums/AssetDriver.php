<?php

namespace App\Enums;

enum AssetDriver: string
{
    case S3 = 's3';
    case FTP = 'ftp';
    case CLOUDINARY = 'cloudinary';

    public function label(): string
    {
        return match ($this) {
            self::S3 => 'Amazon S3',
            self::FTP => 'FTP',
            self::CLOUDINARY => 'Cloudinary',
        };
    }

    public function requiredCredentials(): array
    {
        return match ($this) {
            self::S3 => ['access_key', 'secret_key', 'region', 'bucket'],
            self::FTP => ['host', 'username', 'password', 'root'],
            self::CLOUDINARY => ['cloud_name', 'api_key', 'api_secret'],
        };
    }

    public function credentialLabels(): array
    {
        return match ($this) {
            self::S3 => [
                'access_key' => 'AWS Access Key',
                'secret_key' => 'AWS Secret Key',
                'region' => 'Region',
                'bucket' => 'Bucket Name',
            ],
            self::FTP => [
                'host' => 'FTP Host',
                'username' => 'Username',
                'password' => 'Password',
                'root' => 'Root Path',
            ],
            self::CLOUDINARY => [
                'cloud_name' => 'Cloud Name',
                'api_key' => 'API Key',
                'api_secret' => 'API Secret',
            ],
        };
    }
}
