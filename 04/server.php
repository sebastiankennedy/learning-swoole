<?php

require '../config.php';

// 创建 Server 实例
$server = new Swoole\Server(HOST, PORT);

$server->set(
    [
        'worker_num' => 1,
    ]
);

// 监听主进程启动
$server->on(
    'Start',
    function () {
        echo '主进程启动：1' . PHP_EOL;
        // 设置主进程的名称
        swoole_set_process_name('swoole-master-process');
    }
);

$server->on(
    'shutdown',
    function () {
    }
);

// 监听管理进程启动
$server->on(
    'ManagerStart',
    function () {
        echo '管理进程启动：2' . PHP_EOL;
        swoole_set_process_name('swoole-manager-process');
    }
);

// 监听 Worker 进程启动
$server->on(
    'WorkerStart',
    function () {
        echo '工作进程启动：3' . PHP_EOL;
        swoole_set_process_name('swoole-worker-process');
    }
);

// 监听连接事件
$server->on(
    'connect',
    function ($server, $fd) {
        echo '有新的客户端连接，连接标识为' . $fd . PHP_EOL;
    }
);

// 监听数据接收事件
$server->on(
    'receive',
    function ($server, $fd, $fromId, $data) {
        echo "客户端 {$fd} 发送消息：" . $data . PHP_EOL;
    }
);

// 监听关闭事件
$server->on(
    'close',
    function ($server, $fd) {
        echo "编号为{$fd}的客户端已经关闭" . PHP_EOL;
    }
);

// 启动服务端
$server->start();