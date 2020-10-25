<?php
namespace app\admin\model;
use think\Model;

/**
 * 分类
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class Category extends Model
{
	/**
	 * 库名和表名一起配置(方便之后分库处理)
	 */
	protected $table = 'cthink.__CATEGORY__';
	
	public function lists(){
		$data = \think\Db::table($this->table)->order('`sort` asc')->select();
		return model('Menu')->toFormatTree($data);
	}
	
	public function catesort($data){
		return \think\Db::table($this->table)->where(['id'=>$data['id']])->update(['sort'=>$data['sort']]);
	}
	
}
