<?php

defined('IN_CART') or die;

/**
 *
 * 第三方key，secret
 * 
 */
class Third extends Base
{

    /**
     *
     * cnzz统计
     * 
     */
    public function cnzz()
    {
        if (ispostreq()) {
            //开通cnzz统计
            $url = getConfig("weburl");
            $key = md5($url . "KsjosMaR");
            if ($data = @file_get_contents("http://intf.cnzz.com/user/companion/yuncart.php?domain=$url&key=$key&cms=yuncart")) {
                if (substr($data, 0, 1) == '-') {
                    $this->setHint(__("conn_cnzz_data_failure"));
                } else {
                    $data = explode('@', $data);
                    $replacedata[] = array("key" => "cnzz_siteid", "val" => $data[0], "type" => "cnzz");
                    $replacedata[] = array("key" => "cnzz_password", "val" => $data[1], "type" => "cnzz");

                    DB::getDB()->replaceMulti("config", $replacedata);
                    $this->adminlog("al_cnzz");
                    redirect(url('admin', 'third', 'cnzz'));
                    return;
                }
            } else {
                $this->setHint(__("cannt_conn_cnzz_server"));
            }
        } else {
            $this->data["cnzz"] = DB::getDB()->selectkv("config", "key", "val", "type='cnzz'");
            $this->output("cnzz_index");
        }
    }

    /**
     *
     * 快递100
     * 
     */
    public function kuaidi()
    {
        if (ispostreq()) {
            $text = __("kuaidi");
            $kuaidi_status = isset($_POST["kuaidi_status"]) ? 1 : 0;
            $kuaidi_key = $kuaidi_status ? trim($_POST["kuaidi_key"]) : '';
            $replace_data[] = array("key" => "kuaidi_status", "val" => $kuaidi_status, "type" => "kuaidi");
            $replace_data[] = array("key" => "kuaidi_key", "val" => $kuaidi_key, "type" => "kuaidi");
            DB::getDB()->replaceMulti("config", $replace_data);
            $this->adminlog("al_kuaidi", array("do" => __($kuaidi_status ? "open" : "close")));
            $this->setHint(__("set_success", $text), "third_kuaidi");
        } else {
            $this->data["kuaidi"] = DB::getDB()->selectkv("config", "key", "val", "type='kuaidi'");
            $this->output("kuaidi_index");
        }
    }

    /**
     *
     * 又拍云
     * 
     */
    public function upyun()
    {
        if (ispostreq()) {
            $text = __("upyun");
            $upyun_status = isset($_POST["upyun_status"]) ? 1 : 0;
            $upyun_space = $upyun_status ? trim($_POST["upyun_space"]) : 0;
            $upyun_uname = $upyun_status ? trim($_POST["upyun_uname"]) : 0;
            $upyun_pass = $upyun_status ? trim($_POST["upyun_pass"]) : 0;
            $upyun_domain = $upyun_status ? trim($_POST["upyun_domain"]) : 0;

            $replace_data[] = array("key" => "upyun_status", "val" => $upyun_status, "type" => "upyun");
            $replace_data[] = array("key" => "upyun_space", "val" => $upyun_space, "type" => "upyun");
            $replace_data[] = array("key" => "upyun_uname", "val" => $upyun_uname, "type" => "upyun");
            $replace_data[] = array("key" => "upyun_pass", "val" => $upyun_pass, "type" => "upyun");
            $replace_data[] = array("key" => "upyun_domain", "val" => $upyun_domain, "type" => "upyun");
            $this->adminlog("al_upyun", array("do" => __($upyun_status ? "open" : "close")));

            DB::getDB()->replaceMulti("config", $replace_data);
            $this->setHint(__("set_success", $text), "third_upyun");
        } else {
            $this->data["upyun"] = DB::getDB()->selectkv("config", "key", "val", "type='upyun'");
            $this->output("upyun_index");
        }
    }

    /**
     *
     * 第三方登录
     * 
     */
    public function tlogin()
    {
        $this->data['tlogins'] = DB::getDB()->select("tlogin", "*", "", "order");
        $this->output("tlogin_index");
    }

