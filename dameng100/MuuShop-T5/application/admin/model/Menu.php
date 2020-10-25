<?php
namespace app\admin\model;

use think\Model;

/**
 * 菜单模型
 */

class Menu extends Model {

	//获取树的根到子节点的路径
	public function getPath($id){
		$path = array();
		$nav = $this->where("id='{$id}'")->field('id,pid,title')->find();
		$path[] = $nav;
		if($nav['pid'] !='0'){
			$path = array_merge($this->getPath($nav['pid']),$path);
		}
		return $path;
	}

    /**
     * 写入、编辑方法
     * @param  Array 写入数据的数组
     * @return 写入数据库中的主键ID
     */
	public function editData($data)
    {
        if($data['id']){
            $res=$this->save($data);
        }else{
            $data['id']= create_guid();
            $res=$this->save($data);
        }
        return $res;
    }
    /**
     * 判断、读取下级菜单
     * @param  [type] $pid [description]
     * @return [type]      [description]
     */
    public function subMenu($pid){
        $res =  $this->where(array('pid'=>$pid))->select();
        return $res;
    }
}

