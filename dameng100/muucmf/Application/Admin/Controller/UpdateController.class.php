<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-14
 * Time: AM10:59
 */

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use OT\Database;
use OT\File;

/**
 * 升级包制作规则
 * 压缩方式：zip
 * 升级文件命名规则：将升级文件压缩至update.zip
 * 数据库文件路径：./sql/update.sql
 */
class UpdateController extends AdminController
{
    protected $cloud;
    public function _initialize()
    {
        $this->cloud = C('__CLOUD__').'/index.php?s=muucmf/sysupdate';
        parent::_initialize();
    }

    /*
    *获取本地系统版本
    */
    private function localVersion()
    {
        return File::read_file('./Data/version.ini');
    }
    /*
    *获取云端最新系统版本
    */
    private function cloudVersion()
    {
        return File::read_file($this->cloud.'/newVersion');
    }
    public function index()
    {
        $localVersion = $this->localVersion(); //读取本地版本号
        $cloudVersion = $this->cloudVersion();//读取云端最新版本号

        if(IS_POST){
            $result['status']=1;
            $result['info']='Success';
            $result['data']['localVersion']=$localVersion;
            $result['data']['cloudVersion']=$cloudVersion;

            $this->AjaxReturn($result,'json');

        }else{
            $result = $this->checkVersion($localVersion);//读取云端可更新版本数据
        }
        $this->meta_title = '系统在线更新';
        $this->assign('localVersion',$localVersion);
        $this->assign('cloudVersion',$cloudVersion);
        $this->assign('result',$result);
        $this->display();
        
    }

    /*开始在线更新数据*/
    public function startUpdate()
    {   
        if(IS_POST){
            $this->meta_title = '系统在线更新日志';
            $this->display();
            $this->update();
        }else{
            $this->error('错误的操作！');
        }
    }

    /**
     * 在线更新
     */
    private function update($version){
        //$this->showMsg(C('DB_PREFIX'));exit;
        
        $localVersion = $this->localVersion(); //获取本地版本号
            $this->showMsg('MuuCmf系统当前版本:'.$localVersion);
            $this->showMsg('OneThink系统原始版本:'.ONETHINK_VERSION);
            $this->showMsg('更新开始时间:'.date('Y-m-d H:i:s'));
            $this->showMsg('==========================================================================');
        $result = $this->checkVersion($localVersion); //获取远端数据
        $newVersion = $result['data']['version'];//获取本次更新的版本号
        if($localVersion==$newVersion){
            $this->showMsg('这个版本已经更新过了,更新程序终止','error');
            exit;
        }
        
        //PclZip类库不支持命名空间
        import('OT/PclZip');
        /* 建立更新文件夹 */
        $this->showMsg('开始创建更新文件夹...','title');
        $folder = $this->getUpdateFolder($newVersion);
        $update = C('UPDATE_PATH');
        $folder_path = $update.$folder;
        if(File::mk_dir($folder_path)){
            $this->showMsg('更新文件夹创建成功');
        }else{
            $this->showMsg('更新文件夹创建失败', 'error');
            exit;
        }

        //备份重要文件
            $this->showMsg('开始备份重要程序文件...','title');
            G('start1');
            $backupallPath = $folder_path.'/backupall.zip';
            $zip = new \PclZip($backupallPath);
            $zip->create('Application,ThinkPHP,admin.php,index.php');
            $this->showMsg('成功完成重要程序备份,备份文件路径:<a href=\''.__ROOT__.$backupallPath.'\'>'.$backupallPath.'</a>, 耗时:'.G('start1','stop1').'s','success');

        sleep(1);
        /* 获取更新包 */
        //获取更新包地址
        $updatedUrl = $this->cloud.'/downSysUpdate/id/'.$result['data']['id'];
        if(empty($updatedUrl)){
            $this->showMsg('未获取到更新包的下载地址', 'error');
            exit;
        }
        //下载并保存
        $this->showMsg('开始获取远程更新包...','title');
        sleep(1);
        $zipPath = $folder_path.'/update.zip';
        $downZip = $this->getRemoteDate($updatedUrl);
        if(empty($downZip)){
            $this->showMsg('下载更新包出错，请重试！', 'error');
            exit;
        }
        File::write_file($zipPath, $downZip);
        $this->showMsg('获取远程更新包成功,更新包路径：<a href=\''.__ROOT__.ltrim($zipPath,'.').'\'>'.$zipPath.'</a>', 'success');
        sleep(1);

        /* 解压缩更新包 */ //TODO: 检查权限
        $this->showMsg('开始更新包解压缩','title');
        sleep(1);
        $zip = new \PclZip($zipPath);
        $res = $zip->extract($folder_path.'/Data');
        if($res === 0){
            $this->showMsg('解压缩失败：'.$zip->errorInfo(true).'------更新终止', 'error');
            exit;
        }
        $this->showMsg('更新包解压缩成功', 'success');
        sleep(1);

        $this->showMsg('开始复制文件','title');
        $copyFile = File::copy_dir($folder_path.'/Data','./');//开始复制到更新目录
        if($copyFile){
            
            $this->showMsg('文件复制成功', 'success');
        }
        //exit;//临时终止

        /* 更新数据库 */
        $updatesql = $folder_path.'/Data/sql/update.sql';
        if(is_file($updatesql))
        {
            $this->showMsg('开始更新数据库','title');
            if(file_exists($updatesql))
            {
                $this->updateTable($updatesql); //执行数据库更新
            }
            unlink($updatesql);
            File::del_dir('./sql');//删除多余的sql文件夹
            $this->showMsg('更新数据库完毕', 'success');
        }

        /* 系统版本号更新 */
        $this->showMsg('开始更新系统版本号','title');
        $res = File::write_file(__ROOT__.'./Data/version.ini', $newVersion);
        if($res === false){
            $this->showMsg('更新系统版本号失败', 'error');
            exit;
        }else{
            $this->showMsg('系统版本号已更新至 '.$newVersion);
            $this->showMsg('更新系统版本号成功', 'success');
        }
        sleep(1);

        $this->showMsg('==========================================================================');
        $this->showMsg('在线更新全部完成，如有备份，请及时将备份文件移动至非web目录下！', 'success');
    }

