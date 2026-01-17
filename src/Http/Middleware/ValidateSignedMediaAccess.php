<?php

namespace CleaniqueCoders\LaravelMediaSecure\Http\Middleware;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSignedMediaAccess
{
    /**
     * Handle an incoming request for signed media URLs.
     *
     * This middleware validates:
     * 1. The URL signature is valid and not expired
     * 2. The media access type is valid
     * 3. The media exists
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate the signed URL
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired signature.');
        }

        $type = $request->route('type');
        $uuid = $request->route('uuid');

        // Validate media access type
        abort_if(! MediaAccess::acceptable($type), 422, 'Invalid request type of '.$type);

        // Get the media model
        $media = config('laravel-media-secure.model')::whereUuid($uuid)->firstOrFail();

        // Verify the file exists
        if (! file_exists($media->getPath())) {
            abort(404, 'Media file not found.');
        }

        // Add media to the request for use in the controller
        $request->attributes->add(['media' => $media]);

        return $next($request);
    }
}
