<?php
require_once "vendor/autoload.php";

class Person
{
    public function say($name)
    {
        return "hello ".$name;
    }
}

$server = new \jsonRpc\server\Server("10.90.10.222",8500);
$server->registerService(new Person(),"person");
//$server->deregisterService("person000000005dc0ecc9000000006be98ee5");
