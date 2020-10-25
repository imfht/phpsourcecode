<?php
include("../../config/config.php");
include("../include/function.php");
$redis = Redis_Link();
$worker_thread = Get_Config('worker_thread');
$return['total'] = $worker_thread;
for ($i = 1; $i <= $worker_thread; $i++) {
    $status = $redis->get('Worker_Status_' . $i);
    if (empty($status)) {
        $return['worker'][$i]['status'] = '101';
    } else {
        $worker_step = $redis->get('Worker_Monitor_' . $i);
        if (empty($worker_step)) {
            $return['worker'][$i]['status'] = '0';
        } elseif ($worker_step == 1) {
            $encode_per = $redis->get('Monitor_Per_' . $i);
            $return['worker'][$i]['status'] = '1';
            $return['worker'][$i]['progress'] = $encode_per;
        } elseif ($worker_step == 2) {
            $return['worker'][$i]['status'] = '2';
        } elseif ($worker_step == 3) {
            $return['worker'][$i]['status'] = '3';
        } else {
            $return['worker'][$i]['status'] = '103';
        }
    }
}
echo json_encode($return);