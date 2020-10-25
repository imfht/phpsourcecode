<?php

defined('IN_CART') or die;

/**
 *
 * 用户
 * 
 */
class User extends Base
{

    /**
     *
     * 用户列表
     * 
     */
    public function index()
    {
        list($page, $pagesize) = $this->getRequestPage();

        $where['isdel'] = "0";
        //搜索
        $do = isset($_REQUEST['do']) ? trim($_REQUEST['do']) : "";
        $q = isset($_REQUEST['q']) ? trim($_REQUEST['q']) : "";
        if ($q) {
            is_numeric($q) && ($where['uid'] = $q) || ($where['uname'] = "like '%" . $q . "%'");
        }
        $this->data['orderby'] = isset($_REQUEST["orderby"]) ? trim($_REQUEST["orderby"]) : 'uid';
        $this->data["order"] = isset($_REQUEST["order"]) ? trim($_REQUEST["order"]) : 'desc';
        $this->data["orderrev"] = $this->data['order'] == "desc" ? 'asc' : 'desc';

        $this->data['q'] = $q;


        $orderstr = $this->data['orderby'] . " " . $this->data["order"];
        if ($do == "import") {
            $this->_import($where, $orderstr);
        } else {
            $count = DB::getDB()->selectcount("user", $where);
            if ($count) {
                $this->data["pagearr"] = getPageArr($page, $pagesize, $count);
                $this->data["userlist"] = DB::getDB()->select("user", "*", $where, $orderstr, $this->data['pagearr']['limit']);
            }
            $this->output("user_index");
        }
    }

    private function _import($where, $orderstr)
    {
        $count = DB::getDB()->selectcount("user", $where);
        if (!$count) {
            $this->setHint(__("no_data_import"));
        }
        $users = DB::getDB()->select("user", "*", $where, $orderstr);
        $content = __("uid") . ","
                . __("uname") . ","
                . __("name") . ","
                . "Email" . ","
                . __("linkway") . ","
                . __("sex") . ","
                . __("point") . ","
                . __("regtime") . ","
                . __("regip") . ","
                . __("lastlogintime") . ","
                . CRLF;
        foreach ($users as $user) {
            $content .= $user['uid'] . ","
                    . $user['uname'] . ","
                    . $user['name'] . ","
                    . $user['email'] . ","
                    . $user['link'] . ","
                    . $user['sex'] . ","
                    . $user['point'] . ","
                    . date('m-d', $user['regtime']) . ","
                    . $user['regip'] . ","
                    . ($user['lasttime'] ? date('m-d', $user['lasttime']) : '--') . ","
                    . CRLF
            ;
        }
        import($content);
    }

    /**
     *
     * 添加用户
     * 
     */
    public function useradd()
    {
        $this->data["opertype"] = "add";
        $this->data["leftcur"] = "user_index";
        $this->output("user_oper");
    }

    /**
     *
     * 修改用户
     * 
     */
    public function useredit()
    {
        $uid = intval($_GET["uid"]);
        $this->data["opertype"] = "edit";
        $this->data["user"] = DB::getDB()->selectrow("user", "*", "uid='$uid'");
        $this->data["uid"] = $uid;
        $this->output("user_oper");
    }

    /**
     *
     * 保存用户
     * 
     */
    public function usersave()
    {
        $opertype = $_POST["opertype"];
        $text = __('user');
        switch ($opertype) {
            case 'add':
            case 'edit':
                $name = trim($_POST["name"]);
                $pass = trim($_POST["pass"]);
                $email = trim($_POST["email"]);
                $link = trim($_POST["link"]);
                $note = trim($_POST["note"]);
                $point = intval($_POST["point"]);

                $data = array("point" => $point,
                    "email" => $email,
                    "note" => $note,
                    "link" => $link,
                    "name" => $name
                );
                if ($pass) {
                    $data += encpass($pass);
                }
                $uid = isset($_POST["uid"]) ? intval($_POST["uid"]) : 0;
                if ($uid) {
                    $ret = DB::getDB()->update("user", $data, "uid='$uid'");
                    $this->adminlog("al_user", array("do" => "edit", "uid" => $uid));
                    $this->setHint(__("edit_success", $text));
                } else {
                    $data['uname'] = trim($_POST["uname"]);
                    $data["regip"] = getClientIp();
                    $data["regtime"] = time();
                    $ret = DB::getDB()->insert("user", $data);
                    $this->adminlog("al_user", array("do" => "add", "uname" => $data['uname']));
                    $this->setHint(__("add_success", $text));
                }
                break;
            case 'editfield':
                $field = trim($_POST["field"]);
                $ret = false;
                if ($field == "remove") {
                    $uidstr = trim($_POST["idstr"]);
                    if ($uidstr) {
                        $text = "user";
                        $uids = explode(",", $uidstr);
                        $where = "uid in " . cimplode($uids);
                        $ret = DB::getDB()->update("user", "isdel=1", $where);
                        $titles = DB::getDB()->selectkv("user", "uid", "uname", $where);

                        $recycledata = array();
                        $table = array("table" => "user", "type" => "user", "tablefield" => "uid", "addtime" => time());
                        foreach ($uids as $uid) {
                            $this->adminlog("al_user", array("do" => "remove", "uname" => $titles[$uid]));
                            $recycledata[] = $table + array("tableid" => $uid, "title" => $titles[$uid]);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

    /**
     *
     * 检查用户名
     * 
     */
    public function checkuname()
    {
        $uname = trim($_POST["uname"]);
        $ret = DB::getDB()->selectexist("user", "uname='$uname'");
        exit($ret ? "failure" : "success");
    }

}
