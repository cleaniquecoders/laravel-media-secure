# Strict Mode

This guide explains the strict mode configuration and when to use it.

## What is Strict Mode?

Strict mode controls whether a parent model **must** have a registered policy
for media access.

```php
// config/laravel-media-secure.php
'strict' => env('LARAVEL_MEDIA_SECURE_STRICT', true),
```

## Behavior Comparison

| Condition                  | `strict = true`   | `strict = false`   |
|----------------------------|-------------------|--------------------|
| Parent model has policy    | Policy is checked | Policy is checked  |
| Parent model has no policy | **Access denied** | **Access granted** |

## When to Use Strict Mode

### Enable Strict Mode (`true`) When

- Security is critical (e.g., sensitive documents, private files)
- All models with media should explicitly define access rules
- You want to fail-safe (deny access when uncertain)

### Disable Strict Mode (`false`) When

- You want permissive defaults for models without policies
- Only some models need access control
- You're migrating an existing system gradually

## Configuration

### Via Environment Variable

```env
LARAVEL_MEDIA_SECURE_STRICT=true
```

### Via Config File

```php
'strict' => true,
```

## How It Works Internally

The `MediaPolicy::canAccess()` method checks strict mode:

```php
// Simplified logic
$isStrict = config('laravel-media-secure.strict');
if ($isStrict && is_null(Gate::getPolicyFor($media->model))) {
    return false; // Strict mode: deny if no policy
}

if (! is_null(Gate::getPolicyFor($media->model))) {
    return Gate::allows($mediaAccess->value, $media->model); // Check policy
}

return true; // Non-strict: allow if no policy
```

## Gradual Migration Strategy

If you're adding this package to an existing project:

1. Start with `strict = false`
2. Add policies for sensitive models first
3. Monitor access logs
4. Enable `strict = true` once all models have policies

## Next Steps

- [Setting Up Policies](01-policies.md)
- [Access Control Flow](03-access-flow.md)
