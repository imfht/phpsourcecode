<?php

defined('IN_CART') or die;

/**
 *
 * 后台管理首页
 * 
 */
class Dashboard extends Base
{

    /**
     *
     * 后台管理
     * 
     */
    public function index()
    {
        //出售中的商品数量
        $this->data['itemsales'] = DB::getDB()->selectcount("item", "isdel=0 AND status=1");

        //仓库中的商品数量
        $this->data['itemstocks'] = DB::getDB()->selectcount("item", "isdel=0 AND status=2");

        //未处理购买咨询
        $this->data['userqas'] = DB::getDB()->selectcount("user_qa", "isdel=0 AND replytime=0");

        //未处理商品评论
        $this->data['comments'] = DB::getDB()->selectcount("user_comment", "isdel=0 AND replytime=0");

        //未处理的价格举报
        $this->data['comprices'] = DB::getDB()->selectcount("user_comprice", "isdel=0 AND replytime=0");

        //未处理的到货通知
        $this->data['nostocks'] = DB::getDB()->selectcount("user_notify", "isdel=0 AND isdeal=0 AND type='nostock'");

        //未处理的降价通知
        $this->data['downprices'] = DB::getDB()->selectcount("user_notify", "isdel=0 AND isdeal=0 AND type='downprice'");


        $ttime = strtotime("today");
        //今日订单量
        $this->data['ttrades'] = DB::getDB()->selectcount("trade", "addtime > '$ttime' AND isdel = 0");

        //今日代付款订单
        $this->data['twaitpay'] = DB::getDB()->selectcount("trade", "addtime > '$ttime' AND isdel = 0 AND status='WAIT_PAY'");

        //今日未发货
        $this->data['twaitsend'] = DB::getDB()->selectcount("trade", "addtime > '$ttime' AND isdel = 0 AND status='WAIT_SEND'");

        //今日新增会员
        $this->data['tusers'] = DB::getDB()->selectcount("user", "regtime > '$ttime' AND isdel=0");

        $ytime = $ttime - 86400;
        //昨日订单量
        $this->data['ytrades'] = DB::getDB()->selectcount("trade", "addtime < '$ttime' AND addtime > '$ytime' AND isdel = 0");

        //库存报警
        $this->data['zeroitems'] = DB::getDB()->joincount("item", "product", array("on" => "itemid", "jtype" => "left"), "a.inventory= 0 or b.inventory = 0", "itemid", true);

        //待处理售后服务
        $this->data['aftersales'] = DB::getDB()->selectcount("aftersale", "isdeal=0 AND isdel=0");

        //错误
        $this->error();

        //IE6
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
            $this->data['error'] = __("Ie6_not_support");
        }


        //服务器最新版本
        if (!isset($_SESSION['newversion'])) {
            $_SESSION['newversion'] = false;
            $content = @file_get_contents("http://www.yuncart.com/checkver.php");
            if ($content) {
                $newversion = json_decode($content, true);
                if ($newversion && version_compare($newversion['version'], C_VER, ">")) {
                    $_SESSION['newversion'] = $newversion;
                }
            }
        }
        $this->data['newversion'] = $_SESSION['newversion'];

        //是否删除了install安装目录
        $installdir = SITEPATH . "/install";
        $this->data["installdir"] = file_exists($installdir);
        $this->output("dashboard");
    }

}
