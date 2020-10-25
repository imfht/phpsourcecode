<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminSortBuilder;
use think\Db;

class Schedule extends Admin
{
    /**
     * scheduleList  计划任务列表
     * @author:59262424@qq.com（大蒙）
     */
    public function scheduleList()
    {
        $list = model('common/Schedule')->where(['status'=>['neq',-1]])->select();
        $list = collection($list)->toArray();
        foreach ($list as &$v) {
            list($type, $value) = $this->getTypeAndValue($v['type'], $v['type_value']);
            $v['type_text'] = $type;
            $v['type_value_text'] = $value;
            $v['next_run'] = model('common/Schedule')->calculateNextTime($v);
            $v['last_run'] = model('common/Schedule')->getLastUpdate($v['id']);
        }
        unset($v);

        //显示页面
        $btn_attr['style'] = 'font-weight:700';
        $btn_attr['hide-data'] = 'true';
        $btn_attr['href'] = url('Schedule/run');
        //控制运行按钮文字
        if(model('common/Schedule')->checkIsRunning()){
            $btn_info = '<i class="icon icon-stop"></i>（点击停止）';
            $btn_attr['class'] = 'ajax-post btn-danger';
        }else{
            $btn_info = '<i class="icon icon-play"></i>（点击运行）';
            $btn_attr['class'] = 'ajax-post btn-info';
        }

        $builder = new AdminListBuilder();

        $builder->title('计划任务')
            ->tips('Tips：执行时间较长的计划任务会影响到其他计划任务时间的计算；')
            ->button($btn_info, $btn_attr)
            ->setStatusUrl(url('setScheduleStatus'));

        $btn_attr['href'] = url('Schedule/reRun');
        $btn_attr['class'] = 'btn-warning ajax-post re_run';
        $btn_attr['onclick'] = 'javascript:$(this).text("重启中，请不要做其他操作...")';

        $builder
            ->button('重启计划任务', $btn_attr);
        $builder
            ->buttonNew(url('Schedule/editSchedule'))
            ->buttonDelete()
            ->keyId()
            ->keyText('method', '执行方法')
            ->keyText('args', '参数')
            ->keyText('type_text', '类型')
            ->keyText('type_value_text', '设定时间')
            ->keyTime('start_time', '开始时间')
            ->keyTime('end_time', '结束时间')
            ->keyTime('last_run', '上次执行时间')
            ->keyTime('next_run', '下次执行时间')
            //->keyCreateTime()
            ->keyStatus();

        $builder->keyDoActionEdit('editSchedule?id=###');
        $builder->keyDoActionModalPopup('showLog?id=###', '查看日志', '日志', ['data-title' => '日志']);
        $builder->keyDoActionAjax('execute?id=###','立即执行','btn-danger');//立即执行
        $builder->data($list);
        $builder->explain('计划任务说明','鉴于通过php执行计划任务的稳定性较差，也可第三方接口通过执行【'.url('admin/scheduleRun/index').'】以url的方式执行计划任务');
        $builder->display();
    }

    /**
     * 手动立即执行一条任务
     */
    public function execute()
    {
        $id = input('id',0,'intval');
        //获取该计划任务
        $schedule = model('common/Schedule')->getSchedule($id);
        //执行计划任务
        $res = model('common/Schedule')->runSchedule($schedule);

        if($res){
            $this->success('执行了');
        }else{
            $this->error(model('common/Schedule')->getError());
        }
    }

    /**
     * setScheduleStatus  禁用/启用/删除计划任务
     */
    public function setScheduleStatus(){
        $ids = input('ids');
        $status = input('get.status', 0, 'intval');

        cache('schedule_list',null);

        $builder = new AdminListBuilder();
        $builder->doSetStatus('Schedule', $ids, $status);
    }

    /**
     * showLog  显示日志
     */
    public function showLog()
    {
        $aId = input('id', 0, 'intval');

        $log = model('common/Schedule')->getLog($aId);
        if ($log) {
            $log = explode("\n", $log);
        }
        $this->assign('log', $log);
        $this->assign('id', $aId);
        return $this->fetch();
    }

    /**
     * clearLog  清空日志
     */
    public function clearLog()
    {
        $aId = input('post.id', 0, 'intval');
        $model = model('common/Schedule');
        $rs = $model->clearLog($aId);
        $this->success('清空成功', 'refresh');
    }

