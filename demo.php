<?php

require_once "vendor/autoload.php";

//===========client======================

$client = new \jsonRpc\client\Client("10.90.10.222", 8500);

$param = ["subject"=>"dili", "token"=>"2222222"];
$data = $client->findService("user")->call("user.TokenToId", $param);

if ($data === false) {
    echo $client->getError().PHP_EOL;
    return;
}
echo $data.PHP_EOL;

//===========server==========================
$server = new \jsonRpc\server\Server("10.90.10.222",8500);
$params = [
    "name"  =>  "person",
    "address"   =>  "10.90.10.222",
    "port"  =>  80
];
$serverId = $server->registerService($params);
$server->deregisterService($serverId);
