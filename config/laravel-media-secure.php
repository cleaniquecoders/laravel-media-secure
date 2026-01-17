<?php

use CleaniqueCoders\LaravelMediaSecure\Http\Controllers\MediaController;
use CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess;
use CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateSignedMediaAccess;

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

    /*
    |--------------------------------------------------------------------------
    | Signed URL Configuration
    |--------------------------------------------------------------------------
    |
    | Signed URLs allow sharing media with external users without requiring
    | authentication. The URLs are cryptographically signed and can be set
    | to expire after a specified time.
    |
    */

    'signed' => [
        /**
         * Enable Signed URLs
         *
         * When enabled, the package will register routes for signed URL access.
         * Signed URLs allow unauthenticated access to media for a limited time.
         */
        'enabled' => env('LARAVEL_MEDIA_SECURE_SIGNED_ENABLED', true),

        /**
         * Signed URL Route Prefix
         *
         * The URL prefix for signed media routes. Signed media files will be
         * accessible at URLs like: /media-signed/{type}/{uuid}?expires=...&signature=...
         */
        'prefix' => 'media-signed',

        /**
         * Signed URL Route Name
         *
         * The named route identifier for signed media access routes.
         * Used internally for generating signed URLs.
         */
        'route_name' => 'media.signed',

        /**
         * Default Expiration Time (in minutes)
         *
         * The default time-to-live for signed URLs. After this time,
         * the URL will no longer be valid. Can be overridden when generating URLs.
         *
         * Common values:
         * - 60 (1 hour)
         * - 1440 (24 hours)
         * - 10080 (1 week)
         */
        'expiration' => env('LARAVEL_MEDIA_SECURE_SIGNED_EXPIRATION', 60),

        /**
         * Signed URL Middleware Stack
         *
         * Middleware applied to signed media routes. By default, only the
         * ValidateSignedMediaAccess middleware is applied (no auth required).
         */
        'middleware' => [
            ValidateSignedMediaAccess::class,
        ],
    ],
];
