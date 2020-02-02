<?php

require __DIR__ . '/../../../../config.php';
require __DIR__ . '/../../vendor/autoload.php';

use NetworkIoModels\NonBlocking\Worker;

$server = new Worker(TCP_HOST_PORT);

$server->onStart = function () {
    echo '服务端进程开始运行，监听地址端口：' . TCP_HOST_PORT . PHP_EOL;
};

$server->onConnect = function ($server, $client) {
    echo '新的客户端连接：' . stream_socket_get_name($client, true) . PHP_EOL;
};

$server->onReceive = function ($server, $client, $data) {
    if ($data) {
        echo '客户端 ' . stream_socket_get_name($client, true) . ' 发送数据：' . $data . PHP_EOL;
        $server->send($client, 'Hello, Client.');
    }
};

$server->onClose = function ($server, $client) {
    echo '关闭客户端连接' . stream_socket_get_name($client, true) . PHP_EOL;
};

$server->start();
