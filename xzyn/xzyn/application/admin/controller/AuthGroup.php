<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\AuthGroup as AuthGroups;
use app\common\model\AuthRule;
use app\common\model\AuthGroupAccess;
use app\common\model\User;

class AuthGroup extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new AuthGroups;   //别名：避免与控制名冲突
    }

    public function index()
    {
        $where = [];
        if (input('get.search')){
            $where[] = ['title|notation','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'module asc,level desc,id asc';
        }
        $dataList = $this->cModel->where($where)->order($order)->paginate(15);
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    public function create() {	//添加
        if (request()->isPost()){
            $data = input('post.');
            $data['rules'] = $data['rules'] ? implode(',', $data['rules']) : '';
			$result = $this->validate($data,C_NAME.'.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data);
			}
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            $arModel = new AuthRule();
            $authRuleTree = $arModel->treeList();
            $this->assign('authRuleTree', $authRuleTree);   //树形权限节点列表
            return $this->fetch('edit');
        }
    }

    public function edit($id) {	//编辑
        if (request()->isPost()){
            $data = input('post.');
            if ( isset($data['rules']) ){
                $data['rules'] = $data['rules'] ? implode(',', $data['rules']) : '';
            }
            if (count($data) == 2){
                foreach ($data as $k =>$v){
                    $fv = $k!='id' ? $k : '';
                }
				$result = $this->validate($data,C_NAME.'.'.$fv);
            }else{
            	$result = $this->validate($data,C_NAME.'.edit');
            }
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data, $data['id']);
			}
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            $data = $this->cModel->get($id);
            $this->assign('data', $data);

            $arModel = new AuthRule();
            $authRuleTree = $arModel->treeList('', 1);   //树形权限节点列表

            $rulesArr = explode(',', $data['rules']);   //以前就拥有的权限节点
            foreach ($authRuleTree as $k => $val){
                if(in_array($val['id'], $rulesArr)){
                    $authRuleTree[$k]['ischeck'] = 'y';
                }else {
                    $authRuleTree[$k]['ischeck'] = 'n';
                }
            }

            $this->assign('authRuleTree', $authRuleTree);
            return $this->fetch();
        }
    }

    public function delete() {	//删除
        if (request()->isPost()){
            $id = input('id');
            if (isset($id) && !empty($id)){
                $id_arr = explode(',', $id);
				if( !empty($id_arr) ){
					foreach ($id_arr as $k => $v) {
						if( $v == 1 || $v == 2 || $v ==3 ){
							return ajaxReturn('系统默认角色,不能删除');
						}
					}
				}
                $where[] = [ 'id','in', $id_arr ];
                $result = $this->cModel->where($where)->delete();

                $where[] = [ 'group_id','in', $id_arr ];
                $agaModel = new AuthGroupAccess();
                $agaModel->where($where)->delete();
                if ($result){
                    return ajaxReturn('操作成功', url('index'));
                }else{
                    return ajaxReturn('操作失败');
                }
            }
        }
    }

    public function authUser($id) {	//授权用户
        $agaModel = new AuthGroupAccess();
        if (request()->isPost()){
            $data = input('post.');
            $group_id = $data['id'];   //当前角色ID
            $uid = $data['uid'];   //新提交授权用户数组:[1,2,3,4....]

            $oldData = $agaModel->where(['group_id' => $group_id])->select();
            $oldUser = array();   //以前授权用户
            $mixArr = array();   //交集授权用户
            $addArr = array();   //新增授权用户
            $delArr = array();   //删除授权用户
        	foreach ($oldData as $k =>$v){
                $oldUser[] = $v['uid'];
            }
            $mixArr = array_intersect($uid, $oldUser);
            if (empty($mixArr)){
                $addArr = $uid;
                $delArr = $oldUser;
            }else{
                $addArr = array_diff($uid, $mixArr);
                $delArr = array_diff($oldUser, $mixArr);
            }
            if (!empty($delArr)){
                $where = [
                    'group_id' => $group_id
                ];
                $agaModel->whereIn('uid',$delArr)->where($where)->delete();
            }
            if (!empty($addArr)){
                $addList = array();
                foreach ($addArr as $k => $v){
                    $addList[] = ['group_id' => $group_id, 'uid' => $v];
                }
                $agaModel->saveAll($addList, false);
            }
            return ajaxReturn('操作成功', url('index'));
        }else{
            $authList = $agaModel->alias('a')->join('user u','a.uid = u.id')
                ->field('u.id,u.username,u.name')
                ->where(['group_id' => $id])->select();   //已经拥有权限用户

            $uModel = new User();
            $userList = $uModel->field('id,username,name')->select();   //全部用户

            foreach ($userList as $k => $v){   //删除全部用户中已授权用户
                foreach ($authList as $k2 => $v2){
                    if ($v['id'] == $v2['id']){
                        unset($userList[$k]);
                        break;
                    }
                }
            }
            $this->assign('id', $id);
            $this->assign('userList', $userList);
            $this->assign('authList', $authList);
            return $this->fetch();
        }
    }
}