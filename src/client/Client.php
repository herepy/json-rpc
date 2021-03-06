<?php

namespace jsonRpc\client;

use \SensioLabs\Consul\Services\HealthInterface;
use \SensioLabs\Consul\ServiceFactory;

class Client
{
    protected $consul;

    /**
     * Client constructor.
     * @param string $host 服务中心地址
     * @param int $port 服务中心端口
     */
    public function __construct(string $host,int $port)
    {
        $opt = ["base_uri"=>"http://{$host}:{$port}"];
        $this->consul = new ServiceFactory($opt);
    }

    /**
     * @param $name
     * @param null|string $tag
     * @return Service
     */
    public function findService($name,$tag=null)
    {
        /**
         * @var $health HealthInterface
         */
        $health = $this->consul->get(HealthInterface::class);
        $opt = ["passing"=>1];
        if ($tag) {
            $opt["tag"] = $tag;
        }

        /**
         * @var $response \SensioLabs\Consul\ConsulResponse
         */
        $response = $health->service($name,$opt);
        $data = json_decode($response->getBody(),true);

        if (!$data) {
            throw new \RuntimeException("can not found service {$name}");
        }

        return new Service($data);
    }

}