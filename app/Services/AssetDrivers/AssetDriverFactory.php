<?php

namespace App\Services\AssetDrivers;

use App\Enums\AssetDriver;
use App\Models\CustomerAssetConnection;
use Exception;

class AssetDriverFactory
{
    /**
     * Create a driver instance from a CustomerAssetConnection.
     *
     * @throws Exception
     */
    public static function makeFromConnection(CustomerAssetConnection $connection): AssetDriverInterface
    {
        /** @var mixed $rawDriver */
        $rawDriver = $connection->driver;

        $driver = $rawDriver instanceof AssetDriver
            ? $rawDriver
            : AssetDriver::from($rawDriver);

        return self::make($driver);
    }

    /**
     * Create a driver instance from an enum.
     *
     * @throws Exception
     */
    public static function make(AssetDriver $driver): AssetDriverInterface
    {
        return match ($driver) {
            AssetDriver::S3 => new S3Driver,
            AssetDriver::FTP => new FtpDriver,
            AssetDriver::CLOUDINARY => new CloudinaryDriver,
        };
    }

    /**
     * Build a public URL from a CustomerAssetConnection and identifier.
     *
     * @param  string|array  $identifier  The file path or public_id
     *
     * @throws Exception
     */
    public static function buildUrl(CustomerAssetConnection $connection, $identifier): string
    {
        return self::makeFromConnection($connection)
            ->getPublicUrl($identifier, $connection->getDecryptedConfig());
    }

    /**
     * Test a driver connection with credentials.
     *
     * @throws Exception
     */
    public static function testConnection(AssetDriver $driver, array $credentials): bool
    {
        return self::make($driver)->testConnection($credentials);
    }
}
