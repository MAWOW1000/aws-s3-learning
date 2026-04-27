<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;

class S3Service
{
    private S3Client $s3Client;
    private string $bucket;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region', env('AWS_DEFAULT_REGION', 'us-east-1')),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key', env('AWS_ACCESS_KEY_ID')),
                'secret' => config('filesystems.disks.s3.secret', env('AWS_SECRET_ACCESS_KEY')),
            ],
        ]);
        $this->bucket = config('filesystems.disks.s3.bucket', env('AWS_BUCKET'));
    }

    public function uploadFile($file, string $storageClass = 'STANDARD'): string
    {
        $key = 'uploads/' . uniqid() . '_' . $file->getClientOriginalName();

        $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => fopen($file->getRealPath(), 'r'),
            'ContentType' => $file->getMimeType(),
            'StorageClass' => $storageClass,
            'Metadata' => [
                'original-filename' => $file->getClientOriginalName(),
            ],
        ]);

        return $key;
    }

    public function listFiles(): array
    {
        $result = $this->s3Client->listObjectsV2([
            'Bucket' => $this->bucket,
            'Prefix' => 'uploads/',
        ]);

        $files = [];
        foreach ($result['Contents'] ?? [] as $object) {
            $head = $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $object['Key'],
            ]);

            $files[] = [
                'key' => $object['Key'],
                'size' => $object['Size'],
                'last_modified' => $object['LastModified'],
                'storage_class' => $object['StorageClass'] ?? 'STANDARD',
                'original_name' => $head['Metadata']['original-filename'] ?? basename($object['Key']),
            ];
        }

        return array_reverse($files);
    }

    public function deleteFile(string $key): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
    }

    public function changeStorageClass(string $key, string $newClass): void
    {
        $this->s3Client->copyObject([
            'Bucket' => $this->bucket,
            'CopySource' => urlencode($this->bucket . '/' . $key),
            'Key' => $key,
            'StorageClass' => $newClass,
            'MetadataDirective' => 'COPY',
        ]);
    }

    public function getPresignedUrl(string $key, int $expiresMinutes = 5): string
    {
        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
        info('PresignedUrl command - Bucket: ' . $this->bucket . ', Key: ' . $key);

        $request = $this->s3Client->createPresignedRequest($cmd, "+{$expiresMinutes} minutes");
        $url = (string) $request->getUri();
        info('PresignedUrl generated: ' . $url);
        return $url;
    }

    public function getPublicUrl(string $key): string
    {
        return Storage::disk('s3')->url($key);
    }

    public static function storageClasses(): array
    {
        return [
            'STANDARD' => [
                'name' => 'S3 Standard',
                'description' => 'General purpose storage for frequently accessed data.',
                'retrieval' => 'Milliseconds',
                'min_storage' => 'None',
                'best_for' => 'Frequently accessed data, dynamic websites, content distribution.',
            ],
            'STANDARD_IA' => [
                'name' => 'S3 Standard-Infrequent Access',
                'description' => 'For data that is accessed less frequently, but requires rapid access when needed.',
                'retrieval' => 'Milliseconds',
                'min_storage' => '30 days',
                'best_for' => 'Backups, disaster recovery files, long-term storage with occasional access.',
            ],
            'ONEZONE_IA' => [
                'name' => 'S3 One Zone-Infrequent Access',
                'description' => 'Like Standard-IA, but stored in a single AZ. Lower cost, less resilience.',
                'retrieval' => 'Milliseconds',
                'min_storage' => '30 days',
                'best_for' => 'Secondary backups, re-creatable data, non-critical storage.',
            ],
            'GLACIER_IR' => [
                'name' => 'S3 Glacier Instant Retrieval',
                'description' => 'Archive storage with instant retrieval.',
                'retrieval' => 'Milliseconds',
                'min_storage' => '90 days',
                'best_for' => 'Archives that need immediate access, media assets, medical imaging.',
            ],
            'GLACIER' => [
                'name' => 'S3 Glacier Flexible Retrieval',
                'description' => 'Low-cost archive storage with flexible retrieval options.',
                'retrieval' => 'Minutes to hours',
                'min_storage' => '90 days',
                'best_for' => 'Backups, media archives, data that is rarely accessed.',
            ],
            'DEEP_ARCHIVE' => [
                'name' => 'S3 Glacier Deep Archive',
                'description' => 'Lowest cost storage for long-term retention.',
                'retrieval' => '12-48 hours',
                'min_storage' => '180 days',
                'best_for' => 'Compliance archives, long-term backups, regulatory data retention.',
            ],
            'REDUCED_REDUNDANCY' => [
                'name' => 'S3 Reduced Redundancy Storage',
                'description' => 'Deprecated. Lower redundancy for reproducible data at reduced cost.',
                'retrieval' => 'Milliseconds',
                'min_storage' => 'None',
                'best_for' => 'Reproducible data, thumbnails, derived content that can be recreated.',
            ],
            'INTELLIGENT_TIERING' => [
                'name' => 'S3 Intelligent-Tiering',
                'description' => 'Automatically moves data between tiers based on access patterns.',
                'retrieval' => 'Milliseconds',
                'min_storage' => 'None',
                'best_for' => 'Unknown or changing access patterns, data lakes, user-generated content.',
            ],
        ];
    }
}
