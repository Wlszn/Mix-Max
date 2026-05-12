<?php

declare(strict_types=1);

namespace App\Domain\Services;

use Cloudinary\Cloudinary;
use RuntimeException;

class CloudinaryService extends BaseService
{
    private Cloudinary $cloudinary;

    public function __construct(string $cloudName, string $apiKey, string $apiSecret)
    {
        if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
            throw new RuntimeException(
                'Cloudinary credentials are missing. Please set cloudinary.cloud_name, cloudinary.api_key, and cloudinary.api_secret in config/env.php.'
            );
        }

        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    public function uploadEventImage(string $source): ?string
    {
        $upload = $this->cloudinary->uploadApi()->upload($source, [
            'folder' => 'mix-max/events',
            'resource_type' => 'image',
        ]);

        return $upload['secure_url'] ?? null;
    }
}