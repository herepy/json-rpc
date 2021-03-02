<?php

$socket = socket_create_listen(8081);

while (true) {
    $conn = socket_accept($socket);
    echo "get connect".PHP_EOL;
    $str = socket_read($conn, 1024);
    echo $str.PHP_EOL;

    $data = json_decode($str, true);
    $result = ["id"=>$data["id"],"result"=>rand(0,9),"err"=>null];
    $result = json_encode($result);
    socket_write($conn,$result,strlen($result));
    socket_close($conn);
}