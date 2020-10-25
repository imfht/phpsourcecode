<?php

defined('IN_CART') or die;

/**
 *
 * log
 * 
 */
class Log extends Base
{

    /**
     *
     * 品牌列表
     * 
     */
    public function admin()
    {
        //删除过期log
        $adminlog_status = getConfig("adminlog_status");
        if ($adminlog_status) {
            $autodel = getConfig("adminlog_autodel");
            $deltime = time() - intval($autodel) * 86400;
            DB::getDB()->delete("admin_log", "addtime<'$deltime'");
        }
        $time1 = isset($_REQUEST['time1']) ? strtotime(trim($_REQUEST["time1"])) : "";
        $time2 = isset($_REQUEST['time2']) ? strtotime(trim($_REQUEST['time2'])) : "";
        $do = isset($_REQUEST['do']) ? trim($_REQUEST['do']) : "";

        $this->data = array("time1" => $time1, "time2" => $time2);
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        $where = array();
        if ($time1)
            $where[] = "addtime>'$time1'";
        if ($time2)
            $where[] = "addtime<'$time2'";
        $wherestr = $where ? implode(' AND ', $where) : '';

        if ($do == "import") {
            $this->_import($wherestr);
        } else {
            $count = DB::getDB()->selectcount("admin_log", $wherestr);
            if ($count) {
                //获取分页参数
                $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
                //查询数据
                $this->data["logs"] = DB::getDB()->select("admin_log", "*", $wherestr, "logid DESC", $this->data['pagearr']['limit']);
                foreach ($this->data["logs"] as $key => $log) {
                    $this->data["logs"][$key]['oper'] = __($log['oper'], $log['data'] ? @unserialize($log['data']) : array());
                }
            }
            $this->output("log_admin");
        }
    }

    private function _import($wherestr)
    {
        $count = DB::getDB()->selectcount("admin_log", $wherestr);
        if (!$count) {
            $this->setHint(__("no_data_import"));
        }
        $logs = DB::getDB()->select("admin_log", "*", $wherestr, "logid DESC");
        $content = __("time") . ","
                . __("admin") . ","
                . "ip" . ","
                . __("record")
                . CRLF;
        foreach ($logs as $log) {
            $content .= date('Y-m-d H:i:s', $log['addtime']) . "\t,"
                    . $log['adminid'] . '/' . $log['uname'] . ","
                    . $log['ip'] . ','
                    . __($log['oper'], $log['data'] ? @unserialize($log['data']) : array())
                    . CRLF;
        }
        import($content);
    }

    /**
     *
     * log设置
     * 
     */
    public function set()
    {
        if (ispostreq()) {
            $text = __("log");
            $adminlog_status = isset($_POST["adminlog_status"]) ? 1 : 0;
            $adminlog_autodel = $adminlog_status ? intval($_POST["adminlog_autodel"]) : 30;

            $replacedata[] = array("key" => "adminlog_status", "val" => $adminlog_status, "type" => "log");
            $replacedata[] = array("key" => "adminlog_autodel", "val" => $adminlog_autodel, "type" => "log");

            DB::getDB()->replaceMulti("config", $replacedata);
            $this->adminlog("al_log", array("do" => __($adminlog_status ? "open" : "close")));
            $this->setHint(__("set_success", $text), "log_set");
        } else {
            $this->data["log"] = DB::getDB()->selectkv("config", "key", "val", "type='log'");
            $selected = isset($this->data["log"]['autodel']) ? $this->data["log"]['autodel'] : 30;
            $autodel = getCommonCache("all", "autodel");
            $this->data['autodelopt'] = array2select($autodel, "key", "val", $selected);
            $this->output("log_set");
        }
    }

}
