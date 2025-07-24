# Secure Your Media Access

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cleaniquecoders/laravel-media-secure.svg?style=flat-square)](https://packagist.org/packages/cleaniquecoders/laravel-media-secure) [![Fix PHP code style issues](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/fix-styling.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/fix-styling.yml) [![PHPStan](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/phpstan.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/phpstan.yml) [![Rector CI](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/rector.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/rector.yml) [![run-tests](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/run-tests.yml) [![Update Changelog](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/update-changelog.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/update-changelog.yml) [![Total Downloads](https://img.shields.io/packagist/dt/cleaniquecoders/laravel-media-secure.svg?style=flat-square)](https://packagist.org/packages/cleaniquecoders/laravel-media-secure)

You have documents, but want to limit the access to only logged in users, and also have some other sophisticated / complex rules in order to access the documents, then this package is for you.

This package will securely display or download to your media files.

## Installation

You can install the package via composer:

```bash
composer require cleaniquecoders/laravel-media-secure
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="media-secure-config"
```

## Usage

In case you want more control on who are able to access to the media, you can use the [Laravel Policy](https://laravel.com/docs/12.x/authorization#creating-policies). You just need to define the policy, then it's done. This package will use the policy to handle more sophisticated and complex rules accessing to your media files.

Make sure you are using [Laravel Medialibrary](https://spatie.be/docs/laravel-medialibrary/v10/introduction) package.

When the `require_auth` configuration is enabled (`'require_auth' => true`), the use who want to access to the media require to login.

When the `strict` configuration is enabled (`'strict' => true`), the **parent model of the media** (`$media->model`) is **required to have its own policy** registered.

This policy **must define** the access methods:

* `view`
* `stream`
* `download`

These methods will be used by `MediaPolicy` to determine whether the user is authorised to access the media.

### Why Is This Required?

Since Spatie's Media Library uses polymorphic relationships, media items are attached to various parent models (e.g., `Document`, `Post`, `User`, etc.).
To enforce fine-grained control, `MediaPolicy` delegates authorisation checks to the parent model’s policy.

### What You Must Do

1. Create a policy for the parent model (e.g., `DocumentPolicy`).
2. Define the following methods in that policy:

   * `view(User $user, Document $document)`
   * `stream(User $user, Document $document)`
   * `download(User $user, Document $document)`

### Example: `DocumentPolicy`

```php
namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    public function stream(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    public function download(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }
}
```

> These methods **must be defined** because `MediaPolicy` uses the value from the `MediaAccess` enum to call `Gate::allows($type, $media->model)`.

### Register the Policy

In your `AuthServiceProvider`:

```php
protected $policies = [
    \App\Models\Document::class => \App\Policies\DocumentPolicy::class,
];
```

### What Happens If No Policy Exists?

| Condition        | Result                                                            |
| ---------------- | ----------------------------------------------------------------- |
| `strict = true`  | Access will be denied if the parent model doesn't have a policy   |
| `strict = false` | Access will be granted without checking the parent model's policy |

### Summary

| Requirement                                  | Mandatory | When                              |
| -------------------------------------------- | --------- | --------------------------------- |
| Parent model has a policy                    | ✅         | When `strict = true`              |
| Defines `view`, `stream`, `download` methods | ✅         | For enum-based access control     |
| Policy registered in `AuthServiceProvider`   | ✅         | Required by Laravel's Gate system |

### Helpers

You upload / add media as documented in Laravel Medialibrary. Then to generate links:

```php
// Get the view URL
// https://your-app.com/media/view/some-random-uuid
$view_url = get_view_media_url($media);

// Get the download URL
// https://your-app.com/media/download/some-random-uuid
$download_url = get_download_media_url($media);

// Get the stream URL
// https://your-app.com/media/stream/some-random-uuid
$stream_url = get_stream_media_url($media);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Nasrul Hazim Bin Mohamad](https://github.com/nasrulhazim)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
