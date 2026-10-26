<?php

namespace CleaniqueCoders\LaravelMediaSecure\Tests\Models;

use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Simulates media on a non-local disk (e.g. S3), where getPath() does not
 * point at a readable local file. Serving must go through the disk instead.
 */
class RemoteMedia extends Media
{
    protected $table = 'media';

    public function getPath(string $conversionName = ''): string
    {
        throw new RuntimeException('getPath() must not be used to serve media.');
    }
}
