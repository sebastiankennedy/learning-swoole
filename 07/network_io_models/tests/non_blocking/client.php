<?php

require __DIR__ . '/../../../../config.php';
require __DIR__ . '/../../../../helper.php';

$client = stream_socket_client(TCP_ID_ADDRESS_PORT);
stream_set_blocking($client, false);

if ($client) {
    $data = null;
    $times = 1;

    for ($i = 0; $i < 10; $i++) {
        // 非阻塞发送（non-blocking send）
        echo '开始发送数据' . date('H:i:s', time()) . PHP_EOL;
        fwrite($client, 'Hello, Worker. This is the ' . ($i + 1) . ' time!');
        echo '结束发送数据' . date('H:i:s', time()) . PHP_EOL;

        // 非阻塞读取（non-blocking receive）
        echo '开始读取数据' . date('H:i:s', time()) . PHP_EOL;
        $stream = fread($client, 1024 * 1024 * 2);
        echo '结束读取数据' . date('H:i:s', time()) . PHP_EOL;

        if ($stream) {
            $data = $data . $stream;
            echo '服务端第 ' . $times . ' 次' . stream_socket_get_name($client, true) . ' 发送数据：' . $stream . PHP_EOL;
            $times++;
        }
        sleep(1);
    }

    $start = time();
    // 设置 10 秒超时
    $timeout = 10;
    while (!feof($client) && (time() - $start) < $timeout) {
        // 非阻塞读取（non-blocking receive）
        echo '开始读取数据' . date('H:i:s', time()) . PHP_EOL;
        $stream = fread($client, 1024 * 1024 * 2);
        echo '结束读取数据' . date('H:i:s', time()) . PHP_EOL;

        if ($stream) {
            $data = $data . $stream;
            echo '服务端第 ' . $times . ' 次' . stream_socket_get_name($client, true) . ' 发送数据：' . $stream . PHP_EOL;
            $times++;
        }
        sleep(1);
    }


    fclose($client);
}