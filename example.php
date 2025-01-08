<?php

use localzet\Cron;
use localzet\Server;

require __DIR__ . '/vendor/autoload.php';

$server = new Server;
$server->onServerStart = fn() => new Cron('* * * * * *', function () {
    echo date('Y-m-d H:i:s') . "\n";
});

$server::runAll();