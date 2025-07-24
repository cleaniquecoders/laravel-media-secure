# Changelog

All notable changes to `laravel-media-secure` will be documented in this file.

## Update dependencies and usage setup - 2025-07-24

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/2.2.1...3.0

### Installation

You can install the package via composer:

```bash
composer require cleaniquecoders/laravel-media-secure

```
Publish the config file with:

```bash
php artisan vendor:publish --tag="media-secure-config"

```
### Usage

In case you want more control on who are able to access to the media, you can use the [Laravel Policy](https://laravel.com/docs/12.x/authorization#creating-policies). You just need to define the policy, then it's done. This package will use the policy to handle more sophisticated and complex rules accessing to your media files.

Make sure you are using [Laravel Medialibrary](https://spatie.be/docs/laravel-medialibrary/v10/introduction) package.

When the `require_auth` configuration is enabled (`'require_auth' => true`), the use who want to access to the media require to login.

When the `strict` configuration is enabled (`'strict' => true`), the **parent model of the media** (`$media->model`) is **required to have its own policy** registered.

This policy **must define** the access methods:

* `view`
* `stream`
* `download`

These methods will be used by `MediaPolicy` to determine whether the user is authorised to access the media.

#### Why Is This Required?

Since Spatie's Media Library uses polymorphic relationships, media items are attached to various parent models (e.g., `Document`, `Post`, `User`, etc.).
To enforce fine-grained control, `MediaPolicy` delegates authorisation checks to the parent model’s policy.

#### What You Must Do

1. Create a policy for the parent model (e.g., `DocumentPolicy`).
   
2. Define the following methods in that policy:
   
   * `view(User $user, Document $document)`
   * `stream(User $user, Document $document)`
   * `download(User $user, Document $document)`
   

#### Example: `DocumentPolicy`

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

#### Register the Policy

In your `AuthServiceProvider`:

```php
protected $policies = [
    \App\Models\Document::class => \App\Policies\DocumentPolicy::class,
];

```
#### What Happens If No Policy Exists?

| Condition        | Result                                                            |
| ---------------- | ----------------------------------------------------------------- |
| `strict = true`  | Access will be denied if the parent model doesn't have a policy   |
| `strict = false` | Access will be granted without checking the parent model's policy |

#### Summary

| Requirement                                  | Mandatory | When                              |
| -------------------------------------------- | --------- | --------------------------------- |
| Parent model has a policy                    | ✅         | When `strict = true`              |
| Defines `view`, `stream`, `download` methods | ✅         | For enum-based access control     |
| Policy registered in `AuthServiceProvider`   | ✅         | Required by Laravel's Gate system |

#### Helpers

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
## Update Setup in README - 2025-07-16

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/2.2.0...2.2.1

## Added Laravel 12 and PHP 8.4 Support - 2025-05-01

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/2.1.1...2.2.0

## 2.1.1 - 2024-12-11

- Improve Pest Setup
- Added Rector
- Update dependencies

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/2.1.0...2.1.1

## 2.1.0 - 2024-05-26

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/2.0.0...2.1.0

## Added Laravel 11 Support - 2024-03-21

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/1.2.0...2.0.0

## 1.2.0 - 2024-02-07

### What's Changed

* Update Root Namespace by @nasrulhazim in https://github.com/cleaniquecoders/laravel-media-secure/pull/1

### New Contributors

* @nasrulhazim made their first contribution in https://github.com/cleaniquecoders/laravel-media-secure/pull/1

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/1.1.0...1.2.0

## Added Laravel 10 Support - 2023-02-25

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/1.0.5...1.1.0

## 1.0.5 - 2022-10-25

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/1.0.4...1.0.5

## 1.0.4 - 2022-10-15

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/1.0.3...1.0.4

## 1.0.3 - 2022-10-15

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/1.0.2...1.0.3

## 1.0.2 - 2022-10-15

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/1.0.1...1.0.2

## 1.0.1 - 2022-10-15

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/compare/1.0.0...1.0.1

## 1.0.0 - 2022-10-15

**Full Changelog**: https://github.com/cleaniquecoders/laravel-media-secure/commits/1.0.0
