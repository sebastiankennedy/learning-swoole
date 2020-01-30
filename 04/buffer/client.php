<?php

require "../../config.php";

go(
    function () {
        // 创建同步客户端
        $client = new Swoole\Client(SWOOLE_TCP);

        // 配置客户端
        $client->set(
            [
                'open_length_check' => true,
                // 设置包长度的类型
                'package_length_type' => 'N',
                // 包头从第几个字节开始计算
                'package_length_offset' => 0,
                // 包体从第几个字节开始计算
                'package_body_offset' => 4,
                // 包头包体合计大小
                'package_max_length' => 1024 * 1024 * 2,
            ]
        );

        // 发送连接请求
        $client->connect(IP_ADDRESS, PORT)
        || exit("Connect failed. Error: {$client->errCode}. \n");

        // 一次性发送大量的数据
        $body = json_encode(str_repeat('a', 1024 * 1024 * 1));
        // length 的值不包含包头，package_body_offset 设置为 4
        $data = pack('N', strlen($body)) . $body;
        $client->send($data);

        // 发送多条数据
        for ($i = 0; $i < 10; $i++) {
            $data = mt_rand(1, 9999999);
            $client->send(pack('N', strlen($data)) . $data);
        }

        // 关闭连接
        $client->close();
    }
);
