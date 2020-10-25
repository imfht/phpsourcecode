<?php

/**
 * 系统首页
 */

namespace app\system\admin;

class IndexAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '系统首页',
                'description' => '系统基本信息参数',
            ),
        );
    }

    /**
     * 首页
     */
    public function index() {
        target('system/Statistics', 'service')->refreshStats();
        $this->assign('useInfo', \dux\Config::get('dux.use_info'));
        $this->assign('verInfo', \dux\Config::get('dux.use_ver'));

        $startDate = request('get', 'start_date');
        $stopDate = request('get', 'stop_date');
        $pageMaps = [];
        $pageMaps['start_date'] = $startDate;
        $pageMaps['stop_date'] = $stopDate;

        $this->assign('pageMaps', $pageMaps);
        $this->assign('statsData', target('system/SystemStatistics')->dataStats($startDate, $stopDate));
        $this->assign('sumStats', target('system/SystemStatistics')->countStats());
        $this->assign('contentStats', target('site/SiteContent')->countList());
        $this->systemDisplay();
    }

    /**
     * 个人资料
     */
    public function userData() {
        if(!isPost()) {
            $this->assign('info',  target('system/SystemUser')->getInfo(USER_ID));
            $this->systemDisplay();
        }else{
            $post = request('post');
            $data = array();
            $data['username'] = $post['username'];
            $data['nickname'] = $post['nickname'];
            if($data['password']) {
                $data['password'] = md5($data['password']);
            }
            $data['user_id'] = USER_ID;
            $data = target('system/SystemUser')->create($data);
            if(!$data) {
                $this->error(target('system/SystemUser')->getError());
            }
            if(!target('system/SystemUser')->edit($data)) {
                $this->error('修改资料失败!');
            }
            $this->success('修改资料成功!');

        }
    }

    /**
     * 获取授权信息
     */
    public function licence() {
        $verInfo = \dux\Config::get('dux.use_ver');
        $useInfo = \dux\Config::get('dux.use');
        $url = 'http://www.duxphp.com/service/query/update';
        $data = \dux\lib\Http::curlPost($url, [
            'label' => 'b2c',
            'ver' => $verInfo['ver'],
            'date' => $verInfo['date'],
            'domain' => $_SERVER['HTTP_HOST'],
            'code' => $useInfo['service_key']
        ]);
        if(empty($data)) {
            $this->error('授权服务器暂时无法链接！');
        }
        $data = json_decode($data, true);
        if(empty($data)) {
            $this->error('授权信息不正常！');
        }
        if($data['code'] <> 200) {
            $this->error($data['message'] ? $data['message'] : '授权信息不正常！');
        }
        $this->success($data['message']);
    }

}