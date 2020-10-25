<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use app\admin\builder\AdminListBuilder;
use think\Db;

/**
 * 行为控制器
 */
class Action extends Admin {

    /**
     * 行为日志列表
     * @author huajie <banhuajie@163.com>
     */
    public function actionLog(){
        //获取列表数据
        $aUid=input('get.uid',0,'intval');
        if($aUid) $map['user_id']=$aUid;

        //按时间和行为筛选   路飞
        $sTime=input('post.sTime',0,'text');
        $eTime=input('post.eTime',0,'text');
        $aSelect=input('post.select',0,'intval');
        if($sTime && $eTime) {
            $map['create_time']=array('between',array(strtotime($sTime),strtotime($eTime)));
        }
        if($aSelect) {
            $map['action_id'] = $aSelect;
        }

        $map['status']    =   array('gt', -1);
        list($list,$page)   =   $this->commonLists('ActionLog', $map);
        
        $list = $list->toArray()['data'];
        int_to_string($list);
        //dump($list);
        foreach ($list as $key=>$value){
            //$model_id                  =   get_document_field($value['model'],"name","id");
            //$list[$key]['model_id']    =   $model_id ? $model_id : 0;
            $list[$key]['ip']=long2ip($value['action_ip']);
        }


        $actionList = Db::name('Action')->select();
        $this->assign('action_list', $actionList);

        $this->assign('_list', $list);
        $this->setTitle(lang('_BEHAVIOR_LOG_'));
        return $this->fetch();
    }

    /**
     * 积分日志
     * @param  integer $r [description]
     * @param  integer $p [description]
     * @return [type]     [description]
     */
    public function scoreLog($r=20){

        if(input('type') == 'clear'){
            Db::name('ScoreLog')->where(['id'=>['>',0]])->delete();
            $this->success('清空成功。',url('scoreLog'));
            exit;
        }else{
            $aUid=input('uid',0,'');
            $map=[];
            if($aUid){
                $map['uid']=$aUid;
            }
            
            $scoreLog=Db::name('ScoreLog')->where($map)->order('create_time desc')->paginate($r);
            $totalCount=Db::name('ScoreLog')->count();
            //分页HTML
            $page = $scoreLog->render();
            //转数组处理
            $scoreLog = $scoreLog->toArray()['data'];

            $scoreTypes=model('ucenter/Score')->getTypeListByIndex();

            foreach ($scoreLog as &$v) {
                if(empty($v['uid'])) $v['uid'] = 0;
                $v['adjustType']=$v['action']=='inc'?'增加':'减少';
                $v['scoreType']=$scoreTypes[$v['type']]['title'];
                $class=$v['action']=='inc'?'text-success':'text-danger';
                $v['value']='<span class="'.$class.'">' .  ($v['action']=='inc'?'+':'-'). $v['value']. $scoreTypes[$v['type']]['unit'].'</span>';
                $v['finally_value']= $v['finally_value']. $scoreTypes[$v['type']]['unit'];
            }
            unset($v);
            //dump($scoreLog);
            $listBuilder=new AdminListBuilder();

            $listBuilder->title('积分日志');

            $listBuilder->data($scoreLog);

            $listBuilder->page($page);

            $listBuilder->keyId()->keyUid('uid','用户')->keyText('scoreType','积分类型')->keyText('adjustType','调整类型')->keyHtml('value','积分变动')->keyText('finally_value','积分最终值')->keyText('remark','变动描述')->keyCreateTime();

            $listBuilder->search(lang('_SEARCH_'),'uid','text','输入UID');

            $listBuilder->button('清空日志',['url'=>Url('scoreLog',['type'=>'clear']),'class'=>'btn btn-danger ajax-get confirm']);
            $listBuilder->display();
        }
    }

    /**
     * 查看行为日志
     * @author huajie <banhuajie@163.com>
     */
    public function detail($id = 0){
        empty($id) && $this->error(lang('_PARAMETER_ERROR_'));

        $info = Db::name('ActionLog')->field(true)->find($id);

        $this->assign('info', $info);
        $this->setTitle(lang('_CHECK_THE_BEHAVIOR_LOG_'));
        return $this->fetch();
    }

