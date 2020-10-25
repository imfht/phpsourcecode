<?php
namespace plugins\area\model;
use think\Model;
use util\Tree;

//城市地区
class Area extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__AREA__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;

    public static function getTitleList($where=[])
    {
        return self::where($where)->order('list','desc')->column('id,name');
    }
    
    public static function get_all(){
        return self::where([])->order('list','desc')->order('id','asc')->column('id,pid,name');
    }
    

    public static function getTree($pid = 0, $default_title = '请选择...',$onlytitle=true)
    {
        $where = [];
        $result = [];
        if ($default_title !==false) {
            $result[0] = $default_title;
        }
        
        if ($pid>0) {
            $where['pid'] = $pid;
        }
        
        $data_list = Tree::config(['title' => 'name'])->toList(self::where($where)->order('list desc,id desc')->column(true,'id'));
        foreach ($data_list as $item) {
            $result[$item['id']] = $onlytitle?$item['title_display']:$item;
        }
        return $result;
    }
	
}