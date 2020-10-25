<?php

defined('IN_CART') or die;

/**
 *
 * 图片空间
 *
 */
class Pic extends Base
{

    /**
     *
     * 图片空间
     *
     */
    public function index()
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage(12);
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        $time1 = isset($_REQUEST['time1']) ? strtotime(trim($_REQUEST['time1'])) : "";
        $time2 = isset($_REQUEST['time2']) ? strtotime(trim($_REQUEST['time2'])) : "";
        $this->data += array('q' => $q, "time1" => $time1, "time2" => $time2);

        $where[] = "isdel=0";
        $q && $where["q"] = "`name` like '%" . $q . "%'";
        $time1 && $where["time1"] = "addtime > '$time1'";
        $time2 && $where["time2"] = "addtime < '$time2'";

        $where = implode(' AND ', $where);

        $count = DB::getDB()->selectcount("pic", $where);
        if ($count) {
            //分页
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);

            //查询
            $pics = DB::getDB()->select("pic", "*", $where, "picid DESC", $this->data['pagearr']['limit']);
            $this->data['pics'] = range(1, 12);
            foreach ($pics as $key => $pic) {
                $this->data['pics'][$key] = $pic;
            }
        }
        $this->data['weburl'] = getConfig("weburl");
        $this->output("pic_index");
    }

    /**
     *  
     * 保存一个商品
     *
     * */
    public function picsave()
    {
        $opertype = strtolower($_REQUEST["opertype"]);
        $time = time();
        $text = __('pic');
        switch ($opertype) {
            case 'replace':
                $picid = intval($_GET["picid"]);
                $pic = DB::getDB()->selectrow("pic", "*", "picid='$picid' AND isdel=0");
                if (!$pic) {
                    $this->data['error'] = __("file_not_exists");
                } else {
                    $this->data['pic'] = $pic;
                }
                $this->output("replace_pic");
                break;
            case 'editfield':   //修改特定字段
                $field = strtolower(trim($_POST["field"]));
                $ret = false;
                if ($field == "remove") {//上架，下架，回收站
                    $picidstr = trim($_POST["idstr"]);
                    if ($picidstr) {
                        $picids = explode(",", $picidstr);
                        $where = "picid in " . cimplode($picids);
                        $pics = DB::getDB()->selectkv("pic", "picid", "name", $where);

                        $ret = DB::getDB()->update("pic", "isdel=1", $where);
                        $recycledata = array();
                        $table = array("table" => "pic", "type" => "pic", "tablefield" => "picid", "addtime" => time());

                        foreach ($pics as $picid => $pic) {
                            $this->adminlog("al_pic", array("do" => $field, "name" => $pic));
                            $recycledata[] = $table + array("tableid" => $picid, "title" => $pic);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } else if ($field == "resize") { //重新生成缩略图
                    $picidstr = trim($_POST["idstr"]);
                    if ($picidstr) {
                        $picids = explode(",", $picidstr);
                        $where = "picid in " . cimplode($picids);
                        $pics = DB::getDB()->select("pic", "*", $where, "", "", "picid");
                        require_once COMMONPATH . "/image.class.php";
                        foreach ($pics as $picid => $pic) {
                            $path = $pic['pic'];
                            if (!file_exists(SITEPATH . '/' . $path))
                                continue;
                            $size = getConfig("spic", "50");
                            $ret = Image::thumb($path, "{$path}_{$size}x{$size}.jpg", $size, $size);
                            $mpic = $bpic = 0;
                            if ($pic["mpic"]) {
                                $size = getConfig("mpic", "160");
                                $ret = Image::thumb($path, "{$path}_{$size}x{$size}.jpg", $size, $size);
                                $mpic = 1;
                            }
                            if ($pic["bpic"]) {
                                $size = getConfig("bpic", "310");
                                $ret = Image::thumb($path, "{$path}_{$size}x{$size}.jpg", $size, $size);
                                $bpic = 1;
                            }
                            DB::getDB()->update("pic", "spic=1,bpic='$mpic',mpic='$bpic'", "picid='$picid'");
                            $this->adminlog("al_pic", array("do" => "resize", "name" => $pic['name']));
                        }
                    }
                } else {
                    !in_array($field, array("name")) && exit("failure");
                    $picid = intval($_POST["id"]);
                    $value = trim($_POST["value"]);
                    $this->adminlog("al_pic", array("do" => "edit", "picid" => $picid));
                    $ret = DB::getDB()->update("pic", array($field => $value), "picid='$picid'");
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

    /**
     *
     * 设置
     *
     */
    public function set()
    {
        if (ispostreq()) {
            $spic = intval($_POST["spic"]);
            $bpic = intval($_POST["bpic"]);
            $mpic = intval($_POST["mpic"]);
            $replacedata[] = array("key" => "spic", "val" => $spic, "type" => 'picset');
            $replacedata[] = array("key" => "bpic", "val" => $bpic, "type" => 'picset');
            $replacedata[] = array("key" => "mpic", "val" => $mpic, "type" => 'picset');
            DB::getDB()->replaceMulti("config", $replacedata);
            $this->setHint(__("set_success", __('picset')), "pic_set");
        } else {
            $this->data["picset"] = DB::getDB()->selectkv("config", "key", "val", "type='picset'");
            $this->output("pic_set");
        }
    }

}
