<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Controller;
use app\common\model\Member;
use app\common\model\Follow;
use app\common\model\Tile;

class Index extends Admin
{
    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index()
    {
        if (UID) {

            $aRefresh = input('get.refresh', 0, 'intval');
            if($aRefresh == 1) {
                //一些刷新操作

            }

            $tileModel = new Tile();
            $list = $tileModel->where(array('status' => 1))->order('sort asc')->select();
            foreach($list as &$key) {
                $key['url'] = url($key['url']);
                $key['url_vo'] = url($key['url_vo']);
            }
            $this->assign('list', $list);
            $this->assign('meta_title', lang('_INDEX_MANAGE_'));
            $this->assign('count', $this->getUserCount());

            return $this->fetch();
        } else {
            $this->redirect('admin/Base/login');
        }
    }

    public function stats()
    {
        if ($this->request->isPost()) {
            switch (input('post.method','','text')) {
                case 'saveUserCount': {
                    $this->saveUserCount();
                    break;
                }
                case 'savePortletSort': {
                    $this->savePortletSort();
                    break;
                }
            }
        } else {
            $this->assign('meta_title', lang('_SITE_STATISTICS_'));
            $this->assign('count', $this->getUserCount());
            $this->assign('portlets',userC('PORTLET_SORT',''));

            $this->getOtherCount();
            $this->display();
        }
    }

    private function getOtherCount(){
        $countModel=model('Count');
        list($lostList,$totalCount)=$countModel->getLostListPage($map=1,1,5);
        foreach($lostList as &$val){
            $val['date']=time_format($val['date'],'Y-m-d');
            $val['rate']=($val['rate']*100)."%";
        }
        unset($val);
        $this->assign('lostList',$lostList);

        $today=date('Y-m-d 00:00',time());
        $startTime=strtotime($today." - 10 day");
        $endTime=strtotime($today);
        $consumptionList=$countModel->getConsumptionList($startTime,$endTime);
        $this->assign('consumptionList',json_encode($consumptionList));

        $startTime=strtotime(date('Y-m-d').' - 9 day');
        $activeList=$countModel->getActiveList($startTime,time(),'day');
        $this->assign('activeList',json_encode($activeList));

        $startTime=strtotime(date('Y-m-d').' - '.date('w').' day - 49 day');
        $weekActiveList=$countModel->getActiveList($startTime,time(),'week');
        $this->assign('weekActiveList',json_encode($weekActiveList));

        $startTime=strtotime(date('Y-m-01').' - 9 month');
        $monthActiveList=$countModel->getActiveList($startTime,time(),'month');
        $this->assign('monthActiveList',json_encode($monthActiveList));

        $startTime=strtotime($today." - 9 day");
        $endTime=strtotime($today." - 2 day");
        $remainList=$countModel->getRemainList($startTime,$endTime);
        $this->assign('remainList',$remainList);
        return true;
    }

    private function getUserCount(){
        $today = date('Y-m-d', time());
        $today = strtotime($today);

        config('COUNT_DAY',7);
        $count_day = config('COUNT_DAY');
        $count['count_day'] = $count_day;
        for ($i = $count_day; $i--; $i >= 0) {
            $day = $today - $i * 86400;
            $day_after = $today - ($i - 1) * 86400;
            $week_map = array('Mon' => lang('_MON_'), 'Tue' => lang('_TUES_'), 'Wed' => lang('_WEDNES_'), 'Thu' => lang('_THURS_'), 'Fri' => lang('_FRI_'), 'Sat' => '<strong>' . lang('_SATUR_') . '</strong>', 'Sun' => '<strong>' . lang('_SUN_') . '</strong>');
            $week[] = date('m月d日 ', $day) . $week_map[date('D', $day)];
            $user = UCenterMember()->where('status=1 and reg_time >=' . $day . ' and reg_time < ' . $day_after)->count() * 1;
            $registeredMemeberCount[] = $user;
            if ($i == 0) {
                $count['today_user'] = $user;
            }
        }
        $week = json_encode($week);
        $this->assign('week', $week);
        $count['total_user'] = $userCount = UCenterMember()->where(array('status' => 1))->count();
        $count['today_action_log'] = db('ActionLog')->where('status=1 and create_time>=' . $today)->count();
        $count['last_day']['days'] = $week;
        $count['last_day']['data'] = json_encode($registeredMemeberCount);
        $count['now_inline']=db('Session')->where(1)->count()*1;
        return $count;
    }

