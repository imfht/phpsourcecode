<?php
namespace app\common\model;

use think\Model;

/**
 * 用户组模型
 * @package app\admin\model
 */
class Group extends Model
{
    //protected $table = '__MEMBERDATA__';
	
	//主键不是ID,要单独指定
	public $pk = 'id';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;


	public static function getById($id = '')
    {
        return self::get(['id' => $id]);
    }
    
    public static function getTitleById($id = '')
    {
        $array = self::getById($id) ;
        return $array['title'];
    }
    
    public function getTitleList($map = [])
    {
        return self::where($map)->order('type desc,level asc')->column('id,title');
    }
    
    public function getList($map = [])
    {
        $listdb = self::where($map)->order('type desc,level asc')->column(true);
        foreach($listdb AS $key=>$rs){
            $rs['admindb'] = json_decode($rs['admindb'],true);
            $listdb[$key] = $rs;
        }
        return $listdb;
    }
}