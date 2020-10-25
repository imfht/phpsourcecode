<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use think\Db;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminConfigBuilder;

/**
 * 后台头衔控制器
 */
class Rank extends Admin
{

    /**
     * 头衔管理首页
     * @param int $page
     * @param int $r
     */
    public function index($page = 1, $r = 20)
    {
        //读取数据
        $model = Db::name('Rank');
        $list = Db::name('Rank')->paginate(10);
        // 获取分页显示
        $page = $list->render();
        $list = $list->toArray()['data'];

        foreach ($list as &$val) {
            $val['u_name'] = Db::name('member')->where('uid=' . $val['uid'])->value('nickname');
            $val['types'] = $val['types'] ? lang('_YES_') : lang('_NO_');
            $val['label']='<span class="label" style="border-radius: 20px;background-color:'.$val['label_bg'].';color:'.$val['label_color'].';">'.$val['label_content'].'</span>';
            if($val['logo']==0){
                $val['logo']='';
            }
        }
        $totalCount = $model->count();
        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title(lang('_TITLE_LIST_'))
            ->buttonNew(Url('Rank/editRank'))
            ->keyId()
            ->keyTitle()
            ->keyText('u_name', lang('_UPLOAD_'))
            ->keyImage('logo',lang('_PICTURE_TITLE_'))
            ->keyHtml('label',lang('_WORD_TITLE_'))
            ->keyCreateTime()
            ->keyLink('types', lang('_RECEPTION_IS_AVAILABLE_'), 'changeTypes?id=###')
            ->keyDoActionEdit('editRank?id=###')
            ->keyDoAction('deleteRank?id=###', lang('_DELETE_'))
            ->data($list)
            ->page($page)
            ->display();
    }

    /**
     * 设置头衔前台是否可申请
     * @param null $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function changeTypes($id = null)
    {
        if (!$id) {
            $this->error(lang('_PLEASE_CHOOSE_THE_TITLE_'));
        }
        $types = Db::name('rank')->where(['id'=>$id])->getField('types');
        $types = $types ? 0 : 1;
        $result = Db::name('rank')->where(['id'=>$id])->setField('types', $types);
        if ($result) {
            $this->success(lang('_SET_UP_'));
        } else {
            $this->error(lang('_SET_FAILURE_'));
        }
    }

    /**
     * 删除头衔
     * @param null $id
     */
    public function deleteRank($id = null)
    {
        if (!$id) {
            $this->error(lang('_PLEASE_CHOOSE_THE_TITLE_'));
        }
        $result = Db::name('rank')->where(['id'=>$id])->delete();
        $result1 = Db::name('rank_user')->where('rank_id=' . $id)->delete();
        if ($result) {
            $this->success(lang('_DELETE_SUCCESS_'));
        } else {
            $this->error(lang('_DELETE_FAILED_'));
        }
    }

    /**
     * 编辑头衔
     * @param null $id
     */
    public function editRank($id = null)
    {
        //判断是否为编辑模式
        $isEdit = $id ? true : false;
        if (request()->isPost()) {
            $data['title']=input('post.title','','text');
            $data['logo']=input('post.logo',0,'intval');
            $data['label_content']=input('post.label_content','','text');
            $data['label_color']=input('post.label_color','','text');
            $data['label_bg']=input('post.label_bg','','text');
            $data['types'] = input('post.types',1,'intval');
            $model = Db::name('rank');
            if ($data['title'] == '') {
                $this->error(lang('_PLEASE_FILL_IN_THE_TITLE_'));
            }

            if($data['logo']==''&&$data['label_content']==''){
                $this->error(lang('_THE_TITLE_OF_THE_PICTURE_AND_THE_TITLE_OF_THE_TITLE_'));
            }
            if ($isEdit) {
                $result = $model->where(['id'=> $id])->update($data);
                if (!$result) {
                    $this->error(lang('_CHANGE_FAILED_'));
                }
            } else {
                
                $data['uid'] = is_login();
                $data['create_time'] = time();
                $result = $model->insert($data);
                if (!$result) {
                    $this->error(lang('_CREATE_FAILURE_'));
                }
            }
            $this->success($isEdit ? lang('_EDIT_SUCCESS_') : lang('_ADD_SUCCESS_'), Url('Rank/index'));
        } else {
            $rank['types'] = '1';//默认前台可以申请
            //如果是编辑模式
            if ($isEdit) {
                $rank = Db::name('rank')->where(['id' => $id])->find();
            }
            //显示页面
            $builder = new AdminConfigBuilder();
            $options = array(
                '0' => lang('_NO_'),
                '1' => lang('_YES_')
            );
            $builder
                ->title($isEdit ? lang('_EDIT_TITLE_') : lang('_NEW_TITLE_'))
                ->keyId()
                ->keyTitle()
                ->keySingleImage('logo', lang('_PICTURE_TITLE_'), lang('_THE_ICON_WHICH_DOES_NOT_SET_THE_TEXT_TITLE_THE_SETTING_IS_USEFUL_'))
                ->keyText('label_content',lang('_WORD_TITLE_'))
                ->keyColor('label_color',lang('_TITLE_COLOR_'))
                ->keyColor('label_bg',lang('_TEXT_TITLE_TAG_BACKGROUND_COLOR_'))
                ->keyRadio('types', lang('_RECEPTION_IS_AVAILABLE_'), null, $options)
                ->data($rank)
                ->buttonSubmit(Url('editRank'))->buttonBack()
                ->display();
        }
    }

