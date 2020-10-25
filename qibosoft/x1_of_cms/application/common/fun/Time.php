<?php
namespace app\common\fun;

/**
 * 取时间范围
 */
class Time{
    
    /**
     * 仅仅取某个时间段内的数据
     * @param string $type
     * @return string[]|number[]|string[][]|number[][]
     */
    public static function only($type='',$num=0){
        if ($type=='day'||$type=='d') {
            if ($num>1) {
                $array = [
                    ['<',strtotime(date('Y-m-d 00:00:00'))-3600*24*($num-2)],
                    ['>',strtotime(date('Y-m-d 00:00:00'))-3600*24*($num-1)],
                    'and'
                ];
            }else{
                $array = ['>',strtotime(date('Y-m-d 00:00:00'))];
            }            
        }elseif ($type=='week'||$type=='w') { //按周统计
            if ($num>1){
                $a = strtotime(date('Y-m-d 00:00:00'))-((date('w')?:7)-1)*3600*24;
                $array = [
                    ['<',$a-3600*24*7*($num-2)],
                    ['>',$a-3600*24*7*($num-1)],
                    'and'
                ];
            }else{
                $a = strtotime(date('Y-m-d 00:00:00'))-((date('w')?:7)-1)*3600*24;
                $array = ['>',$a];
            }            
        }elseif($type=='month'||$type=='m'){
            if ($num>1){
                $next_year = date('Y');
                $next_m = date('m')-($num-1);
                if ($next_m<1) {
                    $next_m+=12;
                    $next_year--;
                }
                
                $end_year = date('Y');
                $end_m = date('m')-($num-2);
                if ($end_m<1) {
                    $end_m+=12;
                    $end_year--;
                }
                
                $array = [
                    ['<',strtotime($end_year.'-'.$end_m."-01 00:00:00")],
                    ['>',strtotime($next_year.'-'.$next_m."-01 00:00:00")],
                    'and'
                ];
            }else{
                $array = ['>',strtotime(date('Y-m-01 00:00:00'))];
            }            
        }elseif($type=='year'||$type=='y'){
            if ($num>1){
                $year = date('Y')-($num-1);                
                $array = [
                    ['<',strtotime($year."-12-31 23:59:59")],
                    ['>',strtotime($year."-01-01 00:00:00")],
                    'and'
                ];
            }else{
                $array = ['>',strtotime(date('Y-01-01 00:00:00'))];
            }
        }elseif($type=='quarter'||$type=='q'){  //季度
            if ($num>1){
                $q = ceil(date('m')/3)-$num;
                $year = date('Y');
                if($q<0){
                    do{
                        $year--;
                        $q = $q+4;
                        if($q<0){
                            $ck = true;
                        }else{
                            $ck = false;
                        }
                    }while($ck==true);
                    $start_m = $q*3+1;
                    $end_m = $start_m+2;                    
                }else{                    
                    $start_m = ($q-1)*3+1;
                    $end_m = $q*3;
                }
                $end_day = 31;
                if ($end_m==6||$end_m==9) {
                    $end_day=30;
                }
                $array = [
                    ['<',strtotime("{$year}-{$end_m}-{$end_day} 23:59:59")],
                    ['>',strtotime("{$year}-{$start_m}-01 00:00:00")],
                    'and'
                ];
            }else{
                $m = (ceil(date('m')/3)-1)*3+1;
                $array = ['>',strtotime(date('Y-01-01 00:00:00'))];
            }
        }
        return $array;
    }
    
}