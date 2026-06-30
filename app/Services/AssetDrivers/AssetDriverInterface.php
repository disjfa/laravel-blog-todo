<?php

namespace App\Services\AssetDrivers;

interface AssetDriverInterface
{
    /**
     * Test the connection with the provided credentials.
     *
     * @throws \Exception
     */
    public function testConnection(array $credentials): bool;

    /**
     * Upload a file and return the public URL.
     *
     * @param  string|resource  $contents
     * @return array{public_url: string, provider_asset_id?: string}
     *
     * @throws \Exception
     */
    public function upload(string $path, $contents, array $credentials): array;

    /**
     * Delete a file from the remote storage.
     *
     * @param  string|array  $identifier
     *
     * @throws \Exception
     */
    public function delete($identifier, array $credentials): bool;

    /**
     * Get the public URL for an asset.
     *
     * @param  string|array  $identifier
     *
     * @throws \Exception
     */
    public function getPublicUrl($identifier, array $credentials): string;
}
