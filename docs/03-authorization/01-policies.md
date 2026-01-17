# Setting Up Policies

This guide covers creating policies for parent models to control media access.

## Why Policies?

Spatie MediaLibrary uses polymorphic relationships. Media files are attached to
parent models (e.g., `Document`, `Post`, `User`). This package delegates
authorization to the parent model's policy for fine-grained control.

## Required Policy Methods

Your parent model policy must define these methods:

| Method     | Purpose                              |
|------------|--------------------------------------|
| `view`     | Allow viewing/displaying media inline|
| `stream`   | Allow streaming media                |
| `download` | Allow downloading media              |

## Creating a Policy

### Step 1: Generate the Policy

```bash
php artisan make:policy DocumentPolicy --model=Document
```

### Step 2: Define Access Methods

```php
namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine if the user can view the document's media.
     */
    public function view(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    /**
     * Determine if the user can stream the document's media.
     */
    public function stream(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    /**
     * Determine if the user can download the document's media.
     */
    public function download(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }
}
```

### Step 3: Register the Policy

In `App\Providers\AuthServiceProvider`:

```php
protected $policies = [
    \App\Models\Document::class => \App\Policies\DocumentPolicy::class,
];
```

## Policy Examples

### Owner-Only Access

```php
public function view(User $user, Document $document): bool
{
    return $user->id === $document->user_id;
}
```

### Role-Based Access

```php
public function view(User $user, Document $document): bool
{
    return $user->hasRole('admin') || $user->id === $document->user_id;
}
```

### Team-Based Access

```php
public function view(User $user, Document $document): bool
{
    return $user->team_id === $document->team_id;
}
```

### Different Rules per Access Type

```php
public function view(User $user, Document $document): bool
{
    // Anyone in the organization can view
    return $user->organization_id === $document->organization_id;
}

public function download(User $user, Document $document): bool
{
    // Only owners can download
    return $user->id === $document->user_id;
}

public function stream(User $user, Document $document): bool
{
    // Only premium users can stream
    return $user->isPremium() && $user->organization_id === $document->organization_id;
}
```

## Next Steps

- [Strict Mode](02-strict-mode.md)
- [Access Control Flow](03-access-flow.md)
