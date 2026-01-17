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

## Environment Variables

Configure via `.env`:

```env
LARAVEL_MEDIA_SECURE_REQUIRE_AUTH=true
LARAVEL_MEDIA_SECURE_STRICT=true
```

## Next Steps

- [Customizing Middleware](02-middleware.md)
- [Setting Up Policies](../03-authorization/01-policies.md)
