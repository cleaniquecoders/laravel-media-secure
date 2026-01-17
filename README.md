# Laravel Media Secure

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cleaniquecoders/laravel-media-secure.svg?style=flat-square)](https://packagist.org/packages/cleaniquecoders/laravel-media-secure)
[![PHPStan](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/phpstan.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/phpstan.yml)
[![run-tests](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/cleaniquecoders/laravel-media-secure.svg?style=flat-square)](https://packagist.org/packages/cleaniquecoders/laravel-media-secure)

Secure your media file access with authentication and policy-based authorization.
Built on top of [Spatie Laravel MediaLibrary][medialibrary], this package provides
secure view, download, and stream endpoints with fine-grained access control.

[medialibrary]: https://spatie.be/docs/laravel-medialibrary/v11/introduction

## Features

- Secure media URLs with UUID-based routing
- Three access types: **view**, **download**, and **stream**
- Authentication requirement (configurable)
- Policy-based authorization with parent model delegation
- Customizable middleware stack
- Helper functions for URL generation

## Installation

```bash
composer require cleaniquecoders/laravel-media-secure
```

Publish the config file:

```bash
php artisan vendor:publish --tag="media-secure-config"
```

## Quick Start

### 1. Generate Secure URLs

```php
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Get view URL: /media/view/{uuid}
$viewUrl = get_view_media_url($media);

// Get download URL: /media/download/{uuid}
$downloadUrl = get_download_media_url($media);

// Get stream URL: /media/stream/{uuid}
$streamUrl = get_stream_media_url($media);
```

### 2. Create a Policy for Your Model (Required when `strict = true`)

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

### 3. Register the Policy

```php
// In AuthServiceProvider
protected $policies = [
    \App\Models\Document::class => \App\Policies\DocumentPolicy::class,
];
```

## Documentation

For detailed documentation, see [docs/README.md](docs/README.md).

- [Getting Started](docs/01-getting-started/README.md)
- [Configuration](docs/02-configuration/README.md)
- [Authorization](docs/03-authorization/README.md)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report
security vulnerabilities.

## Credits

- [Nasrul Hazim Bin Mohamad](https://github.com/nasrulhazim)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
