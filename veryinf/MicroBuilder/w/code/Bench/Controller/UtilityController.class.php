<?php
/**
 * 工作台欢迎页
 */
namespace Bench\Controller;
use Core\Model\Utility;
use Core\Util\File;
use Think\Controller;
class UtilityController extends Controller {
    
    public function qrAction() {
        $url = I('get.raw');
        $url = base64_decode($url);
        import_third('qrcode.phpqrcode');
        \QRcode::png($url, false, QR_ECLEVEL_Q, 8);
    }

    public function fileAction() {
        $do = I('get.do');
        $do = in_array($do, array('upload', 'browser')) ? $do : 'upload';
        $type = I('get.type');
        $type = in_array($type, array('image', 'audio')) ? $type : 'image';
        $method = 'file' . ucfirst($type) . ucfirst($do);
        
        $option = @base64_decode(I('post.options'));
        $option = @unserialize($option);
        if(empty($option)) {
            $option = array();
        }
        
        call_user_func(array($this, $method), $option);
    }
    
    private function frameCallback($ret) {
        $callback = I('get.callback');
        $val = json_encode($ret);
        echo '<script type="text/javascript">window.parent.' . $callback . '(' . $val . ');</script>';
        exit;
    }
    
    private function fileImageUpload($option) {
        if(empty($option['width'])) {
            $option['width'] = 600;
        }
        if (!empty($_FILES['file']['name'])) {
            $ret = Utility::upload($_FILES['file']);
            if (is_error($ret)) {
                $this->frameCallback($ret);
            }
            File::imageThumb($ret['abs'], $ret['abs'], $option['width']);
            
            $result = array();
            $result['filename'] = $ret['filename'];
            $result['url'] = $ret['url'];
            $result['error'] = 0;
            $this->frameCallback($result);
        } else {
            $this->frameCallback(error(-1, '请选择要上传的图片！'));
        }
    }
    
    private function fileImageBrowser() {
        $path = I('get.path');
        $path = str_replace(array('./', '../', '//'), '', $path);
        $path = trim($path, '/');
        $path = trim($path, '.');
        $path .= '/';
        $root = MB_ROOT . 'attachment/images/';
        $currentPath = $root . $path;
        $exts = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

        //遍历目录取得文件信息
        $files = array();
        if($path != '/') {
            $files[] = array(
                'filename' => '..',
                'is_dir' => true,
                'datetime' => 0,
            );
            $this->assign('parentPath', str_replace('\\', '/', dirname($path)));
        }
        $pieces = explode('/', $path);
        $crumbs = array();
        if(!empty($pieces)) {
            $line = '';
            foreach($pieces as $piece) {
                if(!empty($piece)) {
                    $line .= '/' . $piece;
                    $crumbs[] = array($piece, $line);
                }
            }
        }
        $this->assign('crumbs', $crumbs);
        $this->assign('currentImage', attach(I('get.file')));

        if(is_dir($currentPath)) {
            if($handle = opendir($currentPath)) {
                while(false !== ($filename = readdir($handle))) {
                    if($filename == '.') continue;
                    if($filename == '..') continue;
                    $file = $currentPath .'/'. $filename;

                    if (is_dir($file)) {
                        $files[] = array(
                            'filename' => $filename,
                            'is_dir' => true,
                            'datetime' => date('Y-m-d H:i:s', filemtime($file)),
                        );
                    } else {
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if(in_array($ext, $exts)) {
                            $entry = array();
                            $entry['filename'] = 'images/' . $path . $filename;
                            $entry['url'] = attach($entry['filename']);
                            $files[] = array(
                                'filename' => $filename,
                                'is_dir' => false,
                                'url' => $entry['url'],
                                'entry' => str_replace('"', '\'', json_encode($entry)),
                                'datetime' => date('Y-m-d H:i:s', filemtime($file)),
                            );
                        }
                    }
                }
            }
        }
        usort($files, array($this, 'fileCompare'));
        $this->assign('path', $path);
        $this->assign('type', 'image');
        $this->assign('callback', I('get.callback'));
        $this->assign('files', $files);
        $this->display('file-browser');
    }
    
    private function fileCompare($a, $b) {
        if ($a['is_dir'] && !$b['is_dir']) {
            return -1;
        } elseif(!$a['is_dir'] && $b['is_dir']) {
            return 1;
        } elseif($a['is_dir'] && $b['is_dir']) {
            return strcmp($a['filename'], $b['filename']);
        } else {
            return $a['datetime'] < $b['datetime'] ? -1 : 1;
        }
    }
}