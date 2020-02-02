<?php

namespace NetworkIoModels\Blocking;

/**
 * Class Worker
 *
 * @package Network\Blocking
 */
class Worker
{
    /**
     * @var null
     */
    public $onStart = null;
    /**
     * @var null
     */
    public $onReceive = null;
    /**
     * @var null
     */
    public $onConnect = null;
    /**
     * @var null
     */
    public $onClose = null;
    /**
     * @var false|resource|null
     */
    public $socket = null;

    /**
     * Worker constructor.
     *
     * @param $socket_address
     */
    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
    }

    /**
     * 启动服务
     */
    public function start()
    {
        if (is_callable($this->onStart)) {
            ($this->onStart)($this->socket);
        }
        $this->accept();
    }

    /**
     * 监听连接
     */
    public function accept()
    {
        while (true) {
            // 阻塞监听客户端连接（blocking receive）
            $client = @stream_socket_accept($this->socket);

            // 如果文件指针无效，将会导致 feof() 函数陷入无限循环当中
            if ($client) {
                // 回调客户端连接成功函数
                if (is_callable($this->onConnect)) {
                    ($this->onConnect)($this, $client);
                }

                // TCP 协议可能合并发送数据
                $data = '';
                while (!feof($client)) {
                    // 每次最多读取 1024 * 1024 * 2 个字节，即 2 M
                    $stream = fread($client, 1024 * 1024 * 2);
                    $data = $data . $stream;

                    // 回调服务端接受数据成功函数
                    if (is_callable($this->onReceive)) {
                        ($this->onReceive)($this, $client, $stream);
                    }
                }

                // 回调服务端关闭连接函数
                if (is_callable($this->onClose)) {
                    ($this->onClose)($this, $client);
                }

                // 关闭客户端连接
                fclose($client);
            }
        }
    }

    /**
     * 发送数据
     *
     * @param $client
     * @param $data
     */
    public function send($client, $data)
    {
        fwrite($client, $data);
    }
}