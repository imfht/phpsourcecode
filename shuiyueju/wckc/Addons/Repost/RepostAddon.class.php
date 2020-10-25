<?php

namespace Addons\Repost;

use Common\Controller\Addon;
use Weibo\Api\WeiboApi;

/**
 * 转发插件
 * @author 嘉兴想天信息科技有限公司
 */
class RepostAddon extends Addon
{

    public $info = array(
        'name' => 'Repost',
        'title' => '转发',
        'description' => '转发',
        'status' => 1,
        'author' => '嘉兴想天信息科技有限公司',
        'version' => '0.1'
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的repost钩子方法
    public function repost($param)
    {
        $weibo = $this->getweiboDetail($param['weiboId']);


        $sourseId = $weibo['data']['sourseId'];

        if (!$sourseId) {
            $sourseId = $param['weiboId'];
        }
        $param['sourseId'] = $sourseId;
        $this->assign('repost_count', $weibo['repost_count']);
        $this->assign($param);
        $this->display('repost');
    }

    /**
     * 转发模版渲染
     * @param $weibo
     * @return mixed
     * autor:xjw129xjt
     */
    public function fetchRepost($weibo)
    {

        $weibo_data = unserialize($weibo['data']);
        $weibo_data['attach_ids'] = explode(',', $weibo_data['attach_ids']);
        $sourse_weibo = $this->getweiboDetail($weibo_data['sourse']['id']);
        $param['weibo'] = $weibo;
        $param['weibo']['sourse_weibo'] = $sourse_weibo;
        $this->assign($param);
        return $this->fetch('display');
    }

    /**
     * 获取微博详细信息
     * @param $weiboId
     * @return bool
     * autor:xjw129xjt
     */
    private function getweiboDetail($weiboId)
    {
        $weibo_check = D('Weibo/Weibo')->where(array('id' => $weiboId, 'status' => 1))->find();

        if ($weibo_check) {
            $this->weiboApi = new WeiboApi();
            $weibo = $this->weiboApi->getWeiboDetail($weiboId);
        } else {
            $weibo['weibo'] = false;
        }

        return $weibo['weibo'];
    }

}