    /**
     * 用户列表
     */
    public function userList()
    {
        $nickname = input('nickname','','text');
        $map['status'] = array('egt', 0);
        if (is_numeric($nickname)) {
            $map['uid|nickname'] = [intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true];
        } else {
            if ($nickname !== '')
                $map['nickname'] = ['like', '%' . (string)$nickname . '%'];
        }
        list($list,$page) = $this->lists('Member', $map);
        $list = $list->toArray()['data'];

        int_to_string($list);
        $this->assign('_list', $list);
        $this->setTitle(lang('_USER_LIST_'));
        return $this->fetch();
    }

    /**
     * 用户头衔列表
     * @param null $id
     * @param int $page
     */
    public function userRankList($id = null, $page = 1)
    {
        if (!$id) {
            $this->error(lang('_PLEASE_SELECT_THE_USER_'));
        }
        $u_name = Db::name('member')->where('uid=' . $id)->value('nickname');
        
        $rankList = Db::name('rank_user')->where(array('uid' => $id, 'status' => 1))->page($page, 20)->order('create_time asc')->select();

        $totalCount = Db::name('rank_user')->where(array('uid' => $id, 'status' => 1))->count();

        foreach ($rankList as &$val) {
            $val['title'] = D('rank')->where('id=' . $val['rank_id'])->getField('title');
            $val['is_show'] = $val['is_show'] ? lang('_SHOW_') : lang('_NOT_SHOW_');
        }

        $builder = new AdminListBuilder();
        $builder
            ->title($u_name . '的头衔列表')
            ->buttonNew(Url('Rank/userAddRank?id=' . $id), lang('_RELATED_NEW_TITLE_'))
            ->keyId()
            ->keyText('title', lang('_TITLE_NAME_'))
            ->keyText('reason', lang('_CAUSE_'))
            ->keyText('is_show', lang('_IS_SHOWN_ON_THE_RIGHT_SIDE_OF_THE_NICKNAME_'))
            ->keyCreateTime()
            ->keyDoActionEdit('Rank/userChangeRank?id=###')
            ->keyDoAction('Rank/deleteUserRank?id=###', lang('_DELETE_'))
            ->data($rankList)
            ->page($page)
            ->display();
    }

