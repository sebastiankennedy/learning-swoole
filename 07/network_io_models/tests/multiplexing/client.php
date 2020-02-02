<?php

require __DIR__ . '/../../../../config.php';

// 创建客户端连接
if (($socket = stream_socket_client(TCP_ID_ADDRESS_PORT, $errNo, $errString)) === false) {
    throw new Exception('Could not create client socket: ' . $errNo . ' - ' . $errString);
}

// 设置非阻塞
stream_set_blocking($socket, false);

// 发送数据
echo '开始发送数据' . date('H:i:s', time()) . PHP_EOL;
stream_socket_sendto($socket, 'Hello, Server.', STREAM_OOB);
echo '结束发送数据' . date('H:i:s', time()) . PHP_EOL;

$boolean = true;
while ($boolean) {
    sleep(1);

    // 读取数据
    echo '开始读取数据' . date('H:i:s', time()) . PHP_EOL;
    $data = stream_socket_recvfrom($socket, 1024 * 1024 * 2);
    echo '开始读取数据' . date('H:i:s', time()) . PHP_EOL;

    if ($data) {
        print_r($data);
        $boolean = false;
    }
}

fclose($socket);


