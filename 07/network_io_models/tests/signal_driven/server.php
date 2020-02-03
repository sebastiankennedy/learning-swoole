<?php

require __DIR__ . '/../../../../config.php';
require __DIR__ . '/../../vendor/autoload.php';

use NetworkIoModels\SignalDriven\Worker;

$worker = new Worker(TCP_HOST_PORT);

$worker->onStart = function () {
    echo '服务端进程开始运行，监听地址端口：' . TCP_HOST_PORT . PHP_EOL;
    echo '当前网络 IO 模型是：信号驱动' . PHP_EOL;
};

$worker->onConnect = function ($server, $client) {
    echo '新的客户端连接：' . stream_socket_get_name($client, true) . PHP_EOL;
};

$worker->onReceive = function ($server, $client, $data) {
    echo '客户端 ' . stream_socket_get_name($client, true) . ' 发送数据：' . $data . PHP_EOL;
    $server->responseContent($client, 'Hello, Client.');
};

$worker->onClose = function ($server, $client) {
};

$worker->start();