    /**
     * 新增用户头衔关联
     * @param null $id
     * @param string $uid
     * @param string $reason
     * @param string $is_show
     * @param string $rank_id
     */
    public function userAddRank($id = null, $uid = '', $reason = '', $is_show = '', $rank_id = '')
    {
        if (request()->isPost()) {
            $is_Edit = $id ? true : false;
            $data = array('uid' => $uid, 'reason' => $reason, 'is_show' => $is_show, 'rank_id' => $rank_id);
            $model = Db::name('rank_user');
            if ($is_Edit) {
                $data['create_time'] = time();
                $result = $model->where(['id'=>$id])->update($data);
                if (!$result) {
                    $this->error(lang('_RELATED_FAILURE_'));
                }
            } else {
                $rank_user = $model->where(['uid' => $uid, 'rank_id' => $rank_id])->find();
                if ($rank_user) {
                    $this->error(lang('_THE_USER_ALREADY_HAS_THE_TITLE_PLEASE_CHOOSE_ANOTHER_TITLE_'));
                }
               
                $data['create_time'] = time();
                $data['status'] = 1;
                $result = $model->insert($data);
                if (!$result) {
                    $this->error(lang('_RELATED_FAILURE_'));
                } else {
                    $rank = Db::name('rank')->where(['id'=>$data['rank_id']])->find();
                    //$logoUrl=getRootUrl().D('picture')->where('id='.$rank['logo'])->getField('path');
                    //$u_name = D('member')->where('uid=' . $uid)->getField('nickname');
                    $content = lang('_TITLE_AWARD_BY_ADMIN_').lang('_COLON_').'[' . $rank['title'] . ']'; //<img src="'.$logoUrl.'" title="'.$rank['title'].'" alt="'.$rank['title'].'">';

                    $user = query_user(array('username', 'space_link'), $uid);

                    $content1 = lang('_TITLE_AWARD_ADMIN_PARAM_',array('nickname'=>$user['nickname'],'title'=>$rank['title'])) . $reason; //<img src="'.$logoUrl.'" title="'.$rank['title'].'" alt="'.$rank['title'].'">';
                    clean_query_user_cache($uid, array('rank_link'));
                    $this->sendMessage($data, $content);
                }
            }
            $this->success($is_Edit ? lang('_EDIT_ASSOCIATED_SUCCESS_') : lang('_ADD_ASSOCIATED_SUCCESS_'), Url('Rank/userRankList?id=' . $uid));
        } else {
            if (!$id) {
                $this->error(lang('_PLEASE_SELECT_THE_USER_'));
            }
            $data['uid'] = $id;
            $ranks = Db::name('rank')->select();
            if (!$ranks) {
                $this->error(lang('_THERE_IS_NO_TITLE_PLEASE_ADD_A_TITLE_'));
            }
            foreach ($ranks as $val) {
                $rank_ids[$val['id']] = $val['title'];
            }
            $data['rank_id'] = $ranks[0]['id'];
            $data['is_show'] = 1;
            $builder = new AdminConfigBuilder();
            $builder
                ->title(lang('_ADD_TITLE_ASSOCIATION_'))
                ->keyId()->keyReadOnly('uid', lang('_USER_ID_'))->keyText('reason', lang('_RELATED_REASONS_'))->keyRadio('is_show', lang('_IS_SHOWN_ON_THE_RIGHT_SIDE_OF_THE_NICKNAME_'), null, array(1 => lang('_YES_'), 0 => lang('_NO_')))->keySelect('rank_id', lang('_TITLE_NUMBER_'), null, $rank_ids)
                ->data($data)
                ->buttonSubmit(Url('userAddRank'))->buttonBack()
                ->display();
        }
    }

