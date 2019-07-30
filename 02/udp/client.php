<?php

require "../../config.php";

// 创建同步客户端
$client = new Swoole\Client(SWOOLE_SOCK_UDP);

// 发送数据
$client->sendto(IP_ADDRESS, PORT, 'I am Client.');

// 接受消息，倘若服务端不发送消息，会一直阻塞，不会关闭连接
echo $client->recv();

// 关闭连接
$client->close();
