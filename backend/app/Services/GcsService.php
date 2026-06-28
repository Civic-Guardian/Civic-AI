<?php

namespace App\Services;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Log;

class GcsService
{
    /**
     * Upload image data to Google Cloud Storage.
     * Returns the public URL of the uploaded object, or null on failure/not-configured.
     */
    public function uploadImage(string $binaryData, string $filename, string $contentType = 'image/jpeg'): ?string
    {
        try {
            $bucketName = SettingsService::get('gcs_bucket_name');
            $keyFileJson = SettingsService::get('gcs_key_file');

            if (empty($bucketName) || empty($keyFileJson)) {
                Log::warning('GCS Upload skipped: credentials or bucket not configured in Admin Settings.');
                return null;
            }

            $storage = new StorageClient([
                'keyFile' => json_decode($keyFileJson, true)
            ]);

            $bucket = $storage->bucket($bucketName);
            
            // Upload image data directly
            // Note: No predefinedAcl because bucket uses Uniform Bucket-Level Access
            $object = $bucket->upload($binaryData, [
                'name' => "hazards/{$filename}",
                'metadata' => [
                    'contentType' => $contentType
                ]
            ]);

            // Construct standard public GCS access URL
            return "https://storage.googleapis.com/{$bucketName}/hazards/{$filename}";

        } catch (\Exception $e) {
            Log::error('Google Cloud Storage upload error: ' . $e->getMessage());
            return null;
        }
    }
}
