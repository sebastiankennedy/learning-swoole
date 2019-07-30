<?php

require "../../config.php";

// 创建异步客户端
$client = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

// 监听连接事件
$client->on('connect', function ($client) {
    $client->send('Hello Sebastian.');
});

// 监听接收事件
$client->on('receive', function ($client, $data) {
    echo $data . PHP_EOL;
});

// 监听错误事件
$client->on('error', function ($client) {
    echo "Connect failed. \n";
});

// 监听关闭事件
$client->on('close', function ($client) {
    echo "Connection close. \n";
});

$client->connect(IP_ADDRESS, PORT)
|| exit("connect failed. Error: {$client->errCode}");

// 使用定时器发送心跳包
swoole_timer_tick(3000, function () use ($client) {
    $client->send('HeartBeat');
});