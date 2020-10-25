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
            $res=$this->allowField(true)->save($data,['id' => $data['id']]);
        }else{
            $data['id'] = create_guid();
            $res=$this->allowField(true)->save($data);
        }
        return $res;
    }

    public function getDataByMap($map=[],$fields= '*'){
        
        $data=$this->where($map)->field($fields)->find();
        
        return $data;
    }

    /**
     * 获取菜单列表
     *
     * @return     <type>  The lists.
     */
    public function getLists($where)
    {
        $menus = $this->where($where)->order('sort asc')->select();

        return $menus;
    }

    /**
     * 获取一级菜单
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function mainMenu()
    {
        $pid = '0';
        $res =  $this->where(array('pid'=>(string)$pid))->select();
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

