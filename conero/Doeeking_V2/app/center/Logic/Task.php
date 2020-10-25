<?php
/*
 *  2017年2月18日 星期六 任务提醒
 *
 */
namespace app\center\Logic;
use app\center\Logic\Controller;
use hyang\Util;
class Task extends Controller{
    public function init(&$opts,$action){
        if($action == 'index'){
            $js = $opts['js'];
            $js[] = 'index/task';
            $opts['js'] = $js;
        }elseif($action == 'edit'){
            $js = $opts['js'];
            $js[] = 'index/task_edit';
            $opts['js'] = $js;
            $opts['require'] = ['datetimepicker'];
        }
    }
    // 主页
    public function main()
    {
        $task = model('Task');
        $code = uInfo('code');
        $count = $task->where('user_code',$code)->count();
        $endCount = $task->where(['user_code'=>$code,'end_mk'=>'Y'])->count();

        $pages = [
            'count'     => $count,
            'needctt'   => ($count - $endCount)
        ];        
        $this->assign('pages',$pages);
        
        if($count && $count >0){
            
            $app = $this->app;
            $bstp = $app->bootstrap();
            $wh = $bstp->getSearchWhere('code');
            $count = $task->where($wh)->count();
            $html = $bstp->GridSearchForm(['__cols__'=>['task'=>'任务','task_stime'=>'任务创建时间','task_etime'=>'任务结束时间','end_mk'=>'是否结束','dateline'=>'截止日期'],'ipts'=>'<input type="hidden" name="task">']);
            $this->assign('searchfrom',$html);
        
            // $map = ['user_code'=>$code];
            $page = $bstp->page_decode();
            $data = $task->where($wh)->order('end_mk,task_stime desc')->page($page,30)->select();
            $trs = '';
            $ctt = 1;
            foreach($data as $v){
                $v = $v->toArray();
                $listno = $v['listno'];
                $etime = $v['task_etime']? $v['task_etime'] : '<label><input type="checkbox" class="mk_task_end" value="'.$listno.'"> 结束任务</label>';
                $endMk = $v['end_mk'];
                $vendMk = ($endMk == 'Y' && !empty($v['task_stime']) && !empty($v['task_etime']))? 'Y/ '.getDays($v['task_etime'],$v['task_stime']) .' 天':$endMk;
                $dateline = ($v['dateline'] && $endMk == 'N')? '还有【'.getDays($v['dateline'],date('Y-m-d')).'】天('.$v['dateline'].')': ($v['dateline']? $v['dateline']:'未设置');
                $trs .= '<tr dataid="'.$listno.'"'.($endMk == 'N'? ' class="danger"':'').'>
                    <td>'.$ctt.'</td>
                    <td>'.($v['task_url']? '<a href="'.$v['task_url'].'" target="_blank">'.$v['task'].'</a>':$v['task']).'</td>
                    <td>'.$v['task_stime'].'</td><td>'.$etime.'</td>
                    <td>'.$vendMk.'</td><td>'.$dateline.'</td>
                    <td><a href="'.urlBuild('!.index/save/task',['__get' => ['uid' => bsjson(['mode'=>'D','listno'=>$listno])]]).'" class="task_del_link">删除</a> <a href="'.urlBuild('!.index/edit/task/'.$listno).'">修改</a></td>
                    </tr>';
                $ctt++; 
            }
            $this->assign('trs',$trs);

            $this->assign('pageBar',$bstp->pageBar($count));
        }

        return $this->fetch('task');
    }
    // 数据编辑页面
    public function edit($view)
    {
        $this->viewInit($view);
        $editParam = [
            'navbar'    => '<li><a href="/conero/center.html?task">任务提醒</a></li>',
            'navActive' => '编辑'
        ];
        $listno = getUrlBind('task');        
        if($listno){
            $data = $this->app->croDb('sys_taskrpt')->where('listno',$listno)->find();
            $data['listno'] = '<input type="hidden" name="listno" value="'.$listno.'"><input type="hidden" name="mode" value="M">';
        }
        else $data = [
            'task_stime' => date('Y-m-d')
        ];
        $this->assign('data',$data);

        $this->editPageParam($editParam);
        $this->form($view);
    }
    // 数据保存
    public function save(){
        $ret = '(_-_) 数据维护失败。';
        $task = model('Task');
        $data = count($_POST)>0? $_POST:$_GET;
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode) unset($data['mode']);        
        $data = Util::dataClear($data,['task_etime','dateline']);
        // 快速结束任务
        if(isset($data['task_end_tip'])){
            $list = explode(',',base64_decode($data['task_end_tip']));
            $ctt = 0;
            foreach($list as $v){
                if($task->where('listno',$v)->update([
                    'end_mk'=>'Y',
                    'task_etime'=> sysdate()
                ])) $ctt += 1;
            }
            $ret = '成功结束任务【'.$ctt.'】/【'.count($list).'】。';
            // println($list);
        }        
        // 数据修改
        elseif($mode == 'M'){
            $map = ['listno'=>$data['listno']];
            unset($data['listno']);
            if($data['end_mk'] == 'Y') $data['task_etime'] = sysdate();
            $ret = ($task->where($map)->update($data))? '数据修改成功':'很遗憾，修改失败了。';
        }
        elseif(isset($data['uid'])){
            $uid = bsjson($data['uid']);
            // if(isset($uid['mode']) && $uid['mode'] == 'D'){
            if(isset($uid['mode']) && $uid['mode'] == 'D'){
                $this->app->pushRptBack('sys_taskrpt',['listno'=>$uid['listno']],true);
                if($task->where('listno',$uid['listno'])->delete()) $this->success('数据被成功删除！');
                else $this->error('糟糕，数据被删除失败！');
            }
            println($uid);die;
        } 
        elseif($mode == 'A'){
            $data['user_code'] = uInfo('code');
            // 未设置时 taskid 时 为自定义 任务提醒
            if(!isset($data['taskid'])){
                $data['taskid'] = 'self_'.date('ymd',time()).rand(1,999999);
                $data['remark'] = (empty($data['remark'])? '':$data['remark']).' 用户新增了自定义任务提醒[系统]';
            }
            if($task->insert($data)) $ret = '您新增一条数据';
            else $this->success('糟糕，数据新增失败！');
            // println($data);die;
        }
        $this->success($ret);
    }
}