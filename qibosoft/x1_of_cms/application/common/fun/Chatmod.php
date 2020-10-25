<?php
namespace app\common\fun;

use app\common\model\Chatmod AS ChatmodModel;

class Chatmod{
    
    /**
     * 获取群聊的模块
     * @param number $pcwap  0通用 1 WAP专用 2 PC专用 3 APP专用
     * @param number $type 0通用 1 群聊使用 2 私聊使用
     * @param number $aid 圈子ID,正数
     * @param number $groupid uid是负数则是圈主用户组,否则就是当前用户的用户组
     * @return mixed|\think\cache\Driver|boolean
     */
    public static function get($pcwap=0,$type=0,$aid=0,$groupid=0){
        $array = cache('chatmod_cfg')?:[];
        if (empty($array)) {
            $_array = ChatmodModel::where('status',1)->order('list desc,id asc')->column(true);
            foreach($_array AS $rs){
                $array[$rs['aid']][] = $rs;                
            }
            cache('chatmod_cfg',$array);
        }
        $_data = $array[$aid]?:$array[0];
        $data = [];
        foreach($_data AS $rs){
            if ($rs['allowgroup']&&!in_array($groupid, explode(',', $rs['allowgroup']))) {
                continue;
            }elseif( $pcwap!=0 && $rs['pcwap']!=0 && $rs['pcwap']!=$pcwap ){
                continue ;
            }elseif( $type!=0 && $rs['type']!=0 && $rs['type']!=$type ){
                continue ;
            }
            $data[] = $rs;
        }
        return $data;
    }
    
}