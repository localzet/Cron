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
$midMinute = strtotime('2026-10-05 12:00:30 UTC');
if ($parser->parse('*/15 * * * * *', $midMinute) !== [$midMinute, $midMinute + 15]) {
    throw new RuntimeException('Six-field schedules must use clock-aligned seconds after the start time.');
}
if ($parser->parse('* * * * *', $midMinute) !== []) {
    throw new RuntimeException('Five-field schedules must not run after the current minute boundary.');
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

    while ((int)date('s') >= 45) {
        usleep(100_000);
    }
    $currentSecond = (int)date('s');
    $rule = implode(',', [$currentSecond + 5, $currentSecond + 10, $currentSecond + 15]) . ' * * * * *';
    $scheduledTimers = new ReflectionProperty(localzet\Cron::class, 'scheduledTimers');
    $scheduledTimers->setAccessible(true);
    $destroyedOccurrences = 0;
    $removedCallbackFired = false;
    $deadlineExceeded = false;
    $deadlineTimer = localzet\Timer::add(20, static function () use (&$deadlineExceeded, $loop): void {
        $deadlineExceeded = true;
        $loop->stop();
    }, null, false);
    $destroyedJob = null;
    $destroyedJob = new localzet\Cron($rule, static function () use (&$destroyedOccurrences, &$destroyedJob, $scheduledTimers, $deadlineTimer, $loop): void {
        ++$destroyedOccurrences;
        $pending = $scheduledTimers->getValue()[$destroyedJob->getId()] ?? [];
        if (count($pending) !== 2) {
            throw new RuntimeException('A fired cron timer was not removed from tracking.');
        }
        localzet\Timer::del($deadlineTimer);
        if (!$destroyedJob->destroy()) {
            throw new RuntimeException('Cron job destruction failed.');
        }
        if (isset($scheduledTimers->getValue()[$destroyedJob->getId()])) {
            throw new RuntimeException('Destroying a cron job left timer IDs tracked.');
        }
        if (localzet\Timer::getAll()['count'] !== 1) {
            throw new RuntimeException('Destroying a cron job did not cancel all remaining timers.');
        }
        $loop->stop();
    });
    $removedJob = new localzet\Cron($rule, static function () use (&$removedCallbackFired): void {
        $removedCallbackFired = true;
    });
    localzet\Timer::add(0.01, static function () use ($removedJob, $scheduledTimers): void {
        $jobTimerCount = count($scheduledTimers->getValue()[$removedJob->getId()] ?? []);
        $timerCount = localzet\Timer::getAll()['count'];
        if (!localzet\Cron::remove($removedJob->getId())) {
            throw new RuntimeException('Cron::remove() failed.');
        }
        if (isset($scheduledTimers->getValue()[$removedJob->getId()])) {
            throw new RuntimeException('Cron::remove() left timer IDs tracked.');
        }
        if (localzet\Timer::getAll()['count'] !== $timerCount - $jobTimerCount) {
            throw new RuntimeException('Cron::remove() did not cancel all future occurrence timers.');
        }
    }, null, false);
    $loop->run();

    if ($deadlineExceeded || $destroyedOccurrences !== 1 || $removedCallbackFired) {
        throw new RuntimeException('Cron job removal did not preserve exactly the first scheduled occurrence.');
    }
    localzet\Timer::delAll();
    echo "Cron job timer cancellation passed.\n";
}
