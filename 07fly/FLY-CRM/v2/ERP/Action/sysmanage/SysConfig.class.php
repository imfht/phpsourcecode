<?php
/*
 *
 * sysmanage.SysConfig  系统配置   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */

class SysConfig extends Action
{

    private $cacheDir = '';//缓存目录

    public function __construct()
    {
        _instance('Action/sysmanage/Auth');
    }

    //得到系统配置参数
    public function sys_info()
    {
        $sql = "select * from fly_sys_config order by id asc;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        foreach ($list as $key => $row) {
            $string = '';
            if ($row['type'] == 'bool') {
                $string .= "<input type='radio' name='" . $row['varname'] . "' vlaue='1'> 是";
                $string .= "<input type='radio' name='" . $row['varname'] . "' vlaue='0'> 否";
            } elseif ($row['type'] == 'string') {
                $string .= "<input type='text' name='" . $row['varname'] . "' value='" . $row['value'] . "' class=\"form-control\">";
            } elseif ($row['type'] == 'number') {
                $string .= "<input type='text' name='" . $row['varname'] . "' value='" . $row['value'] . "' class=\"form-control\">";
            } elseif ($row['type'] == 'bstring') {
                $string .= "<textarea name='" . $row['varname'] . "' cols='100' rows='5' class=\"form-control\">" . $row['value'] . "</textarea>";
            } elseif ($row['type'] == 'text') {
                $string .= '<script id="' . $row['varname'] . '" name="' . $row['varname'] . '" type="text/plain" style="width:100%;height:200px;">' . $row['value'] . '</script>';
                $string .= '
			    <script>
			        $(document).ready(function () {
                        var ue = UE.getEditor("' . $row['varname'] . '");
                        ue.ready(function () {
                            //ue.setContent(""); 
                        });
                     });
                     </script>
			    ';
            }
            $list[$key]["namevalue"] = $string;
        }
        return $list;
    }

    //得到系统配置参数
    public function sys_conf()
    {
        $sql = "select varname,value from fly_sys_config;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        foreach ($list as $key => $row) {
            $rtn[$row['varname']] = $row['value'];
        }
        return $rtn;
    }

    //系统常规设置
    public function sys_config()
    {
        if (empty($_POST)) {
            $list = $this->sys_info();
            $smarty = $this->setSmarty();
            $smarty->assign(array("list" => $list));
            $smarty->display('sysmanage/sys_config.html');
        } else {
            foreach ($_POST as $key => $v) {
                $sql = "update fly_sys_config set value='" . $v . "' where varname='" . $key . "'";
                $this->C($this->cacheDir)->update($sql);
            }
            $this->location('操作成功', "/sysmanage/SysConfig/sys_config/");
            //$this->L("Common")->ajax_json_success("操作成功",'1',"/sysmanage/sysconfig/SysConfig/sys_config/");
        }
    }

    //系统常规设置
    public function sys_config_add()
    {
        if (empty($_POST)) {
            $smarty = $this->setSmarty();
            $smarty->display('sysmanage/sysconfig/sys_config_add.html');
        } else {
            $sql = "select * from fly_sys_config where varname='$_POST[varname]';";
            $one = $this->C($this->cacheDir)->findOne($sql);
            if (empty($one)) {
                $sql = "insert into fly_sys_config(varname,name,value,type)
						values('$_POST[varname]','$_POST[name]','$_POST[value]','$_POST[type]')";
                $this->C($this->cacheDir)->update($sql);
                $this->L("Common")->ajax_json_success("操作成功", '1', "/sysmanage/sysconfig/SysConfig/sys_config/");
            } else {
                $this->L("Common")->ajax_json_error("输出的变量名称已经存在");
            }
        }
    }

}//end class
?>