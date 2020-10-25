<?php

defined('IN_CART') or die;

/**
 *
 * 商品类别
 *
 */
class Cat extends Base
{

    /**
     *
     * 类别列表
     *  
     */
    public function index()
    {
        $this->data["listcat"] = $this->getListCat();
        $this->data["types"] = $this->getTypes();
        $this->output("cat_index");
    }

    /**
     *
     * 修改类别
     *  
     */
    public function catedit()
    {
        $catid = intval($_GET["catid"]);
        $cat = DB::getDB()->selectrow("cat", "*", "catid='$catid'");
        $this->data["opertype"] = "edit";

        $this->data["cat"] = $cat;
        $this->data["catid"] = $catid;
        $this->data["catopt"] = $this->getCatOption(array(), 0, $cat["pid"]);
        $this->data["typeopt"] = $this->getTypes($cat["typeid"], "option");

        $this->output("cat_oper");
    }

    /**
     *  
     *  添加类别
     *
     */
    public function catadd()
    {
        $this->data["opertype"] = "add";
        $this->data["catopt"] = $this->getCatOption();
        $this->data["typeopt"] = $this->getTypes(0, "option");
        $this->output("cat_oper");
    }

    /**
     *
     * 保存一个类别
     *
     */
    public function catsave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $text = __('cat');
        switch ($opertype) {
            case 'edit':
            case 'add':
                $catid = intval($_POST["catid"]);

                //提交参数
                $pid = intval($_POST["pid"]);
                $catname = trim($_POST["catname"]);
                $order = intval($_POST["order"]);
                $typeid = intval($_POST["typeid"]);

                $pagetitle = trim($_POST["pagetitle"]);
                $pagekeywords = trim($_POST["pagekeywords"]);
                $pagedesc = trim($_POST["pagedesc"]);

                $data = array("pid" => $pid,
                    "catname" => $catname,
                    "order" => $order,
                    "typeid" => $typeid,
                    "pagetitle" => $pagetitle,
                    "pagekeywords" => $pagekeywords,
                    "pagedesc" => $pagedesc);

                if ($catid) { //修改
                    $ret = DB::getDB()->update("cat", $data, "catid='$catid'");
                    $this->adminlog("al_cat", array("do" => "edit", "catname" => $catname));
                    $this->setHint(__('edit_success', $text));
                } else { //增加
                    $ret = DB::getDB()->insert("cat", $data);
                    $this->adminlog("al_cat", array("do" => "add", "catname" => $catname));
                    $this->setHint(__('add_success', $text));
                }
                break;
            case 'editfield'://修改特定字段
                $field = trim($_POST["field"]);
                if ($field == "remove") {
                    $ret = false;
                    $catidstr = $_POST["idstr"];
                    if ($catidstr) {
                        $catids = explode(",", $catidstr);
                        //删除大分类前，需要先删除小分类
                        if (DB::getDB()->selectcount("cat", "pid in " . cimplode($catids) . " AND isdel=0")) {
                            exit(__("delcat_has_child"));
                        }
                        $where = "catid in " . cimplode($catids);
                        $ret = DB::getDB()->update("cat", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("cat", "catid", "catname", $where);

                        $recycledata = array();
                        $table = array("table" => "cat", "type" => "cat", "tablefield" => "catid", "addtime" => time());

                        foreach ($catids as $catid) {
                            $this->adminlog("al_cat", array("do" => 'remove', "catname" => $titles[$catid]));
                            $recycledata[] = $table + array("tableid" => $catid, "title" => $titles[$catid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {
                    $catid = intval($_POST["id"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_cat", array("do" => "edit", "catid" => $catid));
                    $ret = DB::getDB()->update("cat", array($field => $value), "catid='$catid'");
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

    /**
     *
     * 类别商品列表
     *
     */
    public function catitem()
    {
        $this->data = array();
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $scatid = isset($_REQUEST["scatid"]) ? intval($_REQUEST["scatid"]) : 0;
        $this->data["catopt"] = $this->getCatOption(null, 0, $scatid, true);

        $cats = $this->getCats("source");

        //全部商品
        if ($scatid == 0) {
            $where = "isdel = 0";
            $count = DB::getDB()->selectcount("item", $where);
            if ($count) {
                $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

                //商品
                $this->data["items"] = DB::getDB()->select("item", "itemid,itemname,itemimg", $where, "order", $this->data['pagearr']['limit'], "itemid");

                //商品分类
                $itemids = array_keys($this->data["items"]);
                $itemcats = DB::getDB()->select("item_cat", "catid,itemid", "itemid in " . cimplode($itemids));
                //整合商品分类到商品
                foreach ($itemcats as $val) {
                    $this->data["items"][$val['itemid']]["cat"][$val["catid"]] = $cats[$val["catid"]]["catname"];
                }
            }
        } else {
            $jpara = array("on" => "itemid");
            $where = array("a" => "catid=$scatid", "b" => "isdel=0");
            $count = DB::getDB()->joincount("item_cat", "item", $jpara, $where);

            if ($count) {
                $data["pagearr"] = getPageArr($page, $pagesize, $count);
                $fields = array("l" => "catid", "r" => "itemname,itemid");
                $this->data["items"] = DB::getDB()->join("item_cat", "item", $jpara, $fields, $where, array("b" => "order"), $data['pagearr']['limit'], "itemid");

                $itemids = array_keys($data["items"]);
                $itemcats = DB::getDB()->select("item_cat", "catid,itemid", "itemid in " . cimplode($itemids));
                foreach ($itemcats as $val) {
                    $this->data["items"][$val['itemid']]["cat"][$val["catid"]] = $cats[$val["catid"]];
                }
            }
            //指定类别
        }
        $this->output("cat_item");
    }

    /**
     *
     * 保存类别商品
     *
     */
    public function catitemsave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $text = __("item_to_cat");
        switch ($opertype) {
            case 'add':
            case 'transfer':
                $catid = intval($_POST["catid"]);
                $itemids = $_POST["itemid"];
                $ret = false;

                //选择itemids的类别
                $tmpexists = DB::getDB()->select("item_cat", "catid,itemid", "itemid in " . cimplode($itemids));
                $exists = array();
                foreach ($tmpexists as $tmp) {
                    $exists[$tmp["itemid"]][] = $tmp["catid"];
                }

                //增加到类别
                if ($opertype == "add") {
                    if ($itemids) {
                        foreach ($itemids as $itemid) {
                            if (isset($exists[$itemid]) && in_array($catid, $exists[$itemid]))
                                continue;
                            $adddata[] = array("itemid" => $itemid, "catid" => $catid);
                        }
                        if ($adddata)
                            $ret = DB::getDB()->insertMulti("item_cat", $adddata);
                    }
                    $this->setHint(__('item_addto_cat_success'), "cat_catitem");
                } else if ($opertype == "transfer") {
                    //转移到类别
                    if ($itemids) {
                        //删除之前的类别
                        DB::getDB()->delete("item_cat", "itemid in " . cimplode($itemids) . "  AND catid!='$catid'");
                        //转移到类别
                        foreach ($itemids as $key => $itemid) {
                            if (isset($exists[$itemid]) && in_array($catid, $exists[$itemid]))
                                continue;
                            $adddata[] = array("itemid" => $itemid, "catid" => $catid);
                        }
                        if ($adddata)
                            $ret = DB::getDB()->insertMulti("item_cat", $adddata);
                    }
                    $this->setHint(__('item_transfer_cat_success'), "cat_catitem");
                }
                break;
            case 'delcat':
                $itemid = intval($_POST["itemid"]);
                $catid = intval($_POST["catid"]);
                $ret = false;
                $count = DB::getDB()->selectcount("item_cat", "itemid='$itemid'");

                if ($itemid && $catid && ($count > 1))
                    $ret = DB::getDB()->delete("item_cat", "itemid='$itemid' AND catid='$catid'");
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
