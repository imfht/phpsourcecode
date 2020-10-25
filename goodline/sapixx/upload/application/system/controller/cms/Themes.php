<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 数据字典管理
 */
namespace app\system\controller\cms;
use  util\Util;
use  filter\Filter;
use  think\Validate;
use  Exception;
class Themes extends Common{

    /**
     * 首页
     */
    public function index(){
        $folder = Filter::filter_path_strict($this->request->param('folder'));
        $path[] = ['name'=>'根目录','url' => url('cms.themes/index')];
        if($this->isFolder($folder)){
            $folder_dir = Util::array_remove_empty(explode('/',$folder));
            $folder_name = '';
            foreach ($folder_dir as $key => $value) {
                $folder_name = $key ? $folder_name.'/'.$value : $value;
                $path[] = ['name'=>$value,'url' => url('cms.themes/index',['folder'=>$folder_name]),];
            }
        }
        $view['folder_path'] = $folder ? $folder.'/':'';
        $view['files']       = $this->folder($folder);  
        $view['folder']      = $folder;
        $view['pathMaps']    = $path;
        return view()->assign($view);
    }

    /**
     * 创建目录
     * @return void
     */
    public function createFolder(){
        $folder  = Filter::filter_path_strict($this->request->param('folder'));
        if($this->request->isPost()){
            if(!$this->isFolder($folder)){
                return enjson(0,'目录不存在');
            }
            $foldername = $this->request->param('foldername');
            if(!Validate::make()->rule(['foldername' => 'alpha|require'])->check(['foldername' => $foldername])){
                return enjson(0,'目录名称只能是字母');
            }
            $folderPath = $this->folderPath($folder).strtolower($foldername);
            if(Util::mkdir($folderPath)){
                return enjson(200,'目录创建成功');
            }
            return enjson(0,'目录创建失败');
        }else{
            if(!$this->isFolder($folder)){
                $this->error('目录不存在');
            }
            $view['folderpath']   = $folder.'/';
            $this->view->engine->layout('admin/main');
            return view()->assign($view);
        }
    }
 
    /**
     * 修改目录
     * @return void
     */
    public function editFolder(){
        $folder  = Filter::filter_path_strict($this->request->param('folder'));
        if($this->request->isPost()){
            if(!$this->isFolder($folder)){
                return enjson(0,'目录不存在');
            }
            $folderpath  = Filter::filter_path_strict($this->request->param('folderpath'));
            if(!$this->isFolder($folderpath)){
                return enjson(0,'目录不存在');
            }
            $foldername = $this->request->param('foldername');
            if(!Validate::make()->rule(['foldername' => 'alpha|require'])->check(['foldername' => $foldername])){
                return enjson(0,'目录名称只能是字母');
            }
            $folderPath = $this->folderPath($folderpath).strtolower($foldername);
            if(rename($this->folderPath($folder),$folderPath)){
                return enjson(200,'目录重命名成功');
            }
             return enjson(0,'目录重命名失败');
        }else{
            if(!$this->isFolder($folder)){
                $this->error('目录不存在');
            }
            $folder_dir = Util::array_remove_empty(explode('/',$folder));
            $foldername = $folder_dir[count($folder_dir)-1];
            $view['foldername'] = $foldername;
            $view['folderpath'] = substr_replace($folder,'',-strlen($foldername));
            $view['oldfolde']   = $folder;
            $this->view->engine->layout('admin/main');
            return view()->assign($view);
        }
    } 

    /**
     * 删除空目录
     * @return void
     */
    public function deleteFolder(){
        $folder  = Filter::filter_path_strict($this->request->param('folder'));
        if(!$this->isFolder($folder) || empty($folder)){
            return enjson(0,'目录不存在');
        }
        try {
            $folderPath = $this->folderPath($folder);
            if(rmdir($folderPath)){
                return enjson(200);
            }
            return enjson();
        }catch (\Exception $e) {
            return enjson(0,'目录非空或无操作775权限');
        }
    }

