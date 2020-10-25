<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;


class EventController extends AdminController
{
    protected $eventModel;
    protected $eventTypeModel;

    function _initialize()
    {
        $this->eventModel = D('Event/Event');
        $this->eventTypeModel = D('Event/EventType');
        parent::_initialize();
    }
    public function config()
    {
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();

//dump($data);
        $admin_config->title(L('_EVENT_BASIC_CONF_'))->data($data)
            ->keyBool('NEED_VERIFY', L('_EVENT_CREATE_AUDIT_'),L('_AUDIT_DEFAULT_NO_NEED_'))
            ->keyBool('SHENHE_SEND_WEIBO', L('_EVENT_AUDIT_SEND_FRESH_'),L('_EVENT_AUDIT_SEND_FRESH_DEFAULT_'))->keyDefault('SHENHE_SEND_WEIBO',0)

            ->keyText('EVENT_SHOW_TITLE', L('_TITLE_NAME_'), L('_HOME_BLOCK_TITLE_'))->keyDefault('EVENT_SHOW_TITLE','热门活动')
            ->keyText('EVENT_SHOW_COUNT', '显示活动的个数', '只有在网站首页模块中启用了活动模块之后才会显示')->keyDefault('EVENT_SHOW_COUNT',4)
            ->keyRadio('EVENT_SHOW_TYPE', '活动的删选范围', '', array('1' => L('_BG_RECOMMEND_'), '0' => L('_EVERYTHING_')))->keyDefault('EVENT_SHOW_TYPE',0)
            ->keyRadio('EVENT_SHOW_ORDER_FIELD', L('_SORT_VALUE_'), L('_TIP_SORT_VALUE_'), array('view_count' => L('_VIEWS_'), 'create_time' => L('_DELIVER_TIME_'), 'update_time' => L('_UPDATE_TIME_'),'attentionCount'=>'报名人数'))->keyDefault('EVENT_SHOW_ORDER_FIELD','view_count')
            ->keyRadio('EVENT_SHOW_ORDER_TYPE', L('_SORT_TYPE_'), L('_TIP_SORT_TYPE_'), array('desc' => L('_COUNTER_'), 'asc' => L('_DIRECT_')))->keyDefault('EVENT_SHOW_ORDER_TYPE','desc')
            ->keyText('EVENT_SHOW_CACHE_TIME', L('_CACHE_TIME_'),L('_TIP_CACHE_TIME_'))->keyDefault('EVENT_SHOW_CACHE_TIME','600')

            ->group(L('_EVENT_BASIC_CONF_'),'NEED_VERIFY,SHENHE_SEND_WEIBO')->group(L('_HOME_SHOW_CONF_'), 'EVENT_SHOW_COUNT,EVENT_SHOW_TITLE,EVENT_SHOW_TYPE,EVENT_SHOW_ORDER_TYPE,EVENT_SHOW_ORDER_FIELD,EVENT_SHOW_CACHE_TIME')

            ->groupLocalComment(L('_LOCAL_COMMENT_CONF_'),'event')
            ->buttonSubmit('', L('_SAVE_'));
        $admin_config->display();
    }
    public function event($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => array('in','0,1'));
        $model = $this->eventModel;
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();

        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        $builder->title(L('_CONTENT_MANAGE_'))
            ->setStatusUrl(U('setEventContentStatus'))->buttonSetStatus(U('setEventContentStatus'),2, L('_AUDIT_UNSUCCESS_'),array())->buttonDelete()->button(L('_RECOMMEND_MAKE_UP_'), array_merge($attr, array('url' => U('doRecommend', array('tip' => 1)))))->button(L('_RECOMMEND_CANCEL_'), array_merge($attr, array('url' => U('doRecommend', array('tip' => 0)))))
            ->keyId()->keyLink('title', L('_TITLE_'), 'Event/Index/detail?id=###')->keyUid()->keyCreateTime()->keyStatus()->keyMap('is_recommend', L('_RECOMMEND_YES_OR_NOT_'), array(0 => L('_NOT_'), 1 => L('_YES_')))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 设置推荐or取消推荐
     * @param $ids
     * @param $tip
     * autor:xjw129xjt
     */
    public function doRecommend($ids, $tip)
    {
        D('Event')->where(array('id' => array('in', $ids)))->setField('is_recommend', $tip);
        $this->success(L('_SUCCESS_SETTING_'), $_SERVER['HTTP_REFERER']);
    }

