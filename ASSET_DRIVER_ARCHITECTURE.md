# Asset Driver Architecture

## Overview
A flexible, factory-based driver system for managing customer assets across multiple storage backends (S3, FTP, Cloudinary) using **League\Flysystem** abstraction.

## Technology Stack
- **S3Driver**: Uses `league/flysystem-aws-s3-v3` adapter
- **FtpDriver**: Uses `league/flysystem-ftp` adapter
- **CloudinaryDriver**: Uses native Cloudinary PHP SDK
- All drivers implement consistent interface for unified usage

## Components

### 1. **AssetDriver Enum** (`app/Enums/AssetDriver.php`)
Defines available drivers and their requirements:
- `S3` - Amazon S3 / S3-compatible services
- `FTP` - FTP server storage
- `CLOUDINARY` - Cloudinary image hosting

Methods:
- `label()` - Human-readable driver name
- `requiredCredentials()` - Array of required credential keys
- `credentialLabels()` - Friendly labels for each credential field

### 2. **AssetDriverInterface** (`app/Services/AssetDrivers/AssetDriverInterface.php`)
Contract for all driver implementations:
- `testConnection(array $credentials): bool` - Validate credentials
- `upload(string $path, $contents, array $credentials): array` - Upload file, returns `public_url` and optional `provider_asset_id`
- `delete($identifier, array $credentials): bool` - Delete remote file
- `getPublicUrl($identifier, array $credentials): string` - Build URL for existing asset

### 3. **Driver Implementations**

#### S3Driver (`app/Services/AssetDrivers/S3Driver.php`)
- **Architecture**: Uses `League\Flysystem\AwsS3V3\AwsS3V3Adapter` for S3 operations
- Supports AWS S3 and S3-compatible services (DigitalOcean Spaces, MinIO, etc.)
- Credentials: `access_key`, `secret_key`, `region`, `bucket`, optional `endpoint`
- Benefits: Cleaner abstraction, better error handling, consistent with Flysystem ecosystem

#### FtpDriver (`app/Services/AssetDrivers/FtpDriver.php`)
- **Architecture**: Uses `League\Flysystem\Ftp\FtpAdapter` for FTP operations
- Connects via standard FTP protocol using Flysystem abstraction
- Credentials: `host`, `username`, `password`, `root` (remote path), `url` (base public URL)
- Returns files at: `{url}/{path}`
- Benefits: No raw FTP functions, better connection management, reliable error handling

#### CloudinaryDriver (`app/Services/AssetDrivers/CloudinaryDriver.php`)
- Uses native Cloudinary PHP SDK (no Flysystem adapter available)
- Credentials: `cloud_name`, `api_key`, `api_secret`
- Returns `provider_asset_id` (Cloudinary public_id) for later reference

### 4. **AssetDriverFactory** (`app/Services/AssetDrivers/AssetDriverFactory.php`)
Factory for instantiating and managing drivers:

```php
// Create driver from CustomerAssetConnection model
$driver = AssetDriverFactory::makeFromConnection($connection);

// Create driver from enum
$driver = AssetDriverFactory::make(AssetDriver::S3);

// Test connection with credentials
$valid = AssetDriverFactory::testConnection(AssetDriver::S3, $credentials);

// Build public URL from connection and identifier
$url = AssetDriverFactory::buildUrl($connection, $path);
```

### 5. **Updated Models**

#### CustomerAssetConnection
- `driver` field now cast to `AssetDriver` enum
- `config_encrypted` cast to `encrypted:array` for storing credential objects
- New method: `getDecryptedConfig()` - Returns decrypted credentials array

### 6. **Updated Form**

#### CustomerAssetConnectionForm
- **Driver Selector**: Live-updating select showing all driver options
- **Dynamic Credentials Section**: Shows only fields for selected driver
  - S3: Access Key, Secret Key, Region, Bucket, Endpoint
  - FTP: Host, Username, Password, Root Path, Base URL
  - Cloudinary: Cloud Name, API Key, API Secret
- Password fields use `PasswordInput` component
- All credential fields prefixed with `config_encrypted.` to store in JSON structure

## Usage Examples

### Uploading an Asset
```php
$connection = CustomerAssetConnection::find($id);
$driver = AssetDriverFactory::makeFromConnection($connection);

$result = $driver->upload(
    path: 'uploads/2024/blog-123.jpg',
    contents: $fileContents,
    credentials: $connection->getDecryptedConfig()
);

// $result = [
//     'public_url' => 'https://...',
//     'provider_asset_id' => '...' // if applicable
// ]
```

### Building Public URL
```php
$connection = CustomerAssetConnection::first();
$publicUrl = AssetDriverFactory::buildUrl($connection, $identifier);
```

### Testing Connection
```php
$credentials = [
    'access_key' => '...',
    'secret_key' => '...',
    'region' => 'us-east-1',
    'bucket' => 'my-bucket',
];

try {
    $valid = AssetDriverFactory::testConnection(AssetDriver::S3, $credentials);
} catch (Exception $e) {
    // Connection failed
}
```

## Database Storage
Credentials are encrypted at rest in the `config_encrypted` column:
```json
{
    "access_key": "AKIAIOSFODNN7EXAMPLE",
    "secret_key": "encrypted...",
    "region": "us-east-1",
    "bucket": "my-bucket"
}
```

## Flysystem Advantages
- **Unified API**: All drivers use the same Filesystem methods (`write()`, `read()`, `delete()`, etc.)
- **Robust Error Handling**: Built-in exception handling for common filesystem errors
- **Connection Management**: Handles connection pooling and lifecycle automatically
- **Visibility Management**: Built-in support for public/private file visibility
- **Metadata**: Easy access to file metadata (timestamps, size, etc.)
- **Extensibility**: Easy to add new drivers by implementing adapters
- **Testing**: Flysystem provides in-memory filesystem for testing

## Dependencies
```json
{
    "league/flysystem-ftp": "^3.31",
    "league/flysystem-aws-s3-v3": "^3.35"
}
```

## Future Enhancements
- Add connection validation action in Filament form with test button
- Implement retry logic for failed uploads with backoff
- Add monitoring/logging for driver operations and error tracking
- Support additional drivers via Flysystem (Google Cloud Storage, Azure Blob, etc.)
- Add batch operations support (upload multiple files, delete multiple)
- Implement file versioning strategy for backups
- Add driver-specific optimization options (compression, caching, etc.)

