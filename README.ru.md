# Localzet Cron

[English documentation](README.md)

Планирование задач через таймеры Localzet Server, выражения с пятью и шестью полями.

## Состояние и совместимость

Парсер поддерживает расписания из пяти полей с точностью до минуты и из шести полей с точностью до секунды. Секунды выравниваются по часам, а прошедшие значения текущей минуты пропускаются. Календарные граничные случаи, часовые пояса/DST и восстановление пропущенных запусков пока вне области библиотеки.

Текущая ветка рассчитана на Localzet Server 7.x. Опубликованные релизы `v1.x` требуют Server `^4.5`; эта линия предназначена для Server 7.

## Зависимости

- `php`: `^8.1`
- `localzet/server`: `^7.0`

## Установка

```sh
composer require localzet/cron:^2.0
```

Используйте линию релизов 2.x с Server 7. Линия 1.x рассчитана на Server 4.

## Использование

Зарегистрируйте задачу при запуске приложения, которое уже работает под Localzet Server:

```php
use localzet\Cron;

new Cron('*/15 * * * * *', static function (): void {
    refreshCache();
}, 'refresh-cache');
```

Пять полей задают точность до минуты, шесть — до секунды. Callback выполняется в event loop Server, поэтому не блокируйте его: длительную работу передавайте в очередь или отдельный процесс.

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