    /**
     * 编辑用户头衔关联
     * @param null $id
     * @param string $uid
     * @param string $reason
     * @param string $is_show
     * @param string $rank_id
     */
    public function userChangeRank($id = null, $uid = '', $reason = '', $is_show = '', $rank_id = '')
    {
        if (request()->isPost()) {
            $is_Edit = $id ? true : false;
            $data = array('uid' => $uid, 'reason' => $reason, 'is_show' => $is_show, 'rank_id' => $rank_id);
            $model = Db::name('rank_user');
            if ($is_Edit) {
                $data['create_time'] = time();
                $result = $model->where(['id'=>$id])->update($data);
                if (!$result) {
                    $this->error(lang('_RELATED_FAILURE_'));
                }
            } else {
                $rank_user = $model->where(array('uid' => $uid, 'rank_id' => $rank_id))->find();
                if ($rank_user) {
                    $this->error(lang('_THE_USER_ALREADY_HAS_THE_TITLE_PLEASE_CHOOSE_ANOTHER_TITLE_'));
                }
                
                $data['create_time'] = time();
                $result = $model->insert($data);

                if (!$result) {
                    $this->error(lang('_RELATED_FAILURE_'));
                } else {
                    $rank = Db::name('rank')->where(['id' => $data['rank_id']])->find();
                    //$logoUrl=getRootUrl().D('picture')->where('id='.$rank['logo'])->getField('path');
                    //$u_name = D('member')->where('uid=' . $uid)->getField('nickname');
                    $content = lang('_TITLE_AWARD_BY_ADMIN_').lang('_COLON_').'[' . $rank['title'] . ']'; //<img src="'.$logoUrl.'" title="'.$rank['title'].'" alt="'.$rank['title'].'">';

                    $user = query_user(array('username', 'space_link'), $uid);

                    $content1 = lang('_TITLE_AWARD_ADMIN_PARAM_',array('nickname'=>$user['nickname'],'title'=>$rank['title'])) . $reason;
                    clean_query_user_cache($uid, array('rank_link'));
                    $this->sendMessage($data, $content);
                }
            }
            $this->success($is_Edit ? lang('_EDIT_ASSOCIATED_SUCCESS_') : lang('_ADD_ASSOCIATED_SUCCESS_'), Url('Rank/userRankList?id=' . $uid));
        } else {
            if (!$id) {
                $this->error(lang('_PLEASE_CHOOSE_THE_TITLE_TO_CHANGE_'));
            }
            $data = Db::name('rank_user')->where(['id'=>$id])->find();
            if (!$data) {
                $this->error(lang('_THE_TITLE_IS_NOT_ASSOCIATED_WITH_THE_TITLE_'));
            }
            $ranks = Db::name('rank')->select();
            if (!$ranks) {
                $this->error(lang('_THERE_IS_NO_TITLE_PLEASE_ADD_A_TITLE_'));
            }
            foreach ($ranks as $val) {
                $rank_ids[$val['id']] = $val['title'];
            }
            $builder = new AdminConfigBuilder();
            $builder
                ->title(lang('_EDIT_TITLE_ASSOCIATION_'))
                ->keyId()
                ->keyReadOnly('uid', lang('_USER_ID_'))
                ->keyText('reason', lang('_RELATED_REASONS_'))
                ->keyRadio('is_show', lang('_IS_SHOWN_ON_THE_RIGHT_SIDE_OF_THE_NICKNAME_'), null, array(1 => lang('_YES_'), 0 => lang('_NO_')))
                ->keySelect('rank_id', lang('_TITLE_NUMBER_'), null, $rank_ids)
                ->data($data)
                ->buttonSubmit(Url('userChangeRank'))
                ->buttonBack()
                ->display();
        }
    }

    /**
     * 删除用户头衔管理
     * @param null $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function deleteUserRank($id = null)
    {
        if (!$id) {
            $this->error(lang('_PLEASE_CHOOSE_THE_TITLE_LINK_'));
        }
        $result = Db::name('rank_user')->where(['id'=>$id])->delete();
        if ($result) {
            $this->success(lang('_DELETE_SUCCESS_'));
        } else {
            $this->error(lang('_DELETE_FAILED_'));
        }
    }

    public function sendMessage($data, $content, $type = '头衔颁发')
    {
        model('Message')->sendMessage($data['uid'], $type, $content, 'ucenter/Message/message',array(),is_login(), 1);
    }

    /**
     * 待审核
     * @param int $page
     */
    public function rankVerify($page = 1)
    {
        $rankList = Db::name('rankUser')->where(['status' => 0])->order('create_time asc')->paginate(20);
        // 获取分页显示
        $page = $rankList->render();
        $rankList = $rankList->toArray()['data'];

        foreach ($rankList as &$val) {
            $val['title'] = Db::name('rank')->where(['id'=>$val['rank_id']])->value('title');
            $val['is_show'] = $val['is_show'] ? lang('_SHOW_') : lang('_NOT_SHOW_');
            //获取用户信息
            $u_user = Db::name('member')->where(['uid'=>$val['uid']])->value('nickname');
            $val['u_name'] = $u_user;
        }
        unset($val);

        $builder = new AdminListBuilder();
        $builder
            ->title(lang('_LIST_OF_TITLES_TO_BE_REVIEWED_'))
            ->buttonSetStatus(Url('setVerifyStatus'), '1', lang('_AUDIT_THROUGH_'), null)
            ->buttonDelete(Url('setVerifyStatus'), lang('_AUDIT_NOT_THROUGH_'))
            ->keyId()
            ->keyText('uid', lang('_USER_ID_'))
            ->keyText('u_name', lang('_USER_NAME_'))
            ->keyText('title', lang('_TITLE_NAME_'))
            ->keyText('reason', lang('_REASONS_FOR_APPLICATION_'))
            ->keyText('is_show', lang('_IS_SHOWN_ON_THE_RIGHT_SIDE_OF_THE_NICKNAME_'))
            ->keyCreateTime()
            ->keyDoActionEdit('Rank/userChangeRank?id=###')
            ->data($rankList)
            ->page($page)
            ->display();
    }

