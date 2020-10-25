<?php

defined('IN_CART') or die;

/**
 *  
 * 满减活动
 *
 *
 * */
class Man extends Base
{

    /**
     *  
     * 满减活动首页
     *
     *
     * */
    public function index()
    {
        list($page, $pagesize) = $this->getRequestPage();
        $count = DB::getDB()->selectcount("man");
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

            $this->data["mans"] = DB::getDB()->select("man", "*", null, "manid DESC", $this->data['pagearr']['limit'], "manid");
        }
        $this->output("man_index");
    }

    /**
     *  
     * 
     * 添加满减活动
     *
     * */
    public function manadd()
    {
        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "man_index";
        $this->output("man_oper");
    }

    /**
     *  
     * 
     * 修改满减活动
     *
     * */
    public function manedit()
    {
        $manid = intval($_GET["manid"]);
        $where = "manid='$manid'";
        $this->data["opertype"] = "edit";
        $this->data['man'] = DB::getDB()->selectrow("man", "*", $where);
        $this->data["rules"] = DB::getDB()->select("man_rule", "*", $where, null, null, "ruleid");
        $this->data["manid"] = $manid;
        $this->output("man_oper");
    }

    /**
     *  
     * 
     * 保存满减活动
     *
     * */
    public function mansave()
    {
        $opertype = strtolower(trim($_REQUEST["opertype"]));
        $text = __("man");
        switch ($opertype) {
            case 'add':
            case 'edit':
                //接收参数
                $subject = trim($_POST["subject"]);
                $desc = trim($_POST["desc"]);
                $begintime = trim($_POST["begintime"]);
                $endtime = trim($_POST["endtime"]);
                $method = intval($_POST["method"]);

                $data = array("subject" => $subject,
                    "desc" => $desc,
                    "begintime" => strtotime($begintime),
                    "endtime" => strtotime($endtime),
                    "method" => $method);

                $tmporders = $_POST["manorder"]; //买家消费
                //如果是普通
                if ($method == 1) {
                    $manorders[key($tmporders)] = array_shift($tmporders);
                } else {
                    $manorders = $tmporders;
                }

                $discounts = $_POST["discount"]; //折扣
                $minus = $_POST["minus"]; //减金额
                $giftnames = $_POST["giftname"]; //送礼物
                $gifturls = $_POST["gifturl"]; //礼物url

                $manid = intval($_POST["manid"]);
                if ($manid) {
                    //更新
                    DB::getDB()->update("man", $data, "manid='$manid'");

                    //更新活动规则
                    $dbrules = DB::getDB()->select("man_rule", "*", "manid='$manid'", null, null, "ruleid");
                    $adddata = array();
                    foreach ($manorders as $key => $order) {
                        $data = array("manorder" => getPrice($order, 2, 'int'),
                            "minus" => in_array(1, $discounts[$key]) ? getPrice($minus[$key], 2, 'int') : 0,
                            "giftname" => in_array(2, $discounts[$key]) ? $giftnames[$key] : '',
                            "gifturl" => in_array(2, $discounts[$key]) ? $gifturls[$key] : '',
                            "nofreight" => in_array(3, $discounts[$key]) ? 1 : 0);
                        if (cstrpos($key, "key")) { //更新
                            $ruleid = intval(trim($key, "key_"));
                            unset($dbrules[$ruleid]);
                            DB::getDB()->update("man_rule", $data, "ruleid='$ruleid'");
                        } else { //增加
                            $adddata[] = $data + array("manid" => $manid);
                        }
                    }
                    if ($adddata)
                        DB::getDB()->insertMulti("man_rule", $adddata);
                    if ($dbrules)
                        DB::getDB()->delete("man_rule", "ruleid in " . cimplode(array_keys($dbrules)));
                    $this->adminlog("al_man", array("do" => "edit", "subject" => $subject));
                    $this->setHint(__("edit_success", $text));
                } else {
                    //入库
                    $manid = DB::getDB()->insert("man", $data);
                    if ($manorders) { //增加规则
                        $adddata = array();
                        foreach ($manorders as $key => $order) {
                            if (!$order)
                                continue;
                            $adddata[] = array("manid" => $manid,
                                "manorder" => getPrice($order, 2, 'int'),
                                "minus" => in_array(1, $discounts[$key]) ? getPrice($minus[$key], 2, 'int') : 0,
                                "giftname" => in_array(2, $discounts[$key]) ? $giftnames[$key] : '',
                                "gifturl" => in_array(2, $discounts[$key]) ? $gifturls[$key] : '',
                                "nofreight" => in_array(3, $discounts[$key]) ? 1 : 0);
                        }
                        if ($adddata)
                            DB::getDB()->insertMulti("man_rule", $adddata);
                    }
                    $this->adminlog("al_man", array("do" => "add", "subject" => $subject));
                    $this->setHint(__("add_success", $text));
                }
                break;
            case 'editfield':
                $field = strtolower($_REQUEST["field"]);
                $ret = false;
                if ($field == "delete") { //删除
                    $manidstr = $_POST["idstr"];
                    if ($manidstr) {
                        $manids = explode(",", $manidstr);
                        $where = "manid in " . cimplode($manids);

                        $mans = DB::getDB()->selectkv("man", "manid", "subject", $where);
                        foreach ($mans as $subject) {
                            $this->adminlog("al_man", array("do" => "delete", "subject" => $subject));
                        }


                        DB::getDB()->delete("man", $where);
                        DB::getDB()->delete("man_rule", $where);
                        exit("success");
                    }
                } else if ($field == "publish") {//设置发布属性
                    $manid = intval($_GET["manid"]);
                    $this->adminlog("al_man", array("do" => "edit", "manid" => $manid));
                    $ret = DB::getDB()->updatebool("man", "ispublish", "manid='$manid'");
                    $this->setHint(__('set_success', array($text, __('publish_property'))));
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
