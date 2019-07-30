<?php

require '../config.php';

// 创建 Server 实例，监听 0.0.0.0:9190 端口
$server = new Swoole\Server(HOST, PORT);

// 监听连接事件
$server->on('connect', function ($server, $fd) {
    echo '有新的客户端连接，连接标识为' . $fd . PHP_EOL;
});

// 监听数据接收事件
$server->on('receive', function ($server, $fd, $fromId, $data) {
    sleep(3);
    $server->send($fd, "服务端回声：" . $data . PHP_EOL);
});

// 监听关闭事件
$server->on('close', function ($server, $fd) {
    echo "编号为{$fd}的客户端已经关闭" . PHP_EOL;
});

// 启动服务端
$server->start();