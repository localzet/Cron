# Localzet Cron

[Русская документация](README.ru.md)

Timer-based cron scheduling for Localzet Server with five- and six-field expressions.

## Status and compatibility

Regression tests reject zero, negative and malformed steps. Full cron calendar semantics, timezone/DST behavior and scheduling from the middle of a minute still need dedicated coverage.

The current branch targets Localzet Server 7.x; the published stable `v1.0.3` requires Server `^4.5`.

## Dependencies

- `php`: `^8.1`
- `localzet/server`: `^7.0`

## Installation

```sh
composer require localzet/cron:dev-main
```

Use the current `dev-main` branch for Server 7. This is not a stable release; do not pin it in production before a compatible tag is published.

## Development checks

```sh
composer validate --strict
composer install
composer dump-autoload --optimize --strict-psr
composer lint
composer test
```

Installation, lint and autoload checks do not establish end-to-end behavior or production readiness.

[Historical usage notes](docs/legacy-readme.md) need verification against the current API.

## Author and license

Ivan Zorin (`localzet`), <creator@localzet.com>, https://www.localzet.com.
Source: https://github.com/localzet/Cron. AGPL-3.0-or-later; [LICENSE](LICENSE). Original copyright and third-party licenses remain applicable.

[Authors](.github/AUTHORS.md) · [Contributing](.github/CONTRIBUTING.md) · [Security](.github/SECURITY.md)