    /**
     * 删除日志
     * @param mixed $ids
     * @author huajie <banhuajie@163.com>
     */
    public function remove($ids = 0){
        empty($ids) && $this->error(lang('_PARAMETER_ERROR_'));
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $res = Db::name('ActionLog')->where($map)->delete();
        if($res !== false){
            $this->success(lang('_DELETE_SUCCESS_'));
        }else {
            $this->error(lang('_DELETE_FAILED_'));
        }
    }

    /**
     * 清空日志
     */
    public function clear(){
        $res = Db::name('ActionLog')->where('1=1')->delete();
        if($res !== false){
            $this->success(lang('_LOG_EMPTY_SUCCESSFULLY_'));
        }else {
            $this->error(lang('_LOG_EMPTY_'));
        }
    }

    /**
     * 导出csv
     */
    public function csv()
    {
        $aIds = input('ids','','text');

        if($aIds){
            $aIds = explode(',',$aIds);
        }
        if(count($aIds)) {
            $map['id'] = array('in', $aIds);
        } else {
            $map['status'] = 1;
        }

        $list = collection(Db::name('ActionLog')->where($map)->order('create_time asc')->select())->toArray();
        //dump($list);exit;
        
        $data = lang('_DATA_MORE_')."\n";
        foreach ($list as $val) {
            $val['create_time'] = time_format($val['create_time']);
            $data.=$val['id'].",".get_action($val['action_id'], 'title').",".get_nickname($val['user_id']).",".long2ip($val['action_ip']).",".$val['remark'].",".$val['create_time']."\n";
        }
        $data = iconv('utf-8', 'gb2312', $data);
        $filename = date('Ymd').'.csv'; //设置文件名
        $this->export_csv($filename, $data); //导出
    }

    private function export_csv($filename, $data)
    {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        echo $data;
    }


    /**
     * 用户行为列表
     */
    public function action()
    {
        $aModule = $this->parseSearchKey('module');

        is_null($aModule) && $aModule = -1;
        if ($aModule != -1) {
            $map['module'] = $aModule;
        }
        unset($_REQUEST['module']);
        $this->assign('current_module', $aModule);
        $map['status'] = array('gt', -1);
        //获取列表数据
        $Action = Db::name('Action')->where(['status' => ['gt', -1]]);

        $list = model('action')->getListByPage($map,'update_time desc','*',20);
        $page = $list->render();
        $this->assign('page',$page);

        $list = $list->toArray()['data'];

        lists_plus($list);

        int_to_string($list);

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);

        $module = model('common/Module')->getAll();
        foreach ($module as $key => $v) {
            if ($v['is_setup'] == 0) {
                unset($module[$key]);
            }
        }
        $module = array_merge([array('name' => '', 'alias' => lang('_SYSTEM_'))], $module);

        $this->assign('module', $module);

        $this->setTitle(lang('_USER_BEHAVIOR_'));

        return $this->fetch();
    }

    protected function parseSearchKey($key = null)
    {
        $action = request()->module() . '_' . request()->controller() . '_' . request()->action();
        $post = input('post.');
        if (empty($post)) {
            $keywords = cookie($action);
        } else {
            $keywords = $post;
            cookie($action, $post);
            $_GET['page'] = 1;
        }

        if (empty($_GET['page'])) {
            cookie($action, null);
            $keywords = null;
        }
        return $key ? $keywords[$key] : $keywords;
    }

    /**
     * 新增、编辑行为
     * @author dameng <59262424@qq.com>
     */
    public function editAction()
    {
        if(request()->isPost()){
            /* 获取数据对象 */
            $data = input('');
            $res = model('common/Action')->editAction($data);
            if (!$res) {
                $this->error(model('common/Action')->getError());
            } else {
                $this->success($res['id'] ? lang('_UPDATE_SUCCESS_') : lang('_NEW_SUCCESS_'), Cookie('__forward__'));
            }
        }else{
            $id = input('id');
            //empty($id) && $this->error(lang('_PARAMETERS_CANT_BE_EMPTY_'));
            if($id){
                $data = Db::name('Action')->field(true)->find($id);

            }else{
                //初始默认数据
                $data = [
                    'name'=>'',
                    'title'=>'',
                    'log'=>'',
                    'module'=>'',
                    'remark'=>'',
                    'rule'=>'',
                    'id'=>''
                ];
            }

            $this->assign('data', $data);
            $module = model('common/Module')->getAll();
            $this->assign('module', $module);

            $this->setTitle(lang('_EDITING_BEHAVIOR_'));

            return $this->fetch();
        }
        
    }

}
