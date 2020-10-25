<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-4
 * Time: 下午5:09
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace News\Widget;

use News\Model\NewsModel;
use Think\Action;

class UcenterBlockWidget extends Action
{
    public function render($uid = 0, $page = 1, $tab = null, $count = 10)
    {
        !$uid && $uid = is_login();
        //查询条件
        $map['uid']=$uid;

        $NewsModel = new NewsModel();
        /* 获取当前分类下资讯列表 */
        if($uid!=is_login()){
            $map['status']=1;
            $map['dead_line']=array('gt',time());
        }
        list($list,$totalCount) = $NewsModel->getListByPage($map,$page,'update_time desc','*',$count);
        foreach($list as &$val){
            if($val['status']==1){
                $val['audit_status']='<span style="color: green;">'.L('_AUDIT_SUCCESS_').'</span>';
            }elseif($val['status']==2){
                $val['audit_status']='<span style="color:#4D9EFF;">'.L('_AUDIT_READY_').'</span>';
            }elseif($val['status']==-1){
                $val['audit_status']='<span style="color: #b5b5b5;">'.L('_AUDIT_FAIL_').'</span>';
            }
        }
        unset($val);
        /* 模板赋值并渲染模板 */
        $this->assign('news_list', $list);
        $this->assign('totalCount',$totalCount);

        $this->display(T('News@Widget/ucenterblock'));
    }
} 