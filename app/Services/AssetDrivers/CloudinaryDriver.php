<?php

namespace App\Services\AssetDrivers;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\CommonTransformation;
use Exception;

class CloudinaryDriver implements AssetDriverInterface
{
    public function testConnection(array $credentials): bool
    {
        try {
            $this->cloudinary($credentials)->adminApi()->ping();

            return true;
        } catch (Exception $e) {
            throw new Exception('Cloudinary connection failed: '.$e->getMessage());
        }
    }

    public function upload(string $path, $contents, array $credentials): array
    {
        $tmpFile = null;

        try {
            if (is_resource($contents)) {
                $tmpFile = tmpfile();
                fwrite($tmpFile, stream_get_contents($contents));
                rewind($tmpFile);
                $filePath = stream_get_meta_data($tmpFile)['uri'];
            } else {
                $tmpFile = tmpfile();
                fwrite($tmpFile, (string) $contents);
                rewind($tmpFile);
                $filePath = stream_get_meta_data($tmpFile)['uri'];
            }

            $result = $this->cloudinary($credentials)->uploadApi()->upload($filePath, [
                'public_id' => pathinfo($path, PATHINFO_FILENAME),
                'folder' => pathinfo($path, PATHINFO_DIRNAME),
                'resource_type' => 'auto',
            ]);

            $publicId = (string) ($result['public_id'] ?? '');

            return [
                'public_url' => filled($publicId)
                    ? $this->getPublicUrl($publicId, $credentials)
                    : (string) ($result['secure_url'] ?? ''),
                'provider_asset_id' => $publicId,
            ];
        } catch (Exception $e) {
            throw new Exception('Cloudinary upload failed: '.$e->getMessage());
        } finally {
            if (is_resource($tmpFile)) {
                fclose($tmpFile);
            }
        }
    }

    public function delete($identifier, array $credentials): bool
    {
        try {
            $this->cloudinary($credentials)->uploadApi()->destroy((string) $identifier, [
                'resource_type' => 'auto',
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception('Cloudinary deletion failed: '.$e->getMessage());
        }
    }

    public function getPublicUrl($identifier, array $credentials): string
    {
        if (is_string($identifier) && str_starts_with($identifier, 'http')) {
            return $identifier;
        }

        return (string) $this->cloudinary($credentials)
            ->image((string) $identifier)
            ->toUrl($this->baseTransformation(), false);
    }

    private function baseTransformation(): CommonTransformation
    {
        return CommonTransformation::fromParams([
            'crop' => 'fill',
            'aspect_ratio' => '16:9',
            'width' => 1280,
        ]);
    }

    private function cloudinary(array $credentials): Cloudinary
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => $credentials['cloud_name'],
                'api_key' => $credentials['api_key'],
                'api_secret' => $credentials['api_secret'],
            ],
        ]);
    }
}
