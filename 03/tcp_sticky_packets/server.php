<?php

require "../../config.php";

// 创建服务端
$server = new Swoole\Server(HOST, PORT);

// 服务端配置
$server->set(
    [
        'worker_num' => 1,
        // 打开 EOF 检测
        'open_eof_check' => true,
        // 启用 EOF 自动分包
        'open_eof_split' => true,
        // 设置 EOF 字符串
        'package_eof' => CUSTOM_EOF,
    ]
);

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
        $item = str_replace(CUSTOM_EOF, '', $data);
        echo $item . PHP_EOL;
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
