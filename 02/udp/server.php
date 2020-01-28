<?php

require '../../config.php';

// 创建 UDP Server 实例
$server = new Swoole\Server(HOST, PORT, SWOOLE_BASE, SWOOLE_UDP);

$server->on(
    'start',
    function ($server) {
        echo '服务端正在监听 ' . HOST . ':' . PORT . ' 端口' . PHP_EOL;
    }
);

// 监听数据接收
$server->on(
    'packet',
    function ($server, $data, $clientInfo) {
        echo '客户端 ' . $clientInfo['address'] . ':' . $clientInfo['port'] . ' 发送信息如下：' . PHP_EOL;
        echo $data . PHP_EOL;

        // 向客户端发送数据
        $server->sendto(
            $clientInfo['address'],
            $clientInfo['port'],
            'Hello, ' . $clientInfo['address'] . ':' . $clientInfo['port'] . ', I am Your Father.' . PHP_EOL
        );
    }
);

// 启动服务端
$server->start();