<?php

use CleaniqueCoders\LaravelMediaSecure\Http\Controllers\MediaController;
use CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess;

return [
    /**
     * Spatie's Media Library Model Class
     *
     * This specifies the Media model class from Spatie's MediaLibrary package
     * that will be used for media file management and database operations.
     */
    'model' => \Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /**
     * Media Access Policy Class
     *
     * This policy class handles authorization logic for media access.
     * It determines whether a user can view, download, or stream specific media files.
     */
    'policy' => \CleaniqueCoders\LaravelMediaSecure\Policies\MediaPolicy::class,

    /**
     * Media Controller Configuration
     *
     * Defines the controller class and method responsible for handling
     * media requests after middleware validation and authorization.
     */
    'controller' => [
        MediaController::class, '__invoke',
    ],

    /**
     * Route Middleware Stack
     *
     * Middleware applied to media routes in the specified order:
     * - 'auth': Ensures user is authenticated
     * - 'verified': Ensures user has verified their email (optional)
     * - ValidateMediaAccess: Validates media access type, authorizes user, and prepares media (MANDATORY)
     *
     * Note: The ValidateMediaAccess middleware is required and cannot be removed
     * as it handles critical security validation and media preparation.
     */
    'middleware' => [
        'auth',
        'verified',
        ValidateMediaAccess::class, // Mandatory - handles validation, authorization, and media preparation
    ],

    /**
     * Media Route URI Prefix
     *
     * The URL prefix for all media routes. Media files will be accessible
     * at URLs like: /media/{type}/{uuid}
     *
     * Example: /media/view/abc123-def456-ghi789
     */
    'prefix' => 'media',

    /**
     * Media Route Name
     *
     * The named route identifier for media access routes.
     * Used for generating URLs with route() helper: route('media', ['type' => 'view', 'uuid' => $uuid])
     */
    'route_name' => 'media',

    /**
     * Authentication Requirement
     *
     * Determines whether authentication is required for media access.
     * When true, users must be logged in to access any media files.
     * Set to false only if you want to allow public media access.
     */
    'require_auth' => env('LARAVEL_MEDIA_SECURE_REQUIRE_AUTH', true),

    /**
     * Strict Authorization Mode
     *
     * When enabled, all media access requests must pass through the MediaPolicy
     * authorization checks. This provides fine-grained control over media access.
     *
     * When disabled, basic authentication (if required) is sufficient.
     * Recommended to keep enabled for maximum security.
     */
    'strict' => env('LARAVEL_MEDIA_SECURE_STRICT', true),
];
