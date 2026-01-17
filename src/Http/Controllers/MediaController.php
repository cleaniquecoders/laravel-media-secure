<?php

namespace CleaniqueCoders\LaravelMediaSecure\Http\Controllers;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  string  $uuid  Media UUID (used by route, media is pre-fetched by middleware)
     */
    public function __invoke(Request $request, string $type, string $uuid): Response
    {
        /** @var Media|null $media */
        $media = $request->attributes->get('media');

        if ($media === null) {
            abort(422, 'Invalid media request');
        }

        $path = $media->getPath();

        if (! file_exists($path)) {
            abort(404, 'Media file not found');
        }

        // Generate ETag based on file modification time and size
        $lastModified = filemtime($path);
        $fileSize = filesize($path);
        $etag = md5($path.$lastModified.$fileSize);

        // Check for conditional requests (browser caching)
        $requestEtag = $request->header('If-None-Match');
        $requestModified = $request->header('If-Modified-Since');

        if ($requestEtag === $etag || ($requestModified && strtotime($requestModified) >= $lastModified)) {
            return response('', 304);
        }

        // Sanitize filename to prevent header injection
        $safeFileName = $this->sanitizeFileName($media->file_name);

        // Build cache headers
        $cacheHeaders = [
            'ETag' => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified).' GMT',
            'Cache-Control' => 'private, max-age=3600',
        ];

        if ($type === MediaAccess::DOWNLOAD->value) {
            return response()->download($path, $safeFileName, $cacheHeaders);
        }

        if ($type === MediaAccess::VIEW->value || $type === MediaAccess::STREAM->value) {
            return $this->streamFile($path, $media->mime_type, $safeFileName, $cacheHeaders);
        }

        abort(422, 'Invalid media request');
    }

    /**
     * Stream a file using chunked transfer to avoid memory issues with large files.
     */
    protected function streamFile(string $path, string $mimeType, string $fileName, array $cacheHeaders): StreamedResponse
    {
        $fileSize = filesize($path);

        $headers = array_merge([
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
        ], $cacheHeaders);

        return response()->stream(function () use ($path) {
            $stream = fopen($path, 'rb');

            if ($stream === false) {
                return;
            }

            // Stream in 8KB chunks to avoid memory issues
            while (! feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }

            fclose($stream);
        }, 200, $headers);
    }

    /**
     * Sanitize filename to prevent header injection attacks.
     * Removes or encodes characters that could be used for header injection.
     */
    protected function sanitizeFileName(string $fileName): string
    {
        // Remove any null bytes, newlines, and carriage returns (header injection prevention)
        $fileName = str_replace(["\0", "\r", "\n", '"'], ['', '', '', "'"], $fileName);

        // Remove any non-printable ASCII characters
        $sanitized = preg_replace('/[\x00-\x1F\x7F]/', '', $fileName);

        // Fallback to a safe default if the filename becomes empty or preg_replace fails
        if ($sanitized === null || $sanitized === '') {
            return 'download';
        }

        return $sanitized;
    }
}
