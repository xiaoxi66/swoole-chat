<?php

$kefulist = [];
// 创建一个websocket对象
$ws = new swoole_websocket_server('0.0.0.0',6060);
// 设置参数
$ws->set([
    'worker_num' => 3
]);

// 建立连接 握手动作已完成
$ws->on('open',function (swoole_websocket_server $svr, swoole_http_request $req){
    // 发一个消息给客户端
    $svr->push($req->fd,'^欢迎进入小希聊天室');
});

// 消息交互
$ws->on('message',function (swoole_websocket_server $server, swoole_websocket_frame $frame) use($kefulist){
    $data= $frame->fd.'说：'.$frame->data;
    // 广播给所有人
    foreach ($server->connection_list() as $fd){
        $server->push(1,$data);
    }
});

// 关闭事件
$ws->on('close', function (swoole_websocket_server $ser,int $fd) {
    // 广播给所有人
    foreach ($ser->connection_list() as $fd){
        $ser->push($fd,$fd.' 离开了我们');
    }
});

// 运行
$ws->start();