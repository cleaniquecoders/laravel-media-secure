# Securely display Media

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cleaniquecoders/laravel-media-secure.svg?style=flat-square)](https://packagist.org/packages/cleaniquecoders/laravel-media-secure) [![Fix PHP code style issues](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/fix-styling.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/fix-styling.yml) [![PHPStan](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/phpstan.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/phpstan.yml) [![Rector CI](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/rector.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/rector.yml) [![run-tests](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/run-tests.yml) [![Update Changelog](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/update-changelog.yml/badge.svg)](https://github.com/cleaniquecoders/laravel-media-secure/actions/workflows/update-changelog.yml) [![Total Downloads](https://img.shields.io/packagist/dt/cleaniquecoders/laravel-media-secure.svg?style=flat-square)](https://packagist.org/packages/cleaniquecoders/laravel-media-secure)

You have documents, but want to limit the access to only logged in users, and also have some other sophisticated / complex rules in order to access the documents, then this package is for you.

This package will securely display or download to your media files.

## Installation

You can install the package via composer:

```bash
composer require cleaniquecoders/laravel-media-secure
```

Add the following in your route file:

```php
use CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecure;

LaravelMediaSecure::routes();
```

Then add the following in your `app/Providers/AuthServiceProvider.php`:

```php
/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->policies[config('laravel-media-secure.model')] = config('laravel-media-secure.policy');
}
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="media-secure-config"
```

By default, all media required logged in user. No guest account.

To add more restriction to all your media, you can update the middleware used:

```php
/**
* Middleware want to apply to the media route.
*/
'middleware' => [
    'auth', 'verified',
],
```

In case you want more control on who are able to access to the media, you can use the [Laravel Policy](https://laravel.com/docs/9.x/authorization#creating-policies). You just need to define the policy, then it's done. This package will use the policy to handle more sophisticated and complex rules accessing to your media files.

## Usage

Make sure you are using [Laravel Medialibrary](https://spatie.be/docs/laravel-medialibrary/v10/introduction) package.

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
