<?php

use Bekwoh\LaravelMediaSecure\Enums\MediaAccess;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

if (! function_exists('get_media_url')) {
    function get_media_url(MediaAccess $mediaAccess, Media $media)
    {
        return route('media', [
            'type' => $mediaAccess->value,
            'uuid' => $media->uuid,
        ]);
    }
}

if (! function_exists('get_view_media_url')) {
    function get_view_media_url(Media $media)
    {
        return get_media_url(MediaAccess::view(), $media);
    }
}

if (! function_exists('get_download_media_url')) {
    function get_download_media_url(Media $media)
    {
        return get_media_url(MediaAccess::download(), $media);
    }
}

if (! function_exists('get_stream_media_url')) {
    function get_stream_media_url(Media $media)
    {
        return get_media_url(MediaAccess::stream(), $media);
    }
}
