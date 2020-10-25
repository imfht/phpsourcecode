<?php
namespace app\common\fun;
use think\Db;

class Alilive{
    
    /**
     * 插入直播记录
     * @param number $uid 用户UID
     * @param number $ext_id 频道主题ID
     * @param string $ext_sys 频道ID
     * @param array $data POST的所有URL地址
     * @return number|string
     */
    public static function add($uid=0,$ext_id=0,$ext_sys='',$data=[]){
        if ( empty($data['push_url']) && empty($data['rtmp_url']) && empty($data['m3u8_url']) ) {
            return ;
        }
        $array = [
            'uid'=>$uid?:0,
            'ext_id'=>abs($ext_id),
            'ext_sys'=>modules_config($ext_sys)['id']?:0,
            'push_url'=>$data['push_url']?:'',
            'flv_url'=>$data['flv_url']?:'',
            'm3u8_url'=>$data['m3u8_url']?:'',
            'rtmp_url'=>$data['rtmp_url']?:'',
            'picurl'=>$data['picurl']?:'',
            'title'=>$data['title']?:'',
            'about'=>$data['about']?:'',
            'create_time'=>time(),
        ];
        $id = Db::name('alilive_log')->insertGetId($array);
        return $id;
    }
    

    
    
}