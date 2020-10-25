<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Admin\Builder\AdminListBuilder;

/**
 * 行为控制器
 * @author huajie <banhuajie@163.com>
 */
class ActionController extends AdminController {

    /**
     * 行为日志列表
     * @author huajie <banhuajie@163.com>
     */
    public function actionLog(){
        //获取列表数据
        $aUid=I('get.uid',0,'intval');
        if($aUid) $map['user_id']=$aUid;

        //按时间和行为筛选   路飞
        $sTime=I('post.sTime',0,'text');
        $eTime=I('post.eTime',0,'text');
        $aSelect=I('post.select',0,'intval');
        if($sTime && $eTime) {
            $map['create_time']=array('between',array(strtotime($sTime),strtotime($eTime)));
        }
        if($aSelect) {
            $map['action_id'] = $aSelect;
        }

        $map['status']    =   array('gt', -1);
        $list   =   $this->lists('ActionLog', $map);
        int_to_string($list);
        foreach ($list as $key=>$value){
            $model_id                  =   get_document_field($value['model'],"name","id");
            $list[$key]['model_id']    =   $model_id ? $model_id : 0;
            $list[$key]['ip']=long2ip($value['action_ip']);
        }

        $actionList = D('Action')->select();
        $this->assign('action_list', $actionList);

        $this->assign('_list', $list);
        $this->meta_title = L('_BEHAVIOR_LOG_');
        $this->display();
    }
    public function scoreLog($r=20,$p=1){

        if(I('type')=='clear'){
            D('ScoreLog')->where(array('id>0'))->delete();
            $this->success('清空成功。',U('scoreLog'));
            exit;
        }else{
            $aUid=I('uid',0,'');
            if($aUid){
                $map['uid']=$aUid;
            }
            $listBuilder=new AdminListBuilder();
            $listBuilder->title('积分日志');
            $map['status']    =   array('gt', -1);
            $scoreLog=D('ScoreLog')->where($map)->order('create_time desc')->findPage($r);

            $scoreTypes=D('Ucenter/Score')->getTypeListByIndex();
            foreach ($scoreLog['data'] as &$v) {
                $v['adjustType']=$v['action']=='inc'?'增加':'减少';
                $v['scoreType']=$scoreTypes[$v['type']]['title'];
                $class=$v['action']=='inc'?'text-success':'text-danger';
                $v['value']='<span class="'.$class.'">' .  ($v['action']=='inc'?'+':'-'). $v['value']. $scoreTypes[$v['type']]['unit'].'</span>';
                $v['finally_value']= $v['finally_value']. $scoreTypes[$v['type']]['unit'];
            }


            $listBuilder->data($scoreLog['data']);

            $listBuilder->keyId()->keyUid('uid','用户')->keyText('scoreType','积分类型')->keyText('adjustType','调整类型')->keyHtml('value','积分变动')->keyText('finally_value','积分最终值')->keyText('remark','变动描述')->keyCreateTime();
            $listBuilder->pagination($scoreLog['count'],$r);
            $listBuilder->search(L('_SEARCH_'),'uid','text','输入UID');

            $listBuilder->button('清空日志',array('url'=>U('scoreLog',array('type'=>'clear')),'class'=>'btn ajax-get confirm'));
            $listBuilder->display();
        }



    }

    /**
     * 查看行为日志
     * @author huajie <banhuajie@163.com>
     */
    public function edit($id = 0){
        empty($id) && $this->error(L('_PARAMETER_ERROR_'));

        $info = M('ActionLog')->field(true)->find($id);

        $this->assign('info', $info);
        $this->meta_title = L('_CHECK_THE_BEHAVIOR_LOG_');
        $this->display();
    }

    /**
     * 删除日志
     * @param mixed $ids
     * @author huajie <banhuajie@163.com>
     */
    public function remove($ids = 0){
        empty($ids) && $this->error(L('_PARAMETER_ERROR_'));
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $res = M('ActionLog')->where($map)->delete();
        if($res !== false){
            $this->success(L('_DELETE_SUCCESS_'));
        }else {
            $this->error(L('_DELETE_FAILED_'));
        }
    }

    /**
     * 清空日志
     */
    public function clear(){
        $res = M('ActionLog')->where('1=1')->delete();
        if($res !== false){
            $this->success(L('_LOG_EMPTY_SUCCESSFULLY_'));
        }else {
            $this->error(L('_LOG_EMPTY_'));
        }
    }

        /**
     * 导出csv
     * @author 路飞<lf@ourstu.com>
     */
    public function csv()
    {
        $aIds = I('ids', array());

        if(count($aIds)) {
            $map['id'] = array('in', $aIds);
        } else {
            $map['status'] = 1;
        }

        $list = M('ActionLog')->where($map)->order('create_time asc')->select();

        $data = L('_DATA_MORE_')."\n";
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

}
