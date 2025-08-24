# Changelog

All notable changes to `laravel-media-secure` will be documented in this file.

## Added Media Access Middleware - 2025-08-24

### Release Notes - Laravel Media Secure v3.1.0

#### 🚀 Major Architecture Refactoring

This release introduces a significant architectural improvement that enhances security, maintainability, and follows Laravel best practices by implementing proper separation of concerns through middleware.

#### ✨ New Features

##### 🛡️ ValidateMediaAccess Middleware

- **New dedicated middleware** `ValidateMediaAccess` for handling all media validation and authorization
- **Centralized security logic** that validates media access types, authorizes users, and prepares media data
- **Mandatory middleware** that cannot be bypassed, ensuring consistent security across all media requests
- **Request attribute injection** - media is pre-validated and attached to request attributes for controller use

##### 📋 Enhanced Configuration

- **Comprehensive documentation** with detailed comments explaining each configuration option
- **Mandatory middleware declaration** using full class reference `ValidateMediaAccess::class`
- **Security-focused explanations** highlighting the importance of each setting
- **Usage examples** and best practices included in configuration comments

#### 🔧 Breaking Changes

##### Controller Refactoring

- **Simplified MediaController** - now only handles response generation after middleware validation
- **Removed validation logic** from controller (moved to middleware)
- **Pre-validated media access** - controller retrieves media from `$request->attributes->get('media')`

##### Middleware Configuration

- **New mandatory middleware** `ValidateMediaAccess::class` added to default middleware stack
- **Updated route configuration** to include the new middleware by default
- **Breaking change**: Applications must include the new middleware in their routes

#### 🧪 Testing Improvements

##### Pest PHP Test Suite

- **Converted from PHPUnit to Pest PHP** for modern, readable test syntax
- **Comprehensive middleware testing** covering all validation scenarios:
  - Access type validation (view/download/stream)
  - Authorization checks with proper user permissions
  - Media attribute injection verification
  - Individual media type handling tests
  
- **Database integration tests** with proper Media model creation
- **Gate mocking** for authorization testing

#### 🔒 Security Enhancements

##### Improved Authorization Flow

1. **Media access type validation** - ensures only valid types (view/download/stream) are accepted
2. **Media existence verification** - validates media exists before authorization
3. **User authorization** - checks user permissions via MediaPolicy
4. **Request preparation** - safely injects validated media into request attributes

##### Middleware Security Features

- **Input validation** using `MediaAccess::acceptable()` method
- **404 responses** for non-existent media (via `firstOrFail()`)
- **403 responses** for unauthorized access attempts
- **422 responses** for invalid media access types

#### 📁 File Structure Changes

##### New Files

```
src/Http/Middleware/ValidateMediaAccess.php  # New middleware class
tests/Feature/MediaMiddlewareTest.php        # Comprehensive Pest tests

```
##### Modified Files

```
src/Http/Controllers/MediaController.php     # Simplified controller logic
config/laravel-media-secure.php            # Enhanced documentation
routes/web.php                             # Updated middleware stack

```
#### 🛠️ Migration Guide

##### For Existing Applications

1. **Update your routes** to include the new middleware:

```php
// Before
Route::get('media/{type}/{uuid}', MediaController::class)
    ->middleware(['auth', 'verified']);

// After  
Route::get('media/{type}/{uuid}', MediaController::class)
    ->middleware(['auth', 'verified', ValidateMediaAccess::class]);

```
2. **Register the middleware** in your `app/Http/Kernel.php` if using custom route definitions:

```php
protected $routeMiddleware = [
    // ... other middleware
    'validate-media-access' => \CleaniqueCoders\LaravelMediaSecure\Http\Middleware\ValidateMediaAccess::class,
];

```
3. **Update configuration** by republishing the config file:

```bash
php artisan vendor:publish --provider="CleaniqueCoders\LaravelMediaSecure\LaravelMediaSecureServiceProvider" --tag="config" --force

```
#### 📈 Performance Improvements

- **Single media query** - middleware fetches media once and reuses it
- **Eliminated duplicate validation** - all validation happens in middleware
- **Streamlined controller logic** - faster response generation

#### 🐛 Bug Fixes

- **Fixed database constraints** in tests by providing all required Media model fields
- **Resolved Mockery conflicts** by using direct model creation instead of complex mocking
- **Improved error handling** with proper HTTP status codes

#### 📚 Documentation Updates

- **Enhanced README** with updated usage examples
- **Comprehensive config comments** explaining security implications
- **Updated CHANGELOG** with detailed migration instructions
- **Added middleware documentation** with best practices

#### 🔮 Future Compatibility

This refactoring provides a solid foundation for:

- **Custom validation rules** - easily extendable middleware
- **Additional media types** - framework ready for new access patterns
- **Advanced authorization** - pluggable authorization strategies
- **Performance optimizations** - cacheable validation results


---

#### 📋 Summary

This release represents a major architectural improvement that:

- ✅ **Enhances security** through dedicated middleware validation
- ✅ **Improves maintainability** with proper separation of concerns
- ✅ **Follows Laravel conventions** using middleware for request preprocessing
- ✅ **Provides comprehensive testing** with modern Pest PHP test suite
- ✅ **Maintains backward compatibility** for most use cases (with middleware addition)

The refactoring ensures that Laravel Media Secure continues to provide robust, secure media access control while following modern Laravel development practices.

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
