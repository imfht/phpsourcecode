<?php
/*
    |--------------------------------------------------------------------------
    | 这里是初始化，以及计算相关人员变动的函数方法
    |--------------------------------------------------------------------------
    */

function startSet($day=1,$yisi=4,$quezhen=1){
    // 初始化参数
    include('common.php');
    if($day=="" || $day==null || $day==0){
        // 初始化跳转，GET传参
        firstDay();
        return $common;
    }elseif($day*1 <= 1){
        return $common;
    }else{
        if($common['h_num'] == 0 || $day <= $common['qf_date']){ //第一个潜伏期没到，无人确诊，无医疗救治
            // 未采取任何治疗措施情况下
            $common['day'] = $day;
            $common['yisi'] = $yisi + $yisi * $common['y_g_rate'] + $quezhen * $common['y_rate'];
            $common['quezhen'] = $quezhen + $quezhen * $common['g_rate'];
            $common['people'] = $common['people'] - $common['yisi'] - $common['quezhen'];
            return $common;
        }else{
            // 采取隔离和相关治疗措施后
            $common['day'] = $day;
            $common['yisi'] = $yisi + $yisi * $common['y_g_rate'] + $quezhen * $common['y_rate'] - $common['h_num']*$common['h_y_rate'];
            $common['quezhen'] = $quezhen + $quezhen * $common['g_rate']- $common['h_num']*$common['h_q_rate'];
            $common['people'] = $common['people'] - $common['yisi'] - $common['quezhen'] + $common['h_num']*$common['h_y_rate'] + $common['h_num']*$common['h_q_rate'];
            return $common;
        }
        
    }

}





// 初始化跳转
function firstDay(){
    header('Location: index.php?day=1&yisi=0&quezhen=1');
}


// 随机输出指定数量的坐标值
function get_data($num){
    $data='[';
    for($i=0;$i<$num;$i++){
        $x=rand(0,100);
        $y=rand(0,100);
        $data .='['.$x.','.$y.'],'; 
    }
    return $data.']';
}