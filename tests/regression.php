<?php

declare(strict_types=1);

/**
 * @package     Localzet Cron
 * @link        https://github.com/localzet/Cron
 *
 * @author      Ivan Zorin <creator@localzet.com>
 * @copyright   Copyright © 2016-2026 Localzet Group. All rights reserved.
 * @license     https://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License v3.0
 *
 *              This program is free software: you can redistribute it and/or modify
 *              it under the terms of the GNU Affero General Public License as published
 *              by the Free Software Foundation, either version 3 of the License, or
 *              (at your option) any later version.
 *
 *              This program is distributed in the hope that it will be useful,
 *              but WITHOUT ANY WARRANTY; without even the implied warranty of
 *              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *              GNU Affero General Public License for more details.
 *
 *              You should have received a copy of the GNU Affero General Public License
 *              along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 *              For any questions, please contact <creator@localzet.com>
*/

require dirname(__DIR__) . '/vendor/autoload.php';
$parser = new localzet\CronParser();
foreach (['*/0 * * * * *', '0 */00 * * * *', '* * */-1 * * *', '* * * */0 * *', '* * * * */0 *', '* * * * * */0', '*/1/2 * * * * *'] as $rule) {
    if ($parser->isValid($rule)) {
        throw new RuntimeException("Invalid step accepted: $rule");
    }
    try {
        $parser->parse($rule, 0);
        throw new RuntimeException('Expected invalid rule rejection.');
    } catch (InvalidArgumentException) {
        echo "OK: rejects $rule\n";
    }
}
date_default_timezone_set('UTC');
$start = strtotime('2026-10-05 12:00:00 UTC');
if ($parser->parse('*/15 * * * * *', $start) !== [$start, $start + 15, $start + 30, $start + 45]) {
    throw new RuntimeException('Valid second steps changed.');
}
if ($parser->parse('0,30 * * * * *', $start) !== [$start, $start + 30]) {
    throw new RuntimeException('Valid second lists changed.');
}
echo "Cron parser regression passed.\n";

if (class_exists(localzet\Server\Events\Select::class)) {
    $loop = new localzet\Server\Events\Select();
    localzet\Timer::init($loop);
    $fired = false;
    localzet\Timer::add(0.001, static function () use (&$fired): void {
        $fired = true;
    }, null, false);
    $loop->run();
    if (!$fired) {
        throw new RuntimeException('Server 7 timer callback did not fire.');
    }
    echo "Server 7 timer integration passed.\n";
}