    /**
     * 保存用户统计设置
     */
    private function saveUserCount()
    {
        $count_day = input('post.count_day', config('COUNT_DAY'), 'intval', 7);
        if (db('Config')->where(array('name' => 'COUNT_DAY'))->setField('value', $count_day) === false) {
            $this->error(lang('_ERROR_SETTING_') . lang('_PERIOD_'));
        } else {
            cache('DB_CONFIG_DATA', null);
            $this->success(lang('_SUCCESS_SETTING_'), 'refresh');
        }
    }

    /**
     * 保存Portlet排序
     */
    private function savePortletSort()
    {
        set_user_config('PORTLET_SORT',input('post.data'));
    }

    /**
     * 添加常用操作
     * @author 路飞<lf@ourstu.com>
     */
    public function addTo()
    {
        $tileId = input('post.id', '', 'intval');

        $rs = db('tile')->where(array('aid' => $tileId, 'status' => 1))->find();
        if($rs) {
            return array('status' => 0, 'info' => '请勿重复添加！');
        } else {
            $nav = model('Menu')->getPath($tileId);
            $max = model('Tile')->max('sort');

            $data['aid'] = $tileId;
            $data['icon'] = 'direction';
            $data['sort'] = $max+1;
            $data['status'] = 1;
            $data['title'] = $nav[1]['title'];
            $data['title_vo'] = $nav[0]['title'];
            $data['url'] = $nav[1]['url'];
            $data['url_vo'] = $nav[0]['url'];
            $data['tile_bg'] = '#1ba1e2';

            $res = db('tile')->add($data);
            if($res) {
                return array('status' => 1, 'info' => '添加成功');
            } else {
                return array('status' => 0, 'info' => '添加失败');
            }
        }
    }

    /**
     * 删除常用操作
     * @author 路飞<lf@ourstu.com>
     */
    public function delTile()
    {
        $tileId = input('post.id', '', 'intval');

        $res = db('tile')->where(array('id' => $tileId))->delete();
        if($res) {
            return array('status' => 1, 'info' => '删除成功', 'tile_id' => $tileId);
        } else {
            return array('status' => 0, 'info' => '删除失败');
        }
    }

    /**
     * 修改常用操作
     * @author 路飞<lf@ourstu.com>
     */
    public function setTile()
    {
        $tileId = input('post.id', '', 'intval');
        $tileIcon = input('post.icon', '', 'text');
        $tileBg = input('post.tile_bg', '', 'text');

        $data['icon'] = substr($tileIcon, 5);
        $data['tile_bg'] = $tileBg;
        $res = db('tile')->where(array('id' => $tileId))->save($data);

        if($res){
            return array('status' => 1, 'info' => '保存成功', 'tile_id' => $tileId, 'tile_icon' => $data['icon'], 'tile_bg' => $tileBg);
        }else{
            return array('status' => 0, 'info' => '保存失败');
        }
    }

    /**
     * 常用操作排序
     * @author 路飞<lf@ourstu.com>
     */
    public function sortTile()
    {
        $ids = input('post.ids', '', 'text');

        $i = 1;
        foreach($ids as $val) {
            if($val) {
                $val = substr($val,5);
                model('Tile')->where(array('id' => $val))->setField(array('sort' => $i));
                $i++;
            }
        }
    }



}