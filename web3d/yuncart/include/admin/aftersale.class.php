<?php

defined('IN_CART') or die;

/**
 *
 * 商品售后 
 *
 */
class Aftersale extends Base
{

    /**
     *
     * 商品售后
     *  
     */
    public function index()
    {
        list($page, $pagesize) = $this->getRequestPage();

        $onarr = array("on" => "orderid");
        $where = array("a" => "isdel=0");
        $count = DB::getDB()->joincount("aftersale", "order", $onarr, $where);

        if ($count) {
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            //查询数据
            $this->data["afterlist"] = DB::getDB()->join("aftersale", "order", $onarr, array("a" => "*", "b" => "itemid,itemname,itemimg"), $where, array("a" => "afterid DESC"), $this->data['pagearr']['limit']);
        }

        $this->output("aftersale_index");
    }

    public function aftersaleoper()
    {
        $opertype = trim($_REQUEST["opertype"]);
        switch ($opertype) {
            case 'editfield':
                $field = strtolower(trim($_POST["field"]));
                $ret = false;
                if ($field == "remove") {
                    $afteridstr = trim($_POST["idstr"]);
                    if ($afteridstr) {
                        $afterids = explode(",", $afteridstr);
                        $where = "afterid in " . cimplode($afterids);
                        $ret = DB::getDB()->update("aftersale", "isdel=1", $where);
                        $titles = DB::getDB()->select("aftersale", "afterid,way", $where, null, null, "afterid");

                        $recycledata = array();
                        $table = array("table" => "aftersale", "type" => "aftersale", "tablefield" => "afterid", "addtime" => time());
                        foreach ($afterids as $afterid) {
                            $recycledata[] = $table + array("tableid" => $afterid, "title" => getCommonCache($titles[$afterid]["way"], "aftersale"));
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                }
                exit($ret ? "success" : "failure");
                break;
            case 'deal':
                $afterid = intval($_POST["afterid"]);
                $time = time();
                DB::getDB()->update("aftersale", "isdeal=1,dealtime=$time", "afterid='$afterid'");
                exit("success");
                break;
            case 'view':
                $afterid = intval($_GET["afterid"]);
                $onarr = array("on" => "orderid");
                $where = array("a" => "afterid='$afterid'");
                $this->data['after'] = DB::getDB()->joinrow("aftersale", "order", $onarr, array("a" => "*", "b" => "*"), $where);
                $this->data['user'] = DB::getDB()->selectrow("user", "*", "uid='" . $this->data['after']['uid'] . "'");
                $this->output("aftersale_info");
                break;
        }
    }

    /**
     *
     * 商品售后设置
     *  
     */
    public function set()
    {
        $this->data["aftersale"] = DB::getDB()->selectkv("config", "key", "val", "type='aftersale'");
        $this->output("aftersale_set");
    }

    /**
     *
     * 商品售后设置
     *  
     */
    public function setsave()
    {
        $data['aftersale_service'] = @implode(",", $_POST["service"]);
        $data['aftersale_back'] = intval($_POST['back']);
        $data['aftersale_change'] = intval($_POST['change']);
        $data['aftersale_repair'] = intval($_POST['repair']);
        $data['aftersale_backinfo'] = trim($_POST["backinfo"]);
        $replacedata = array();
        foreach ($data as $key => $val) {
            $replacedata[] = array("key" => $key, "val" => $val, "type" => "aftersale");
        }
        if ($replacedata)
            DB::getDB()->replaceMulti("config", $replacedata);
        $this->adminlog("al_aftersale");
        $this->setHint(__("set_success", __('aftersale')), "aftersale_set");
    }

}
