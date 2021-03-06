<?php

require '../../config.php';

// 创建客户端
$client = new Swoole\Client(SWOOLE_SOCK_TCP);

// 连接服务端
$client->connect(IP_ADDRESS, PORT);

// 约定结束符
$string = '\r\n';

// 一次性发送多条数据 - 模拟粘包
for ($i = 0; $i < 20; $i++) {
    $client->send('654321');
}

// 使用结束符一次性发送多条数据 - 约定结束符分割粘包
for ($i = 0; $i < 20; $i++) {
    $client->send('123456' . $string);
}

$client->close();