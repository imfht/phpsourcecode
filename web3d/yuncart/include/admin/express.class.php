<?php

defined('IN_CART') or die;

/**
 *
 * 配送方式 
 *
 */
class Express extends Base
{

    /**
     *
     * 配送方式
     *  
     */
    public function company()
    {
        $this->data["companylist"] = DB::getDB()->select("express_company", "*", "isdel=0", "order");
        $this->data['kuaidi_key'] = getConfig("kuaidi_key");
        $this->output("express_company");
    }

    /**
     *
     * 配送方式
     *  
     */
    public function companyadd()
    {
        $this->output("express_companyoper");
    }

    /**
     *
     * 配送方式
     *  
     */
    public function companysave()
    {
        $opertype = strtolower(trim($_REQUEST["opertype"]));
        $text = __("express_company");
        switch ($opertype) {
            case 'add':
                $company = trim($_POST["company"]);
                $kuaidi = trim($_POST["kuaidi"]);
                $this->adminlog("al_excom", array("do" => "add", "company" => $company));
                DB::getDB()->insert("express_company", array("company" => $company, "kuaidi" => $kuaidi));
                $this->setHint(__("add_success", $text), "express_company");
                break;
            case 'editfield':
                $field = strtolower($_REQUEST["field"]);
                $ret = false;
                if ($field == "remove") {//删除
                    $companyidstr = $_POST["idstr"];
                    if ($companyidstr) {
                        $companyids = cimplode($companyidstr);
                        $where = "companyid in " . cimplode($companyids);
                        $companys = DB::getDB()->selectkv("express_company", "companyid", "company", $where);

                        $recycledata = array();
                        $table = array("table" => "express_company", "type" => "expresscompany", "tablefield" => "companyid", "addtime" => time());

                        foreach ($companys as $companyid => $company) {
                            $this->adminlog("al_excom", array("do" => "remove", "company" => $company));
                            $recycledata[] = $table + array("tableid" => $companyid, "title" => $company);
                        }
                        $ret = DB::getDB()->update("express_company", "isdel=1", $where);
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {
                    !in_array($field, array("company", "kuaidi")) && exit("failure");
                    $companyid = intval($_POST["id"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_excom", array("do" => "edit", "companyid" => $companyid));
                    $ret = DB::getDB()->update("express_company", array($field => $value), "companyid='$companyid'");
                }
                exit($ret ? "success" : "failure");
                break;
            case 'save':
                $companyids = $_POST["companyid"];
                foreach ($companyids as $key => $companyid) {
                    DB::getDB()->update("express_company", array("order" => $key + 1), "companyid='$companyid'");
                }
                $this->adminlog("al_excom_order");
                $this->setHint(__("edit_success", $text), "express_company");
                break;
        }
    }

    /**
     *
     * 配送方式
     *  
     */
    public function way()
    {
        $this->data["waylist"] = DB::getDB()->select("express_way", "*", "isdel=0", "order");
        $this->output("express_way");
    }

    /**
     *
     * 添加一个配送方式
     *  
     */
    public function wayadd()
    {
        $this->data["opertype"] = "add";
        $this->data["groups"] = getCommonCache("all", "group");
        $this->data["leftcur"] = "express_way";
        $this->output("express_wayoper");
    }

    /**
     *
     * 修改一个配送方式
     *  
     */
    public function wayedit()
    {
        $wayid = intval($_GET["wayid"]);
        $this->data["groups"] = getCommonCache("all", "group");

        $where = "wayid='$wayid'";
        $this->data["opertype"] = "edit";
        $this->data['way'] = DB::getDB()->selectrow("express_way", "*", $where);
        $this->data["wayid"] = $wayid;

        //特定城市
        if ($this->data["way"]['feetype'] == "self") {
            $this->data['provs'] = DB::getDB()->select("express_prov", "*", $where);
        }
        $this->data["leftcur"] = "express_way";
        $this->output("express_wayoper");
    }

    /**
     *
     * 保存一个配送方式
     *  
     */
    public function waysave()
    {
        $opertype = strtolower(trim($_REQUEST["opertype"]));
        $text = __("expressway");
        switch ($opertype) {
            case 'add':
            case 'edit':
                //接收参数
                $name = trim($_POST["name"]);
                $desc = trim($_POST["desc"]);
                $wayid = intval($_POST["wayid"]);

                //处理默认费用
                $feetype = trim($_POST["feetype"]);
                if ($feetype == "gene") {
                    $price = getPrice($_POST["price2"], 2, 'int');
                    $defaultfee = 0;
                } elseif (isset($_POST["defaultfee"])) {
                    $price = getPrice($_POST["price1"], 2, 'int');
                    $defaultfee = 1;
                } else {
                    $price = 0;
                    $defaultfee = 0;
                }

                $data = array("name" => $name,
                    "feetype" => $feetype,
                    "defaultfee" => $defaultfee,
                    "price" => $price,
                    "desc" => $desc);

                //处理配送方式
                if ($wayid) {
                    $this->adminlog("al_exway", array("do" => "edit", "name" => $name));
                    DB::getDB()->update("express_way", $data, "wayid='$wayid'");
                    $text = __("edit_success", $text);
                } else {
                    $data['order'] = 50;
                    $this->adminlog("al_exway", array("do" => "add", "name" => $name));
                    $wayid = DB::getDB()->insert("express_way", $data);
                    $text = __("add_success", $text);
                }

                //处理特定城市费用
                if ($feetype == "self") {
                    $hideprovs = $_POST["hideprov"];
                    $prices = $_POST["price"];

                    //组装特定城市
                    $adddata = array();
                    foreach ($hideprovs as $key => $hideprov) {
                        $price = getPrice($prices[$key], 2, 'int');
                        if (!$price || !$hideprov)
                            continue;
                        $adddata[] = array("wayid" => $wayid, "price" => $price, "province" => $hideprov);
                    }
                    //如果是修改
                    if ($opertype == "edit")
                        DB::getDB()->delete("express_prov", "wayid='$wayid'");
                    if ($adddata)
                        DB::getDB()->insertMulti("express_prov", $adddata);
                }
                $this->setHint($text, "express_way");
                break;
            case 'editfield':
                $field = strtolower($_REQUEST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $wayidstr = $_POST["idstr"];
                    if ($wayidstr) {
                        $wayids = explode(",", $wayidstr);
                        $where = "wayid in " . cimplode($wayids);
                        $ret = DB::getDB()->update("express_way", "isdel=1", $where);
                        $ways = DB::getDB()->selectkv("express_way", "wayid", "name", $where);

                        $recycledata = array();
                        $table = array("table" => "express_way", "type" => "expressway", "tablefield" => "wayid", "addtime" => time());
                        foreach ($ways as $wayid => $name) {
                            $this->adminlog("al_exway", array("do" => "remove", "name" => $name));
                            $recycledata[] = $table + array("tableid" => $wayid, "title" => $name);
                        }
                        $ret = DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else if ($field == "status") {
                    $wayid = intval($_GET["wayid"]);
                    $this->adminlog("al_exway", array("do" => "edit", "wayid" => $wayid));
                    $ret = DB::getDB()->updatebool("express_way", "status", "wayid='$wayid'");
                    $this->setHint(__('set_success', $text), "express_way");
                }
                exit($ret ? "success" : "failure");
            case 'save':
                $wayids = $_POST["wayid"];
                foreach ($wayids as $key => $wayid) {
                    DB::getDB()->update("express_way", array("order" => $key + 1), "wayid='$wayid'");
                }
                $this->adminlog("al_exway_order");
                $this->setHint(__("edit_success", $text), "express_way");
                break;
        }
    }

    /**
     *  
     * 发货设置
     *
     *
     * */
    public function addr()
    {
        $this->data["addrlist"] = DB::getDB()->select("express_addr", "*", "isdel=0", array("getdefault DESC", "backdefault DESC"));
        $this->output("express_addr");
    }

    /**
     *
     * 增加一个发货地址 
     * 
     */
    public function addradd()
    {
        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "express_addr";
        $this->output("express_addroper");
    }

    /**
     *
     * 修改一个发货地址
     * 
     */
    public function addredit()
    {
        $addrid = intval($_GET["addrid"]);
        $this->data["opertype"] = "edit";
        $this->data["addrid"] = $addrid;
        $this->data['addr'] = DB::getDB()->selectrow("express_addr", "*", "addrid='$addrid'");
        $this->getDistrictopt($this->data['addr']['province'], $this->data['addr']['city'], $this->data['addr']['district']);
        $this->data["leftcur"] = "express_addr";
        $this->output("express_addroper");
    }

    /**
     *
     * 保存发货地址设置  
     * 
     */
    public function addrsave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("expressaddr");
        switch ($opertype) {
            case 'add':
            case 'edit':
                //接收参数
                $linkman = trim($_POST["linkman"]);
                $province = intval($_POST["province"]);
                $city = intval($_POST["city"]);
                $district = intval($_POST["district"]);
                $address = trim($_POST["address"]);
                $zipcode = trim($_POST["zipcode"]);

                $link = trim($_POST["link"]);
                $company = trim($_POST["company"]);

                //数据
                $data = array("linkman" => $linkman, "province" => $province, "city" => $city, "district" => $district, "address" => $address,
                    "zipcode" => $zipcode, "link" => $link, "company" => $company);

                $addrid = intval($_POST["addrid"]);
                if ($addrid) {
                    //更新发货设置
                    $ret = DB::getDB()->update("express_addr", $data, "addrid='$addrid'");
                    $this->adminlog("al_exaddr", array("do" => "edit", "address" => $address));
                    $this->setHint(__("edit_success", $text), "express_addr");
                } else {
                    //新建发货设置
                    $count = DB::getDB()->selectcount("express_addr", "isdel=0");
                    if (!$count) {
                        $data["getdefault"] = $data["backdefault"] = 1;
                    }
                    $this->adminlog("al_exaddr", array("do" => "add", "address" => $address));
                    $sendid = DB::getDB()->insert("express_addr", $data);
                    $this->setHint(__("add_success", $text), "express_addr");
                }
                break;
            case 'editfield':
                $field = trim($_POST["field"]);
                $addrid = intval($_POST["idstr"]);
                if ($field == "getdefault") {//设置默认发货地址
                    DB::getDB()->update("express_addr", "getdefault=0", "getdefault=1");
                    $this->adminlog("al_exaddr", array("do" => "edit", "addrid" => $addrid));
                    $ret = DB::getDB()->update("express_addr", "getdefault=1", "addrid='$addrid'");
                } elseif ($field == "backdefault") {//设置默认退货地址
                    DB::getDB()->update("express_addr", "backdefault=0", "backdefault=1");
                    $this->adminlog("al_exaddr", array("do" => "edit", "addrid" => $addrid));
                    $ret = DB::getDB()->update("express_addr", "backdefault=1", "addrid='$addrid'");
                } elseif ($field == "remove") {//移到回收站
                    $where = "addrid = '$addrid'";
                    $title = DB::getDB()->selectrow("express_addr", "addrid,address,getdefault,backdefault", $where, null, null, "addrid");
                    if ($title['getdefault'] || $title['backdefault']) {
                        exit(__("express_addr_default_cannot_delete"));
                    }
                    $ret = DB::getDB()->update("express_addr", "isdel=1", $where);

                    $recycledata = array();
                    $table = array("table" => "express_addr", "type" => "expressaddr", "tablefield" => "addrid", "addtime" => time());
                    $recycledata = $table + array("tableid" => $addrid, "title" => $title["address"]);
                    $this->adminlog("al_exaddr", array("do" => "remove", "address" => $title["address"]));
                    DB::getDB()->insert("recycle", $recycledata);
                }
                exit($ret ? "success" : "error");
                break;
        }
    }

    /**
     *  
     * 快递单模版
     *
     *
     * */
    public function tpl()
    {
        $this->data['company'] = DB::getDB()->selectkv("express_company", "companyid", "company", "isdel=0", "order");
        $this->data["tpllist"] = DB::getDB()->select("express_tpl", "*", "isdel=0", "isdefault DESC");
        $this->output("express_tpl");
    }

    /**
     *
     * 增加一个快递单 
     * 
     */
    public function tpladd()
    {
        $picid = isset($_GET["picid"]) ? intval($_GET["picid"]) : 0;

        $this->data["opertype"] = "add";
        $this->data['leftcur'] = "express_tpl";
        //模版图片
        $pics = DB::getDB()->select("express_pic", "*", "", "", "", "picid");
        $this->data["tpl"] = $picid ? $pics[$picid] : array();
        $this->data["picopt"] = array2select($pics, "picid", "name", $picid);

        //快递公司
        $companies = DB::getDB()->selectkv("express_company", "companyid", "company", "isdel=0", "order");
        $this->data["companyopt"] = array2select($companies, "key", "val", $this->data["tpl"] ? $this->data["tpl"]["companyid"] : 0);


        $this->data["printopt"] = getCommonCache("all", "printopt");

        $this->output("express_tploper");
    }

    /**
     *
     * 修改一个快递单 
     * 
     */
    public function tpledit()
    {
        $tplid = intval($_GET["tplid"]);

        $this->data["opertype"] = "edit";
        $this->data['leftcur'] = "express_tpl";

        $this->data['tpl'] = DB::getDB()->selectrow("express_tpl", "*", "tplid='$tplid'");

        //快递公司
        $companies = DB::getDB()->selectkv("express_company", "companyid", "company", "isdel=0", "order");
        $this->data["companyopt"] = array2select($companies, "key", "val", $this->data['tpl']['companyid']);

        //打印选项
        $this->data["printopt"] = getCommonCache("all", "printopt");
        $this->data["selprintopt"] = DB::getDB()->select("express_opt", "*", "tplid='$tplid'", "", "", "code");

        $this->data["selkeys"] = array_keys($this->data["selprintopt"]);
        $this->data["tplid"] = $tplid;
        $this->output("express_tploper");
    }

    /**
     *
     * 保存快递单模版 
     * 
     */
    public function tplsave()
    {

        $opertype = strtolower($_REQUEST["opertype"]);
        $text = __("expresstpl");

        switch ($opertype) {
            case 'add':
            case 'edit':
                $printtext = getCommonCache("all", "printopt");
                //接收参数
                $name = trim($_POST["name"]);
                $companyid = $_POST["companyid"];
                $printopts = $_POST["printopt"];
                $printscales = $_POST["printscale"];
                $width = intval($_POST["width"]);
                $height = intval($_POST["height"]);
                if (!$width || !$height)
                    $width = $height = 0;

                $tplpic = trim($_POST["tplpic"]);

                $data = array("name" => $name, "companyid" => $companyid, "width" => $width, "height" => $height, "tplpic" => $tplpic);

                $tplid = intval($_POST["tplid"]);
                if ($tplid) {

                    $where = "tplid='$tplid'";
                    //更新快递单模版
                    $ret = DB::getDB()->update("express_tpl", $data, $where);

                    //此处更新内容较多,先删除表中记录，再更新记录
                    DB::getDB()->delete("express_opt", $where);

                    if ($printopts) {
                        $adddata = array();
                        foreach ($printopts as $key => $printopt) {
                            list($width, $height, $top, $left) = @explode("_", $printscales[$key]);
                            $adddata[] = array("width" => intval($width),
                                "height" => intval($height),
                                "top" => $top,
                                "left" => $left,
                                "code" => $printopt,
                                "name" => $printtext[$printopt]["name"],
                                "tplid" => $tplid);
                        }
                        if ($adddata) {
                            $ret = DB::getDB()->insertMulti("express_opt", $adddata);
                        }
                    }
                    $this->adminlog("al_extpl", array("do" => "edit", "name" => $name));
                    $this->setHint(__("edit_success", $text), "express_tpl");
                } else {
                    //查看是否有记录
                    $data["isdefault"] = DB::getDB()->selectcount("express_tpl") ? 0 : 1;
                    //添加快递单模版
                    $tplid = DB::getDB()->insert("express_tpl", $data);
                    if (!$tplid)
                        $this->setHint(__("add_failure", $text), "express_tpl");

                    //添加打印选项
                    if ($printopts) {
                        $adddata = array();
                        foreach ($printopts as $key => $printopt) {
                            list($width, $height, $top, $left) = @explode("_", $printscales[$key]);
                            $adddata[] = array("width" => intval($width),
                                "height" => intval($height),
                                "top" => $top, "left" => $left,
                                "code" => $printopt,
                                "name" => $printtext[$printopt]["name"],
                                "tplid" => $tplid);
                        }
                        if ($adddata) {
                            $ret = DB::getDB()->insertMulti("express_opt", $adddata);
                        }
                    }
                    $this->adminlog("al_extpl", array("do" => "add", "name" => $name));
                    $this->setHint(__("add_success", $text), "express_tpl");
                }
                break;
            case 'editfield':
                $field = trim($_POST["field"]);
                if ($field == "default") {
                    $tplid = intval($_POST["idstr"]);
                    DB::getDB()->update("express_tpl", "isdefault=0", "isdefault=1");
                    $this->adminlog("al_extpl", array("do" => "edit", "tplid" => $tplid));
                    DB::getDB()->update("express_tpl", "isdefault=1", "tplid='$tplid'");
                    exit("success");
                } elseif ($field == "remove") {
                    $tplid = intval($_POST["idstr"]);
                    if ($tplid) {
                        $where = "tplid='$tplid'";
                        $ret = DB::getDB()->update("express_tpl", "isdel=1", $where);
                        $name = DB::getDB()->selectval("express_tpl", "name", $where);

                        $recycledata = array();
                        $table = array("table" => "express_tpl", "type" => "expresstpl", "tablefield" => "tplid", "addtime" => time());

                        $this->adminlog("al_extpl", array("do" => "remove", "name" => $name));

                        $recycledata[] = $table + array("tableid" => $tplid, "title" => $name);
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                    exit("success");
                }
                exit("failure");
                break;
        }
    }

}
