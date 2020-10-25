<?php

defined('IN_CART') or die;

/**
 *
 * 商品品牌
 * 
 */
class Brand extends Base
{

    /**
     *
     * 品牌列表
     * 
     */
    public function index()
    {
        $this->data["brandlist"] = DB::getDB()->select("brand", "*", "isdel=0", "order");
        $this->output("brand_index");
    }

    /**
     *
     * 添加品牌
     * 
     */
    public function brandadd()
    {
        $this->data["opertype"] = "add";
        $this->output("brand_oper");
    }

    /**
     *
     * 修改品牌
     * 
     */
    public function brandedit()
    {
        $brandid = intval($_GET["brandid"]);
        $this->data["opertype"] = "edit";
        $this->data["brand"] = DB::getDB()->selectrow("brand", "*", "brandid='$brandid'");
        $this->data["brandid"] = $brandid;
        $this->output("brand_oper");
    }

    /**
     *
     * 保存品牌
     * 
     */
    public function brandsave()
    {
        $opertype = trim($_POST["opertype"]);
        $text = __('brand');
        switch ($opertype) {
            case "add":
            case 'edit':
                //提交参数
                $brandid = intval($_POST["brandid"]);

                $brandname = trim($_POST["brandname"]);
                $brandurl = trim($_POST["brandurl"]);

                $brandlogo = trim($_POST["pic"]);

                $data = array("brandname" => $brandname,
                    "brandurl" => $brandurl,
                    "brandlogo" => $brandlogo);

                if ($brandid) { //修改品牌
                    $ret = DB::getDB()->update("brand", $data, "brandid='$brandid'");
                    $this->adminlog("al_brand", array("do" => "edit", "brandname" => $brandname));
                    $hint = __("edit_success", $text);
                } else {  //添加品牌
                    $ret = DB::getDB()->insert("brand", $data);
                    $this->adminlog("al_brand", array("do" => "add", "brandname" => $brandname));
                    $hint = __("add_success", $text);
                }
                $this->setHint($hint);
                break;
            case 'editfield': //修改品牌字段
                $field = trim($_POST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $brandidstr = trim($_POST["idstr"]);
                    if ($brandidstr) {

                        $brandids = explode(",", $brandidstr);
                        $where = "brandid in " . cimplode($brandids);

                        $ret = DB::getDB()->update("brand", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("brand", "brandid", "brandname", $where);

                        $recycledata = array();
                        $table = array("table" => "brand", "type" => "brand", "tablefield" => "brandid", "addtime" => time());
                        foreach ($brandids as $brandid) {
                            $this->adminlog("al_brand", array("do" => "remove", "brandname" => $titles[$brandid]));
                            $recycledata[] = $table + array("tableid" => $brandid, "title" => $titles[$brandid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {
                    !in_array($field, array("order", "brandname", "brandurl")) && exit("failure");
                    $value = trim($_POST['value']);
                    $brandid = intval($_POST['id']);
                    $this->adminlog("al_brand", array("do" => "edit", "brandid" => $brandid));
                    $ret = DB::getDB()->update("brand", array($field => $value), "brandid='$brandid'");
                }
                exit($ret ? "success" : "failure");
                break;
            case 'save':
                $brandids = $_POST["brandid"];
                foreach ($brandids as $key => $brandid) {
                    DB::getDB()->update("brand", array("order" => $key + 1), "brandid='$brandid'");
                }
                $this->adminlog("al_brand_order");
                $this->setHint(__('return_list', $text));
                break;
        }
    }

}
