<?php

defined('IN_CART') or die;

/**
 *  
 * 栏目管理
 * idtype 1 链接地址 2 文章 3 商品类别
 *
 * */
class Navi extends Base
{

    /**
     *  
     * 栏目列表
     *
     *
     * */
    public function index()
    {
        $this->data["navilist"] = DB::getDB()->select("navi", "*", "isdel=0", "order");
        $this->output("navi_index");
    }

    /**
     *  
     * 增加栏目
     *
     * */
    public function naviadd()
    {
        //商品类别
        $this->data["catopt"] = $this->getTopCatOption();
        //文章类别
        $this->data["articleopt"] = $this->getArticleSort();

        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "navi_index";
        $this->output("navi_oper");
    }

    /**
     *  
     * 修改栏目
     *
     * */
    public function naviedit()
    {
        $naviid = intval($_GET["naviid"]);
        $this->data["opertype"] = "edit";
        $this->data["naviid"] = $naviid;
        $this->data["navi"] = DB::getDB()->selectrow("navi", "*", "naviid='$naviid'");
        //商品类别
        $selected = $this->data['navi']['tabletype'] == 3 ? $this->data['navi']['tableid'] : 0;
        $this->data["catopt"] = $this->getTopCatOption($selected);
        //文章类别
        $selected = $this->data['navi']['tabletype'] == 2 ? $this->data['navi']['tableid'] : 0;
        $this->data["articleopt"] = $this->getArticleSort(0, $selected);

        $this->output("navi_oper");
    }

    /**
     *  
     * 保存栏目
     *
     * */
    public function navisave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $text = __('navi');
        switch ($opertype) {
            case 'add':
            case 'edit':
                $naviname = trim($_POST["naviname"]);
                $tabletype = intval($_POST["tabletype"]);

                $data = array("naviname" => $naviname, "tabletype" => $tabletype);
                if ($tabletype == 1) {
                    $data["naviurl"] = trim($_POST["naviurl"]);
                } else if ($tabletype == 2) {//所有文章都指向帮助中心
                    $data["tableid"] = intval($_POST["articlesort"]);
                    $data['naviurl'] = url('index', 'content', 'view');
                } else if ($tabletype == 3) {
                    $catid = trim($_POST["itemcat"]);
                    $data["tableid"] = $catid;
                    $data['naviurl'] = url('index', 'listing', 'index', "catid=$catid");
                }

                if ($opertype == "add") {
                    $ret = DB::getDB()->insert("navi", $data);
                    $this->adminlog("al_navi", array("do" => "add", "naviname" => $naviname));
                    $this->setHint(__("add_success", $text));
                } else {
                    $naviid = intval($_POST["naviid"]);
                    $this->adminlog("al_navi", array("do" => "edit", "naviname" => $naviname));
                    $ret = DB::getDB()->update("navi", $data, "naviid='$naviid'");
                    $this->setHint(__("edit_success", $text));
                }
                break;
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $naviidstr = $_POST["idstr"];
                    $naviids = explode(",", $naviidstr);
                    if ($naviidstr) {
                        $where = "naviid in " . cimplode($naviids);
                        $ret = DB::getDB()->update("navi", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("navi", "naviid", "naviname", $where);

                        $recycledata = array();
                        $table = array("table" => "navi", "type" => "navi", "tablefield" => "naviid", "addtime" => time());
                        foreach ($naviids as $naviid) {
                            $this->adminlog("al_navi", array("do" => "remove", "naviname" => $titles[$naviid]));
                            $recycledata[] = $table + array("tableid" => $naviid, "title" => $titles[$naviid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {
                    $naviid = intval($_POST["id"]);
                    $field = trim($_POST["field"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_navi", array("do" => "edit", "naviid" => $naviid));
                    $ret = DB::getDB()->update("navi", array($field => $value), "naviid='$naviid'");
                }
                exit($ret ? "success" : "failure");
                break;
            case 'save':
                $naviids = $_POST["naviid"];
                foreach ($naviids as $key => $naviid) {
                    DB::getDB()->update("navi", array("order" => $key + 1), "naviid='$naviid'");
                }
                $this->adminlog("al_navi_order");
                $this->setHint(__("edit_success", $text));
                break;
        }
    }

}
