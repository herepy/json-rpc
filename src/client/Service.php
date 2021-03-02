<?php

namespace rpcClient\client;

class Service
{
    protected $list;
    protected $conn;
    protected $connected = false;
    protected $maxReadSize = 1024;
    protected $err;

    public function __construct($list)
    {
        $this->list = $list;
    }

    protected function connect()
    {
        $service = $this->getService();
        $host = $service["Service"]["Address"];
        $port = $service["Service"]["Port"];

        $this->conn = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        $this->connected = socket_connect($this->conn, $host, $port);

        if ($this->connected === false) {
            throw new \RuntimeException(socket_strerror(socket_last_error($this->conn)));
        }

        return $this->connected;
    }

    public function call(string $method, $params)
    {
        if ($this->connected === false) {
            $this->connect();
        }

        $data = [
            "id"    =>  microtime(true) * 1000,
            "method"=>  $method,
            "params"=>  [$params]
        ];
        $data = json_encode($data);

        if (socket_write($this->conn, $data, strlen($data)) === false) {
            $this->err = socket_strerror(socket_last_error($this->conn));
            return false;
        }

        $result = socket_read($this->conn, $this->maxReadSize);
        if ($result === false) {
            $this->err = socket_strerror(socket_last_error($this->conn));
            return false;
        }

        return $result;
    }

    public function getService()
    {
        if (count($this->list) == 0) {
            throw new \RuntimeException("service list is empty, please try to find service");
        }

        return $this->list[array_rand($this->list)];
    }

    public function getError()
    {
        return $this->err;
    }

    public function __destroy()
    {
        if ($this->connected) {
            socket_close($this->conn);
        }
    }

}