# Localzet Cron

[English documentation](README.md)

Планирование задач через таймеры Localzet Server, выражения с пятью и шестью полями.

## Состояние и совместимость

Тесты отклоняют нулевые, отрицательные и некорректные шаги. Календарная семантика cron, часовые пояса/DST и запуск планировщика посреди минуты требуют отдельных проверок.

Эта версия рассчитана на Localzet Server 7.x.

## Зависимости

- `php`: `^8.1`
- `localzet/server`: `^7.0`

## Установка

```sh
composer require localzet/cron
```

## Проверки разработки

```sh
composer validate --strict
composer install
composer dump-autoload --optimize --strict-psr
composer lint
composer test
```

Установка, lint и автозагрузка не подтверждают сквозное поведение или готовность к эксплуатации.

[Исторические примеры](docs/legacy-readme.md) нужно сверять с текущим API.

## Автор и лицензия

Ivan Zorin (`localzet`), <creator@localzet.com>, https://www.localzet.com.
Source: https://github.com/localzet/Cron. AGPL-3.0-or-later; [LICENSE](LICENSE). Сохраняются исходные уведомления авторов и лицензии сторонних компонентов.

[Authors](.github/AUTHORS.md) · [Contributing](.github/CONTRIBUTING.md) · [Security](.github/SECURITY.md)
