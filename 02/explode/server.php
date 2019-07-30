<?php

require "../../config.php";

// 创建服务端
$server = new Swoole\Server(HOST, PORT);

// 服务端配置
$server->set([
    'worker_num' => 1,
]);

// 约定结束符
$string = '\r\n';

// 监听连接
$server->on('connect', function ($server, $fd) {
    echo "有新的客户端连接，套接字描述符是：{$fd}" . PHP_EOL;
});

// 监听数据接收
$server->on('receive', function ($server, $fd, $reactorId, $data) use ($string) {
    static $count = 1;
    echo '---------------' . $count . '---------------' . PHP_EOL;
    // Client 10 次发送少量数据，Server 多次接收，Client 1 次发送大量数据，Server 多次接收
    $data = explode($string, $data);
    var_dump(array_filter($data));
    echo '---------------' . $count . '---------------' . PHP_EOL;
    $count++;
});

// 监听连接断开
$server->on('close', function ($server, $fd) {
    echo "客户端 {$fd} 断开连接" . PHP_EOL;
});

// 服务器开启
$server->start();
