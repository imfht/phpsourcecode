<?php

defined('IN_CART') or die;

/**
 *
 * 到货通知
 * 
 */
class Notify extends Base
{

    /**
     *
     * 到货通知
     * 
     */
    public function nostock()
    {
        $this->_notify('nostock');
    }

    public function downprice()
    {
        $this->_notify('downprice');
    }

    private function _notify($type)
    {
        //分页
        list($page, $pagesize) = $this->getRequestPage();

        //排序
        $this->data['orderby'] = isset($_REQUEST["orderby"]) ? trim($_REQUEST["orderby"]) : 'notifyid';
        $this->data["order"] = isset($_REQUEST["order"]) ? trim($_REQUEST["order"]) : 'desc';
        $this->data["orderrev"] = $this->data['order'] == "desc" ? 'asc' : 'desc';
        $orderstr = $this->data['orderby'] . " " . $this->data["order"];

        $where = "isdel=0 AND type='$type'";
        $count = DB::getDB()->selectcount("user_notify", $where);
        if ($count) {
            //获取分页参数
            $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
            //查询数据
            $this->data["notifylist"] = DB::getDB()->select("user_notify", "*", $where, $orderstr, $this->data['pagearr']['limit']);
        }
        $this->data['type'] = $type;
        $this->output("notify_index");
    }

    /**
     *
     * 保存到货通知
     * 
     */
    public function notifysave()
    {
        $opertype = trim($_POST["opertype"]);
        $type = trim($_REQUEST['type']);
        !in_array($type, array("nostock", "downprice")) && $type = "downprice";
        switch ($opertype) {
            case 'editfield':
                $field = trim($_POST["field"]);
                if ($field == "remove") { //移除通知
                    $notifyidstr = trim($_POST["idstr"]);
                    if ($notifyidstr) {
                        $notifyids = explode(",", $notifyidstr);
                        $where = "notifyid in " . cimplode($notifyids);
                        $ret = DB::getDB()->update("user_notify", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("user_notify", "notifyid", "itemname", $where);

                        $recycledata = array();
                        $table = array("table" => "user_notify", "type" => "usernotify", "tablefield" => "notifyid", "addtime" => time());

                        foreach ($titles as $notifyid => $itemname) {
                            $recycledata[] = $table + array("tableid" => $notifyid, "title" => $itemname);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                } elseif ($field == "notify") {//发送通知
                    $notifyidstr = trim($_POST["idstr"]);
                    if (!$notifyidstr)
                        exit("failure");
                    //发送方式
                    $mq = new MQ($type);

                    //需要发送的notifylist
                    $notifyids = explode(",", $notifyidstr);
                    $where = "notifyid in " . cimplode($notifyids);
                    $notifylist = DB::getDB()->select("user_notify", "*", $where);
                    if ($notifylist) {
                        $itemids = array();
                        foreach ($notifylist as $notify) {
                            $itemids[] = $notify["itemid"];
                        }
                        $items = DB::getDB()->selectkv("item", "itemid", "price", "itemid in " . cimplode($itemids));
                        foreach ($notifylist as $notify) {
                            if (!isset($items[$notify['itemid']]))
                                continue;
                            $replacement = array(def($notify['uname']),
                                def($notify['email']),
                                def($notify['itemname']),
                                getPrice($items[$notify['itemid']]),
                                getconfig('weburl') . "?" . $notify['itemid']);
                            $mq->send($notify["uid"], array(
                                "mobile" => $notify['mobile'],
                                "replacement" => $replacement,
                                "email" => $notify['email'],
                                "uname" => $notify['uname']));
                        }
                    }
                    $ret = DB::getDB()->update("user_notify", "notifytime='" . time() . "'", $where);
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
