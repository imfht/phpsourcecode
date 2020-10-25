<?php

defined('IN_CART') or die;

/**
 *
 * 管理员
 * 
 */
class Admin extends Base
{

    /**
     *
     * 管理员
     * 
     */
    public function index()
    {

        $adminlist = DB::getDB()->select("admin", "*", "isdel=0");
        $rolearr = DB::getDB()->selectkv("role", "roleid", "name", "isdel=0");
        foreach ($adminlist as $key => $val) {
            if (!$val['issuper'] && $val['role']) {
                $roles = explode(",", $val['role']);
                $roletext = array();
                foreach ($roles as $role) {
                    $roletext[] = $rolearr[$role];
                }
                $adminlist[$key]['role'] = implode(" ", $roletext);
            }
        }
        $this->data['adminlist'] = $adminlist;
        $this->output("admin_index");
    }

    /**
     *
     * 添加管理员
     * 
     */
    public function adminadd()
    {
        $this->data["opertype"] = "add";
        $this->data["roles"] = DB::getDB()->selectkv("role", "roleid", "name", "isdel=0");
        $this->data["leftcur"] = "admin_index";
        $this->output("admin_oper");
    }

    /**
     *
     * 修改管理员
     * 
     */
    public function adminedit()
    {
        $adminid = intval($_GET["adminid"]);
        $this->data["opertype"] = "edit";
        $this->data["roles"] = DB::getDB()->selectkv("role", "roleid", "name", "isdel=0");
        $this->data["admin"] = DB::getDB()->selectrow("admin", "*", "adminid=$adminid");
        $this->data["adminid"] = $adminid;
        $this->output("admin_oper");
    }

    /**
     *
     * 保存管理员
     * 
     */
    public function adminsave()
    {

        $opertype = trim($_REQUEST["opertype"]);
        $text = __('admin');
        switch ($opertype) {
            case "add":
            case 'edit':
                //提交参数
                $name = trim($_POST["name"]);
                $depart = trim($_POST["depart"]);
                $memo = trim($_POST["memo"]);
                $adminid = intval($_POST["adminid"]);
                $issuper = intval($_POST["issuper"]);
                $email = trim($_POST["email"]);
                $pass = !empty($_POST["pass"]) ? trim($_POST["pass"]) : "";
                $role = !$issuper && isset($_POST['role']) ? implode(",", $_POST["role"]) : "";

                if (!$issuper && !$role) {
                    $this->setHint(__("common_admin_must_role"));
                }

                $data = array("issuper" => $issuper,
                    "role" => $role,
                    "name" => $name,
                    "email" => $email,
                    "depart" => $depart,
                    "memo" => $memo);



                if ($pass) {
                    $data += encpass($pass);
                }
                if ($adminid) { //修改管理员
                    //判断DB中是否至少有1个超级管理员
                    if (!$data['issuper'] && !DB::getDB()->selectexist("admin", "issuper =1 AND isdel=0 AND adminid!='$adminid'")) {
                        $this->setHint(__("superadmin_least_one"));
                    }

                    $this->adminlog("al_admin", array("do" => "edit", "adminid" => $adminid));
                    $ret = DB::getDB()->update("admin", $data, "adminid='$adminid'");
                    $this->setHint(__("edit_success", $text));
                } else {  //添加管理员
                    $uname = trim($_POST["uname"]);
                    $data += array("uname" => $uname, "addtime" => time());
                    $this->adminlog("al_admin", array("do" => "add", "uname" => $uname));
                    $ret = DB::getDB()->insert("admin", $data);
                    $this->setHint(__("add_success", $text));
                }
                break;
            case 'editfield': //修改管理员字段
                $field = trim($_POST["field"]);
                if ($field == "remove") {
                    $adminidstr = trim($_POST["idstr"]);
                    if ($adminidstr) {
                        $adminids = explode(",", $adminidstr);
                        $where = "adminid in " . cimplode($adminids);

                        //判断DB中是否至少有1个超级管理员
                        if (!DB::getDB()->selectexist("admin", "issuper =1 AND isdel=0 AND adminid not in " . cimplode($adminids))) {
                            exit(__("superadmin_least_one"));
                        }

                        $ret = DB::getDB()->update("admin", "isdel=1", $where);
                        $admins = DB::getDB()->selectkv("admin", "adminid", "uname", $where);

                        $recycledata = array();
                        $table = array("table" => "admin", "type" => "admin", "tablefield" => "adminid", "addtime" => time());

                        foreach ($admins as $adminid => $uname) {
                            $this->adminlog("al_admin", array("do" => "remove", "uname" => $uname));
                            $recycledata[] = $table + array("tableid" => $adminid, "title" => $uname);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
