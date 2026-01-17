# Installation

This guide covers the installation and initial setup of Laravel Media Secure.

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x
- [Spatie Laravel MediaLibrary][medialibrary] 10.5+ or 11.5+

[medialibrary]: https://spatie.be/docs/laravel-medialibrary

## Install via Composer

```bash
composer require cleaniquecoders/laravel-media-secure
```

## Publish Configuration

Publish the configuration file to customize the package behavior:

```bash
php artisan vendor:publish --tag="media-secure-config"
```

This creates `config/laravel-media-secure.php` with all available options.

## Verify Installation

The package automatically registers:

- Routes at `/media/{type}/{uuid}`
- `MediaPolicy` for the Media model
- `validate-media-access` middleware alias

## Next Steps

- [Basic Usage](02-basic-usage.md)
- [Configuration Options](../02-configuration/01-options.md)
