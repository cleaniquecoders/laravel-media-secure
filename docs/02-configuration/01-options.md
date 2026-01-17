# Configuration Options

This guide covers all configuration options available in `config/laravel-media-secure.php`.

## Configuration File

After publishing, the configuration file is located at `config/laravel-media-secure.php`.

## Options Reference

### model

The Spatie MediaLibrary model class.

```php
'model' => \Spatie\MediaLibrary\MediaCollections\Models\Media::class,
```

Override if you use a custom Media model.

### policy

The policy class for authorizing media access.

```php
'policy' => \CleaniqueCoders\LaravelMediaSecure\Policies\MediaPolicy::class,
```

### controller

The controller handling media requests.

```php
'controller' => [
    \CleaniqueCoders\LaravelMediaSecure\Http\Controllers\MediaController::class,
    '__invoke',
],
```

### middleware

The middleware stack applied to media routes.

```php
'middleware' => [
    'auth',
    'verified',
    \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,
],
```

See [Customizing Middleware](02-middleware.md) for details.

### prefix

The URL prefix for media routes.

```php
'prefix' => 'media',
```

Changes the URL from `/media/{type}/{uuid}` to `/{prefix}/{type}/{uuid}`.

### route_name

The named route identifier.

```php
'route_name' => 'media',
```

Used with `route('media', ['type' => 'view', 'uuid' => $uuid])`.

### require_auth

Whether authentication is required to access media.

```php
'require_auth' => env('LARAVEL_MEDIA_SECURE_REQUIRE_AUTH', true),
```

| Value   | Behavior                      |
|---------|-------------------------------|
| `true`  | Users must be logged in       |
| `false` | Allows unauthenticated access |

### strict

Whether parent model must have a policy.

```php
'strict' => env('LARAVEL_MEDIA_SECURE_STRICT', true),
```

| Value   | Behavior                                    |
|---------|---------------------------------------------|
| `true`  | Access denied if parent model has no policy |
| `false` | Access granted without policy check         |

See [Strict Mode](../03-authorization/02-strict-mode.md) for details.

## Signed URL Options

The `signed` array contains all configuration for signed URLs:

### signed.enabled

Enable or disable signed URL functionality.

```php
'signed' => [
    'enabled' => env('LARAVEL_MEDIA_SECURE_SIGNED_ENABLED', true),
],
```

### signed.prefix

The URL prefix for signed media routes.

```php
'signed' => [
    'prefix' => 'media-signed',
],
```

Changes the URL from `/media-signed/{type}/{uuid}` to `/{prefix}/{type}/{uuid}`.

### signed.route_name

The named route identifier for signed URLs.

```php
'signed' => [
    'route_name' => 'media.signed',
],
```

### signed.expiration

Default expiration time for signed URLs in minutes.

```php
'signed' => [
    'expiration' => env('LARAVEL_MEDIA_SECURE_SIGNED_EXPIRATION', 60),
],
```

Common values:
- `15` - 15 minutes
- `60` - 1 hour
- `1440` - 24 hours
- `10080` - 1 week

### signed.middleware

The middleware stack for signed URL routes.

```php
'signed' => [
    'middleware' => [
        \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateSignedMediaAccess::class,
    ],
],
```

## Environment Variables

Configure via `.env`:

```env
# Authentication & Authorization
LARAVEL_MEDIA_SECURE_REQUIRE_AUTH=true
LARAVEL_MEDIA_SECURE_STRICT=true

# Signed URLs
LARAVEL_MEDIA_SECURE_SIGNED_ENABLED=true
LARAVEL_MEDIA_SECURE_SIGNED_EXPIRATION=60
```

## Next Steps

- [Customizing Middleware](02-middleware.md)
- [Setting Up Policies](../03-authorization/01-policies.md)
- [Using Signed URLs](../04-signed-urls/README.md)
