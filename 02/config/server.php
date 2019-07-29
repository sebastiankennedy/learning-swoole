<?php

require "../../config.php";

// 创建服务端
$server = new Swoole\Server(HOST, PORT);

// 服务端配置
$server->set([
    'worker_num' => 1,
    'open_eof_check' => true,
    'package_eof' => '\r\n',
]);

// 监听连接
$server->on('connect', function ($server, $fd) {
    echo "有新的客户端连接，套接字描述符是：{$fd}" . PHP_EOL;
});

// 监听数据接收
$server->on('receive', function ($server, $fd, $reactorId, $data) {
    var_dump($data);
});

// 监听连接断开
$server->on('close', function ($server, $fd) {
    echo "客户端 {$fd} 断开连接" . PHP_EOL;
});

// 服务器开启
$server->start();
