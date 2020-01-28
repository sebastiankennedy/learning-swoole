<?php

require '../../config.php';

// 创建 Server 实例
$server = new Swoole\Server(HOST, PORT);

// 设置
$server->set(
    [
        // 一个连接 10 秒内没有发送任何数据，强制关闭连接
        'heartbeat_idle_time' => 10,
        // 3 秒遍历一次客户端连接
        'heartbeat_check_interval' => 3,
    ]
);

// 监听连接事件
$server->on(
    'connect',
    function ($server, $fd) {
        echo "编号为 {$fd} 的客户端已经连接。" . PHP_EOL;
    }
);

// 监听数据接收事件
$server->on(
    'receive',
    function ($server, $fd, $fromId, $data) {
        echo "编号为 {$fd} 的客户端发送数据：" . $data . PHP_EOL;
        $server->send($fd, 'Hello, I am Your Father.');
    }
);

// 监听关闭事件
$server->on(
    'close',
    function ($server, $fd) {
        echo "编号为 {$fd} 的客户端已经关闭。" . PHP_EOL;
    }
);

// 启动服务端
$server->start();