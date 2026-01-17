<?php

namespace CleaniqueCoders\LaravelMediaSecure\Http\Middleware;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateMediaAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $type = $request->route('type');
        $uuid = $request->route('uuid');

        // Validate media access type
        abort_if(! MediaAccess::acceptable($type), 422, 'Invalid request type of '.$type);

        // Get the media model
        $media = config('laravel-media-secure.model')::whereUuid($uuid)->firstOrFail();

        // Check if authentication is required and user is not authenticated
        $user = $request->user();
        if (config('laravel-media-secure.require_auth') && is_null($user)) {
            abort(401, 'Authentication required.');
        }

        // Check authorization (only if user is authenticated)
        if (! is_null($user)) {
            abort_if($user->cannot($type, $media), 403, 'Unauthorized Access.');
        }

        // Add media to the request for use in the controller
        $request->attributes->add(['media' => $media]);

        return $next($request);
    }
}
