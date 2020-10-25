<?php

defined('IN_CART') or die;

/**
 *  
 * 数据管理
 *
 *
 * */
class Data extends Base
{

    /**
     *  
     * 备份数据
     *
     *
     * */
    public function backup()
    {
        $dir = DATADIR . "/dbbak";
        $files = glob($dir . "/*.bak");
        !is_array($files) && $files = array();

        $this->data["files"] = array();
        foreach ($files as $file) {
            $lines = file($file);
            foreach ($lines as $line) {
                $arr = explode("=", $line);
                $this->data["files"][basename($file, ".bak")][$arr[0]] = ($arr[0] == 'filetime') ? intval($arr[1]) : $arr[1];
            }
        }
        krsort($this->data["files"]);
        $this->output("data_backup");
    }

    /**
     *  
     * 执行备份数据
     *
     *
     * */
    public function backupok()
    {
        include_once STAGEPATH . "/dumpdb.class.php";
        if (isset($_GET['tableorder'])) { //非第一次备份
            $tableorder = intval($_GET["tableorder"]);
            $tablepos = intval($_GET["tablepos"]);
            $filename = trim($_GET["filename"]);
            $vol = intval($_GET["vol"]);
            $dumpdb = new Dumpdb($vol, $filename, $tablepos, $tableorder);
            $dumpdb->dump_tables();
        } else {       //第一次备份
            //获取所有表
            $dumpdb = new Dumpdb();
            $dumpdb->dump_tables();
        }
        if ($dumpdb->getFinished()) {  //已经结束
            $this->adminlog('al_backup');
            echo __("backup_finished") . "<script>setTimeout(function(){ window.location.reload(); },1000)</script>";
            //生成文件
            $filetime = time();
            $vol = $dumpdb->getNextVol() - 1;
            $filename = $dumpdb->getFileName();
            $crlf = $dumpdb->getDeLimiter();
            $content = "filename={$filename}" . $crlf
                    . "vol={$vol}" . $crlf
                    . "filetime={$filetime}" . $crlf;
            ;
            $file = $dumpdb->getSqlFileDir() . "/{$filename}.bak";
            cwritefile($file, $content);
        } else {
            $url = url("admin", "data", "backupok", "tableorder={$dumpdb->getTableOrder()}&tablepos={$dumpdb->getTablePos()}&filename={$dumpdb->getFileName()}&vol={$dumpdb->getnextVol()}", false);
            echo __("backup_vol", $dumpdb->getNextVol()) . "<script type='text/javascript'>$.oper.runjs('{$url}')</script>";
        }
    }

    /**
     *
     * 删除备份文件
     * 
     */
    public function backupsave()
    {
        $opertype = trim($_POST["opertype"]);
        switch ($opertype) {
            case 'editfield': //修改字段
                $field = trim($_POST["field"]);
                if ($field == "delete") { //直接删除sql记录，不记录回收站
                    //删除bak文件
                    $bakdir = DATADIR . "/dbbak";
                    $filestr = trim($_POST["idstr"]);
                    $filenames = explode(",", $filestr);
                    foreach ($filenames as $filename) { //删除
                        $file = $bakdir . "/" . $filename . ".bak";
                        if (!is_file($file))
                            continue;

                        if (preg_match("/vol=(\d+)/", file_get_contents($file), $matches)) {//如果匹配正确
                            $vol = intval($matches[1]);
                            for ($i = 1; $i <= $vol; $i++) { //删除分卷
                                $sqlfile = $bakdir . "/" . $filename . "_{$i}.sql";
                                if (!is_file($sqlfile))
                                    continue;
                                @unlink($sqlfile);
                            }
                            $ret = @unlink($file);
                        }
                    }
                }
                exit($ret ? "success" : "failure");
                break;
        }
    }

