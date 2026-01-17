# Access Control Flow

This guide explains how authorization works internally when accessing media.

## Request Flow Diagram

```text
Request: GET /media/view/{uuid}
           │
           ▼
┌─────────────────────────────────┐
│     Middleware Stack            │
│  (auth, verified, etc.)         │
└─────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────┐
│   ValidateMediaAccess           │
│   Middleware                    │
│                                 │
│  1. Validate type (view/        │
│     download/stream)            │
│  2. Fetch media by UUID         │
│  3. Check authorization         │
│  4. Attach media to request     │
└─────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────┐
│      MediaController            │
│                                 │
│  - Serve file based on type     │
│  - view/stream: inline display  │
│  - download: force download     │
└─────────────────────────────────┘
           │
           ▼
       Response
```

## Authorization Decision Flow

```text
MediaPolicy::canAccess()
           │
           ▼
┌──────────────────────────┐
│ require_auth enabled?    │
│ AND user not logged in?  │──Yes──▶ DENY
└──────────────────────────┘
           │ No
           ▼
┌──────────────────────────┐
│ File exists on disk?     │──No───▶ DENY
└──────────────────────────┘
           │ Yes
           ▼
┌──────────────────────────┐
│ strict mode enabled?     │
│ AND no policy exists?    │──Yes──▶ DENY
└──────────────────────────┘
           │ No
           ▼
┌──────────────────────────┐
│ Policy exists for        │
│ parent model?            │
└──────────────────────────┘
     │           │
    Yes          No
     │           │
     ▼           ▼
┌──────────┐  ┌──────────┐
│ Check    │  │ ALLOW    │
│ policy   │  │ (non-    │
│ method   │  │ strict)  │
└──────────┘  └──────────┘
     │
     ▼
  Policy
  result
```

## Component Responsibilities

### ValidateMediaAccess Middleware

Located at `src/Http/Middleware/ValidateMediaAccess.php`

**Responsibilities:**

1. Validate access type using `MediaAccess::acceptable()`
2. Fetch media by UUID using configured model
3. Check user authorization: `$request->user()->cannot($type, $media)`
4. Attach media to request attributes for controller

### MediaPolicy

Located at `src/Policies/MediaPolicy.php`

**Responsibilities:**

1. Check authentication requirement
2. Verify file exists on disk
3. Handle strict mode logic
4. Delegate to parent model policy

### MediaController

Located at `src/Http/Controllers/MediaController.php`

**Responsibilities:**

1. Retrieve media from request attributes
2. Serve file based on access type:
   - `view`/`stream`: Return with inline Content-Disposition
   - `download`: Return with attachment Content-Disposition

## Key Code Paths

### URL Generation

```php
get_view_media_url($media)
    → get_media_url(MediaAccess::VIEW, $media)
    → route('media', ['type' => 'view', 'uuid' => $media->uuid])
```

### Authorization Check

```php
ValidateMediaAccess::handle()
    → $request->user()->cannot($type, $media)
    → MediaPolicy::view/stream/download()
    → MediaPolicy::canAccess()
    → Gate::allows($mediaAccess->value, $media->model)
    → ParentModelPolicy::view/stream/download()
```

## Next Steps

- [Setting Up Policies](01-policies.md)
- [Strict Mode](02-strict-mode.md)
