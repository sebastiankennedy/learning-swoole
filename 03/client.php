<?php

require "../config.php";

// 创建同步客户端
$client = new Swoole\Client(SWOOLE_TCP, SWOOLE_ASYNC);

// 配置包头包体
$client->set([
    'open_length_check' => 1,
    // 设置包头的长度
    'package_length_type' => 'N',
    // 包长度从哪里开始计算
    'package_length_offset' => 0,
    // 包体从第几个字节开始计算
    'package_body_offset' => 4,
    'package_max_length' => 1024 * 1024 * 3,
    // IO 缓冲区的大小
    'buffer_output_size' => 1024 * 1024 * 6,
]);

// 发送连接请求
$client->connect(IP_ADDRESS, PORT)
|| exit("Connect failed. Error: {$client->errCode}. \n");

// 设置包体
$body = json_encode(str_repeat('a', 1024 * 1024 * 2));
$header = pack("N", strlen($body));
$content = $header . $body;

$client->send($content);

// 关闭连接
$client->close();
