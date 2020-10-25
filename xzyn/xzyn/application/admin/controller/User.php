<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\User as Users;
use app\common\model\AuthGroup;
use app\common\model\AuthGroupAccess;
use app\common\model\UserInfo;
use think\Db;

class User extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new Users;   //别名：避免与控制名冲突
    }

    public function index()
    {
        $where = [];
        if (input('get.search')){
            $where[] = ['username|name|email|moblie','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'id desc';
        }
        $dataList = $this->cModel->where($where)->order($order)->paginate(15);
        $agMolde = new AuthGroup();
        $agList = $agMolde->select();
        $agListArr = [];
        $agListArrPic = [];
        foreach ($agList as $k => $v){
            $agListArr[$v['id']] = $v['title'];
            $agListArrPic[$v['id']] = $v['pic'];
        }
        foreach ($dataList as $k => $v){
            $v->userGroup;
            if (!empty($v['userGroup'])){
                foreach ($v['userGroup'] as $k2 => $v2){
                    $v['userGroup'][$k2]['title'] = $agListArr[$v2['group_id']];
                    $v['userGroup'][$k2]['pic'] = $agListArrPic[$v2['group_id']];
                }
            }
        }
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    public function create() {	//添加
        if (request()->isPost()){
            $data = input('post.');
			$result = $this->validate($data,C_NAME.'.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data);
				$uid = $this->cModel->getLastInsID();
	            $uiModel = new UserInfo();
	            $infoData = ['uid' => $uid];
	            $result2 = $uiModel->data($infoData, true)->save();
			}
            if ($result && $result2){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            return $this->fetch('create');
        }
    }

    public function edit($id) {	//编辑
        if (request()->isPost()){
            $data = input('post.');
            if ($data['actions'] == 'password'){    //修改密码
                if ( $data['id'] == UID ){          //修改他人密码需验证旧密码
                    $result = $this->validate($data,C_NAME.'.password');
					if( true !== $result ){
						return ajaxReturn($result);
					}else{
                        if ( $data['id'] != UID ){
                            $oldData = $this->cModel->where(['id' => $data['id'], 'password' => md5($data['oldpassword'])])->find();
                            if (empty($oldData)){
                                ajaxReturn('旧密码错误，请重新填写');
                            }
                        }
						$result = $this->cModel->allowField(true)->save($data, $data['id']);
					}
                }
            }elseif ($data['actions'] == 'avatar'){   //修改头像
                $uiModel = new UserInfo();
                $where = ['uid' => $data['id']];
                unset($data['actions']);
                $result = $uiModel->allowField(true)->where($where)->update($data);
            }elseif ($data['actions'] == 'infos'){   //修改附加信息
                $uiModel = new UserInfo();
                $where = ['uid' => $data['id']];
                if ( isset($data['birthday']) ){
                    $data['birthday'] = strtotime($data['birthday']);
                }
                unset($data['actions']);
                $result = $uiModel->allowField(true)->where($where)->update($data);
            }else{   //修改信息
                if (count($data) == 3){
                	unset($data['actions']);
                    foreach ($data as $k =>$v){
                        $fv = $k!='id' ? $k : '';
                    }
					$result = $this->validate($data,C_NAME.'.'.$fv);
					if( true !== $result ){
						return ajaxReturn($result);
					}else{
						$result = $this->cModel->allowField(true)->save($data, $data['id']);
					}
                }else{
                	$result = $this->validate($data,C_NAME.'.edit');
					if( true !== $result ){
						return ajaxReturn($result);
					}else{
						$result = $this->cModel->allowField(true)->save($data, $data['id']);
					}
                }
            }
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            if ($id > 0){
                $data = $this->cModel->get($id);
                $data->userInfo;   //用户附加信息数据
                $this->assign('data', $data);
                return $this->fetch();
            }
        }
    }

    public function delete() {	//删除用户数据
        if (request()->isPost()){
            $id = input('id');
            if (isset($id) && !empty($id)){
	            $id_arr = explode(',', $id);   //用户数据
	            $where1[] = [ 'uid','in', $id_arr ];
	            $uiModel = new UserInfo();
	            $data = $uiModel->where($where1)->select();   //查询用户附加表信息【用于删除头像】

	            $where2[] = [ 'id','in', $id_arr ];
	            $result = $this->cModel->where($where2)->delete();   //删除主表数据

	            $agaModel = new AuthGroupAccess();
	            $agaModel->where($where1)->delete();   //删除用户分配角色

	            $result2 = $uiModel->where($where1)->delete();   //删除用户附加表
	            // 提交事务
	            if ($result && $result2){
	                foreach ($data as $k => $v){
	                    if ( !empty($v['avatar']) && $v['avatar'] != '/static/common/img/default.png'){
	                    	if( file_exists(WEB_PATH.$v['avatar']) ){
	                        	unlink(WEB_PATH.$v['avatar']);   //删除头像文件
							}
	                    }
	                }
					$Archive = new \app\common\model\Archive;
					$ArchiveReply = new \app\common\model\ArchiveReply;
					$ZanLog = new \app\common\model\ZanLog;
					$LoginLog = new \app\common\model\LoginLog;
					$TokenUser = new \app\common\model\TokenUser;

					$where_arc[] = ['writer','in',$id_arr];
					$Archive_data = $Archive->where( $where_arc )->select();
					if( !empty($Archive_data) ){
						$ArchiveReply_id = $ArchiveReply->where([ [ 'uid','in', $id_arr ] ])->column('id');
						if( !empty($ArchiveReply_id) ){
							foreach ($ArchiveReply_id as $k => $v){
								$ArchiveReply_zid = $ArchiveReply->where([ [ 'pid','in', $v ] ])->column('id');
								$ArchiveReply->where([ [ 'id','in', $ArchiveReply_zid ] ])->delete();	//删除文章回复 [子级回复]
							}
						}
						$ArchiveReply->where([ [ 'uid','in', $id_arr ] ])->delete();	//删除文章回复
						foreach ($Archive_data as $k => $v){
							if( !empty($v['litpic']) && $v['litpic'] != '/static/common/img/logo.jpg' ){
	                    		if ( file_exists(WEB_PATH.$v['litpic']) ){
	                    			unlink(WEB_PATH.$v['litpic']);	//删除文章图片
								}
							}
							db($v['mod'])->where(['aid' => $v['id']])->delete();	//删除文章关联数据
							$ZanLog->where( ['aid' => $v['id']] )->delete();	//删除 赞记录
							$ArchiveReply->where( ['aid' => $v['id']] )->delete();	//删除文章回复
						}
						$Archive->where($where_arc)->delete();	//删除文章
					}
					$ZanLog->where([ [ 'uid','in', $id_arr ] ])->delete();	//删除 赞记录
					$LoginLog->where([ [ 'uid','in', $id_arr ] ])->delete();	//删除 登录记录
					$TokenUser->where([ [ 'uid','in', $id_arr ] ])->delete();	//删除 Token 记录
					db('Collect')->where([ [ 'uid','in', $id_arr ] ])->delete();   //删除 收藏文章的记录
	                return ajaxReturn('操作成功', url('index'));
	            }else{
	                return ajaxReturn('操作失败');
	            }
            }
        }
    }

    public function authGroup($id) {	//授权角色
        $agaModel = new AuthGroupAccess;
        if (request()->isPost()){
            $data = input('post.');
            $uid = $data['id'];
            $group_id = $data['group_id'];
            $where = ['uid' => $uid];
            $agaModel->where($where)->delete();
            if (!empty($group_id)){
                $addList = array();
                foreach ($group_id as $k =>$v){
                    $addList[] = ['uid' => $uid, 'group_id' => $v];
                }
                $agaModel->saveAll($addList, false);
            }
            return ajaxReturn('操作成功', url('index'));
        }else{
            if ($id > 0){
                $agModel = new AuthGroup();
                $groupList = $agModel->where(['status' => 1])->order('module ASC,level ASC,id ASC')->select();   //所有正常角色
                $userGroup = $agaModel->where(['uid' => $id])->select();   //当前用户已拥有角色
                foreach ($groupList as $k => $v){
                    foreach ($userGroup as $k2 => $v2){
                        if ($v2['group_id'] == $v['id']){
                            $groupList[$k]['ischeck'] = 'y';
                            break;
                        }
                    }
                }
                $data = $this->cModel->get($id);
                $this->assign('data', $data);
                $this->assign('groupList', $groupList);
                return $this->fetch();
            }
        }
    }
}