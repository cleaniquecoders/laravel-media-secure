# Customizing Middleware

This guide covers how to customize the middleware stack for media routes.

## Default Middleware Stack

The default middleware configuration:

```php
'middleware' => [
    'auth',
    'verified',
    \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,
],
```

## ValidateMediaAccess Middleware

The `ValidateMediaAccess` middleware is **mandatory** and must always be included.
It performs:

1. Validates the access type (`view`, `download`, `stream`)
2. Fetches the media by UUID
3. Checks user authorization via the policy
4. Attaches the media to the request for the controller

## Customization Examples

### Remove Email Verification

If you don't require email verification:

```php
'middleware' => [
    'auth',
    \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,
],
```

### Allow Guest Access

For public media (with `require_auth` set to `false`):

```php
'middleware' => [
    \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,
],
```

### Add Rate Limiting

Protect against abuse:

```php
'middleware' => [
    'auth',
    'throttle:60,1',
    \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,
],
```

### Add Custom Middleware

Add your own middleware for logging or additional checks:

```php
'middleware' => [
    'auth',
    'verified',
    \App\Http\Middleware\LogMediaAccess::class,
    \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,
],
```

## Middleware Alias

The package registers `validate-media-access` as a middleware alias.
You can use either:

```php
// Full class reference
\CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,

// Or the alias (when using string-based middleware)
'validate-media-access',
```

## Next Steps

- [Configuration Options](01-options.md)
- [Setting Up Policies](../03-authorization/01-policies.md)