    /**
     * 编辑模板
     * @return void
     */
    public function createFile(){
        $folder = Filter::filter_path_strict($this->request->param('folder'));
        if(!$this->isFolder($folder)){
            $this->error('文件路径未找到');
        }
        if($this->request->isPost()){
            $filename = $this->request->param('filename');
            if(!Validate::make()->rule(['filename' => 'alpha|require'])->check(['filename' => $filename])){
                $this->error('文件名必须填写,且仅支持字母');
            }
            $folderPath = $this->folderPath($folder).strtolower($filename).'.html';
            if(is_file($folderPath)){
                $this->error('文件已经存在');
            }
            $filecontent = Filter::filter_phptag($this->request->param('filecontent','','htmlspecialchars_decode'));
            if(Util::mkfile($folderPath,$filecontent)){
                $this->error('文件创建成功',url('cms.themes/index',['folder' => $folder]));
            }
            $this->error('文件创建失败');
        }else{
            $path[] = ['name'=>'根目录','url' => url('cms.themes/index')];
            $folder_dir = Util::array_remove_empty(explode('/',$folder));
            $folder_name = '';
            foreach ($folder_dir as $key => $value) {
                $folder_name = $key ? $folder_name.'/'.$value : $value;
                $path[] = ['name'=>$value,'url' => url('cms.themes/index',['folder'=>$folder_name]),];
            }
            $view['filepath']     = $folder.'/';
            $view['pathMaps']     = $path;
            return view()->assign($view);
        }
    }
    
    /**
     * 编辑模板
     * @return void
     */
    public function editFile(){
        $file   = Filter::filter_path_strict($this->request->param('file'));
        $folder = substr($file,0,strripos($file,"/"));
        if(!$this->isFolder($folder)){
            $this->error('文件路径未找到');
        }
        if($this->request->isPost()){
            $filecontent = Filter::filter_phptag($this->request->param('filecontent','','htmlspecialchars_decode'));
            $result  = $this->tplEdit($file,$filecontent);
            if($result){
                return redirect('cms.themes/index',['folder' => $folder]);
            }
            $this->error('文件编辑失败');
        }else{
            $path[] = ['name'=>'根目录','url' => url('cms.themes/index')];
            $folder_dir = Util::array_remove_empty(explode('/',$folder));
            $folder_name = '';
            foreach ($folder_dir as $key => $value) {
                $folder_name = $key ? $folder_name.'/'.$value : $value;
                $path[] = ['name'=>$value,'url' => url('cms.themes/index',['folder'=>$folder_name]),];
            }
            $view['file']         = $file;
            $view['fileContent']  = $this->getFileContent($file);
            $view['filesname']    = basename($file);
            $view['filepath']     = $folder.'/';
            $view['pathMaps']     = $path;
            return view()->assign($view);
        }
    }

    /**
     * 删除文件
     * @return void
     */
    public function deleteFile(){
        $file  = Filter::filter_path_strict($this->request->param('file'));
        $folder = substr($file,0,strripos($file,"/"));
        if(!$this->isFolder($folder) || empty($file)){
            $this->error('文件路径未找到');
        }
        try {
            $folder_dir = Util::array_remove_empty(explode('/',$file));
            $filepath   = $this->folderPath($folder).$folder_dir[count($folder_dir)-1];
            if(file_exists($filepath)){
                if(unlink($filepath)){
                    return enjson(200);
                }
            }
            return enjson();
        }catch (\Exception $e) {
            return enjson(0,'文件删除失败');
        }
    }
    
    /**
     * 查找包含目录
     * @param string $path
     * @return void
     */
    protected function isFolder($path = null){
        $path = realpath(PATH_THEMES.$path);
        return is_dir($path) ? true : false;
    }
        
    /**
     * 查找包含目录
     * @param string $path
     * @return void
     */
    protected function folderPath($path = null){
        $path = realpath(PATH_THEMES.$path);
        return is_dir($path) ? $path.DS : PATH_THEMES;
    }

    /**
     * 查找包含目录
     * @param string $path
     * @return void
     */
    protected function folder($path = null){
        $dirpath  = $this->folderPath($path);
        $filearray = []; 
        $files = scandir($dirpath);
        foreach ($files as $value) {
            if(is_dir($dirpath.$value)){
                if ($value != "." && $value != ".."){
                    $filearray['folder'][] = $value; 
                }
            }else{
                $filearray['file'] [] = $value; 
            }
        }
        if(empty($filearray['folder'])){
            $filearray['folder'] = [];
        }
        if(empty($filearray['file'])){
            $filearray['file']  = [];
        }
        return $filearray;
    } 

    /**
     * 读取文件内容
     *
     * @param [type] $files
     * @return void
     */
    protected function getFileContent($files){
        $files_path = PATH_THEMES.$files;
        if(is_file($files_path)){
            return file_get_contents($files_path);
        }
        return FALSE;
    }

    /**
     * 读取文件内容
     * @param [type] $files
     * @param [type] $file_contents
     * @return void
     */
    protected function TplEdit($files,$file_contents){
        $files_path = PATH_THEMES.$files;
        if(is_file($files_path)){
            if(file_put_contents($files_path,$file_contents)){
                return true;
            }
        }
        return FALSE;
    }
}