<?php
/*
 *
 * sysmanage.SysLog  系统日志管理   
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


class Upgrade extends Action
{
    private $cacheDir = '';//缓存目录
    private $version = '20200701';//当前版本

    public function __construct()
    {
        _instance('Action/sysmanage/Auth');
        $this->file = _instance('Extend/File');
        $this->zip = _instance('Extend/Zip');
    }

    /**
     * 升级地址
     * @return string
     * Author: lingqifei created by at 2020/5/16 0016
     */
    public function serverip()
    {
        $server = "http://www.07fly.top/upgrade/v2";
        return $server;
    }

    /**
     * 升级地址
     * @return string
     * Author: lingqifei created by at 2020/5/16 0016
     */
    public function server_upgrade()
    {
        $server = "http://07fly.top/index/AuthVersion";
        return $server;
    }

    /**
     * 授权地址
     * @return string
     * Author: lingqifei created by at 2020/5/16 0016
     */
    public function serverip_auth()
    {
        $server = "http://07fly.top";
        return $server;
    }

    /**
     * 返回当前版本号
     * @return string
     * Author: lingqifei created by at 2020/5/16 0016
     */
    public function version()
    {
        return $this->version;
        $versionfile = ROOT . S . 'version';
        $txt = $this->file->read_file($versionfile);
        if ($txt) {
            return $txt;
        } else {
            $this->file->write_file($versionfile, $this->version);
            return '20200101';
        }
    }

    /**
     * 返回当前授权码
     * @return string
     * Author: lingqifei created by at 2020/5/16 0016
     */
    public function syskey()
    {
        $syskey = ROOT . S . 'syskey';
        $txt = $this->file->read_file($syskey);
        if ($txt) {
            return $txt;
        } else {
            return '';
        }
    }

    //文件备份目录
    public function upgrade_backup_dir()
    {
        $path = "upload/upgrade_backup/";
        $this->file->create_dir($path);
        return $path;
    }

    //文件下载目录
    public function upgrade_down_dir()
    {
        $path = "upload/upgrade_down/";
        $this->file->create_dir($path);
        return $path;
    }

    /**检查是否下载了升级包
     * @param $version
     * @return mixed
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function check_down_verion($version)
    {
        $downpath = $this->upgrade_down_dir();
        $downfile = $downpath . $version . '.zip';
        $result = $this->file->exists_file($downfile);
        if ($result) {
            return $downfile;
        } else {
            return false;
        }
    }


    /**
     * 判断文件是否存在，支持本地及远程文件
     * @param String $file 文件路径
     * @return Boolean
     */
    function check_file_exists($file)
    {
        // 远程文件
        if (strtolower(substr($file, 0, 4)) == 'http') {
            $header = get_headers($file, true);
            return isset($header[0]) && (strpos($header[0], '200') || strpos($header[0], '304'));
            // 本地文件
        } else {
            return file_exists($file);
        }
    }

    /** 升级备份原程序
     * @return bool|string
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function upgrade_backup()
    {
        $source_path = $this->file->dir_replace(APP_ROOT);
        $backup_path = $this->upgrade_backup_dir() . date("YmdHis", time()) . "";
        $dirarr = array('Action', 'Extend', 'View');
        foreach ($dirarr as $dir) {
            $backup_dir = $backup_path . "/{$dir}/";
            $rtn[] = $this->file->create_dir($backup_dir);
            $rtn[] = $this->file->handle_dir($source_path . "/{$dir}", $backup_dir, 'copy', true);
        }
        if (in_array("0", $rtn, TRUE)) {
            return false;
        } else {
            $backup_file = $backup_path . '.zip';
            $rtn = $this->zip->zip($backup_file, $backup_path);
            if ($rtn) {
                $this->file->remove_dir($backup_dir . '/', true);
            }
            return $backup_file;
        }
    }

    /**下载升级文件
     * @param null $version
     * @return bool
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function upgrade_down($version = null)
    {
        $version = $this->_REQUEST("ver");
        $downpath = $this->upgrade_down_dir();
        $downpath = $this->file->dir_replace($downpath);
        $isauth=$this->is_auth();
        if(!$isauth){
            $rtn = array('error' => 1, 'message' => '未授权用户不能下载升级文件在');
            echo json_encode($rtn);
            exit;
        }
        $server = $this->server_upgrade();    //获取网络信息
        $url = "$server/get_version_info?ver=$version&sys=v2";
        $info = $this->file->read_file($url);//得到服务器返回包的地址
        $info = json_decode($info, true);
        $pakurl = $info['filename'];
        $result = $this->check_file_exists($pakurl);
        if ($result) {
            $finfo = $this->file->get_file_type("$pakurl");
            $result = $this->file->down_remote_file($pakurl, $downpath, $finfo['basename'], $type = 1);
            echo json_encode($result);
        } else {
            $rtn = array('error' => 1, 'message' => '下载升级文件不存在');
            echo json_encode($rtn);
        }
    }

    /**下载升级文件
     * @param null $version
     * @return bool
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function upgrade_remark($version = null)
    {
        $version = $this->_REQUEST("ver");
        $server = $this->server_upgrade();    //获取网络信息
        $url = "$server/get_version_info?ver=$version&sys=v2";
        $info = $this->file->read_file($url);//得到服务器返回包的地址
        $info = json_decode($info, true);
        $smarty = $this->setSmarty();
        $smarty->assign(array("info" => $info, 'version' => $version));
        $smarty->display('sysmanage/sys_upgrade_remark.html');
    }

    /**执行升级文件
     * @param null $version
     * @return bool
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function upgrade_exec($version = null)
    {
        $version = $this->_REQUEST("ver");
        $downfile = $this->check_down_verion($version);
        if ($downfile) {
            $downfile = $this->file->dir_replace(ROOT . S . $downfile);
            return $this->zip->unzip($downfile, ROOT);
        }
    }


    /**验证平台信息
     * @param null $version
     * @return bool
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function upgrade_signal_check()
    {
        $server = $this->serverip_auth();    //获取网络信息
        $url = "$server/index/AuthDomain/client_check.html?u=07fly.top&k=07fly.top";
        $result=$this->L('Common')->open_curl($url,'');
        if ($result) {
            $rtn = array('statusCode' => 200, 'message' => '<span class="text-success">通信正常</span>');
        } else {
            $rtn = array('statusCode' => 300, 'message' => '<span class="text-danger">通信异常</span>');
        }
        return $rtn;
    }


    /**验证授权信息
     * @param null $version
     * @return bool
     * Author: lingqifei created by at 2020/4/1 0001
     */
    public function upgrade_auth_check()
    {
        $domain = $_SERVER['HTTP_HOST'];
        $syskey = $this->syskey();    //授权码
        $server = $this->serverip_auth();    //获取网络信息
        $url = "$server/index/AuthDomain/client_check.html?u=$domain&k=$syskey";
        $result=$this->L('Common')->open_curl($url,'');
        //$result = file_get_contents($url);
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * 授权注册
     * Author: lingqifei created by at 2020/6/6 0006
     */
    public function upgrade_auth_reg()
    {
        $syskey = $this->_REQUEST("syskey");
        $filepath = ROOT . S . 'syskey';
        if (empty($syskey)) {
            $rtn = array('statusCode' => 300, 'message' => '授权码不能为空');
        } else {
            file_put_contents($filepath, $syskey);
            $res = $this->upgrade_auth_check();
            if ($res['code'] == '1') {
                $rtn = array('statusCode' => 200, 'message' => '授权码注册成功');
            } else {
                $rtn = array('statusCode' => 300, 'message' => $res['message']);
            }
        }
        echo json_encode($rtn);
    }

    /**
     *  判断是否授权
     * @return bool     false|true
     *
     * Author: lingqifei created by at 2020/6/6 0006
     */
    public function is_auth()
    {
        $res = $this->upgrade_auth_check();
        if ($res['code'] == '1') {
            return true;
        } else {
            return false;
        }
    }


}//end class
?>