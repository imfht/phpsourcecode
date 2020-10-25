<?php
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;

$master = new Worker("tcp://127.0.0.1:" . $masterport);
$master->count = 1;
$master->name = "proxy-master";
$master->onWorkerStart = function($worker) {
    $worker->pps = Array("in" => 0, "out" => 0);
    $worker->pps_temp = Array("in" => 0, "out" => 0);
    $worker->active_conn = 0;
    $worker->speed = Array("in" => 0, "out" => 0);
    $worker->speed_temp = Array("in" => 0, "out" => 0);
    echo "[" . date('Y-m-d H:i:s') . "][Master][Startup][Info] Master started.\n";
};
$master->onMessage = function($connection, $buffer) use ($master){
    $arr = json_decode($buffer, true);
    if($arr['action'] == "reconn"){
        if($arr['worker'] == "timer"){
            $connection->workerid = "timer";
            echo "\r[" . date('Y-m-d H:i:s') . "][Master][Info] Timer reconnected.          \n";
            echo "\rStatus: Conns: " . $master->active_conn . ", PPS: in: " . $master->pps["in"] . ", out: " . $master->pps["out"] . " Speed: in: " . round($master->speed["in"] / 1024, 3) . "KB/s, out: " . round($master->speed["out"] / 1024, 3) . "KB/s             \r";
        } else {
            $connection->workerid = $arr['worker'];
            $connection->proxyid = $arr['proxy'];
            echo "\r[" . date('Y-m-d H:i:s') . "][Master][Info] Worker {$connection->proxyid}-{$connection->workerid} reconnected.          \n";
            echo "\rStatus: Conns: " . $master->active_conn . ", PPS: in: " . $master->pps["in"] . ", out: " . $master->pps["out"] . " Speed: in: " . round($master->speed["in"] / 1024, 3) . "KB/s, out: " . round($master->speed["out"] / 1024, 3) . "KB/s             \r";
        }
    }
    if($arr['action'] == "new"){
        if($arr['worker'] == "timer"){
            $connection->workerid = "timer";
            echo "\r[" . date('Y-m-d H:i:s') . "][Master][Info] Timer started.          \n";
            echo "\rStatus: Conns: " . $master->active_conn . ", PPS: in: " . $master->pps["in"] . ", out: " . $master->pps["out"] . " Speed: in: " . round($master->speed["in"] / 1024, 3) . "KB/s, out: " . round($master->speed["out"] / 1024, 3) . "KB/s             \r";
        } else {
            $connection->workerid = $arr['worker'];
            $connection->proxyid = $arr['proxy'];
            echo "\r[" . date('Y-m-d H:i:s') . "][Master][Info] Worker {$connection->proxyid}-{$connection->workerid} started.          \n";
            echo "\rStatus: Conns: " . $master->active_conn . ", PPS: in: " . $master->pps["in"] . ", out: " . $master->pps["out"] . " Speed: in: " . round($master->speed["in"] / 1024, 3) . "KB/s, out: " . round($master->speed["out"] / 1024, 3) . "KB/s             \r";
        }
    }
    if($arr['action'] == "new_conn"){
        $connection->ip = $arr['ip'];
        $connection->port = $arr['port'];
        $connection->uid = $arr['uid'];
        $master->active_conn ++;
        echo "\r[" . date('Y-m-d H:i:s') . "][Master][Info][User: {$connection->proxyid}-{$connection->workerid}-{$connection->uid}] Server bridge [/{$connection->ip}:$connection->port] connected.      \n";
        echo "\rStatus: Conns: " . $master->active_conn . ", PPS: in: " . $master->pps["in"] . ", out: " . $master->pps["out"] . " Speed: in: " . round($master->speed["in"] / 1024, 3) . "KB/s, out: " . round($master->speed["out"] / 1024, 3) . "KB/s             \r";
        
    }
    if($arr['action'] == "new_msg"){
        //$master->pps_temp ++;
        //$master->speed_temp = $master->speed_temp + $arr["strlen"];
        if($arr['handle'] == "in"){
            $master->pps_temp["in"]++;
            $master->speed_temp["in"] = $master->speed_temp["in"] + $arr["strlen"];
        }
        if($arr['handle'] == "out"){
            $master->pps_temp["out"]++;
            $master->speed_temp["out"] = $master->speed_temp["out"] + $arr["strlen"];
        }
    }
    if($arr['action'] == "close_conn"){
        $connection->ip = $arr['ip'];
        $connection->port = $arr['port'];
        $connection->uid = $arr['uid'];
        $master->active_conn --;
        echo "\r[" . date('Y-m-d H:i:s') . "][Master][Info][User: {$connection->proxyid}-{$connection->workerid}-{$connection->uid}] Server bridge [/{$connection->ip}:$connection->port] disconnected.      \n";
        echo "\rStatus: Conns: " . $master->active_conn . ", PPS: in: " . $master->pps["in"] . ", out: " . $master->pps["out"] . " Speed: in: " . round($master->speed["in"] / 1024, 3) . "KB/s, out: " . round($master->speed["out"] / 1024, 3) . "KB/s             \r";
    }
    if($arr['action'] == "timer"){
        $master->pps = $master->pps_temp;
        $master->pps_temp = Array("in" => 0, "out" => 0);
        $master->speed = $master->speed_temp;
        $master->speed_temp = Array("in" => 0, "out" => 0);
        echo "\rStatus: Conns: " . $master->active_conn . ", PPS: in: " . $master->pps["in"] . ", out: " . $master->pps["out"] . " Speed: in: " . round($master->speed["in"] / 1024, 3) . "KB/s, out: " . round($master->speed["out"] / 1024, 3) . "KB/s             \r";
    }
};
$master->onClose = function($connection) use ($master) {
    if($connection->workerid == "timer"){
        echo "[" . date('Y-m-d H:i:s') . "][Master][Info] Timer stopped.\n";
        unset($connection->workerid);
    } else {
        echo "[" . date('Y-m-d H:i:s') . "][Master][Info] Worker {$connection->proxyid}-{$connection->workerid} stopped.\n";
        unset($connection->proxyid);
        unset($connection->workerid);
    }
};
$master->onWorkerStop = function($worker) {
    echo "[" . date('Y-m-d H:i:s') . "][Master][Info] Master stopped.\n";
};
