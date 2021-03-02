<?php
/**
 * Created by PHPSTORM.
 * User: pengyu
 * Time: 2021/3/2 17:11
 */


namespace jsonRpc\server;


use SensioLabs\Consul\ConsulResponse;
use SensioLabs\Consul\Services\Agent;
use SensioLabs\Consul\Services\AgentInterface;
use \SensioLabs\Consul\ServiceFactory;

class Server
{
    /**
     * @var Agent
     */
    protected $agent;
    protected $nameToService = [];

    /**
     * Client constructor.
     * @param string $host 服务中心地址
     * @param int $port 服务中心端口
     */
    public function __construct(string $host,int $port)
    {
        $opt = ["base_uri"=>"http://{$host}:{$port}"];
        $factory= new ServiceFactory($opt);
        $this->agent = $factory->get(AgentInterface::class);
    }

    public function registerService(object $obj,string $name, array $tags = [])
    {
        if (isset($this->nameToService[$name])) {
            return;
        }

        $serviceId = $name."_".spl_object_hash($obj);
        $params = [
            "Id"    =>  $serviceId,
            "Name"  => $name,
            "Tags"  => $tags,
            "Address"=> "10.90.10.222", //todo 从配置文件读取
            "Port"=> 8888,
            "EnableTagOverride"=> false,
            "Check"=> [
                "Tcp"=> "10.90.10.222:8888",
                "Interval"=> "10s",
                "Timeout"=> "2s"
            ]
        ];

        /**
         * @var $response ConsulResponse
         */
        $response = $this->agent->registerService($params);
        return $serviceId;
    }

    public function deregisterService($serviceId)
    {
        $this->agent->deregisterService($serviceId);
    }
}