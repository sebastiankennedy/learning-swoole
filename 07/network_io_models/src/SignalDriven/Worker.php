<?php

namespace NetworkIoModels\SignalDriven;

use Closure;

/**
 * Class Worker
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
            $client = stream_socket_accept($this->socket);

            if ($client) {
                // 为当前进程的指定信号安装一个信号处理器，SIGIO 的意思是通知进程指定套接字描述符可以进行 IO 处理
                pcntl_signal(SIGIO, $this->sigioHandler($client));

                // 发送一个信号通知指定进程，posix_getpid() 返回当前进程 ID
                posix_kill(posix_getpid(), SIGIO);

                // 此函数调用每个等待信号的处理器
                pcntl_signal_dispatch();
            }
        }

        stream_socket_shutdown($this->socket, STREAM_SHUT_RDWR);
    }

    /**
     * @param $client
     *
     * @return Closure
     */
    public function sigioHandler($client)
    {
        return function ($sigio) use ($client) {
            if ($client) {
                if (is_callable($this->onConnect)) {
                    ($this->onConnect)($this, $client);
                }
                echo '当前触发信号为' . $sigio . PHP_EOL;

                $data = stream_socket_recvfrom($client, 1024 * 1024 * 2);
                if (is_callable($this->onReceive)) {
                    ($this->onReceive)($this, $client, $data);
                }

                stream_socket_shutdown($client, STREAM_SHUT_RDWR);
                if (is_callable($this->onClose)) {
                    ($this->onClose)($client, $data);
                }
            }
        };
    }

    /**
     * @param $client
     * @param $data
     */
    public function responseContent($client, $data)
    {
        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
        $response .= "Connection: keep-alive\r\n";
        $response .= "Content-length: " . strlen($data) . "\r\n\r\n";
        $response .= $data;

        stream_socket_sendto($client, $response);
    }
}