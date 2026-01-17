# Basic Usage

This guide covers generating secure URLs and understanding the access types.

## Access Types

Laravel Media Secure provides three access types defined in the `MediaAccess` enum:

| Type       | URL Pattern              | Behavior                       |
|------------|--------------------------|--------------------------------|
| `view`     | `/media/view/{uuid}`     | Display inline in browser      |
| `download` | `/media/download/{uuid}` | Force download with filename   |
| `stream`   | `/media/stream/{uuid}`   | Stream content (same as view)  |

## Generating Secure URLs

Use the helper functions to generate secure URLs for your media files:

```php
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Assume you have a model with media attached via Spatie MediaLibrary
$document = Document::find(1);
$media = $document->getFirstMedia('attachments');

// Generate view URL (inline display)
$viewUrl = get_view_media_url($media);
// Result: https://your-app.com/media/view/abc123-def456-...

// Generate download URL (force download)
$downloadUrl = get_download_media_url($media);
// Result: https://your-app.com/media/download/abc123-def456-...

// Generate stream URL
$streamUrl = get_stream_media_url($media);
// Result: https://your-app.com/media/stream/abc123-def456-...
```

## Generic URL Helper

For more control, use the generic helper with the `MediaAccess` enum:

```php
use CleaniqueCoders\LaravelMediaSecure\Enums\MediaAccess;

$url = get_media_url(MediaAccess::VIEW, $media);
$url = get_media_url(MediaAccess::DOWNLOAD, $media);
$url = get_media_url(MediaAccess::STREAM, $media);
```

## Using in Blade Templates

```blade
<a href="{{ get_view_media_url($media) }}">View Document</a>
<a href="{{ get_download_media_url($media) }}">Download</a>

{{-- For images --}}
<img src="{{ get_view_media_url($media) }}" alt="Secure Image">

{{-- For PDFs --}}
<iframe src="{{ get_view_media_url($media) }}" width="100%" height="600"></iframe>
```

## Signed URLs (Shareable)

Signed URLs allow sharing media with external users without requiring authentication.
The URLs are cryptographically signed and expire after a configurable time.

```php
// Generate signed URLs with default expiration
$viewUrl = get_signed_view_url($media);
$downloadUrl = get_signed_download_url($media);
$streamUrl = get_signed_stream_url($media);

// Generate with custom expiration (in minutes)
$viewUrl = get_signed_view_url($media, 30);  // 30 minutes
$downloadUrl = get_signed_download_url($media, 1440);  // 24 hours

// Generate with DateTime expiration
$viewUrl = get_signed_view_url($media, now()->addWeek());
```

### Using Signed URLs in Blade

```blade
{{-- Share a download link that expires in 24 hours --}}
<a href="{{ get_signed_download_url($media, 1440) }}">Download (expires in 24h)</a>

{{-- Embed an image with a 1-week expiry --}}
<img src="{{ get_signed_view_url($media, 10080) }}" alt="Shared Image">
```

For more details, see [Signed URLs](../04-signed-urls/README.md).

## Next Steps

- [Configuration Options](../02-configuration/01-options.md)
- [Setting Up Policies](../03-authorization/01-policies.md)
- [Signed URLs](../04-signed-urls/README.md)
