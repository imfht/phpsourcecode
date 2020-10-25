<?php
/**
 * 上传图片
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Editor_UploadController extends AbsController {

    /**
     * 入口
     * 
     * @return void
     */
    public function indexAction() {
        try {
            $this->_process();
        } catch(\Exception\Abs $e) {
            $msg = $e->getMessage();
            echo "<font color=\"red\"size=\"2\">{$msg}</font>";
        }
    }
    
    /**
     * 处理方法
     * 
     * @return void
     */
    protected function _process() {
        $extension_allow = ',jpg,gif,png,';
        $uploadFilename = $_FILES['upload']['name'];
        $extension = strtolower(pathInfo($uploadFilename, PATHINFO_EXTENSION));
        
        if(strpos($extension_allow, ",{$extension},") === false) {
            throw new Exception\Msg('*文件格式不正确（必须为.jpg/.gif/.png文件）');
        }
        
        //移至临时文件
        $uniq_id = uniqid();
        $tmp_file = TMP_PATH . "/upload/{$uniq_id}.{$extension}";
        $tmp_dir = dirname($tmp_file);
        if(!is_dir($tmp_dir)) {
            mkdir($tmp_dir, 0775, true);
        }
        move_uploaded_file($_FILES['upload']['tmp_name'], $tmp_file);
    
        //上传
        $path = 'upload/' . date('Y') . '/' . "{$uniq_id}.{$extension}";
        Model\Qiniu::upload($path, $tmp_file);
    
        //删除临时文件
        try {
            @unlink($tmp_file);
        } catch(Exception $e) {
        }
    
        $blog = Model\Blog::show();
        $previewname = 'http://' . $blog['data']['qiniu-domain'] . '/' . $path;
    
        //成功回调
        $callback = $_REQUEST['CKEditorFuncNum'];
        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction({$callback},'" . $previewname . "','');</script>";

    }
}
