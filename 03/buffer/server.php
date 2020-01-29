<?php

require '../../config.php';

// 创建 Server 实例，监听 0.0.0.0:9190 端口
$server = new Swoole\Server(HOST, PORT);

$server->set([
    'worker_num' => 1,
    // 开启包长检测特性
    'open_length_check' => true,
    // 设置包长度的类型
    'package_length_type' => 'N',
    // 包头从第几个字节开始计算
    'package_length_offset' => 0,
    // 包体从第几个字节开始计算
    'package_body_offset' => 4,
    // 包头包体合计大小
    'package_max_length' => 1024 * 1024 * 2,
]);

// 监听连接事件
$server->on('connect', function ($server, $fd) {
    echo '有新的客户端连接，连接标识为' . $fd . PHP_EOL;
});

// 监听数据接收事件
$server->on('receive', function ($server, $fd, $fromId, $data) {
    var_dump('消息长度：', unpack('N', $data));
    var_dump('消息内容', substr($data, 4));
});

// 监听关闭事件
$server->on('close', function ($server, $fd) {
    echo "编号为{$fd}的客户端已经关闭" . PHP_EOL;
});

// 启动服务端
$server->start();