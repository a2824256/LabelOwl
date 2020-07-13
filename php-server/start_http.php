<?php

use Workerman\Worker;
use Workerman\Protocols\Http\Response;

require_once './Autoloader.php';

$worker = new Worker('http://127.0.0.1:2347');

$worker->onMessage = function ($connection, $request) {
    $id = $request->get('pid', 0);
    $type = $request->get('type', 'pic');
    $response = new Response(200, [
        'Content-Type' => 'image/jpeg',
        'Access-Control-Allow-Origin' => '*'
    ], file_get_contents("./static/images/0.jpg"));
    $connection->send($response);
};

// 运行worker
Worker::runAll();