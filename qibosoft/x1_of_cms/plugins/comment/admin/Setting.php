<?php
namespace plugins\comment\admin;

use app\common\controller\admin\Setting AS _Setting;
use think\Db;

class Setting extends _Setting
{    
    /**
     * 参数设置
     * {@inheritDoc}
     * @see \app\common\controller\admin\Setting::index()
     */
    public function index($group=null){
        //不存在参数设置,自动创建
        if (empty($this->getNavIds())) {
            $data = [
                    'title'=>'参数设置',
                    'sys_id'=> $this->getSysId(),
                    'list'=>0,
                    'ifsys'=>0,
                    'ifshow'=>0,
            ];
            $groupid = Db::name('config_group')->insert($data,false,true);
        }
        
        //强制要加上的参数,即使用户删除了,也会自动补上
        $this->config = [
                [
                        'c_key'=>'can_post_comment_group',
                        'title'=>'允许发表评论的用户组',
                        'c_value'=>'',
                        'form_type'=>'checkbox',
                        'options'=>'app\\common\\model\\Group@getTitleList@[{"id":["<>",2]}]',
                        'ifsys'=>0,
                        'list'=>100,
                ],
                [
                        'c_key'=>'post_auto_pass_comment_group',
                        'title'=>'发布评论自动通过审核的用户组',
                        'c_value'=>'',
                        'form_type'=>'checkbox',
                        'options'=>'app\\common\\model\\Group@getTitleList@[{"id":["<>",2]}]',
                        'ifsys'=>0,
                        'list'=>99,
                ],
                [
                        'c_key'=>'allow_guest_post_comment',
                        'title'=>'是否允许游客进行评论',
                        'c_value'=>'',
                        'form_type'=>'radio',
                        'options'=>"0|不允许\r\n1|允许评论",
                        'ifsys'=>0,
                        'list'=>98,
                ],
                [
                        'c_key'=>'guest_auto_pass_comment',
                        'title'=>'游客评论是否自动通过审核',
                        'c_value'=>'',
                        'form_type'=>'radio',
                        'options'=>"0|未审核\r\n1|自动通过审核",
                        'ifsys'=>0,
                        'list'=>96,
                ],
			
				[
                        'c_key'=>'forbid_comnent_phone_noyz',
                        'title'=>'未验证手机是否禁止发评论',
                        'c_value'=>'0',
                        'form_type'=>'radio',
                        'options'=>"0|不限\r\n1|未验证不允许发布",
                        'ifsys'=>0,
                        'list'=>0,
                ],
				[
                        'c_key'=>'forbid_pass_phone_noyz',
                        'title'=>'未验证手机是否不给通过审核',
                        'c_value'=>'0',
                        'form_type'=>'radio',
                        'options'=>"0|不限制\r\n1|限制",
                        'ifsys'=>0,
                        'list'=>0,
                ],
        ];
        return parent::index($group);
    }
}

