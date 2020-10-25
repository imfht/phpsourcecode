<?php
/**
 * 管理中心欢迎页
 */
namespace Control\Controller;
use Core\Model\Account;
use Core\Model\Member;
use Core\Model\Pay;
use Core\Model\Utility;
use Think\Controller;

class AppController extends Controller {
    
    public function _initialize(){
        C('FRAME_ACTIVE', 'webapp');
    }
    public function _empty() {
        $this->success('还未启用');
    }
    
    public function paymentAction() {
        $a = new Account();
        $accounts = array();
        $weixins = $a->table('__PLATFORM_WEIXIN__')->field('`id`, `appid`, `secret`')->where("`level`=2")->select();
        if(!empty($weixins)) {
            $weixins = coll_key($weixins, 'id');
            $ids = coll_neaten($weixins, 'id');
            $accounts = $a->table('__PLATFORMS__')->field('`id`,`title`')->where('`id` IN (' . implode(',', $ids) . ')')->select();
            foreach($accounts as &$acc) {
                $acc['appid'] = $weixins[$acc['id']]['appid'];
                $acc['secret'] = $weixins[$acc['id']]['secret'];
            }
        }
        Pay::loadSettings();
        $setting = C('PAY');
        if(IS_POST) {
            $input = array();
            $input['alipay'] = I('post.alipay');
            $input['alipay']['enable'] = $input['alipay']['enable'] == 'true' ? 1 : 0;
            if(!empty($input['alipay']['enable'])) {
                if(empty($input['alipay']['partner']) || empty($input['alipay']['account']) || empty($input['alipay']['secret'])) {
                    $this->error('支付宝支付资料输入不完整');
                }
            }
            $setting[Pay::OPT_ALIPAY] = $input['alipay'];
            
            $input['weixin'] = I('post.weixin');
            $input['weixin']['enable'] = $input['weixin']['enable'] == 'true' ? 1 : 0;
            if(!empty($input['weixin']['enable'])) {
                if(empty($input['weixin']['partner']) || empty($input['weixin']['key']) || empty($input['weixin']['mchid'])) {
                    $this->error('微信支付资料输入不完整');
                }
            }
            $setting[Pay::OPT_WEIXIN] = $input['weixin'];
            if(Pay::saveSettings($setting)) {
                $this->success('操作成功');
                exit;
            } else {
                $this->error('操作失败, 请稍后重试');
            }
        }
        $pay = array();
        $pay['weixin'] = $setting[Pay::OPT_WEIXIN];
        $pay['alipay'] = $setting[Pay::OPT_ALIPAY];
        $this->assign('pay', $pay);
        
        $this->assign('accounts', $accounts);
        $this->display();
    }
}