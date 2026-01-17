# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Run a single test file
./vendor/bin/pest tests/Feature/MediaMiddlewareTest.php

# Run a specific test
./vendor/bin/pest --filter "test name"

# Static analysis (PHPStan level 4)
composer analyse

# Code formatting (Laravel Pint)
composer format

# Rector (automated refactoring)
composer rector
```

## Architecture

This is a Laravel package that provides secure media access for files managed by Spatie's Laravel MediaLibrary. It adds authentication and policy-based authorization to media file access.

### Request Flow

1. Request hits route: `GET /media/{type}/{uuid}` where type is `view`, `stream`, or `download`
2. Middleware stack processes request (configurable, default: `auth`, `verified`, `ValidateMediaAccess`)
3. `ValidateMediaAccess` middleware:
   - Validates the access type using `MediaAccess` enum
   - Fetches media by UUID
   - Checks authorization via `MediaPolicy`
   - Attaches media to request attributes
4. `MediaController` serves the file based on access type

### Key Components

- **`MediaAccess` enum** (`src/Enums/MediaAccess.php`): Defines three access types: VIEW, STREAM, DOWNLOAD
- **`MediaPolicy`** (`src/Policies/MediaPolicy.php`): Core authorization logic that delegates to parent model policies when `strict` mode is enabled
- **`ValidateMediaAccess` middleware** (`src/Http/Middleware/ValidateMediaAccess.php`): Validates requests and performs authorization checks
- **Helper functions** (`support/helpers.php`): `get_view_media_url()`, `get_download_media_url()`, `get_stream_media_url()`

### Configuration

Config file: `config/laravel-media-secure.php`

Key settings:

- `require_auth`: When true, users must be authenticated (env: `LARAVEL_MEDIA_SECURE_REQUIRE_AUTH`)
- `strict`: When true, parent model must have a policy with `view`, `stream`, `download` methods (env: `LARAVEL_MEDIA_SECURE_STRICT`)
- `middleware`: Customizable middleware stack for media routes

### Policy Delegation

When `strict` mode is enabled, `MediaPolicy` delegates authorization to the parent model's policy. The parent model (e.g., Document, Post) must have a policy with `view`, `stream`, and `download` methods that accept the user and model instance.

## Testing

Uses Pest with Orchestra Testbench. Test helpers in `tests/Pest.php`:

- `login()`: Authenticate as a user
- `user()`: Create/retrieve test user
- `media()`: Create test media attached to a user
