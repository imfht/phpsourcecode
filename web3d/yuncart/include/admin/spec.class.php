<?php

defined('IN_CART') or die;

/**
 *
 * 商品规格 
 * 
 */
class Spec extends Base
{

    /**
     *
     * 商品规格列表
     * 
     */
    public function index()
    {
        $this->data["speclist"] = DB::getDB()->select("spec", "*", "", "", "", "specid");
        $values = DB::getDB()->select("specval", "specid,name", "", "order");
        if ($values) {
            foreach ($values as $value) {
                $this->data["speclist"][$value['specid']]['vals'][] = $value['name'];
            }
        }
        $this->output("spec_index");
    }

    /**
     *
     * 增加商品规格
     * 
     */
    public function specadd()
    {
        $this->data["opertype"] = "add";

        $this->output("spec_oper");
    }

    /**
     *
     * 快速增加商品规格
     * 
     */
    public function qspecadd()
    {
        $type = intval($_POST["type"]);
        $specs = array(
            1 => array("name" => "颜色", "memo" => "服装-鞋帽", "type" => "pic",
                "val" => array("黑色" => "uploads/spec/2.gif",
                    "红色" => "uploads/spec/7.gif",
                    "橙色" => "uploads/spec/3.gif",
                    "黄色" => "uploads/spec/1.gif",
                    "蓝色" => "uploads/spec/4.gif",
                    "灰色" => "uploads/spec/5.gif")
            ),
            array("name" => "尺码", "memo" => "服装", "type" => "text",
                "val" => array("均码", "XXS", "XS", "S", "M", "L", "XL", "XXL", "XXXL")
            ),
            array("name" => "尺码", "memo" => "男士鞋类", "type" => "text",
                "val" => array("36", "37", "38", "39", "40", "41", "42", "43", "45", "46", "47")
            ),
            array("name" => "尺码", "memo" => "女士鞋类", "type" => "text",
                "val" => array("33", "34", "35", "36", "37", "38", "39", "40", "41")
            ),
            array("name" => "尺码", "memo" => "儿童鞋类", "type" => "text",
                "val" => array("12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36")
            )
        );
        if (!isset($specs[$type]))
            exit("failure");

        $spec = $specs[$type];

        //规格
        $data = array("name" => $spec['name'], "memo" => $spec['memo'], "type" => $spec['type']);
        $specid = DB::getDB()->insert("spec", $data);

        //规则值
        $vals = $spec['val'];
        $order = 0;
        $adddata = array();
        foreach ($vals as $key => $val) {
            if ($spec['type'] == 'pic') { //如果是图片类型
                $data = array("specid" => $specid, "name" => $key, "img" => $val, "order" => ++$order);
            } else {
                $data = array("specid" => $specid, "name" => $val, "order" => ++$order);
            }
            $adddata[] = $data;
        }
        DB::getDB()->insertMulti("specval", $adddata);
        $this->adminlog("al_spec", array("do" => "add", "name" => $spec['name']));
        exit("success");
    }

    /**
     *
     * 修改商品规格
     * 
     */
    public function specedit()
    {
        $specid = intval($_GET["specid"]);
        $where = "specid='$specid'";

        $this->data["specid"] = $specid;
        $this->data["spec"] = DB::getDB()->selectrow("spec", "*", $where);
        $this->data["specvalues"] = DB::getDB()->select("specval", "name,img,specvalid", $where, "order");
        $this->data["opertype"] = "edit";

        $this->output("spec_oper");
    }

    /**
     *
     * 保存商品规格
     * 
     */
    public function specsave()
    {
        $opertype = trim($_POST["opertype"]);
        $text = __("spec");
        switch ($opertype) {
            case 'add':
            case 'edit':
                //接收参数
                $name = trim($_POST["name"]);
                $memo = trim($_POST["memo"]);
                $type = trim($_POST["type"]);

                $valnames = $_POST["valnames"];
                $valimgs = $type == "pic" ? $_POST["valimgs"] : array();

                $data = array("name" => $name, "memo" => $memo, "type" => $type);
                $specid = intval($_POST["specid"]);

                if ($specid) { //修改规格
                    $where = "specid='$specid'";
                    //更新spec表
                    DB::getDB()->update("spec", $data, $where);

                    //处理valnames
                    $exists = DB::getDB()->selectcol("specval", "specvalid", $where, "order");

                    $arrkeys = array_keys($valnames);
                    $delarr = $exists;
                    $adddata = array();
                    $order = 0;
                    foreach ($valnames as $key => $valname) {
                        if (!$valname)
                            continue;
                        $data = array("name" => $valname,
                            "specid" => $specid,
                            "img" => isset($valimgs[$key]) ? $valimgs[$key] : "",
                            "order" => ++$order);
                        if (cstrpos($key, "key")) { //如果是需要更新
                            $specvalid = intval(preg_replace("/key_/", "", $key));
                            if (($index = array_search($specvalid, $delarr)) !== false)
                                unset($delarr[$index]);
                            DB::getDB()->update("specval", $data, "specvalid='$specvalid'");
                        } else {     //不存在的元素，组合增加数组
                            $adddata[] = $data;
                        }
                    }
                    if ($adddata) { //增加
                        DB::getDB()->insertMulti("specval", $adddata);
                    }
                    if ($delarr) { //删除
                        DB::getDB()->delete("specval", "specvalid in " . cimplode($delarr));
                    }
                    $this->adminlog("al_spec", array("do" => "edit", "name" => $name));
                    $this->setHint(__("edit_success", $text), "spec_index");
                } else { //添加规格
                    $specid = DB::getDB()->insert("spec", $data);

                    //处理valnames;
                    if ($valnames) {
                        $adddata = array();
                        foreach ($valnames as $k => $valname) {
                            $adddata[] = array("specid" => $specid,
                                "name" => $valname,
                                "order" => $k + 1,
                                "img" => isset($valimgs[$k]) ? $valimgs[$k] : '');
                        }
                        if ($adddata) {
                            DB::getDB()->insertMulti("specval", $adddata);
                        }
                    }
                    $this->adminlog("al_spec", array("do" => "add", "name" => $name));
                    $this->setHint(__("add_success", $text), "spec_index");
                }

                break;
            case 'editfield':
                $field = strtolower($_POST["field"]);
                $ret = false;
                if ($field == "delete") { //彻底删除
                    $specidstr = $_POST["idstr"];
                    if ($specidstr) {
                        $specids = explode(",", $specidstr);
                        $where = "specid in " . cimplode($specids);

                        $specnames = DB::getDB()->selectkv("spec", "specid", "name", $where);
                        foreach ($specnames as $name) {
                            $this->adminlog("al_spec", array("do" => "del", "name" => $name));
                        }

                        DB::getDB()->delete("spec", $where);
                        DB::getDB()->delete("specval", $where);
                    }
                }
                exit("success");
                break;
        }
    }

}
