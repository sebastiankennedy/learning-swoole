<?php

require __DIR__ . '/../../../../config.php';

$client = stream_socket_client(TCP_ID_ADDRESS_PORT);

if ($client) {
    $data = '';
    for ($i = 1; $i < 11; $i++) {
        // 非阻塞发送（non-blocking send）
        echo '开始发送数据' . date('H:i:s', time()) . PHP_EOL;
        fwrite($client, 'Hello, Worker.This is the ' . $i . ' time.');
        echo '结束发送数据' . date('H:i:s', time()) . PHP_EOL;

        // 阻塞读取（blocking receive）
        echo '开始读取数据' . date('H:i:s', time()) . PHP_EOL;
        $stream = fread($client, 1024 * 1024 * 2);
        echo '结束读取数据' . date('H:i:s', time()) . PHP_EOL;

        $data = $data . $stream;
        echo '服务端 ' . stream_socket_get_name($client, true) . ' 发送数据：' . $stream . PHP_EOL;
        sleep(2);
    }

    fclose($client);
}