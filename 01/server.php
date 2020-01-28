<?php

require '../config.php';

// 创建同步 TCP 服务端，监听 0.0.0.0:9501 端口
$server = new Swoole\Server(HOST, PORT, SWOOLE_BASE, SWOOLE_TCP);

// 进程开始运行之后回调
$server->on(
    'start',
    function ($server) {
        echo '服务端正在监听 ' . HOST . ':' . PORT . ' 端口' . PHP_EOL;
    }
);

// 监听连接事件
$server->on(
    'connect',
    function ($server, $fd) {
        echo '有新的客户端连接，连接标识为 ' . $fd . PHP_EOL;
    }
);

// 监听数据接收事件
$server->on(
    'receive',
    function ($server, $fd, $fromId, $data) {
        $server->send($fd, "服务端回声：" . $data . PHP_EOL);
    }
);

// 监听关闭事件
$server->on(
    'close',
    function ($server, $fd) {
        echo "编号为 {$fd} 的客户端已经关闭" . PHP_EOL;
    }
);

// 启动服务
$server->start();