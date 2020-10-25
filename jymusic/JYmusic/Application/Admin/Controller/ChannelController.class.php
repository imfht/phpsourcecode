<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;

/**
 * 后台频道控制器
 * @author 战神~~巴蒂 <378020023@qq.com>
 */

class ChannelController extends AdminController {

    /**
     * 频道列表
     */
    public function index(){
        /* 获取频道列表 */
        $map  = array('status' => array('gt', -1));
        $list = $this->lists(M('Channel'),$map,'sort asc,id asc',true);
        $this->assign('list', $list);
        $this->meta_title = '导航管理';
        $this->display();
    }

    /**
     * 添加频道
     */
    public function add(){
        if(IS_POST){
            $Channel = D('Channel');
            $data = $Channel->create();
            if($data){
                $id = $Channel->add();
                if($id){
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_channel', 'channel', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Channel->getError());
            }
        } else {
            $channel = M('Channel')->field(true)->select();
            $channel = D('Common/Tree')->toFormatTree($channel,$title = 'title');
            $channel = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级导航')), $channel);
			$this->assign('Channel', $channel);
			$this->assign('info', null);
            $this->meta_title = '新增导航';
            $this->display('edit');
        }
    }

    /**
     * 编辑频道
     */
    public function edit($id = 0){
        if(IS_POST){
            $Channel = D('Channel');
            $data = $Channel->create();
            if($data){
                if($Channel->save()){
                    //记录行为
                    action_log('update_channel', 'channel', $data['id'], UID);
                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($Channel->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Channel')->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $channel = M('Channel')->field(true)->select();
            $channel = D('Common/Tree')->toFormatTree($channel,$title = 'title');
            $channel = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级导航')), $channel);
			$this->assign('Channel', $channel);
            $this->assign('info', $info);
            $this->meta_title = '编辑导航';
            $this->display();
        }
    }

    /**
     * 删除频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Channel')->where($map)->delete()){
            //记录行为
            action_log('update_channel', 'channel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 导航排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');
            $pid = I('get.pid');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }else{
                if($pid !== ''){
                    $map['pid'] = $pid;
                }
            }
            $list = M('Channel')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '导航排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Channel')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！');
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}