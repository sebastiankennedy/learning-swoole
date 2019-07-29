<?php

require "../config.php";

// 创建同步客户端
$client = new Swoole\Client(SWOOLE_TCP, SWOOLE_ASYNC);

// 发送连接请求
$client->connect(IP_ADDRESS, PORT)
|| exit("Connect failed. Error: {$client->errCode}. \n");

// 向服务端发送数据
$client->send("Hello World");

// 从服务端接收数据
$response = $client->recv();

// 输出数据
echo $response . PHP_EOL;

// 关闭连接
$client->close();
