<?php

defined('IN_CART') or die;

/**
 *
 *  商品属性列表
 * 
 */
class Type extends Base
{

    /**
     *
     *  商品类目列表
     * 
     */
    public function index()
    {
        $this->data["types"] = DB::getDB()->select("type", "*", "isdel=0", "typeid DESC");
        $this->output("type_index");
    }

    /**
     *
     * 增加一个商品类目
     * 
     */
    public function typeadd()
    {
        $this->data['brands'] = $this->getBrands();
        $this->data["opertype"] = "add";

        //规格
        $this->data["allspecs"] = DB::getDB()->select("spec", "specid,name,memo", null, null, null, "specid");

        $this->output("type_oper");
    }

    /**
     *
     * 修改一个商品类目
     * 
     */
    public function typeedit()
    {
        $typeid = intval($_GET["typeid"]);
        $where = "typeid='$typeid'";
        $this->data['refer'] = isset($_GET["refer"]) ? trim($_GET['refer']) : '';
        $this->data["opertype"] = "edit";

        $this->data["typeid"] = $typeid;
        $this->data["type"] = DB::getDB()->selectrow("type", "*", $where);

        //品牌
        $this->data['brands'] = $this->getBrands();
        $this->data['selbrands'] = DB::getDB()->selectcol("type_brand", "brandid", $where);

        //扩展属性
        $this->data["properties"] = DB::getDB()->select("type_property", "*", $where, "order");

        //规格
        $this->data["allspecs"] = DB::getDB()->select("spec", "specid,name,memo", null, null, null, "specid");


        $this->data["specs"] = DB::getDB()->select("type_spec", "*", $where, "order");

        $this->output("type_oper");
    }

    /**
     *  
     * 修改一个商品属性
     *
     * */
    public function property()
    {
        if (ispostreq()) {
            $propertyid = intval($_POST["propertyid"]);
            $where = "propertyid='$propertyid'";
            $property = DB::getDB()->selectrow("type_property", "*", $where);

            //属性
            $propertyname = trim($_POST["propertyname"]);
            $dptype = intval($_POST["dptype"]);
            $isdp = intval($_POST["isdp"]);
            $selval = array();
            if ($dptype == 3) { //如果是手动输入
                //删除propertyvalue
                DB::getDB()->delete("type_propertyvalue", $where);
            } else {
                //属性值
                $dbexists = DB::getDB()->selectkv("type_propertyvalue", "valueid", "order", $where);
                $values = $_POST["values"];
                $order = 1;
                $adddata = array();
                foreach ($values as $valueid => $value) {
                    $selval[] = str_replace(",", "", $value);
                    $order ++;
                    if (cstrpos($valueid, "key_")) { //更新
                        $valueid = trim($valueid, "key_");
                        DB::getDB()->update("type_propertyvalue", array("propertyvalue" => $value, "order" => $order), "valueid='$valueid'");
                        unset($dbexists[$valueid]);
                    } else { //增加
                        $adddata[] = array(
                            "propertyvalue" => $value,
                            "order" => $order,
                            "propertyid" => $propertyid,
                            "typeid" => $property['typeid']
                        );
                    }
                }
                //增加
                if ($adddata)
                    DB::getDB()->insertMulti("type_propertyvalue", $adddata);
                //删除
                if ($dbexists)
                    DB::getDB()->delete("type_propertyvalue", "valueid in " . cimplode(array_keys($dbexists)));
            }


            //保存属性
            DB::getDB()->update("type_property", array("propertyname" => $propertyname, "dptype" => $dptype, "isdp" => $isdp, "selval" => implode(",", $selval)), $where);


            redirect(url('admin', 'type', 'typeedit', "refer=property&typeid=" . $property['typeid']));
        } else {
            $propertyid = intval($_GET["propertyid"]);
            $where = "propertyid='$propertyid'";
            $this->data['property'] = DB::getDB()->selectrow("type_property", "*", $where);

            $this->data['propertyvalues'] = DB::getDB()->select("type_propertyvalue", "*", $where, "order");
            $this->output("type_property");
        }
    }

