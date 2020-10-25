<?php
namespace app\common\model;
use think\Model;

/**
 * 资源文件上传存储为数据库方式
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class Attach extends Model
{
	/**
	 * 库名和表名一起配置(方便之后分库处理)
	 */
	protected $table = 'cthink.__ATTACH__';
	
	/**
	 * 根据attach_id获取一天资源数据的信息
	 * @param int $attrac_id 资源编号id
	 */
	public function getAttachById($attrac_id){
		return \think\Db::table($this->table)->where(['attach_id'=>$attrac_id])->find();
	}
	
	/**
	 * 插入一条资源文件
	 */
	public function add($data){
		return \think\Db::table($this->table)->insertGetId($data);
	}
	
	/**
	 * 根据md5值查询数据库是否存在相同的图片，如果有直接返回，不将资源保存到服务器
	 */
	public function getAttachinfo($md5val){
		return \think\Db::table($this->table)->where(['`md5`'=>$md5val])->find();
	}
	
	/**
	 * 通过id批量查询结果,例如：attach_id值为12,14,123
	 */
	public function getInlist($idlist){
		return \think\Db::table($this->table)->where(['attach_id'=>['in',$idlist]])->select();
	}
	
}
