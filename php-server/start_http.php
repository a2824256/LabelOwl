<?php

use Workerman\Worker;
use Workerman\Protocols\Http\Response;

require_once './Autoloader.php';
$worker = new Worker('http://127.0.0.1:2347');
$worker->name = 'Http';
$worker->onMessage = function ($connection, $request) {
    $header = [
        'Access-Control-Allow-Origin' => '*'
    ];
    $body = '';
    $id = $request->get('pid', '0.jpg');
    $type = $request->get('type', 'pic');
    try{
        switch ($type) {
//            返回分类信息
            case "classes":
                $path = "./static/images";
                $temp=array_values(array_diff(scandir($path),array('..','.')));
                $json = [
                    'status' => 1,
                    'images' => $temp,
                    'labels' => [
                        "0" => "mouth",
                        "1" => "consil",
                        "2" => "pp-wall"],
                    'img_status' => [
                        "测试员",
                        ""
                    ]
                ];
                $header['Content-Type'] = 'text/json;charset=utf-8';
                $body = json_encode($json);
                break;
//            返回图片数据
            case "pic":
                $header['Content-Type'] = 'image/jpeg';
                $path = "./static/images/" . $id;
                $body = file_get_contents($path);
                break;
            default:
                $header['Content-Type'] = 'text/html;charset=utf-8';
                $body = "错误请求";
                break;
        }
        $response = new Response(200, $header, $body);
        $connection->send($response);
    }catch (Exception $e){
        $header['Content-Type'] = 'text/html;charset=utf-8';
        $body = $e->getMessage();
        $response = new Response(200, $header, $body);
        $connection->send($response);
    }
};

// 运行worker
Worker::runAll();