<?php
namespace app\admin\model;
use think\Model;
/**
 * 后台菜单
 */
class AdminMenu extends Model{
    //获取菜单
    public function loadList($where = array(), $id=0){
        $data=$this->loadData($where);
        $cat = new \org\Category(array('id', 'pid', 'name', 'cname'));
        $data = $cat->getTree($data, intval($id));
        return $data;
    }
    /**
     * 菜单数据
     */
    public function loadData($where = array(), $limit = 0,$order='sort ASC,id DESC'){
        $list=$this->where($where)->order($order)->limit($limit)->select();
        return $list;
    }

    /**
     * 新增
     */
    public function add(){
        return $this->allowField(true)->save($_POST);
    }
    /**
     * 更新
     */
    public function edit(){
        if (empty(input('post.id'))){
            return false;
        }
        $where['id']=input('post.id');
        return $this->allowField(true)->save($_POST,$where);
    }
	/**
     * 获取所有菜单
     */
	public function getMenu($loginUserInfo = array(),$cutUrl = '',$urlComplete = true){
		$list = (array)get_all_service('Menu','Admin');

        if (!empty($loginUserInfo)&&(ADMIN_ID!=1)&&!empty($loginUserInfo['menu_purview'])){
            $list=get_menu_purview($list['data']['list'],$loginUserInfo['menu_purview']);
        }
        return $list;
	}
    /**
     * 获取用户组权限所有菜单
     */
    public function getPurMenu(){
        $list = (array)get_all_service('Menu','Admin');
        $list=$list['data']['list'];
        if ($list){
            $data=array();
            foreach ($list as $key=>$val){
                $data[$key]['id']=$val['id'];
                $data[$key]['pid']=$val['pid'];
                $data[$key]['url']=$val['url'];
                $data[$key]['name']=$val['name'];
                $data[$key]['iconfont']=$val['iconfont'];
                if (!empty($val['act'])){
                    $data[$key]['act']=json_decode($val['act'],true);
                }
                if (!empty($val['sub'])){
                    $i=1000;
                    foreach ($val['sub'] as $kk=>$vv){
                        $data[$key]['sub'][$kk]['id']=$vv['id'];
                        $data[$key]['sub'][$kk]['pid']=$vv['pid'];
                        $data[$key]['sub'][$kk]['url']=$vv['url'];
                        $data[$key]['sub'][$kk]['name']=$vv['name'];
                        $data[$key]['sub'][$kk]['iconfont']=$vv['iconfont'];
                        if (!empty($vv['act'])){
                            $data[$key]['sub'][$kk]['act']=json_decode($vv['act'],true);
                        }
                        if (!empty($vv['sub'])){
                            foreach ($vv['sub'] as $kkk=>$vvv){
                                $i++;
                                $data[$key]['sub'][$i]['id']=$vvv['id'];
                                $data[$key]['sub'][$i]['pid']=$vvv['pid'];
                                $data[$key]['sub'][$i]['url']=$vvv['url'];
                                $data[$key]['sub'][$i]['name']=$vvv['name'];
                                $data[$key]['sub'][$i]['iconfont']=$vvv['iconfont'];
                                if (!empty($vvv['act'])){
                                    $data[$key]['sub'][$i]['act']=json_decode($vvv['act'],true);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
    public function menuLoadlist($id=0){
        $data=$this->getMenuArr();
        $cat = new \org\Category(array('id', 'pid', 'name', 'cname'));
        $data = $cat->getTree($data, intval($id));
        return $data;
    }
    /**
     * 获取一维所有菜单
     */
    public function getMenuArr(){
        $list = (array)get_all_service('Menu','Admin');
        $list=$list['data']['list'];
        if ($list){
            $data=array();
            $i=0;
            foreach ($list as $key=>$val){
                $data[$i]['id']=$val['id'];
                $data[$i]['pid']=$val['pid'];
                $data[$i]['url']=$val['url'];
                $data[$i]['name']=$val['name'];
                $data[$i]['iconfont']=$val['iconfont'];
                $i++;
                if (!empty($val['sub'])){
                    foreach ($val['sub'] as $kk=>$vv){
                        $data[$i]['id']=$vv['id'];
                        $data[$i]['pid']=$vv['pid'];
                        $data[$i]['url']=$vv['url'];
                        $data[$i]['name']=$vv['name'];
                        $data[$i]['iconfont']=$vv['iconfont'];
                        $i++;
                        if (!empty($vv['sub'])){
                            foreach ($vv['sub'] as $kkk=>$vvv){
                                $data[$i]['id']=$vvv['id'];
                                $data[$i]['pid']=$vvv['pid'];
                                $data[$i]['url']=$vvv['url'];
                                $data[$i]['name']=$vvv['name'];
                                $data[$i]['iconfont']=$vvv['iconfont'];
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
    /**
     * 获取所有操作
     */
    public function getPurview(){
        $list = get_all_service('Purview','Admin');
        if(empty($list)){
            return $list;
        }
        return $list;
    }
    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($id){
        $map = array();
        $map['id'] = $id;
        return $this->getWhereInfo($map);
    }
    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where){
        $info = $this->where($where)->find();
        if (!empty($info['act'])){
            $info['act']=json_decode($info['act'],true);
        }
        return $info;
    }
    /**
     * 删除数据
     * @param 栏目id $class_id
     * @return 1|0
     */
    public function del($id){
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }
}