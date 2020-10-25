<?php
namespace app\common\model;

use think\Model;

/**
 * 应用市场.云端市场购买的应用，主要是做升级核对，但频道、插件、钩子不在这个表，目前主要是风格，后续可以拓展更多的
 * @package app\admin\model
 */
class Market extends Model
{
    //protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	//public $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 取出所有应用
     * @param array $map
     * @return unknown
     */
    public static function get_list($map=[]){
//         if(!is_table('market')){
//             if(is_file(APP_PATH.'common/upgrade/12.sql')){
//                 into_sql(APP_PATH.'common/upgrade/12.sql');
//             }else{
//                 return ;
//             }
//         }
        $data = self::where($map)->column(true);
        return  $data;
    }

	
}