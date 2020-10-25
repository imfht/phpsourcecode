<?php
namespace plugins\config_set\model;
use think\Model;


//微信公众号客户留言信息记录
class Group extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__CONFIG_GROUP__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	//protected $pk = 'id';
    
    /**
     * 取得系统参数设置的分类数组
     * @param string $ifshow 系统核心设置才赋值true,是否在核心设置那里统一管理,频道或插件只能设置为false
     * @param string $sys_id 频道或插件ID,系统核心设置的话,要赋值为false
     * @param string $field 取哪些字段
     * @return unknown
     */
    public static function getNavTitle($ifshow=false,$sys_id=false,$field='id,title'){
        $map = [];
        $sys_id!==false && $map = ['sys_id'=>$sys_id];
        if($ifshow){
            $map['ifshow'] = 1;
        }
        return self::where($map)->order('list','desc')->column($field);
    }
    
    //根据插件或模块或系统得到对应的所有分组ID，方便读取对应的所有字段
    public static function getIdsBySys($sys_id=0){
        if($sys_id!=0){
            return self::where(['sys_id'=>$sys_id])->column('id');
        }else{
            return self::where(['sys_id'=>0])->whereOr(['ifsys'=>1])->column('id');
        }
    }
    
    public static function getNav($ifsys=false,$sys_id=false){
        $tab_list = [];
        foreach ( self::getNavTitle($ifsys,$sys_id) AS $key => $value) {
            $tab_list[$key]['title'] = $value;
            $tab_list[$key]['url']   = auto_url('index', ['group' => $key]);
        }
        return $tab_list;
    }
}