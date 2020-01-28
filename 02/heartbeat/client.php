<?php

require "../../config.php";

use Swoole\Coroutine\Client;

go(
    function () {
        $client = new Client(SWOOLE_TCP);

        $client->connect(IP_ADDRESS, PORT);

        $client->send('Hello World');

        while (true) {
            sleep(3);

            $data = $client->recv();

            if ($data) {
                echo '服务端发送数据为：' . $data . PHP_EOL;
            }
            swoole_timer_after(
                3000,
                function () use ($client) {
                    $client->send('维持心跳');
                }
            );
        }

        $client->close();
    }
);