    /**
     * editSchedule  新增/编辑计划任务
     */
    public function editSchedule()
    {
        $aId = input('id', 0, 'intval');
        if (request()->isPost()) {
            $data['id'] = $aId;
            $aMethod = $data['method'] = input('post.method', '', 'text');
            $aArgs = $data['args'] = input('post.args', '', 'text');
            $aType = $data['type'] = input('post.type_key', 0, 'intval');
            $aTypeValue = $data['type_value'] = input('post.type_value', '', 'text');
            $aStartTime = $data['start_time'] = input('post.start_time', 0, 'intval');
            $aEndTime = $data['end_time'] = input('post.end_time', 0, 'intval');
            $aIntro = $data['intro'] = input('post.intro', '', 'text');
            $aLever = $data['lever'] = input('post.lever', '', 'text');

            if (empty($aMethod)) {
                $this->error('请填写执行方法');
            }
            if (empty($aType)) {
                $this->error('请选择类型');
            }
            if (empty($aTypeValue)) {
                $this->error('请填写设置值');
            }
            if ($aType != 1) {
                if (empty($aStartTime)) {
                    $this->error('请填写开始时间');
                }
                if (empty($aEndTime)) {
                    $this->error('请填写结束时间');
                }
            }

            if (empty($aIntro)) {
                $this->error('请填写介绍');
            }

            if ($aType == 1) {
                $data['type_value'] = strtotime($data['type_value']);
            }

            $res = model('Schedule')->editSchedule($data);

            if ($res) {
                $this->success(($aId == 0 ? '添加' : '编辑') . '成功', Url('scheduleList'));
            } else {
                $this->error(($aId == 0 ? '添加' : '编辑') . '失败');
            }

        } else {
            $builder = new AdminConfigBuilder();

            if ($aId != 0) {
                $tip = '编辑';
                $schedule = Db::name('Schedule')->find($aId);
                $schedule['type_key'] = $schedule['type']; //当name为type时select有点错误。不知道为什么，用其他变量替换

            } else {
                $tip = '新增';
                $schedule = [];
            }
            $type_value_html = '';

            $builder
                ->title($tip . '计划任务')
                ->keyId()
                ->keyText('method', "执行方法", "只能执行Model中的方法，如 <span style='color: red'>Home/Index->test</span> 则表示执行 model('Home/Index')->test();")
                ->keyText('args', "执行参数", "url的写法，如 <span style='color: red'>a=1&b=2</span> ")
                ->keySelect('type_key', '类型', '计划任务的类型', [1 => '执行一次', 2 => '每隔一段时间执行', 3 => '每个时间点执行'])
                ->keyUserDefined('type_value', '设定时间', '', 'admin@schedule/edit', ['schedule' => $schedule])
                ->keyTime('start_time', '开始时间')
                ->keyTime('end_time', '结束时间')
                ->keyTextArea('intro', '介绍', '该介绍将会被写入日志')
                ->keyText('lever', '优先级')
                ->data($schedule)
                ->buttonSubmit(url('Schedule/editSchedule'))
                ->buttonBack()
                ->display();
        }
    }

    /**
     * getTypeAndValue   获取计划任务类型和值
     * @param $type
     * @param $value
     * @return array
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    private function getTypeAndValue($type, $value)
    {
        switch ($type) {
            case 1:
                $type = '执行一次';
                $value = date('Y-m-d h:i', $value);
                break;
            case 2:
                $type = '每隔一段时间执行';
                break;
            case 3:
                $type = '每个时间点执行';
                break;
        }

        return array($type, $value);
    }

    /**
     * run  运行/停止计划任务
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function run()
    {
        $model = model('common/Schedule');
        //dump(file_get_contents(APP_PATH.'../data/schedule/lock.txt'));exit;
        if ($model->checkIsRunning()) {
            $model->setStop();
            $this->success('设置成功~已停止！');
        } else {
            $this->_run();
            $this->success('设置成功~运行中！');
        }
    }

    /**
     * reRun  重启计划任务
     * @author:大蒙 59262424@qq.com
     */
    public function reRun()
    {
        $model = model('common/Schedule');
        $model->setStop();
        //}
        $this->_run();
        $this->success('successfully');
    }

    /**
     * _run  运行计划任务
     * @author:大蒙
     */
    private function _run()
    {  
        $time = time();
        $url = url('api/Schedule/runSchedule', ['time' => $time, 'token' => md5($time . config('database.auth_key'))],'html',true);
        $SSL = substr($url, 0, 8) == "https://" ? true : false;  
        $CA = false; //HTTPS时是否进行严格认证 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);  //设置过期时间为1秒，防止进程阻塞

        if ($SSL) {  
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        }
        curl_setopt($ch, CURLOPT_USERAGENT, '');
        curl_setopt($ch, CURLOPT_REFERER, 'b');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);

        //var_dump($url);  //查看报错信息 
        curl_close($ch);
    }

    public function debug()
    {
        model('admin/Count')->dayCount();
    }

}