    /**
     * 审核页面
     * @param int $page
     * @param int $r
     * autor:xjw129xjt
     */
    public function verify($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 2);
        $model = $this->eventModel;
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        $builder->title('审核内容')
            ->setStatusUrl(U('setEventContentStatus'))->buttonEnable('', L('_AUDIT_SUCCESS_'))->buttonDelete()
            ->keyId()->keyLink('title', L('_TITLE_'), 'Event/Index/detail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 设置状态
     * @param $ids
     * @param $status
     * autor:xjw129xjt
     */
    public function setEventContentStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        if ($status == 1) {
            foreach ($ids as $id) {
                $content = D('Event')->find($id);
                D('Common/Message')->sendMessage($content['uid'],$title = L('_MESSAGE_AUDIT_EVENT_CONTENT_'), L('_MESSAGE_AUDIT_EVENT_CONTENT_VICE_'),  'Event/Index/detail', array('id' => $id ), is_login(), 2);
                if(modC('SHENHE_SEND_WEIBO',0,'Event')){
                    if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                        /*同步微博*/
                        $user = query_user(array('username', 'space_link'), $content['uid']);
                        $weibo_content = L('_MESSAGE_AUDIT_EVENT_CONTENT1_') . $user['username'] . L('_MESSAGE_AUDIT_EVENT_CONTENT2_') . $content['title'] . L('_MESSAGE_AUDIT_EVENT_CONTENT3_') . "http://$_SERVER[HTTP_HOST]" . U('Event/Index/detail', array('id' => $content['id']));
                        $model = D('Weibo/Weibo');
                        $model->addWeibo(is_login(), $weibo_content);
                        /*同步微博end*/
                    }
                }
            }

        }
        $builder->doSetStatus('Event', $ids, $status);

    }

    public function contentTrash($page = 1, $r = 10)
    {
        //显示页面
        $builder = new AdminListBuilder();
        $builder->clearTrash('Event');
        //读取微博列表
        $map = array('status' => -1);
        $model = D('Event');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        $builder->title(L('_CONTENT_TRASH_'))
            ->setStatusUrl(U('setEventContentStatus'))->buttonRestore()->buttonClear()
            ->keyId()->keyLink('title', L('_TITLE_'), 'Event/Index/detail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }


    public function index()
    {

        //显示页面
        $builder = new AdminTreeListBuilder();

        $tree = D('Event/EventType')->getTree(0, 'id,title,sort,pid,status');


        $builder->title(L('_EVENT_CATEGORY_MANAGE_'))
            ->buttonNew(U('Event/add'))->setLevel(1)
            ->data($tree)
            ->display();
    }

    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
            if ($id != 0) {
                $eventtype = $this->eventTypeModel->create();
                if ($this->eventTypeModel->save($eventtype)) {

                    $this->success(L('_SUCCESS_EDIT_'));
                } else {
                    $this->error(L('_FAIL_EDIT_'));
                }
            } else {
                $eventtype = $this->eventTypeModel->create();
                if ($this->eventTypeModel->add($eventtype)) {

                    $this->success(L('_SUCCESS_ADD_'));
                } else {
                    $this->error(L('_FAIL_ADD_'));
                }
            }

        } else {
            $builder = new AdminConfigBuilder();
            $eventtypes =$this->eventTypeModel->where(array('pid'=>0))->select();
            $opt = array();
            foreach ($eventtypes as $eventtype) {
                $opt[$eventtype['id']] = $eventtype['title'];
            }
            if ($id != 0) {
                $eventtype = $this->eventTypeModel->find($id);
            } else {
                $eventtype = array('pid' => $pid, 'status' => 1);
            }


            $builder->title(L('_CATEGORY_ADD_'))->keyId()->keyText('title', L('_TITLE_'))
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($eventtype)
                ->buttonSubmit(U('Event/add'))->buttonBack()->display();
        }

    }

    public function operate($type = 'move', $from = 0)
    {
        $builder = new AdminConfigBuilder();
        $from = D('EventType')->find($from);

        $opt = array();
        $types = $this->eventTypeModel->select();
        foreach ($types as $event) {
            $opt[$event['id']] = $event['title'];
        }
        if ($type === 'move') {

            $builder->title(L('_CATEGORY_MOVE_'))->keyId()->keySelect('pid',L('_FATHER_CLASS_'), L('_FATHER_CLASS_SELECT_'), $opt)->buttonSubmit(U('Event/add'))->buttonBack()->data($from)->display();
        } else {

            $builder->title(L('_CATEGORY_COMBINE_'))->keyId()->keySelect('toid', L('_CATEGORY_T_COMBINE_'), L('_CATEGORY_T_COMBINE_SELECT_'), $opt)->buttonSubmit(U('Event/doMerge'))->buttonBack()->data($from)->display();
        }

    }

    public function doMerge($id, $toid)
    {
        $effect_count=D('Event')->where(array('type_id'=>$id))->setField('type_id',$toid);
        D('EventType')->where(array('id'=>$id))->setField('status',-1);
        $this->success(L('_SUCCESS_CATEGORY_COMBINE_') . $effect_count . L('_CONTENT_GE_'), U('index'));
        //TODO 实现合并功能 issue
    }




    public function eventTypeTrash($page = 1, $r = 20)
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash('EventType');

        //读取微博列表
        $map = array('status' => -1);
        $model = $this->eventTypeModel;
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面

        $builder->title(L('_EVENT_TYPE_TRASH_'))
            ->setStatusUrl(U('setStatus'))->buttonRestore()->buttonClear()
            ->keyId()->keyText('title', L('_TITLE_'))->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }
    /**
     * 设置活动分类状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setStatus($ids, $status)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        if(in_array(1,$ids)){
            $this->error(L('_ERROR_EVENT_ID_DELETE_').L('_EXCLAMATION_'));
        }
        $builder = new AdminListBuilder();
        $builder->doSetStatus('EventType', $ids, $status);
    }
}
