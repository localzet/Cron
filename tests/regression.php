<?php

declare(strict_types=1);

/**
 * @package Localzet Cron
 * @link https://github.com/localzet/Cron
 * @author Ivan Zorin <creator@localzet.com>
 * @copyright Copyright (c) 2026 Localzet Group
 * @license https://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License v3.0 or later
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