    /**
     *
     * 第三方登录保存
     *  
     */
    public function tloginsave()
    {
        $opertype = strtolower(trim($_REQUEST["opertype"]));
        $text = __("tlogin");
        switch ($opertype) {
            case 'editfield':
                $field = strtolower($_REQUEST["field"]);
                $ret = false;
                if ($field == "publish") {//修改发布状态
                    $tloginid = intval($_GET["tloginid"]);
                    $ret = DB::getDB()->updatebool("tlogin", "ispublish", "tloginid='$tloginid'");
                    $this->adminlog("al_tlogin", array("do" => "edit", "tloginid" => $tloginid));
                    $this->setHint(__('set_success', array($text, __('publish_property'))), "third_tlogin");
                } else {
                    !in_array($field, array("appkey", "appsecret")) && exit("failure");
                    $value = trim($_POST["value"]);
                    $tloginid = intval($_POST["id"]);
                    $this->adminlog("al_tlogin", array("do" => "edit", "tloginid" => $tloginid));
                    $ret = DB::getDB()->update("tlogin", array($field => $value), "tloginid='$tloginid'");
                }
                exit($ret ? "success" : "failure");
            case 'save':
                $tloginids = $_POST["tloginid"];
                foreach ($tloginids as $key => $tloginid) {
                    DB::getDB()->update("tlogin", array("order" => intval($key) + 1), "tloginid='$tloginid'");
                }
                $url = url("admin", "tlogin", "index");
                $this->adminlog("al_tlogin_order");
                $this->setHint(__("set_success", $text), "third_tlogin");
                break;
        }
    }

    /**
     *  
     * 淘宝开放平台
     *
     *
     * */
    public function taobao()
    {
        if (ispostreq()) {
            $taobao_key = $_POST["taobao_key"];
            $taobao_secret = $_POST["taobao_secret"];
            $replacedata[] = array("key" => "taobao_key", "val" => $taobao_key, "type" => "taobao");
            $replacedata[] = array("key" => "taobao_secret", "val" => $taobao_secret, "type" => "taobao");
            DB::getDB()->replaceMulti("config", $replacedata);
            $this->adminlog("al_tbkey");
            $this->setHint(__("set_success", __('conn_taobao')), "third_taobao");
        } else {
            $this->data["taobao"] = DB::getDB()->selectkv("config", "key", "val", "type='taobao'");
            $this->output("taobao_set");
        }
    }

    /**
     *  
     * etao设置
     *
     *
     * */
    public function etao()
    {
        if (ispostreq()) {
            if ($_FILES['etao_file']['name']) {//如果上传文件
                $name = $_FILES['etao_file']['name'];
                if ($name != "etao_domain_verify.txt") {//如果文件名不正确
                    $this->setHint("etao_upload_name_error");
                }

                require COMMONPATH . "/upload.class.php";
                $upload = new Upload(array("txt"), array(), 1024, SITEPATH, false);
                if (!$upload->uploadfile()) {
                    $error = $upload->getError();
                    $this->setHint($error);
                }
            }

            $etao_status = isset($_POST["etao_status"]) ? intval($_POST["etao_status"]) : 0;
            $replacedata[] = array("key" => "etao_status", "val" => $etao_status, "type" => "etao");
            $etao_account = $etao_status ? $_POST["etao_account"] : '';
            $etao_postfee = $etao_status ? $_POST["etao_postfee"] : '';
            $replacedata[] = array("key" => "etao_account", "val" => $etao_account, "type" => "etao");
            $replacedata[] = array("key" => "etao_postfee", "val" => $etao_postfee, "type" => "etao");

            $this->adminlog("al_etao", array("do" => __($etao_status ? "open" : "close")));
            DB::getDB()->replaceMulti("config", $replacedata);

            $this->setHint(__("set_success", __('conn_etao')), "third_etao");
        } else {
            $this->data["etao"] = DB::getDB()->selectkv("config", "key", "val", "type='etao'");
            $this->data['weburl'] = getConfig('weburl');
            $this->output("etao_set");
        }
    }

}
