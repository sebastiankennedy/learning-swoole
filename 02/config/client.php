<?php

require '../../config.php';

// 创建客户端
$client = new Swoole\Client(SWOOLE_SOCK_TCP);

// 连接服务端
$client->connect(IP_ADDRESS, PORT);

// 一次性发送多条数据
for ($i = 0; $i < 10; $i++) {
    $client->send('123456');
}
