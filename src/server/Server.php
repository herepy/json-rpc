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

    public function registerService(array $params)
    {
        if (!$params) {
            throw new \RuntimeException("params cat not be empty");
        }
        $params = array_change_key_case($params);

        if (!isset($params["name"]) || !isset($params["address"]) || !isset($params["port"])) {
            throw new \RuntimeException("name,address and port is required");
        }

        $defaultCheck = [
            "Tcp"       => $params["address"].":".$params["port"],
            "Interval"  => "15s",
            "Timeout"   => "2s"
        ];

        $serviceId = isset($params["id"]) ? $params["id"] : $params["name"]."_".gethostname();
        $check = isset($params["check"]) ? $params["check"] : $defaultCheck;
        $params = [
            "Id"        =>  $serviceId,
            "Name"      =>  $params["name"],
            "Tags"      =>  isset($params["tag"]) ? $params["tag"] : [],
            "Address"   =>  $params["address"],
            "Port"      =>  $params["port"],
            "EnableTagOverride"=> false,
            "Check"     =>  $check
        ];

        /**
         * @var $response ConsulResponse
         */
        $response = $this->agent->registerService($params);
        if ($response->getStatusCode() != 200) {
            return false;
        }

        return $serviceId;
    }

    public function deregisterService($serviceId)
    {
        $this->agent->deregisterService($serviceId);
    }

}