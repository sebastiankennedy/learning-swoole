<?php

require '../config.php';

$server = stream_socket_server('tcp://' . HOST . ':' . PORT);

while (true) {
    echo '监听连接，阻塞状态' . PHP_EOL;
    $client = @stream_socket_accept($server);
    echo '获取连接，结束阻塞' . PHP_EOL;

    $data = fread($client, 65535);
    echo $data . PHP_EOL;

    fwrite($client, 'Hello, Client.');
    fclose($client);
}