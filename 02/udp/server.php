<?php

require '../../config.php';

// 创建 UDP Server 实例，监听 0.0.0.0:9190 端口
$server = new Swoole\Server(HOST, PORT, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

$server->on('packet', function ($server, $data, $clientInfo) {
    $server->sendto($clientInfo['address'], $clientInfo['port'], '服务端回声：' . $data . PHP_EOL);
});

// 启动服务端
$server->start();