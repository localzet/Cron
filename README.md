# Localzet Cron

[Русская документация](README.ru.md)

Timer-based cron scheduling for Localzet Server with five- and six-field expressions.

## Status and compatibility

The parser supports five-field minute schedules and six-field second schedules. It aligns second-based rules to clock seconds and skips times that have already passed in the current minute. Calendar edge cases, timezone/DST behavior and missed-run recovery are outside the current scope.

The current branch targets Localzet Server 7.x. Published `v1.x` releases require Server `^4.5`; this is the Server 7 release line.

## Dependencies

- `php`: `^8.1`
- `localzet/server`: `^7.0`

## Installation

```sh
composer require localzet/cron:^2.0
```

Use the 2.x release line with Server 7. The 1.x release line targets Server 4.

## Usage

Register a task while bootstrapping an application that is already running under Localzet Server:

```php
use localzet\Cron;

new Cron('*/15 * * * * *', static function (): void {
    refreshCache();
}, 'refresh-cache');
```

Five-field rules use minute precision. Six-field rules include seconds. Callbacks run on the Server event loop, so keep them non-blocking and hand long-running work to a queue or another process.

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
