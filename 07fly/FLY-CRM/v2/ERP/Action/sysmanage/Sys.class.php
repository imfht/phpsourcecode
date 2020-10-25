<?php

/*
 * 后台系统配置管理类
 *
 */

class Sys extends Action
{

    private $cacheDir = '';//缓存目录
    private $auth;

    public function __construct()
    {
        $this->auth = _instance('Action/sysmanage/Auth');
        $this->upgrade = _instance('Action/sysmanage/Upgrade');
        $this->upgradedata = _instance('Action/sysmanage/UpgradeData');
        $this->zip = _instance('Extend/Zip');
        $this->file = _instance('Extend/File');
    }

    //得到系统配置参数
    public function get_sys_info()
    {
        $sql = "select * from fly_sys_config;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $assArr = array();
        if (is_array($list)) {
            foreach ($list as $key => $row) {
                $assArr[$row["varname"]] = $row["value"];
            }
        }
        return $assArr;
    }

    //系统密码设置
    public function sys_password_modify()
    {
        if (empty($_POST)) {
            $smarty = $this->setSmarty();
            $smarty->display('sysmanage/sys_password_modify.html');
        } else {
            $oldpassword = md5($_POST["oldpassword"]);
            $newpassword = md5($_POST["newpassword"]);
            $newpassword1 = md5($_POST["newpassword1"]);
            if ($newpassword != $newpassword1) {
                $this->L("Common")->ajax_json_error("两次密码不一样,请细心检查是否因大小写原因造成");
            }
            $sql = "select id from fly_sys_user where account='" . SYS_USER_ACC . "' and password='$oldpassword';";
            $one = $this->C($this->cacheDir)->findOne($sql);
            if (!empty($one)) {
                $sql = "update fly_sys_user set password='$newpassword' where account='" . SYS_USER_ACC . "';";
                if ($this->C($this->cacheDir)->update($sql) >= 0) {
                    $this->L("Common")->ajax_json_success("操作成功");
                }
            } else {
                $this->L("Common")->ajax_json_error("输入的旧密码不正确请重新输入");
            }

        }
    }

    //获取操作日志
    public function sys_log()
    {
        //**获得传送来的数据作分页处理
        $currentPage = $this->_REQUEST("pageNum");//第几页
        $numPerPage = $this->_REQUEST("numPerPage");//每页多少条
        $currentPage = empty($currentPage) ? 1 : $currentPage;
        $numPerPage = empty($numPerPage) ? $GLOBALS["pageSize"] : $numPerPage;

        //用户查询参数
        $searchKeyword = $this->_REQUEST("searchKeyword");
        $searchValue = $this->_REQUEST("searchValue");
        $startdate = $this->_REQUEST("startdate");
        $enddate = $this->_REQUEST("enddate");
        $editor = $this->_REQUEST("org_account");

        $where = "0=0 ";
        if (!empty($searchValue)) {
            $where .= " and $searchKeyword like '%$searchValue%'";
        }
        if ($startdate) {
            $where .= " and adddatetime>'$startdate' ";
        }
        if ($enddate) {
            $where .= " and adddatetime<='$enddate' ";
        }
        if ($editor) {
            $where .= " and editor='$editor' ";
        }
        $countSql = "select * from fly_sys_log where $where";
        $totalCount = $this->C($this->cacheDir)->countRecords($countSql);    //计算记录数
        $beginRecord = ($currentPage - 1) * $numPerPage;
        $sql = "select * from fly_sys_log  where $where order by id desc limit $beginRecord,$numPerPage";
        $list = $this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循环
        $assignArray = array('list' => $list, 'searchKeyword' => $searchKeyword, 'searchValue' => $searchValue,
            'startdate' => $startdate, 'enddate' => $enddate, 'editor' => $editor,
            "numPerPage" => $numPerPage, "totalCount" => $totalCount, "currentPage" => $currentPage
        );

        return $assignArray;
    }

    //调用显示
    public function sys_log_show()
    {
        $list = $this->sys_log();
        $smarty = $this->setSmarty();
        $smarty->assign($list);//框架变量注入同样适用于smarty的assign方法
        $smarty->display('sysmanage/sys_log.html');
    }

