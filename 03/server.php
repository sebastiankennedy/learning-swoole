<?php
require '../config.php';

// 创建 Server 实例，监听 0.0.0.0:9190 端口
$server = new Swoole\Server(HOST, PORT);

// 配置包头包体
$server->set([
    'worker_num' => 1,
    'open_length_check' => 1,
    // 设置包头的长度
    'package_length_type' => 'N',
    // 包长度从哪里开始计算
    'package_length_offset' => 0,
    // 包体从第几个字节开始计算
    'package_body_offset' => 4,
    'package_max_length' => 1024 * 1024 * 3,
    // 输出缓冲区的大小
    'buffer_output_size' => 1024 * 1024 * 3,
]);

// 监听连接事件
$server->on('connect', function ($server, $fd) {
    echo '有新的客户端连接，连接标识为' . $fd . PHP_EOL;
});

// 监听数据接收事件
$server->on('receive', function ($server, $fd, $fromId, $data) {
    // 解包
    $data = unpack('N', $data);
    // 包的长度
    var_dump($data);
});

// 监听关闭事件
$server->on('close', function ($server, $fd) {
    echo "编号为{$fd}的客户端已经关闭" . PHP_EOL;
});

// 启动服务端
$server->start();