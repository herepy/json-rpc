<?php
/**
 * Created by PHPSTORM.
 * User: pengyu
 * Time: 2021/3/3 10:50
 */

/**
 * @param string $method
 * @param array $params
 * @return false|string
 */
function encode(string $method, array $params)
{
    $data = [
        "id"    =>  microtime(true) * 1000,
        "method"=>  $method,
        "params"=>  [$params]
    ];
    $data = json_encode($data);

    return $data;
}

/**
 * @param $data
 * @return array
 */
function decode($data)
{
    return json_decode($data, true);
}