    //系统栏目和权限列表
    public function sys_menu($id = null)
    {
        $list = require(EXTEND . 'Menu.php');
        $role_menu = array();
        $role_mod = array();
        if ($id) {
            $result = $this->sys_role_power_one($id);
            $role_menu = explode(',', $result["sys_menu"]);
            $role_mod = explode(',', $result["sys_action"]);
        }
        $string = "<table id=menu_power>";
        $string .= "<tr bgcolor='#FBF5C6'><td>栏目</td><td>菜单</td></tr>";
        $cnt = 1;
        if (is_array($list)) {
            foreach ($list as $key => $row) {
                $bgcolor = ($cnt % 2 == 0) ? "#FBF5C6" : "#F9F9F9";
                $string .= "<tr bgcolor=" . $bgcolor . "><td width='10%'>" . $row["desc"] . "<input type='checkbox' name='menuID[]' value='" . $key . "' ";
                if (in_array($key, $role_menu)) $string .= " checked";
                $string .= " onclick='test(this);'></td><td>";
                foreach ($row["menuitem"] as $item_key => $item) {
                    $string .= "<table><tr><td width='15%'><input type='checkbox' name='menuID[]' value='" . $item_key . "' ";
                    if (in_array($item_key, $role_menu)) $string .= " checked";
                    $string .= "> " . $item["desc"] . "</td><td align=left>";
                    if (is_array($item["mod"])) {
                        foreach ($item["mod"] as $mod_key => $m_va) {
                            $string .= "<li style='list-style:none;width:100px;float:left;'><input type='checkbox' name='modID[]' value='" . $mod_key . "' ";
                            if (in_array($mod_key, $role_mod)) $string .= " checked";
                            $string .= "> " . $m_va . "</li>";
                        }
                    }
                    $string .= "</td></tr></table>";
                }
                $cnt++;
                $string .= "</td></tr>";
            }
            $string .= "</table>";
        }
        return $string;
    }


    /**获取远程文件升级信息
     * Author: lingqifei created by at 2020/4/2 0002
     */
    public function sys_upgrade()
    {
        //在线数据
        $server = $this->upgrade->server_upgrade();
        $version = $this->upgrade->version();
        $upurl = "$server/get_version/?sys=v2&ver=$version";
        $up_list = $this->file->read_file($upurl);
        $list = json_decode($up_list, true);
        if (is_array($list['data'])) {
            $listdata = $list['data'];
            foreach ($listdata as &$row) {
                $status = $this->upgrade->check_down_verion($row['version']);
                if (!$status) {
                    $row['status'] = '<font color="red">文件没有下载</font>';
                    $row['operate'] = '<a href="javascript:void(0);" class="downfile" data-id="' . $row['version'] . '">点击下载更新包文件HTTP</a>';
                } else {
                    $row['status'] = '	<font color="green">文件已下载[文件完整]</font>';
                    $row['operate'] = '	<a href="javascript:void(0);" class="upgrade" data-id="' . $row['version'] . '" data-step="1">点击升级更新包</a>';
                }
            }
        } else {
            $listdata = array();
        }

        // print_r($listdata);

        $auth = $this->upgrade->is_auth();
        if (!$auth) {
            $this->upgradedata->update_data_init();
        }
        $authorize = $this->upgrade->upgrade_auth_check();
        $signal = $this->upgrade->upgrade_signal_check();
        $smarty = $this->setSmarty();
        $smarty->assign(array("list" => $listdata, 'version' => $version, 'authorize' => $authorize, 'signal' => $signal));
        $smarty->display('sysmanage/sys_upgrade.html');
    }

    /**
     * 执行在线线函数
     * Author: lingqifei created by at 2020/5/16 0016
     */
    public function sys_upgrade_online()
    {
        $step = $this->_REQUEST("step");
        $ver = $this->_REQUEST("ver");
        if ($step == 1) {
            $rtn = $this->upgrade->upgrade_backup();
            $rtn = '测试';
            $txt = "备份完成!  当前版本程序备份文件为： $rtn";
            $result = array(
                'statusCode' => 300,
                'message' => $txt,
                'step' => 2,
                'ver' => $ver,
            );
        } elseif ($step == 2) {
            $rtn = $this->upgrade->upgrade_exec($ver);
            $txt = "系统升级完成! 程序已经覆盖当前系统目录 ";
            $result = array(
                'statusCode' => 300,
                'message' => $txt,
                'step' => 3,
                'ver' => $ver,
            );
        } elseif ($step == 3) {
            $rtn = $this->upgradedata->update_data_sql();
            $txt = "数据库升级完成! ";
            $result = array(
                'statusCode' => '300',
                'message' => $txt,
                'step' => 4,
                'ver' => $ver,
            );
        } elseif ($step == 4) {
            $txt = "系统升级完成! ";
            $result = array(
                'statusCode' => '200',
                'message' => $txt,
                'step' => 4,
                'ver' => $ver,
            );
        }
        echo json_encode($result);
    }

    //导入升级文件删除
    public function sys_upgrade_del()
    {
        $dirname = $this->L("Upload")->upload_upgrade_path();
        $filename = ($_GET["filename"]) ? $_GET["filename"] : $_POST["filename"];
        $this->File()->unlink_file($dirname . $filename);
        $this->L("Common")->ajax_json_success("删除成功", "1", "/Sys/sys_upgrade/");
    }

}//end class
?>