    /**
     *  
     * 保存一个商品类目
     *
     * */
    public function typesave()
    {
        $opertype = strtolower(trim($_POST["opertype"]));
        $text = __("type");
        $url = url("admin", "type", "hint");

        switch ($opertype) {
            case 'add':
            case 'edit':
                //增加一个类目
                $typeid = isset($_POST["typeid"]) ? intval($_POST["typeid"]) : 0;
                $typename = trim($_POST["typename"]);

                //扩展属性
                $propertyids = empty($_POST["propertyids"]) ? array() : $_POST["propertyids"];
                $propertynames = empty($_POST["propertynames"]) ? array() : $_POST["propertynames"];
                $dptypes = empty($_POST["dptypes"]) ? array() : $_POST["dptypes"];
                $selvals = empty($_POST["selvals"]) ? array() : $_POST["selvals"];
                $isdps = empty($_POST["isdps"]) ? array() : $_POST["isdps"];


                //规格
                $specids = empty($_POST["specids"]) ? array() : $_POST["specids"];
                $specdptypes = empty($_POST["specdptypes"]) ? array() : $_POST["specdptypes"];


                //品牌
                $tmpbrands = isset($_POST["brand"]) ? $_POST["brand"] : array();
                !is_array($tmpbrands) && $tmpbrands = array();
                $data = array("typename" => $typename);

                if ($typeid) {
                    $where = "typeid='$typeid'";
                    //修改
                    $ret = DB::getDB()->update("type", $data, $where);

                    //品牌
                    $selbrands = DB::getDB()->selectcol("type_brand", "brandid", $where);

                    $needdel = array_diff($selbrands, $tmpbrands);
                    $needadd = array_diff($tmpbrands, $selbrands);

                    //增加品牌
                    if (!empty($needadd)) {
                        foreach ($needadd as $brandid) {
                            $branddata[] = array("brandid" => $brandid, "typeid" => $typeid);
                        }
                        if (!empty($branddata)) {
                            $ret = DB::getDB()->insertMulti("type_brand", $branddata);
                        }
                    }

                    //删除品牌
                    if (!empty($needdel)) {
                        $ret = DB::getDB()->delete("type_brand", "typeid='$typeid' AND brandid in " . cimplode($needdel));
                    }


                    //扩展属性
                    //数据库中保存的属性
                    $dbproperties = DB::getDB()->selectkv("type_property", "propertyid", "order", $where);

                    $order = 1;
                    $adddata = array();
                    foreach ($propertyids as $k => $propertyid) {
                        $order++;
                        if (cstrpos($k, "key_")) {//修改，修改排序
                            $propertyid = intval(trim($k, "key_"));
                            $where = "propertyid='$propertyid'";
                            DB::getDB()->update("type_property", array("order" => $order), $where);
                            unset($dbproperties[$propertyid]);
                        } else {//增加
                            $propertyname = trim($propertynames[$k]);
                            $selval = trim(str_replace("，", ",", $selvals[$k]));
                            $dptype = intval($dptypes[$k]);
                            if ($dptype == 3 || $dptype == 4) {
                                $selval = "";
                            }
                            $isdp = isset($isdps[$k]) ? 1 : 0;
                            $propertyid = DB::getDB()->insert("type_property", array("propertyname" => $propertyname,
                                "dptype" => $dptype,
                                "isdp" => $isdp,
                                "typeid" => $typeid,
                                "selval" => $selval,
                                "order" => $order));
                            if ($dptype == 1 || $dptype == 2) { //增加属性值
                                $selvalarr = explode(",", $selval);
                                foreach ($selvalarr as $key => $val) {
                                    $adddata[] = array("propertyid" => $propertyid, "propertyvalue" => $val, "order" => $key + 1, "typeid" => $typeid);
                                }
                            }
                        }
                    }
                    //增加属性值
                    if ($adddata)
                        DB::getDB()->insertMulti("type_propertyvalue", $adddata);

                    //删除属性
                    if ($dbproperties) {
                        $where = "propertyid in " . cimplode(array_keys($dbproperties));
                        DB::getDB()->delete("type_property", $where);
                        DB::getDB()->delete("type_propertyvalue", $where);
                    }


                    //规格,全部清空后，重新插入
                    $dbexist = DB::getDB()->selectkv("type_spec", "specid", "order", "typeid='$typeid'");
                    $adddata = array();
                    $order = 1;
                    foreach ($specids as $k => $specid) {
                        $order ++;
                        if (cstrpos($k, "key_")) {//修改
                            $specid = intval(trim($k, "key_"));
                            DB::getDB()->update("type_spec", array("order" => $order), "typeid='$typeid' AND specid='$specid'");
                            unset($dbexist[$specid]);
                        } else {
                            $adddata[] = array("typeid" => $typeid, "specid" => $specid, "order" => $order, "specdptype" => 1);
                        }
                    }
                    //删除
                    if ($dbexist)
                        DB::getDB()->delete("type_spec", "typeid='$typeid' AND specid in" . cimplode(array_keys($dbexist)));
                    if ($adddata)
                        DB::getDB()->insertMulti("type_spec", $adddata);

                    //提示成功
                    $this->setHint(__("edit_success", $text));
                } else {

                    //增加  type
                    $typeid = DB::getDB()->insert("type", $data);

                    //增加  type_brand
                    $branddata = array();
                    foreach ($tmpbrands as $brandid) {
                        if (!$brandid)
                            continue;
                        $branddata[] = array("brandid" => $brandid, "typeid" => $typeid);
                    }
                    if ($branddata) {
                        $ret = DB::getDB()->insertMulti("type_brand", $branddata);
                    }

                    //扩展属性
                    $adddata = array();
                    foreach ($propertynames as $k => $propertyname) {
                        $selval = trim(str_replace("，", ",", $selvals[$k])); //可选值
                        $dptype = intval($dptypes[$k]);      //前台表现形式
                        $isdp = isset($isdps[$k]) ? 1 : 0; //是否显示					
                        $order = $k + 1; //排序
                        $propertyid = DB::getDB()->insert("type_property", array("propertyname" => trim($propertyname),
                            "dptype" => $dptype,
                            "isdp" => $isdp,
                            "typeid" => $typeid,
                            "selval" => $selval,
                            "order" => $order));
                        if ($selval && ($dptypes[$k] == 1 || $dptypes[$k] == 2)) {
                            $selvalarr = explode(",", $selval);
                            foreach ($selvalarr as $key => $selval) {
                                if (!$selval)
                                    continue;
                                $adddata[] = array("propertyid" => $propertyid, "propertyvalue" => $selval, "order" => $key + 1, "typeid" => $typeid);
                            }
                        }
                    }
                    //增加属性值
                    if ($adddata) {
                        DB::getDB()->insertMulti("type_propertyvalue", $adddata);
                    }

                    //规格
                    $adddata = array();
                    foreach ($specids as $k => $specid) {
                        $adddata[] = array("typeid" => $typeid, "specid" => $specid, "specdptype" => intval($specdptypes[$k]), "order" => $k + 1);
                    }
                    if ($adddata) {
                        DB::getDB()->insertMulti("type_spec", $adddata);
                    }

                    //提示成功
                    $this->setHint(__("add_success", $text));
                }
                break;
            case 'editfield':
                //修改特定字段
                $field = strtolower(trim($_POST["field"]));
                $ret = false;
                if ($field == "typename") {

                    //更改类目名称	
                    $typeid = intval($_POST["id"]);
                    $value = trim($_POST["value"]);
                    $ret = DB::getDB()->update("type", array($field => $value), "typeid=$typeid");
                } else if ($field == "order") {

                    //更改类目排序
                    $typeid = intval($_POST["id"]);
                    $value = intval($_POST["value"]);
                    $ret = DB::getDB()->update("type", array($field => $value), "typeid=$typeid");
                } else if ($field == "remove") {

                    //移除类目
                    $ret = false;
                    $typeidstr = $_POST["idstr"];
                    if ($typeidstr) {

                        //更新isdel
                        $typeids = explode(",", $typeidstr);
                        $where = "typeid in (" . implode(",", $typeids) . ")";
                        $ret = DB::getDB()->update("type", "isdel=1", $where);

                        //添加到回收站
                        $titles = DB::getDB()->select("type", "typeid,typename", $where, null, null, "typeid");
                        $recycledata = array();
                        $table = array("table" => "type", "type" => "type", "tablefield" => "typeid", "addtime" => time());

                        foreach ($typeids as $typeid) {
                            $recycledata[] = $table + array("tableid" => $typeid, "title" => $titles[$typeid]["typename"]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

    public function getSpec()
    {
        $specid = intval($_GET['specid']);
        $where = "specid='$specid'";
        $this->data["spec"] = DB::getDB()->selectrow("spec", "*", $where);
        $this->data["specval"] = DB::getDB()->select("specval", "*", $where, "order");
        $this->output("getspec");
    }

}
