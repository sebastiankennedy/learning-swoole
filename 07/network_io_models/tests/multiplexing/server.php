<?php

require __DIR__ . '/../../../../config.php';
require __DIR__ . '/../../vendor/autoload.php';

use NetworkIoModels\Multiplexing\Worker;

$server = new Worker(TCP_HOST_PORT);

$server->onStart = function () {
    echo '服务端进程开始运行，监听地址端口：' . TCP_HOST_PORT . PHP_EOL;
};

$server->onConnect = function ($server, $client) {
    echo '新的客户端连接：' . stream_socket_get_name($client, true) . PHP_EOL;
    $response = "HTTP/1.1 200 OK\r\n";
    $response .= "Content-Type: text/html;charset=UTF-8\r\n";
    $response .= "Connection: keep-alive\r\n";
    $response .= "Content-length: " . strlen('Hello, Server.') . "\r\n\r\n";
    $response .= 'Hello, Client.';

    sleep(mt_rand(5, 15));
    stream_socket_sendto($client, $response);
};

$server->onReceive = function ($server, $client, $data) {
    echo '客户端 ' . stream_socket_get_name($client, true) . ' 发送数据：' . $data . PHP_EOL;
    stream_socket_shutdown($client, STREAM_SHUT_RDWR);
};

$server->start();