    /**
     * 审核不通过
     * @param int $page
     */
    public function rankVerifyFailure($page = 1)
    {
        $model = Db::name('rankUser');
        $rankList = $model->where(['status' => -1])->order('create_time asc')->paginate(20);

        // 获取分页显示
        $page = $rankList->render();
        $rankList = $rankList->toArray()['data'];

        foreach ($rankList as &$val) {
            $val['title'] = Db::name('rank')->where(['id'=>$val['rank_id']])->value('title');
            $val['is_show'] = $val['is_show'] ? lang('_SHOW_') : lang('_NOT_SHOW_');
            //获取用户信息
            $u_user = Db::name('member')->where(['uid'=>$val['uid']])->value('nickname');
            $val['u_name'] = $u_user;
        }
        unset($val);

        $builder = new AdminListBuilder();
        $builder
            ->title(lang('_THE_TITLE_OF_THE_APPLICATION_FOR_THE_LIST_'))
            ->buttonSetStatus(Url('setVerifyStatus'), '1', lang('_AUDIT_THROUGH_'), null)
            ->keyId()
            ->keyText('uid', lang('_USER_ID_'))
            ->keyText('u_name', lang('_USER_NAME_'))
            ->keyText('title', lang('_TITLE_NAME_'))
            ->keyText('reason', lang('_REASONS_FOR_APPLICATION_'))
            ->keyText('is_show', lang('_IS_SHOWN_ON_THE_RIGHT_SIDE_OF_THE_NICKNAME_'))
            ->keyCreateTime()
            ->keyDoActionEdit('Rank/userChangeRank?id=###')
            ->data($rankList)
            ->page($page)
            ->display();
    }

    public function setVerifyStatus($ids, $status)
    {

        $model_user = Db::name('rankUser');
        $model = Db::name('rank');
        if ($status == 1) {
            foreach ($ids as $val) {
                $rank_user = $model_user->where('id=' . $val)->field('uid,rank_id,reason')->find();
                $rank = $model->where('id=' . $rank_user['rank_id'])->find();
                $content = l('_RECEPTION_TITLE_PASSED_BY_ADMIN_').lang('_COLON_').'[' . $rank['title'] . ']';

                $user = query_user(array('nickname', 'space_link'), $rank_user['uid']);

                $content1 = lang('_RECEPTION_PASSED_BY_ADMIN_PARAM_',array('nickname'=>$user['nickname'],'title'=>$rank['title'])) . $rank_user['reason'];
                clean_query_user_cache($rank_user['uid'], array('rank_link'));
                $this->sendMessage($rank_user, $content, lang('_TITLE_APPLICATION_FOR_APPROVAL_'));
            }
        } else if ($status = -1) {
            foreach ($ids as $val) {
                $rank_user = $model_user->where('id=' . $val)->field('uid,rank_id')->find();
                $rank = $model->where('id=' . $rank_user['rank_id'])->find();
                $content = lang('_ASK_REFUSED_BY_ADMIN_').lang('_COLON_').'[' . $rank['title'] . ']';
                $this->sendMessage($rank_user, $content, lang('_THE_TITLE_OF_THE_APPLICATION_FOR_APPROVAL_IS_NOT_PASSED_'));
            }
        }
        $builder = new AdminListBuilder();
        $builder->doSetStatus('rankUser', $ids, $status);
    }
}
