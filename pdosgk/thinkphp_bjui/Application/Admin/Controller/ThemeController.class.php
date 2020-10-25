<?php
/**
 *
 * 主题
 * @author Lain
 *
 */
namespace Admin\Controller;

use Admin\Controller\AdminController;
use \Lain\FileUtil;

class ThemeController extends AdminController
{
    public function _initialize()
    {
        $action = array(
            // 'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
            //'allow'=>array('index')
        );
        B('Admin\\Behaviors\\Authenticate', '', $action);
    }

    //管理
    public function manage()
    {
        $page_list = template_list();
        $this->assign('page_list', $page_list);
        $this->display();
    }

    public function add()
    {
        if (IS_POST) {
            //判断是否已经存在
            $dirname       = I('post.name');
            $file          = I('post.file');
            $template_list = template_list();
            $list          = array_column($template_list, 'dirname');
            if (in_array($dirname, $list)) {
                $this->ajaxReturn(array('statusCode' => 300, 'message' => '已存在该标识'));
            }
            if (empty($file) || !is_file($file)) {
                $this->ajaxReturn(array('statusCode' => 300, 'message' => '请上传'));
            }

            $extract_path = './Uploads/tmp';

            //先删除文件
            FileUtil::unlinkDir($extract_path . '/html/');
            FileUtil::unlinkDir($extract_path . '/static/');
            // unlink($extract_path.'/html.zip');
            // unlink($extract_path.'/static.zip');
            //解压文件
            if(!unzip($file, $extract_path)){
                $this->ajaxReturn(array('statusCode' => 300, 'message' => '解压失败'));
            }

            $html_dir_path   = APP_PATH . 'Home/View/' . $dirname;
            $static_dir_path = './Public/theme/' . $dirname;
            mk_dir($html_dir_path);
            mk_dir($static_dir_path);
            //解压出html.zip， 和static.zip
            FileUtil::moveDir($extract_path . '/html/', $html_dir_path);
            FileUtil::moveDir($extract_path . '/static/', $static_dir_path);
            
            //取出文件
            if (1) {
                $this->ajaxReturn(array('statusCode' => 200, 'closeCurrent' => true, 'tabid' => 'Theme_manage', 'message' => '保存成功'));
            } else {
                $this->ajaxReturn(array('statusCode' => 300, 'message' => '保存失败ERROR:003'));
            }
        } else {
            $this->display();
        }
    }

    public function edit(){
        $dirname         = I('get.dirname');
        $template_list = template_list();
        foreach ($template_list as $key => $value) {
            if($value['dirname'] == $dirname){
                $detail = $value;
                break;
            }
        }
        if(empty($detail)){
            $this->ajaxReturn(array('statusCode' => 300, 'message' => '不存在该标识'));
        }

        if(IS_POST){
            $info = I('post.info');
            $name = $info['name'];
            //修改中文名
            $filepath = APP_PATH . 'Home/View/' . $dirname . '/config.php';
            if (file_exists($filepath)) {
                $arr = include $filepath;
                $arr['name'] = $name;
            } else {
                $arr = array('name'=>$name,'disable'=>0, 'dirname'=>$dirname);
            }
            @file_put_contents($filepath, '<?php return '.var_export($arr, true).';?>');
            $this->ajaxReturn(array('statusCode' => 200, 'closeCurrent' => true, 'tabid' => 'Theme_manage', 'message' => '保存成功'));
        }else{
            $this->assign('Detail', $detail);
            $this->display();
        }
    }

    //文章模块上传缩略图
    public function ajaxUpload()
    {
        // $dir = I('get.dir');
        $dir = 'tmp';

        $upload           = new \Think\Upload(); // 实例化上传类
        $upload->maxSize  = 10485760; // 设置附件上传大小10M
        $upload->exts     = array('zip'); // 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = $dir . '/'; // 设置附件上传（子）目录
        // 上传文件
        $info    = $upload->upload();
        $img_url = $upload->rootPath . $info['file']['savepath'] . $info['file']['savename'];
        if (!$info) {
            // 上传错误提示错误信息
            $this->ajaxReturn(array('error' => 1, 'message' => $upload->getError()));
        } else {
            // 上传成功
            // $downloadedfile = array('filename'=>$info['file']['name'], 'filepath'=>$img_url, 'filesize'=>$info['file']['size'], 'fileext'=>$info['file']['ext'], 'authcode' => $info['file']['md5'], 'savepath'=>$info['file']['savepath'], 'savename'=>$info['file']['savename']);
            // D('Attachment')->saveData($downloadedfile);
            $this->ajaxReturn(array('statusCode' => 200, 'message' => "上传成功！", "filename" => $img_url));
        }
    }

    //删除
    public function delete(){
        $dirname         = I('get.dirname');
        $html_dir_path   = APP_PATH . '/Home/View/' . $dirname;
        $static_dir_path = './Public/theme/' . $dirname;
        if (is_dir($html_dir_path)) {
            //先清空
            deleteDir($html_dir_path);
        }

        if (is_dir($static_dir_path)) {
            deleteDir($static_dir_path);
        }
        $this->ajaxReturn(array('statusCode' => 200, 'message' => '保存成功'));
    }

    //导出
    public function export()
    {
        // $FileUtil = new \Lain\FileUtil;
        $dirname         = I('get.dirname');
        $html_dir_path   = APP_PATH . '/Home/View/' . $dirname;
        $static_dir_path = './Public/theme/' . $dirname;

        $save_path = './Uploads/theme/' . $dirname . '/';
        FileUtil::unlinkFile('./Uploads/theme/' . $dirname . '.zip');
        FileUtil::unlinkDir($save_path);
        mk_dir($save_path);
        FileUtil::copyDir($html_dir_path, $save_path.'html');
        FileUtil::copyDir($static_dir_path, $save_path.'static');

        zip($save_path, './Uploads/theme/' . $dirname . '.zip');
        FileUtil::unlinkDir($save_path);

        header("Content-type:application/zip");
        //设置文件下载名不包含路径
        $filename = $dirname . '.zip';

        //设置文件加载方式为激活下载框
        header("Content-Disposition:attachment;filename=$filename");
        readfile('./Uploads/theme/' . $dirname . '.zip');
        // echo 'done';
    }
}
