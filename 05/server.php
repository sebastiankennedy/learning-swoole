<?php

require '../config.php';

$server = new Swoole\Server(HOST, PORT);

$server->set(
    [
        'worker_num' => 1,
    ]
);

$server->on(
    'start',
    function () {
    }
);

$server->on(
    'managerStart',
    function () {
    }
);

$server->on(
    'workerStart',
    function () {
    }
);

$server->on(
    'task',
    function () {
    }
);

$server->on(
    'receive',
    function () {
    }
);

$server->start();