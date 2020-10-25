<?php
use think\Db;
// - finance/functions
function budNoRec($type)
{
    // 季度
    $month = date('m');
    if($type == 's'){
        $num = intval($month) / 3;
        if($num<1) return "01";
        switch($num){
            case ($num <= 1):return "01";
            case (1< $num && $num <= 2): return "02";
            case (2< $num && $num <= 3): return "03";
            case (3< $num && $num <= 4): return "04";
            default: return "";
        }
    }else return $month;
}
// 财务编号 - 年月季度等解析 2016-10-0/2016-00-01/2016-00-00
function fNoParseText($no){
    $tmpArr = explode('-',$no);
    $text = '';
    if($tmpArr[2] == $tmpArr[1] && $tmpArr[1] == '00'){
        $text = $tmpArr[0].'年度';
    }
    elseif($tmpArr[2] == '00' && $tmpArr[1] != $tmpArr[2]){
        $text = $tmpArr[0].'年'.$tmpArr[1].'月';
    }
    else $text = $tmpArr[0].'年'.$tmpArr[2].'季度';
    return $text;
}
// -财务登账快速写入解析 (2016-11-30: 名称)[事务甲方 : 金额 -> 事务乙方]{类型 # 备注}
function fcset_parse($str,$fn=null,$fnArg=null)
{
    $data = explode(',',trim($str));
    $saveData = [];$json = '';
    $lmt1 = '/\(.*\)/';//()
    $lmt2 = '/\[.*\]/';//[]
    $lmt3 = '/\{.*\}/';//{}
    foreach($data as $v)
    {
        if(empty($v)) continue;
        $v = str_replace(' ','',$v);
        preg_match($lmt1,$v,$tmp);
        $tmp = isset($tmp[0])? $tmp[0]:$tmp;
        if(is_string($tmp)) $tmp = str_replace('(','"',str_replace(')','"',$tmp));
        if($tmp) $json = '"use_date":'.str_replace(':','","name":"',$tmp);
        //debugOut([$tmp,$json],true);

        preg_match($lmt2,$v,$tmp);
        $tmp = isset($tmp[0])? $tmp[0]:$tmp;
        if(is_string($tmp)){
            $tmp = str_replace('[','"',str_replace(']','"',$tmp));
            //$tmp = '"master":'.str_replace('<-','","type":"IN","sider":"',str_replace('->','","type":"OU","sider":"',str_replace(':','","figure":"',$tmp)));
            $tmp = '"master":'.preg_replace('/(<-)|(<)/','","type":"IN","sider":"',preg_replace('/(->)|(>)/','","type":"OU","sider":"',str_replace(':','","figure":"',$tmp)));            
        }        
        if($tmp) $json .= ($json? ',':'').$tmp;
        //debugOut([$tmp,$json],true);

        preg_match($lmt3,$v,$tmp);
        $tmp = isset($tmp[0])? $tmp[0]:$tmp;
        if(is_string($tmp)){
            $tmp = str_replace('{','"',str_replace('}','"',$tmp));
            // println($tmp);die;
            // $tmp = '"purpose":'.preg_replace('/(\|)|(#)|(\^)|(\/)/','","explanin":"',$tmp);
            // $tmp = '"purpose":'.preg_replace('/(\|)|(#)|(\^)|(\/)|(:)/','","explanin":"',$tmp);
            $tmp = '"purpose":'.preg_replace('/(:)/','","explanin":"',$tmp);
            // println($tmp);die;
        }
        if($tmp) $json .= ($json? ',':'').$tmp;
        if($json) $json = '{'.$json.'}';
        // println($json);die;
        $json = json_decode($json,true);
        //debugOut($json,true);
        if(is_callable($fn)) $fn($json,$fnArg,$v);
        else $saveData[] = $json;
    }
    return $saveData;
}
/*
select DATE_FORMAT(from_date,'%e'),date_format(now(),'%e') from `finc_budget` where from_date is not null;
select DATE_FORMAT(from_date,'%e') as tm,
    date_format(now(),'%e') as tc,
    if(DATE_FORMAT(from_date,'%e')>date_format(now(),'%e'),DATE_FORMAT(from_date,'%e'),date_format(now(),'%e')) as tmax,
    if(cast(DATE_FORMAT(from_date,'%e') as unsigned) > cast(date_format(now(),'%e') as unsigned),DATE_FORMAT(from_date,'%e'),date_format(now(),'%e')) as tmax2
    from `finc_budget` where from_date is not null;
SELECT * FROM `finc_budget` WHERE DATE_FORMAT(from_date,'%e') > date_format(now(),'%e');
*/ 
// 例行财务计划生成
function fcMkregularRecord($callback=null){
    $wh = 'center_id=\''.uInfo('cid').'\' and bud_no=\'regulars\' and from_date is not null and cast(date_format(from_date,\'%e\') as unsigned) > cast(date_format(now(),\'%e\') as unsigned) and (to_date is null) or (to_date is not null and to_date > curdate())';
    $data = Db::table('finc_budget')->where($wh)->select();
    $trs = '';$i = 1;$callbackType = null;$retArr = [];
    foreach($data as $v){
        $rfn = $v['related_fn'];
        $dateObj = date_create($v['from_date']);
        $date = '';
        if(substr_count($rfn,'M') >0) $date = date('Y-m-'.date_format($dateObj,'d'),time());
        // 时间倒记
        $days = empty($date)? 0:getDays(sysdate(),$date);
        if($days == 0) continue;
        if(empty($date)) $date = '解析失败';
        $ctt = substr_count($rfn,'+');
        if($ctt > 0) $rfn = str_replace('+','',$rfn);
        elseif(substr_count($rfn,'-')>0) $rfn = str_replace('-','',$rfn);
        $type = $ctt > 0? '收入':'支出';      
        $figure = '';      
        if(substr_count($rfn,'/')>0) $figure = substr($rfn,strpos($rfn,'/')+1);     
        if(is_callable($callback)){
            $tmp = $callback([
                'date' => $date,'type' => $type,'figure' => $figure,'days' => $days,'no'=>$i
            ],$v);
            if(!is_array($tmp)) $trs .= $tmp;
            else{
                $retArr[] = $tmp;
            }
        }else{
            $trs .= '<tr><td>'.$i.'</td><td>'.$date.'</td><td>'.$v['name'].'</td><td>'.$type.'</td><td>'.$figure.'</td><td>'.$days.'</td></tr>';
        }
        $i += 1;
    }
    if(count($retArr)>0) return $retArr;
    return $trs;
}