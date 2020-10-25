<?php

defined('IN_CART') or die;
require_once THIRDPATH . '/taobao/taobao.class.php';

/**
 *
 * 地区
 *
 */
class District extends Base
{

    /**
     *
     * 类别列表
     *  
     */
    public function index()
    {
        $this->data["districts"] = Dis::getDistrict();
        $this->output("district_index");
    }

    /**
     *  
     *  添加类别
     *
     */
    public function districtadd()
    {
        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "district_index";
        $this->output("district_oper");
    }

    /**
     *
     * 保存一个类别
     *
     */
    public function districtsave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $text = __('district');
        switch ($opertype) {
            case 'add':
                //提交参数
                $district = trim($_POST["district"]);
                $province = intval($_POST["province"]);
                $city = intval($_POST["city"]);
                $order = intval($_POST["order"]);
                $zipcode = trim($_POST["zipcode"]);
                $pid = 0;
                if ($city) {
                    $pid = $city;
                } else if ($province) {
                    $pid = $province;
                }
                $data = array("district" => $district,
                    "pid" => $pid,
                    "order" => $order ? $order : 50,
                    "zipcode" => $zipcode);
                $this->adminlog("al_district", array("do" => "add", "district" => $district));
                $ret = DB::getDB()->insert("district", $data);
                $this->setHint(__('add_success', $text), "district_index");
                break;
            case 'editfield'://修改特定字段
                $field = trim($_POST["field"]);
                if ($field == "remove") {
                    $ret = false;
                    $districtidstr = $_POST["idstr"];
                    if ($districtidstr) {
                        $districtids = explode(",", $districtidstr);
                        //删除大分类前，需要先删除小分类
                        if (DB::getDB()->selectcount("district", "pid in " . cimplode($districtids) . " AND isdel=0")) {
                            exit(__("deldistrict_has_child"));
                        }
                        $where = "districtid in " . cimplode($districtids);
                        $ret = DB::getDB()->update("district", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("district", "districtid", "district", $where);

                        $recycledata = array();
                        $table = array("table" => "district", "type" => "district", "tablefield" => "districtid", "addtime" => time());

                        foreach ($districtids as $districtid) {
                            $this->adminlog("al_district", array("do" => "remove", "district" => $titles[$districtid]));
                            $recycledata[] = $table + array("tableid" => $districtid, "title" => $titles[$districtid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else {
                    !in_array($field, array("district", "order")) && exit("failure");
                    $districtid = intval($_POST["id"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_district", array("do" => "edit", "districtid" => $districtid));
                    $ret = DB::getDB()->update("district", array($field => $value), "districtid='$districtid'");
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
