<?php
namespace Addon\Bridge\Bench\Controller;
use Addon\Bridge\Model\Bridge;
use Core\AddonController;

class ConnectController extends AddonController {
    public function weixinAction() {
        C('FRAME_CURRENT', $this->U('connect/weixin'));
        util_curd($this, 'weixin');
    }
    
    public function weixinList() {
        $b = new Bridge($this->addon);
        $condition = '';
        $pars = array();
        $platforms = $b->table('__BR_BRIDGES__')->where($condition)->bind($pars)->select();
        $this->assign('platforms', $platforms);
        $this->display('weixins');
    }

    public function weixinCreate() {
        if(IS_POST) {
            $rec = coll_elements(array('title', 'url', 'token', 'remark'), I('post.'));
            if(empty($rec['title']) || empty($rec['url']) || empty($rec['token'])) {
                $this->error('请填写完整后保存');
            }
            $b = new Bridge($this->addon);
            $rec = $b->create($rec);
            if(!empty($rec)) {
                $this->success('保存接入平台成功', $this->U('connect/weixin'));
                exit;
            } else {
                $this->error('保存失败, 可能是因为这个平台已经接入过, 请检查 URL');
            }
        }
        $this->display('weixin-form');
    }
    
    public function weixinModify() {
        $id = intval(I('get.id'));
        if(empty($id)) {
            $this->error('访问错误');
        }
        $b = new Bridge($this->addon);
        $platform = $b->getOne($id);
        if(empty($platform)) {
            $this->error('访问错误');
        }
        if(IS_POST) {
            $rec = coll_elements(array('title', 'url', 'token', 'remark'), I('post.'));
            if(empty($rec['title']) || empty($rec['url']) || empty($rec['token'])) {
                $this->error('请填写完整后保存');
            }
            $rec = $b->table('__BR_BRIDGES__')->data($rec)->where("`id`='{$id}'")->save();
            if(!empty($rec)) {
                $this->success('保存接入平台成功', $this->U('connect/weixin'));
                exit;
            } else {
                $this->error('保存失败, 可能是因为这个平台已经接入过, 请检查 URL');
            }
        }
        $this->assign('entity', $platform);
        $this->display('weixin-form');
    }
    
    public function weixinDelete() {
        $id = intval(I('get.id'));
        if(empty($id)) {
            $this->error('访问错误');
        }
        $b = new Bridge($this->addon);
        $b->remove($id);
        $this->success('删除接入平台成功', $this->U('connect/weixin'));
    }
}