    /**
     *  
     * 恢复备份数据
     *
     *
     * */
    public function import()
    {
        include_once STAGEPATH . "/dumpdb.class.php";
        $filename = trim($_GET["filename"]);
        $file = DATADIR . "/dbbak/" . $filename . ".bak";
        if (!is_file($file))
            exit(__("file_not_exists"));

        if (preg_match("/vol=(\d+)/", file_get_contents($file), $matches)) {//如果匹配
            $totalvol = intval($matches[1]);
            $vol = isset($_GET["vol"]) ? intval($_GET['vol']) : 1;
            $dumpdb = new Dumpdb($vol, $filename);
            $dumpdb->import();
            if ($vol < $totalvol) {
                $vol ++;
                $url = url("admin", "data", "import", "filename={$filename}&vol=" . $vol, false);
                echo __("import_vol", $vol) . "<script>$.oper.runjs('{$url}')</script>";
            } else {
                echo __("import_finished") . "<script>setTimeout(function(){ window.location.reload() },1000)</script>";
            }
        }
    }

    /**
     *  
     * 优化数据库
     *
     *
     * */
    public function optimize()
    {
        if (ispostreq()) {
            $prefix = DB::getDB()->getTablePrefix();
            $tables = DB::getDB()->selectsql("SHOW TABLE STATUS WHERE `Data_Free`>0 LIKE '{$prefix}%';", "col");

            foreach ($tables as $table) {
                $row = DB::getDB()->selectsql("OPTIMIZE TABLE $table", "row");
                if ($row['Msg_type'] == 'error' && cstrpos($row['Msg_text'])) {
                    DB::getDB()->query("REPAIR TABLE $table");
                }
            }
            $num = intval($_POST['num']);
            $this->adminlog("al_optimize");
            $this->setHint(__("optimize_finish", $num), "data_optimize");
        } else {
            $prefix = DB::getDB()->getTablePrefix();
            $tables = DB::getDB()->selectsql("SHOW TABLE STATUS WHERE `Data_Free`>0 LIKE '{$prefix}%';");
            $this->data["num"] = 0;
            foreach ($tables as $table) {
                $this->data["num"] += $table["Data_free"];
            }
            $this->output("optimize_index");
        }
    }

    /**
     *  
     * 清除体验数据
     *
     *
     * */
    public function cleartest()
    {
        if (ispostreq()) {
            $uname = trim($_POST["uname"]);
            $pass = trim($_POST["pass"]);
            $admin = DB::getDB()->selectrow("admin", "pass,salt,issuper", "uname='" . $uname . "'");
            if (!empty($admin) && $admin["issuper"] && checkpass($pass, $admin['salt'], $admin['pass'])) {
                //体验数据删除
                $this->adminlog("al_cleartest");
                $this->cleardb();
                $this->setHint(__("cleartest_success"), "data_cleartest");
            } else {
                $this->setHint(__("cleartest_not_priv"), "data_cleartest");
            }
        } else {
            $this->output("cleartest_index");
        }
    }

    /**
     *  
     * 清楚所有的表
     *
     *
     * */
    private function cleardb()
    {
        //所有表
        $prefix = DB::getDB()->getTablePrefix();
        $tables = DB::getDB()->selectsql("SHOW TABLE STATUS LIKE '{$prefix}%';", "col");


        //需要排除的表
        $exclude = array("{$prefix}adfront",
            "{$prefix}adpic",
            "{$prefix}district",
            "{$prefix}session",
            "{$prefix}tlogin",
            "{$prefix}express_company",
            "{$prefix}express_printopt",
            "{$prefix}express_pic",
            "{$prefix}message_set",
            "{$prefix}payment",
            "{$prefix}pic",
            "{$prefix}config");
        //需要删除部分数据的表
        $deltables = array(
            "{$prefix}admin" => "adminid!=1",
        );

        foreach ($tables as $table) {
            if (in_array($table, $exclude))
                continue; //如果是排除的表，

            if (isset($deltables[$table])) { //如果是有些记录需要删除
                DB::getDB()->query("DELETE FROM $table WHERE $deltables[$table]");
            } else {//清空其他的表
                DB::getDB()->query("TRUNCATE TABLE $table");
            }
        }
        return true;
    }

}