    /*
    *检测云端新版本信息
    */
    private function checkVersion($localVersion='')
    {   
        $result = file_get_contents($this->cloud.'/index/enable_version/'.$localVersion);//读取云端可更新版本数据
        $result = json_decode($result,true);//转换为数组格式
        return $result;
    }

    /**
     * 获取远程数据
     * @author huajie <banhuajie@163.com>
     */
    private function getRemoteUrl($url = '', $method = '', $param = ''){
        $opts = array(
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
        );
        if($method === 'post'){
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $param;
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        return $data;
    }
    /*获取远程数据*/
    private function getRemoteDate($url = '')
    {
        $file = fopen ($url, "rb");
        if ($file) {
            //获取文件大小
            $filesize = -1;
            $headers = get_headers($url, 1);
            if ((!array_key_exists("Content-Length", $headers))) $filesize=0;
            $filesize = $headers["Content-Length"];
            
            //不是所有的文件都会先返回大小的，有些动态页面不先返回总大小，这样就无法计算进度了
            if ($filesize != -1) {
                $this->showMsg('更新包大小'.$filesize.'byte');//在前台显示文件大小
            }
                $this->showMsg('准备下载更新包','downloadBox');
            $downlen=0;
                while(!feof($file)) {
                    $data=fread($file, 1024 * 8 );//默认获取8K
                    $downlen+=strlen($data);//累计已经下载的字节数
                    echo "<script>setDownloaded($downlen,$filesize);</script>";//在前台显示已经下载文件大小
                    $result .= $data;
                    ob_flush();
                    flush();
                }
            if ($file) {
                fclose($file);
            }
        }
        return $result;
    }
    /*
    *更新数据库
    */
    private function updateTable($updatesql,$prefix = 'muucmf_')
    {
        $Model = M();
        $sql = File::read_file($updatesql);
        $sql = str_replace("\r\n", "\n", $sql);
        $sql = str_replace("\r", "\n", $sql);
        $sql = explode(";\n", trim($sql));
        //替换表前缀
        $orginal = C('DB_PREFIX');
        $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
        foreach($sql as $value)
        {
            $value = trim($value);
            if (empty($value)) continue;
            if (substr($value, 0, 3) == 'SET') continue;
            if (substr($value, 0, 12) == 'CREATE TABLE') {
                $name = preg_replace("/^CREATE TABLE IF NOT EXISTS `(\w+)` .*/s", "\\1", $value);
                $msg = '创建数据表'.$name;
            }
            if (substr($value, 0, 10) == 'DROP TABLE') {
                $name = preg_replace("/^DROP TABLE IF EXISTS `(\w+)` .*/s", "\\1", $value);
                $msg = '删除数据表'.$name;
            }
            if (substr($value, 0, 11) == 'ALTER TABLE') {
                $name = preg_replace("/^ALTER TABLE IF EXISTS `(\w+)` .*/s", "\\1", $value);
                $msg = '更新数据表'.$name;
            }
            if (substr($value, 0, 11) == 'INSERT INTO') {
                $name = preg_replace("/^INSERT INTO `(\w+)` .*/s", "\\1", $value);
                $msg = '数据表'.$name.'写入数据';
            }
            $Model->query(trim($value));
            if($Model){
                $this->showMsg($msg .'...成功');
            }else{
                $this->showMsg($msg .'...失败','error');
            }
        }
        unset($value);
    }
    /**
     * 实时显示提示信息
     * @param  string $msg 提示信息
     * @param  string $class 输出样式（success:成功，error:失败）
     * @author huajie <banhuajie@163.com>
     */
    private function showMsg($msg, $class = ''){
        echo "<script type=\"text/javascript\">showmsg(\"{$msg}\",\"{$class}\")</script>";
        flush();
        ob_flush();
    }
    /**
     * 生成更新文件夹名
     * @author huajie <banhuajie@163.com>
     */
    private function getUpdateFolder($newVersion){
        return 'update_'.$newVersion;
    }

}