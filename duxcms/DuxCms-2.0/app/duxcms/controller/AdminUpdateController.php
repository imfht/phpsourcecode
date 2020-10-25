<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 更新管理
 */

class AdminUpdateController extends AdminController {

    public $domain = 'http://www.duxcms.com/index.php?r=/';
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '更新管理',
                'description' => '升级当前网站到最新版',
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('更新管理'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->adminDisplay();
    }

    /**
     * 获取最新版本
     */
    public function getVer(){
        $verTime = config('DUX_TIME');
        if(empty($verTime)){
            $this->error('没有发现版本号！');
        }
        $url = $this->domain . 'service/Update/index';
        $data = array();
        $data['ver_time'] = $verTime;
        $info = \framework\ext\Http::doGet($url,$data,10);
        $info = json_decode($info,true);
        if(empty($info)){
            $this->error('无法获取版本信息，请稍后再试！');
        }
        if($info['status']){
            $this->success($info['data']);
        }else{
            $this->error($info['data']);
        }
        
    }

    /**
     * 下载更新
     */
    public function dowload(){
        $url = request('post.url');
        if(empty($url)){
            $this->error('没有发现更新地址，请稍后重试！');
        }
        $updateDir = DATA_PATH.'update/';
        if (!file_exists($updateDir)){
            if(!mkdir($updateDir)){
                $this->error('抱歉，无法为您创建更新文件夹，请手动创建目录【'.$updateDir.'】');
            }
        }
        $fileName = explode('/', $url);
        $fileName = end($fileName);
        //开始下载文件
        $fileName = $updateDir.$fileName;
        $file = \framework\ext\Http::doGet($url,30);
        if(!file_put_contents($fileName, $file)){
            $this->error('无法保存更新文件请检查目录【'.$updateDir.'】是否有写入权限！');
        }
        $this->success('文件下载成功，正在执行解压操作！');
    }

    /**
     * 解压文件
     */
    public function unzip(){
        $url = request('post.url');
        $fileName = explode('/', $url);
        $fileName = end($fileName);
        $updateDir = DATA_PATH.'update/';
        $file = $updateDir.$fileName;
        if(!is_file($file)){
             $this->error('没有发现更新文件，请稍后重试！');
        }
        $dir = $updateDir.'tmp_'.config('DUX_TIME');
        $zip = new \framework\ext\Zip();
        if($zip->decompress($file,$dir)){
            $this->success('文件解压成功，等待更新操作！');
        }else{
            $this->error('解压文件失败请检查目录【'.$dir.'】是否有写入权限！');
        }
        
    }

    /**
     * 更新文件
     */
    public function upfile(){
        $updateDir = DATA_PATH.'update/';
        $dir = $updateDir.'tmp_'.config('DUX_TIME');
        if(!copy_dir($dir,ROOT_PATH)){
            $this->error('无法复制更新文件，请检查网站是否有写入权限！');
        }
        //更新SQL文件
        $file = ROOT_PATH.'update/'.config('DUX_TIME').'.sql';
        if(is_file($file)){
            $sqlData = \framework\ext\Install::mysql($file, 'dux_', $data['DB_PREFIX']);
            //开始导入数据库
            foreach ($sqlData as $sql) {
                if(false === target('Update')->execute($sql)){
                    $this->error($sql . '...失败！');
                }
            }
        }
        //清理更新文件
        del_dir(ROOT_PATH.'update');
        del_dir(ROOT_PATH.$dir);
        $this->success('更新成功，稍后为您刷新！');       
        
    }

    /**
     * 查询授权
     */
    public function Authorize(){
        $url = 'http://www.duxcms.com/index.php?r=service/Authorize/index';
        $info = \framework\ext\Http::doGet($url,30);
        echo $info;
    }

}

