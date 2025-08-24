<?php

namespace CleaniqueCoders\LaravelMediaSecure\Http\Middleware;

use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;
use Closure;
use Illuminate\Http\Request;

class ValidateMediaAccess
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $type = $request->route('type');
        $uuid = $request->route('uuid');

        // Validate media access type
        abort_if(! MediaAccess::acceptable($type), 422, 'Invalid request type of '.$type);

        // Get the media model
        $media = config('laravel-media-secure.model')::whereUuid($uuid)->firstOrFail();

        // Check authorization
        abort_if($request->user()->cannot($type, $media), 403, 'Unauthorized Access.');

        // Add media to the request for use in the controller
        $request->attributes->add(['media' => $media]);

        return $next($request);
    }
}
