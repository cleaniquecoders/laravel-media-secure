# Signed URLs

Signed URLs allow you to share media files with external users without requiring them to be authenticated. The URLs are cryptographically signed using your application's key and can be configured to expire after a specified time.

## Use Cases

Signed URLs are ideal for:

- **Email attachments**: Share download links in emails that expire after a period
- **External sharing**: Provide temporary access to clients or partners
- **API integrations**: Allow third-party applications to access media
- **Embedding**: Embed media in external websites or applications
- **Public previews**: Share document previews without exposing permanent URLs

## How It Works

1. Generate a signed URL using the helper functions or Facade
2. The URL includes a cryptographic signature and expiration timestamp
3. When accessed, the middleware validates the signature and checks expiration
4. If valid, the media is served; otherwise, a 403 error is returned

The signature is generated using Laravel's built-in URL signing, which uses HMAC-SHA256 with your `APP_KEY`.

## Generating Signed URLs

### Using Helper Functions

```php
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Get a media instance
$media = $document->getFirstMedia('attachments');

// Generate signed URLs with default expiration (from config)
$viewUrl = get_signed_view_url($media);
$downloadUrl = get_signed_download_url($media);
$streamUrl = get_signed_stream_url($media);

// Generate with custom expiration (in minutes)
$viewUrl = get_signed_view_url($media, 30);        // 30 minutes
$downloadUrl = get_signed_download_url($media, 1440); // 24 hours

// Generate with DateTime expiration
$viewUrl = get_signed_view_url($media, now()->addWeek());

// Generic helper with MediaAccess enum
use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;

$url = get_signed_media_url(MediaAccess::VIEW, $media, 60);
```

### Using the Facade

```php
use CleaniqueCoders\LaravelMediaSecure\Facades\LaravelMediaSecure;

// Generate signed URLs
$viewUrl = LaravelMediaSecure::signedViewUrl($media);
$downloadUrl = LaravelMediaSecure::signedDownloadUrl($media, 60);
$streamUrl = LaravelMediaSecure::signedStreamUrl($media, now()->addHours(2));

// Generic method with MediaAccess enum
use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;

$url = LaravelMediaSecure::signedUrl(MediaAccess::DOWNLOAD, $media, 120);
```

### Using the Service Directly

```php
use CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecure;

$service = app(LaravelMediaSecure::class);

$url = $service->signedViewUrl($media);
$url = $service->signedDownloadUrl($media, 60);
```

## URL Format

Signed URLs follow this format:

```
https://your-app.com/media-signed/{type}/{uuid}?expires={timestamp}&signature={hash}
```

Example:
```
https://your-app.com/media-signed/view/abc123-def456?expires=1704067200&signature=a1b2c3d4...
```

## Configuration

Configure signed URLs in `config/laravel-media-secure.php`:

```php
'signed' => [
    // Enable or disable signed URL functionality
    'enabled' => env('LARAVEL_MEDIA_SECURE_SIGNED_ENABLED', true),

    // URL prefix for signed routes
    'prefix' => 'media-signed',

    // Route name for signed URLs
    'route_name' => 'media.signed',

    // Default expiration time in minutes
    'expiration' => env('LARAVEL_MEDIA_SECURE_SIGNED_EXPIRATION', 60),

    // Middleware stack for signed routes
    'middleware' => [
        \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateSignedMediaAccess::class,
    ],
],
```

### Environment Variables

```env
# Enable/disable signed URLs
LARAVEL_MEDIA_SECURE_SIGNED_ENABLED=true

# Default expiration in minutes (60 = 1 hour)
LARAVEL_MEDIA_SECURE_SIGNED_EXPIRATION=60
```

### Common Expiration Values

| Minutes | Duration |
|---------|----------|
| 15      | 15 minutes |
| 60      | 1 hour |
| 1440    | 24 hours |
| 10080   | 1 week |
| 43200   | 30 days |

## Security Considerations

### Signature Validation

The middleware automatically validates:

1. **Signature authenticity**: The signature must match the expected value
2. **Expiration**: The URL must not be expired
3. **Media existence**: The requested media must exist
4. **File existence**: The physical file must exist on disk

### Best Practices

1. **Use short expirations**: Set the shortest practical expiration time
2. **Rotate APP_KEY carefully**: Changing `APP_KEY` invalidates all signed URLs
3. **Log access**: Consider logging signed URL access for auditing
4. **HTTPS only**: Always use HTTPS in production to prevent URL interception

### Disabling Signed URLs

If you don't need signed URLs, disable them:

```env
LARAVEL_MEDIA_SECURE_SIGNED_ENABLED=false
```

This prevents the routes from being registered.

## Response Headers

Signed URL responses include caching headers for performance:

- `ETag`: Content hash for cache validation
- `Last-Modified`: File modification timestamp
- `Cache-Control`: `private, max-age=3600`

These headers enable browser caching while respecting the signed URL expiration.

## Error Responses

| Status | Description |
|--------|-------------|
| 200    | Success - media served |
| 403    | Invalid or expired signature |
| 404    | Media not found |
| 422    | Invalid access type |

## Examples

### Share a Document via Email

```php
$media = $document->getFirstMedia('contracts');

// Generate a 24-hour download link
$downloadLink = get_signed_download_url($media, 1440);

// Send in email
Mail::to($client)->send(new ContractEmail($downloadLink));
```

### Embed in External Application

```php
$media = $post->getFirstMedia('images');

// Generate a 1-week view URL for embedding
$imageUrl = get_signed_view_url($media, 10080);

// Return in API response
return response()->json([
    'image_url' => $imageUrl,
    'expires_at' => now()->addWeek()->toIso8601String(),
]);
```

### Temporary Preview Access

```php
$media = $report->getFirstMedia('reports');

// Generate a 15-minute preview link
$previewUrl = get_signed_view_url($media, 15);

return view('reports.preview', compact('previewUrl'));
```

## Comparison: Authenticated vs Signed URLs

| Feature | Authenticated URLs | Signed URLs |
|---------|-------------------|-------------|
| Requires login | Yes | No |
| Uses policies | Yes | No |
| Time-limited | No | Yes |
| Shareable | No | Yes |
| URL format | `/media/{type}/{uuid}` | `/media-signed/{type}/{uuid}?...` |
| Security | Policy-based | Signature-based |

## Next Steps

- [Configuration Options](../02-configuration/01-options.md)
- [Authorization & Policies](../03-authorization/README.md)
