<?php
namespace app\member\controller;

use app\common\controller\BaseMember;
use app\common\model\User;
use app\common\model\UserInfo;

class Users extends BaseMember
{
    public function initialize(){
        parent::initialize();
    }

    public function index() {
		if(request()->isPost()){
			$userModel = new User();
			$data = input('post.');

			if ($data['actions'] == 'password'){    //修改密码

				$result = $this->validate($data,'User.password');
				if(true !== $result){
				    // 验证失败 输出错误信息
				    return ajaxReturn($result);
				}else{
					$result = $userModel->allowField(true)->save($data, $data['id']);
				}

			}elseif($data['actions'] == 'wximg'){	//修改微信图片
				$userInfoModel = new UserInfo();
				$userdata = $userInfoModel->where(['uid'=>$data['id']])->find();
				delimg($userdata['wx_imgurl']);	//删除之前图片
				$result = $userInfoModel->allowField(true)->save($data,['uid'=>$data['id']]);
			}else{	//修改信息
				$data['is_share'] = json_encode($data['row']);
				$result = $this->validate($data,'User.edit');
				if(true !== $result){
				    // 验证失败 输出错误信息
				    return ajaxReturn($result);
				}else{
					$result = $userModel->allowField(true)->save($data, $data['id']);
					if($result){
						$datas = ['qq'=>$data['qq'],'weixin'=>$data['weixin'],'birthday'=>$data['birthday'],'info'=>$data['info']];
						$userModel->userInfo->save($datas,['uid'=>$data['id']]);
					}
				}
			}
            if ($result){
                return ajaxReturn('修改成功', url('index'),1);
            }
		}else{
			return $this->fetch();
		}
    }



}
