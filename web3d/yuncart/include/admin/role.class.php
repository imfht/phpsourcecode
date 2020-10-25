<?php

defined('IN_CART') or die;

/**
 *
 * 管理员角色
 * 
 */
class Role extends Base
{

    /**
     *
     * 角色列表
     * 
     */
    public function index()
    {
        $this->data["rolelist"] = DB::getDB()->select("role", "*", "isdel=0");
        $this->output("role_index");
    }

    /**
     *
     * 添加角色
     * 
     */
    public function roleadd()
    {
        $this->data["opertype"] = "add";
        $this->data["privlist"] = getCommonCache("all", "privilege");
        $this->data["leftcur"] = "role_index";
        $this->output("role_oper");
    }

    /**
     *
     * 修改角色
     * 
     */
    public function roleedit()
    {
        $roleid = intval($_GET["roleid"]);
        $this->data["opertype"] = "edit";
        $this->data["roleid"] = $roleid;
        $this->data["role"] = DB::getDB()->selectrow("role", "*", "roleid=$roleid");
        $this->data["privlist"] = getCommonCache("all", "privilege");
        $this->data["leftcur"] = "role_index";
        $this->output("role_oper");
    }

    /**
     *
     * 保存角色
     * 
     */
    public function rolesave()
    {
        $opertype = trim($_POST["opertype"]);
        $text = __('role');
        switch ($opertype) {
            case "add":
            case 'edit':
                //提交参数
                $name = trim($_POST["name"]);
                $desc = trim($_POST["desc"]);
                $privilege = isset($_POST["privilege"]) ? $_POST["privilege"] : array();
                $roleid = intval($_POST["roleid"]);
                $privilegestr = implode(",", $privilege);

                $data = array("name" => $name, "desc" => $desc, "privilege" => $privilegestr);

                if ($roleid) { //添加管理员角色
                    DB::getDB()->update("role", $data, "roleid='$roleid'");
                    $this->adminlog("al_role", array("do" => "edit", "name" => $name));
                    $this->setHint(__("edit_success", $text));
                } else { //修改管理员角色
                    $ret = DB::getDB()->insert("role", $data);
                    $this->adminlog("al_role", array("do" => "add", "name" => $name));
                    $this->setHint(__("add_success", $text));
                }
                break;
            case 'editfield':    //修改管理员角色字段
                $field = trim($_POST["field"]);
                if ($field == "name") {  //修改角色名称
                    $value = trim($_POST["value"]);
                    $roleid = intval($_POST['id']);
                    $this->adminlog("al_role", array("do" => "edit", "roleid" => $roleid));
                    $ret = DB::getDB()->update("role", array("name" => $value), "roleid='$roleid'");
                } elseif ($field == "remove") { //删除
                    $ret = false;
                    $roleidstr = $_POST["idstr"];
                    if ($roleidstr) {
                        $roleids = explode(",", $roleidstr);
                        $where = "roleid in " . cimplode($roleids);
                        $ret = DB::getDB()->update("role", "isdel=1", $where);
                        $roles = DB::getDB()->selectkv("role", "roleid", "name", $where);
                        $recycledata = array();
                        $table = array("table" => "role", "type" => "role", "tablefield" => "roleid", "addtime" => time());

                        foreach ($roles as $roleid => $name) {
                            $this->adminlog("al_role", array("do" => "remove", "name" => $name));
                            $recycledata[] = $table + array("tableid" => $roleid, "title" => $name);
                        }
                        DB::getDB()->insertMulti("recycle", $recycledata);
                    }
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

}
