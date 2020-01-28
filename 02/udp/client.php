<?php

error_reporting(E_ERROR);

require '../../config.php';

// 创建同步客户端
$client = new Swoole\Client(SWOOLE_SOCK_UDP);

// 发送数据
$client->sendto(IP_ADDRESS, PORT, 'Hello Server, I am a Udp Sync Client.') || exit('客户端发送信息失败');

// 接受消息
$bool = true;
while ($bool) {
    sleep(1);
    $data = $client->recv(65535, Swoole\Client::MSG_WAITALL);

    if ($data) {
        echo $data . PHP_EOL;
        $bool = false;
    } else {
        echo swoole_strerror(swoole_last_error()) . PHP_EOL;
    }
}

// 关闭连接
$client->close();
