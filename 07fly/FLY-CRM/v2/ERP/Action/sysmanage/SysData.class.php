<?php
/*
 *
 * sysmanage.SysData  数据库备份恢复   
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

class SysData extends Action
{

    private $cacheDir = ''; //缓存目录
    private $dojs = '';
    private $bkdir = '';
    private $auth;

    public function __construct()
    {
        $this->auth = _instance('Action/sysmanage/Auth');

        $this->fp = _instance('Extend/File');

        //跳转到一下页的JS
        $gotojs = "function GotoNextPage(){
			document.gonext." . "submit();
		}" . "\r\nset" . "Timeout('GotoNextPage()',500);";

        $this->dojs = "<script language='javascript'>$gotojs</script>";
        $this->bkdir = ROOT . "/data/";
        $this->fp->create_dir($this->bkdir);


    }

    //数据库备份
    public function sys_data()
    {
        $tables = $this->sys_data_showtables();
        $smarty = $this->setSmarty();
        $smarty->assign(array("list" => $tables));
        $smarty->display('sysmanage/sys_data.html');
    }

    //权限初始化
    public function sys_data_role_init()
    {
        $sql = "select id from fly_sys_menu";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $menu = array();
        foreach ($list as $row) {
            $menu[] = $row['id'];
        }

        $sql = "select value from fly_sys_method";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $meth = array();
        foreach ($list as $row) {
            $meth[] = $row['value'];
        }
        $menu = implode(",", $menu);
        $meth = implode(",", $meth);
        $sql = "update `fly_sys_power` set access_value='$menu' where master='role' and master_value='1' and access='SYS_MENU'";

        $this->C($this->cacheDir)->update($sql);
        $sql = "update `fly_sys_power` set access_value='$meth' where master='role' and master_value='1' and access='SYS_METHOD'";
        $this->C($this->cacheDir)->update($sql);
        $this->L("Common")->ajax_json_success("操作成功");

    }

    //执行备份数据库
    public function sys_data_back()
    {
        $tables = $this->sys_data_showtables();
        $smarty = $this->setSmarty();
        $smarty->assign(array("list" => $tables));
        $smarty->display('sysmanage/sys_data_back.html');
    }

    //执行备份函数
    public function sys_data_back_done()
    {
        $bkdir = $this->bkdir;
        $fp = $this->L("File");
        $tablearr = $this->_REQUEST("tablearr"); //需要备份的表
        $isstruct = $this->_REQUEST("isstruct"); //备份表结构
        $startpos = $this->_REQUEST("startpos"); //开始表的位置
        $iszip = $this->_REQUEST("iszip");
        $nowtable = $this->_REQUEST("nowtable");
        $fsize = $this->_REQUEST("fsize");
        $psize = $this->_REQUEST("psize");
        $datatype = $this->_REQUEST("datatype");

        if (empty($tablearr)) {
            ShowMsg('你没选中任何表！', 'javascript:;');
            exit();
        }
        if (!is_dir($bkdir)) {
            $fp->create_dir($bkdir);
        }

        //初始化使用到的变量
        if (is_array($tablearr)) {
            $tablearr = implode(",", $tablearr);
        }
        $tables = explode(',', $tablearr);

        if (!isset($isstruct)) {
            $isstruct = 0;
        }
        if (!isset($startpos)) {
            $startpos = 0;
        }
        if (!isset($iszip)) {
            $iszip = 0;
        }
        if (empty($nowtable)) {
            $nowtable = '';
        }
        if (empty($fsize)) {
            $fsize = 2048;
        }
        if (empty($psize)) {
            $psize = 1000;
        }
        $fsizeb = $fsize * 1024;

        //第一页的操作
        if ($nowtable == '') {
            $tmsg = '';
            $list = $fp->list_dir_info($bkdir);
            foreach ($list as $onefile) {
                $fp->unlink_file($onefile);
            }
            $tmsg .= "清除备份目录旧数据完成...<br />";

            if ($isstruct == 1) {
                $bkfile = $bkdir . "/tables_struct_" . substr(md5(time() . mt_rand(1000, 5000)), 0, 16) . ".txt";
                $mysql_version = $this->C($this->cacheDir)->version();
                $fp = fopen($bkfile, "w");
                foreach ($tables as $t) {
                    fwrite($fp, "DROP TABLE IF EXISTS `$t`;\r\n\r\n");
                    $row = $this->C($this->cacheDir)->findOne("SHOW CREATE TABLE " . $GLOBALS['DB']['DBname'] . "." . $t);

                    //去除AUTO_INCREMENT
                    $row[1] = preg_replace("#AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}#i", "", end($row));

                    //4.1以下版本备份为低版本
                    if ($datatype == 4.0 && $mysql_version > 4.0) {
                        $eng1 = "#ENGINE=MyISAM[ \r\n\t]{1,}DEFAULT[ \r\n\t]{1,}CHARSET=" . $cfg_db_language . "#i";
                        $tableStruct = preg_replace($eng1, "TYPE=MyISAM", $row[1]);
                    } //4.1以下版本备份为高版本
                    else if ($datatype == 4.1 && $mysql_version < 4.1) {
                        $eng1 = "#ENGINE=MyISAM DEFAULT CHARSET={$cfg_db_language}#i";
                        $tableStruct = preg_replace("TYPE=MyISAM", $eng1, $row[1]);
                    } //普通备份
                    else {
                        $tableStruct = $row[1];
                    }
                    fwrite($fp, '' . $tableStruct . ";\r\n\r\n");
                }
                fclose($fp);
                $tmsg .= "备份数据表结构信息完成...<br />";
            }
            $tmsg .= "<font color='red'>正在进行数据备份的初始化工作，请稍后...</font>";
            $doneForm = "<form name='gonext' method='post' action='" . ACT . "/sysmanage/SysData/sys_data_back_done/'>
			   <input type='hidden' name='isstruct' value='$isstruct' />
			   <input type='hidden' name='dopost' value='bak' />
			   <input type='hidden' name='fsize' value='$fsize' />
			   <input type='hidden' name='psize' value='$psize' />
			   <input type='hidden' name='tablearr' value='$tablearr' />
			   <input type='hidden' name='nowtable' value='{$tables[0]}' />
			   <input type='hidden' name='startpos' value='0' />
			   <input type='hidden' name='iszip' value='$iszip' />\r\n</form>\r\n" . $this->dojs . "\r\n";
            $this->put_info($tmsg, $doneForm);
            exit();
        } //执行分页备份
        else {
            $j = 0;
            $fs = $bakStr = '';
            $filedarr = $this->get_table_filed($nowtable);
            //分析表里的字段信息
            $intable = "INSERT INTO `$nowtable` VALUES(";
            foreach ($filedarr as $row1) {
                $fs[$j] = trim($row1["Field"]);
                $j++;
            }
            $fsd = $j - 1;

            $m = 0;
            $sql = "SELECT * FROM `$nowtable` limit $startpos,$psize";
            $list = $this->C($this->cacheDir)->findAll($sql);

            $bakfilename = "$bkdir/{$nowtable}_{$startpos}_" . substr(md5(time() . mt_rand(1000, 5000)), 0, 16) . ".txt";

            foreach ($list as $row2) {
                //				if($m < $startpos){
                //					$m++;
                //					continue;
                //				}

                //检测数据是否达到规定大小
                /*//if(strlen($bakStr) > $fsizeb)
                if(strlen($bakStr) > $fsizeb)
                {
                    $fp = fopen($bakfilename,"w");
                    fwrite($fp,$bakStr);
                    fclose($fp);
                    $tmsg = "<font color='red'>完成到{$m}条记录的备份，继续备份{$nowtable}...</font>";
                    $doneForm = "<form name='gonext' method='post' action='".ACT."/sysmanage/sys/SysData/sys_data_back_done/'>
                    <input type='hidden' name='isstruct' value='$isstruct' />
                    <input type='hidden' name='dopost' value='bak' />
                    <input type='hidden' name='fsize' value='$fsize' />
                    <input type='hidden' name='tablearr' value='$tablearr' />
                    <input type='hidden' name='nowtable' value='$nowtable' />
                    <input type='hidden' name='startpos' value='$m' />
                    <input type='hidden' name='iszip' value='$iszip' />\r\n</form>\r\n".$this->dojs."\r\n";
                    $this->put_info($tmsg,$doneForm);
                    exit();
                }*/

                //正常情况
                $line = $intable;
                for ($j = 0; $j <= $fsd; $j++) {
                    if ($j < $fsd) {
                        $line .= "'" . $this->RpLine(addslashes($row2[$fs[$j]])) . "',";
                    } else {
                        $line .= "'" . $this->RpLine(addslashes($row2[$fs[$j]])) . "');\r\n";
                    }
                }
                $m++;
                $bakStr .= $line;
            }


            //如果数据比卷设置值小
            if ($bakStr != '') {
                $fp = fopen($bakfilename, "w");
                fwrite($fp, $bakStr);
                fclose($fp);
                unset($bakStr);
            }
            $nowpos = $startpos + $m;
            if ($m >= $psize) {
                $tmsg = "<font color='red'>完成到{$nowpos}条记录的备份，继续备份{$nowtable}...</font>";
                $doneForm = "<form name='gonext' method='post' action='" . ACT . "/sysmanage/SysData/sys_data_back_done/'>
					<input type='hidden' name='isstruct' value='$isstruct' />
					<input type='hidden' name='dopost' value='bak' />
					<input type='hidden' name='fsize' value='$fsize' />
					<input type='hidden' name='psize' value='$psize' />
					<input type='hidden' name='tablearr' value='$tablearr' />
					<input type='hidden' name='nowtable' value='$nowtable' />
					<input type='hidden' name='startpos' value='$nowpos' />
					<input type='hidden' name='iszip' value='$iszip' />\r\n</form>\r\n" . $this->dojs . "\r\n";
                $this->put_info($tmsg, $doneForm);
                exit();
            }

            //判断是否循环了所有的数据库表
            for ($i = 0; $i < count($tables); $i++) {
                if ($tables[$i] == $nowtable) {
                    if (isset($tables[$i + 1])) {
                        $nowtable = $tables[$i + 1];
                        $startpos = 0;
                        break;
                    } else {
                        $this->put_info("完成所有数据备份！", "");
                        exit();
                    }
                }
            }
            $tmsg = "<font color='red'>完成到{$m}条记录的备份，继续备份{$nowtable}...</font>";
            $doneForm = "<form name='gonext' method='post' action='" . ACT . "/sysmanage/SysData/sys_data_back_done/'>
			  <input type='hidden' name='isstruct' value='$isstruct' />
			  <input type='hidden' name='fsize' value='$fsize' />
			  <input type='hidden' name='psize' value='$psize' />
			  <input type='hidden' name='tablearr' value='$tablearr' />
			  <input type='hidden' name='nowtable' value='$nowtable' />
			  <input type='hidden' name='startpos' value='$startpos'>\r\n</form>\r\n" . $this->dojs . "\r\n";
            $this->put_info($tmsg, $doneForm);
            exit();
        }
        //分页备份代码结束
    }

    //数据恢复
    public function sys_data_res()
    {
        $bkdir = $this->bkdir;
        $fp = $this->L("File");
        $list = $fp->list_dir_info($bkdir);

        foreach ($list as $onefile) {
            $one = $fp->dir_replace($onefile);
            $info = $fp->list_info($one);
            $filename = $info["filename"];
            if (!preg_match("#txt$#", $filename)) {
                continue;
            }
            if (preg_match("#tables_struct#", $filename)) {
                $structfile = $filename;
            } else if (filesize("$bkdir/$filename") > 0) {
                $filelists[] = $filename;
            }
        }
        //print_r($list);
        $smarty = $this->setSmarty();
        $smarty->assign(array("list" => $filelists, "structfile" => $structfile));
        $smarty->display('sysmanage/sys_data_res.html');
    }

    //数据库恢复执行
    public function sys_data_res_done()
    {
        $bkdir = $this->bkdir;
        $fp = $this->L("File");

        $bakfiles = $this->_REQUEST("bakfiles");
        $structfile = $this->_REQUEST("structfile");
        $delfile = $this->_REQUEST("delfile");
        $startgo = $this->_REQUEST("startgo");

        if ($bakfiles == '') {
            $this->put_info("没指定任何要还原的文件！", "");
            exit();
        }

        if (is_array($bakfiles)) {
            $bakfiles = implode(",", $bakfiles);
        }

        $bakfilesTmp = $bakfiles;
        $bakfiles = explode(',', $bakfiles);
        if (empty($structfile)) {
            $structfile = "";
        }
        if (empty($delfile)) {
            $delfile = 0;
        }
        if (empty($startgo)) {
            $startgo = 0;
        }

        if ($startgo == 0 && $structfile != '') {
            $tbdata = '';
            $fp = fopen("$bkdir/$structfile", 'r');
            while (!feof($fp)) {
                $tbdata .= fgets($fp, 1024);
            }
            fclose($fp);
            $querys = explode(';', $tbdata);
            foreach ($querys as $q) {
                if (strlen($q) >= 10) {
                    $this->C($this->cacheDir)->updt(trim($q) . ';');
                }
            }
            if ($delfile == 1) {
                @unlink("$bkdir/$structfile");
            }
            $tmsg = "<font color='red'>完成数据表信息还原，准备还原数据...</font>";
            $doneForm = "<form name='gonext' method='post' action='" . ACT . "/sysmanage/SysData/sys_data_res_done/'>
			<input type='hidden' name='startgo' value='1' />
			<input type='hidden' name='delfile' value='$delfile' />
			<input type='hidden' name='bakfiles' value='$bakfilesTmp' />
			</form>\r\n" . $this->dojs . "\r\n";
            $this->put_info($tmsg, $doneForm);
            exit();
        } else {
            $nowfile = $bakfiles[0];
            $bakfilesTmp = preg_replace("#" . $nowfile . "[,]{0,1}#", "", $bakfilesTmp);
            $oknum = 0;
            if (filesize("$bkdir/$nowfile") > 0) {
                $fp = fopen("$bkdir/$nowfile", 'r');
                while (!feof($fp)) {
                    $line = trim(fgets($fp, 512 * 1024));
                    if ($line == "") continue;
                    $rs = $this->C($this->cacheDir)->updt($line);
                    if ($rs) $oknum++;
                }
                fclose($fp);
            }
            if ($delfile == 1) {
                @unlink("$bkdir/$nowfile");
            }
            if ($bakfilesTmp == "") {
                $this->put_info("成功还原所有的文件的数据！", "");
                exit();
            }
            $tmsg = "成功还原{$nowfile}的{$oknum}条记录<br/><br/>正在准备还原其它数据...";
            $doneForm = "<form name='gonext' method='post' action='" . ACT . "/sysmanage/SysData/sys_data_res_done/'>
			<input type='hidden' name='startgo' value='1' />
			<input type='hidden' name='delfile' value='$delfile' />
			<input type='hidden' name='bakfiles' value='$bakfilesTmp' />
			</form>\r\n" . $this->dojs . "\r\n";
            $this->put_info($tmsg, $doneForm);
            exit();
        }
    }

    //获取系统表
    public function sys_data_showtables()
    {
        $sql = "SHOW TABLES";
        $list = $this->C($this->cacheDir)->findAll($sql);
        foreach ($list as $key => $row) {
            $va = end($row);
            $cnt = $this->C($this->cacheDir)->findOne("select count(*) as cnt from `$va`");

            $list[$key]['cnt'] = $cnt['cnt'];
            $list[$key]['name'] = $va;
        }
        return $list;
    }

    //获得表的字段
    public function get_table_filed($tabname)
    {
        $sql = "SHOW FULL COLUMNS FROM $tabname";
        $list = $this->C($this->cacheDir)->findAll($sql);
        return $list;
    }

    //提示信息输出
    function put_info($msg1, $msg2)
    {
        $msginfo = "<html>\n<head>
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
			<title>提示信息</title>
			<base target='_self'/>\n</head>\n<body leftmargin='0' topmargin='0'>\n<center>
			<br/>
			<div style='width:400px;padding-top:4px;height:24;font-size:10pt;border-left:1px solid #cccccc;border-top:1px solid #cccccc;border-right:1px solid #cccccc;background-color:#DBEEBD;'>提示信息！</div>
			<div style='width:400px;height:100px;font-size:10pt;border:1px solid #cccccc;background-color:#F4FAEB'>
			<span style='line-height:160%'><br/>{$msg1}</span>
			<br/><br/></div>\r\n{$msg2}";
        echo $msginfo . "</center>\n</body>\n</html>";
    }

    function RpLine($str)
    {
        $str = str_replace("\r", "\\r", $str);
        $str = str_replace("\n", "\\n", $str);
        return $str;
    }
} //end class
?>