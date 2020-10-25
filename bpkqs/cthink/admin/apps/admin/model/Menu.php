<?php
namespace app\admin\model;
use think\Model;
use app\admin\controller\Auth;

/**
 * 权限菜单管理
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class Menu extends Model
{
	/**
	 * 库名和表名一起配置(方便之后分库处理)
	 */
	protected $table = 'cthink.__MENU__';
	
	private $formatTree; //用于树型数组完成递归格式的全局变量
	
	/**
	 * 菜单列表
	 */
	public function lists($page = 15,$map = []){
		$list = \think\Db::table($this->table)->where($map)->paginate($page);
		return $list;
	}
	
	/**
	 * 将menu菜单以tree的形式展示出来
	 */
	public function getMenuTree(){
		$list = \think\Db::table($this->table)->where(['status'=>1])->select();
		return $list;
	}
	
	/**
	 * 通过id获取一条菜单信息
	 * @param int $id 菜单id
	 */
	public function getFindOne($id){
		return \think\Db::table($this->table)->where(['id'=>$id])->find();
	}
	
	/**
	 * 添加菜单
	 */
	public function addMenu($data){
		return \think\Db::table($this->table)->insertGetId($data);
	}
	
	/**
	 * 编辑菜单
	 */
	public function editMenu($input){
		return \think\Db::table($this->table)->update($input);
	}
	
	/**
	 * 设置菜单的状态信息
	 */
	public function stateMenu($map,$data){
		return \think\Db::table($this->table)->where($map)->update($data);
	}
	
	/**
	 * 物理删除
	 */
	public function removeMenu($id){
		$return = false;
		$map = explode(',',$id);
		$m = \think\Db::table($this->table)->field('id,pid')->where(['id'=>['in',$map]])->select();
		$count = 0;
		foreach($m as $k=>$v){
			$count += \think\Db::table($this->table)->field('id')->where(['pid'=>$v['id']])->count();
		}
		if($count == 0){
			$return =  \think\Db::table($this->table)->delete($map);
		}
		return $return;
	}
	
	/**
	 * 将格式数组转换为树
	 *
	 * @param array $list
	 * @param integer $level 进行递归时传递用的参数
	 */
	private function _toFormatTree($list,$level=0,$title = 'title') {
		foreach($list as $key=>$val){
			$tmp_str='┃'.str_repeat("&nbsp;&nbsp;┣",$level);
			$tmp_str.="";

			$val['level'] = $level;
			$val['title_show'] =$level==0?'┣'.$val[$title]."&nbsp;":$tmp_str.$val[$title]."&nbsp;";
			if(!array_key_exists('_child',$val)){
				array_push($this->formatTree,$val);
			}else{
				$tmp_ary = $val['_child'];
				unset($val['_child']);
				array_push($this->formatTree,$val);
				$this->_toFormatTree($tmp_ary,$level+1,$title); //进行下一层递归
			}
		}
		return;
	}

	public function toFormatTree($list,$title = 'title',$pk='id',$pid = 'pid',$root = 0){
		$list = list_to_tree($list,$pk,$pid,'_child',$root);
		$this->formatTree = array();
		$this->_toFormatTree($list,0,$title);
		return $this->formatTree;
	}
	
	/**
	 * 获取菜单节点
	 */
	public function returnNodes($tree = true){
        static $tree_nodes = array();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        if((int)$tree){
            $list = \think\Db::table($this->table)->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if( stripos($value['url'],request()->module())!==0 ){
                    $list[$key]['url'] = request()->module().'/'.$value['url'];
                }
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            $nodes = \think\Db::table($this->table)->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],request()->module())!==0 ){
                    $nodes[$key]['url'] = request()->module().'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }
	
	/**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author zhanghd <328380798@qq.com>
     */
    public function getMenus(){
		$auth_menu = session('auth_menu');
		if($auth_menu){
			return $auth_menu;
		}else{
			//获取根菜单
			$root_menu = \think\Db::table($this->table)->where(['pid'=>0,'status'=>1])->order('`sort` asc')->select();
			$root = [];
			//判断是否添加了根菜单，并且去验证是否具备权限
			if($root_menu){
				foreach($root_menu as $k=>$v){
					//超级管理员不需要检测菜单权限
					if(intval(is_administrator()) != intval(session('user_auth.uid'))){
						if($this->checkRule(request()->module().'/'.$v['url'])){
							$root[$k] = $v;
						}
					}else{
						$root[$k] = $v;
					}
					
				}
				//判断是否有授权的根菜单
				if(count($root) > 0){
					foreach($root as $kk=>$vv){
						$child = \think\Db::table($this->table)->where(['status'=>1,'pid'=>$vv['id']])->order('`sort` asc')->select();
						if(count($child) >0){
							foreach($child as $key=>$val){
								//超级管理员不需要检测菜单权限
								if(intval(is_administrator()) != intval(session('user_auth.uid'))){
									if($this->checkRule(request()->module().'/'.$val['url'])){
										$root[$kk]['_child'][$key] = $val;
									}
								}else{
									$root[$kk]['_child'][$key] = $val;
								}
							}
						}else{
							$root[$kk]['_child'] = '';
						}
					}
				}
			}
			session('auth_menu',$root);
			return $root;
		}
		
    }
	
	/**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     */
    protected function checkRule($rule){
		$return = false;
        $auth = new Auth();
		if($auth->check($rule, session('user_auth.uid'),['in','1,2'])){
			$return = true;
		}
		return $return;
    }
}
