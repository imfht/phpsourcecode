<?php

/**
 * 检查行为限制
 * @param  [type]  $action    [description]
 * @param  [type]  $model     [description]
 * @param  [type]  $record_id [description]
 * @param  [type]  $user_id   [description]
 * @param  boolean $ip        [description]
 * @return [type]             [description]
 */
function check_action_limit($action = null, $model = null, $record_id = null, $user_id = null, $ip = false)
{
    $obj = model('ActionLimit');

    $item = array('action' => $action, 'model' => $model, 'record_id' => $record_id, 'user_id' => $user_id, 'action_ip' => $ip);
    if(empty($record_id)){
        unset($item['record_id']);
    }

    $obj->checkOne($item);

    $return = array();
    if (!$obj->state) {
        $return['state'] = $obj->state;
        $return['info'] = $obj->info;
        $return['url'] = $obj->url;
    }else{
        $return['state'] = true;
    }
    return $return;
}

function get_time_ago($type = 'second', $some = 1, $time = null)
{
    $time = empty($time) ? time() : $time;
    switch ($type) {
        case 'second':
            $result = $time - $some;
            break;
        case 'minute':
            $result = $time - $some * 60;
            break;
        case 'hour':
            $result = $time - $some * 60 * 60;
            break;
        case 'day':
            $result = strtotime('-' . $some . ' day', $time);
            break;
        case 'week':
            $result = strtotime('-' . ($some * 7) . ' day', $time);
            break;
        case 'month':
            $result = strtotime('-' . $some . ' month', $time);
            break;
        case 'year':
            $result = strtotime('-' . $some . ' year', $time);
            break;
        default:
            $result = $time - $some;
    }
    return $result;
}

function get_time_unit($key = null){

    $array = array(
        'second' => lang('_TIME_SECOND_'), 
        'minute' => lang('_TIME_MINUTE_'), 
        'hour' => lang('_HOUR_'), 
        'day' => lang('_TIME_DAY_'), 
        'week' => lang('_TIME_WEEK_'), 
        'month' => lang('_TIME_MONTH_'), 
        'year' => lang('_TIME_YEAR_')
    );
    return empty($key)?$array:$array[$key];
}

/**
 * 单位格式时间转换成时间戳
 * @param string $str 单位格式时间
 * @param string $type +:生成的是之后的时间撮，-:生成的是之前的时间撮
 * @param null $time 基准时间点
 * @return array|int|null
 * @author 郑钟良<zzl@ourstu.com>
 */
function unitTime_to_time($str='1 day',$type='-',$time=null)
{
    $time = empty($time) ? time() : $time;
    $str=explode(' ',$str);
    switch ($str[1]) {
        case 'second':
            if($type=='-'){
                $result=$time-$str[0];
            }else{
                $result=$time+$str[0];
            }
            break;
        case 'minute':
            if($type=='-'){
                $result=$time-$str[0] * 60;
            }else{
                $result=$time+$str[0] * 60;
            }
            break;
        case 'hour':
            if($type=='-'){
                $result=$time-$str[0] * 60 * 60;
            }else{
                $result=$time+$str[0] * 60 * 60;
            }
            break;
        case 'day':
            $result = strtotime($type . $str[0] . ' day', $time);
            break;
        case 'week':
            $result = strtotime($type . ($str[0] * 7) . ' day', $time);
            break;
        case 'month':
            $result = strtotime($type . $str[0] . ' month', $time);
            break;
        case 'year':
            $result = strtotime($type . $str[0] . ' year', $time);
            break;
        default:
            $result = $time - $str[0];
    }
    return $result;
}

/**
 * 30 day -> 30 天
 * 单位格式时间转换成可显示的中文单位格式时间
 * @param string $str
 * @return string
 * @author 郑钟良<zzl@ourstu.com>
 */
function unitTime_to_showUnitTime($str='1 day')
{
    $str=explode(' ',$str);
    $replace=get_time_unit();
    $str[1]=$replace[$str[1]];
    $str=implode(' ',$str);
    return $str;
}


function get_punish_name($key){
    !is_array($key) && $key = explode(',',$key);
    $obj =model('ActionLimit');
    $punish = $obj->punish;
    $return = array();
    foreach($key as $val){
        foreach($punish as $v){
            if($v[0] == $val){
                $return[]= $v[1];
            }
        }
    }
    return implode(',',$return);
}

function get_action_name($key){
    !is_array($key) && $key = explode(',',str_replace(array('[',']'),'',$key));
    $return = array();
    foreach($key as $val){
        $return[] = db('Action')->where(['name'=>$val])->value('title');
    }
    return implode(',',$return);
}