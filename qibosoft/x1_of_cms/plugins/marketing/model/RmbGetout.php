<?php

namespace plugins\marketing\model;
use think\Model;


//余额提现
class RmbGetout extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    //protected $table = '__MONEYLOG__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	//protected $pk = 'id';
    
    /**
     * 标签调用
     * @param array $tag_array
     * @return unknown
     */
    public static function get_label($tag_array=[]){
        $map = [];
        $cfg = unserialize($tag_array['cfg']);
        if($cfg['ifpay']=='1'){
            $map['ifpay'] = 1;
        }
        
        if($cfg['where']){  //用户自定义的查询语句
            $_array = fun('label@where',$cfg['where'],$cfg);
            if($_array){
                $map = array_merge($map,$_array);
            }
        }
        $order = in_array($order, ['id','money']) ? $order : 'id';
        $by = $by=='asc' ? 'asc' : 'desc';
        $rows = intval($cfg['rows']) ?: 5;
        $data_list = self::where($map)->order($order,$by)->paginate($rows);
        $data_list->each(function($rs,$key){
            //$rs['username'] = get_user_name($rs['uid']);
            $rs['icon'] = get_user_icon($rs['uid']);
            return $rs;
        });
        
            return $data_list;
    }
    
    public static function get_top($tag_array=[]){
        $cfg = unserialize($tag_array['cfg']);
        $rows = intval($cfg['rows']) ?: 5;
        $data_list = getArray(self::where('ifpay',1)->group('uid')->select());
        $array = [];
        foreach($data_list AS $key=>$rs){
            $array[$key] = self::where('uid',$rs['uid'])->sum('money');
        }
        arsort($array);
        $data = [];
        $i = 0;
        foreach($array AS $key=>$value){
            $i++;
            if ($i>$rows) {
                break;
            }
            $data[$key] = array_merge($data_list[$key],['total_money'=>$value]);
        }
        return $data;
    }
	
}