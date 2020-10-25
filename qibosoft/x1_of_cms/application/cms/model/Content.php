<?php
namespace app\cms\model;

use app\common\model\C;

//模型内容处理
class Content extends C
{
    /**
     * 删除单条内容
     * @param number $id 内容ID
     * @param number $mid 模型ID,可为空
     * @return boolean
     */
    public static function deleteData($id=0,$mid=0){
        $result = parent::deleteData($id,$mid);
        if ($result===true && class_exists('Pages')) { //必须绝对等于,因为1的时候是软删除
            Pages::where('aid',$id)->delete();            
        }
        Info::where('aid',$id)->delete();
        return $result;
    }
}
