<?php

require "../../config.php";

// 创建服务端
$server = new Swoole\Server(HOST, PORT);

// 监听网络连接
$server->on(
    'connect',
    function ($server, $fd) {
        echo "有客户端连接，套接字描述符是：{$fd}" . PHP_EOL;
    }
);

// 监听数据接收
$server->on(
    'receive',
    function ($server, $fd, $reactorId, $data) {
        echo $data . PHP_EOL;
    }
);

// 监听连接断开
$server->on(
    'close',
    function ($server, $fd) {
        echo "有客户端断开，套接字描述符是：{$fd}" . PHP_EOL;
    }
);

$server->start();
