<?php

namespace Addons\Retopic;

use Common\Controller\Addon;

/**
 * 推荐话题插件
 * @author onep2p
 */
class RetopicAddon extends Addon
{

    public $info = array(
        'name' => 'Retopic',
        'title' => '热门话题推荐插件',
        'description' => '提供给后台推荐展示的话题区域，让用户及时了解热门信息',
        'status' => 1,
        'author' => 'onep2p',
        'version' => '0.1'
    );

    public function install(){return true;}

    public function uninstall(){return true;}
    
    //实现的Rank钩子方法
    public function weiboSide($param){
        $rank=S('topic_rank');
        if(empty($rank)){
            $retopic = D('Topic')->where('is_top = 1')->order('id desc')->limit(5)->select();
            foreach($retopic as $key=>$val){
                $retopic[$key]['reCount'] =  D('Weibo')->where("`content` LIKE  '%#".$val['name']."#%' and status = 1 ")->count();
            }
            $rank=$retopic;
            S('topic_rank',$rank,60);
        }

    	$this->assign('list',$rank);
        $this->display('Retopic');
    }
}