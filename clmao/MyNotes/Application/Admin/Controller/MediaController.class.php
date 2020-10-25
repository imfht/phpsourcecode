<?php

namespace Admin\Controller;

use Think\Controller;

class MediaController extends Controller {
    /*
     * 文件列表
     */

    public function index() {
        $this->title='媒体库';
        //遍历目录取得文件信息
        $current_path = './Public/uploads';
        $files = clmao_scandir($current_path);
        clmao_getOneArr($files);
        $files = array();
        $allowExt = array(
            'img' => array('.png', '.jpg', '.jpeg', '.gif', '.bmp'),
            'video.png' => array('.flv', '.swf', '.mkv',
                '.avi', '.rm', '.rmvb', '.mpeg',
                '.mpg', '.ogg', '.ogv',
                '.mov', '.wmv', '.mp4', '.webm',),
            'audio.png' => array('.mp3', '.wav', '.mid'),
            'archive.png' => array('.rar', '.zip', '.tar', '.gz', '.7z', '.bz2', '.cab', '.iso'),
            'document.png' => array('.doc', '.docx', '.pdf'),
            'spreadsheet.png' => array('.xls', '.xlsx',),
            'interactive.png' => array('.ppt', '.pptx'),
            'text.png' => array('.txt', '.md', 39 => '.xml'),
        );
        foreach ($GLOBALS['one_arr'] as $k => $v) {
            $ext = strrchr($v, '.');
            $url = __ROOT__ . trim($v, '.');
            foreach ($allowExt as $key => $val) {
                if (in_array($ext, $val)) {
                    $files[$k]['img'] = $key;
                    if ($files[$k]['img'] == 'img') {
                        $files[$k]['img'] = $url;
                    } else {
                        $files[$k]['img'] = __ROOT__ . '/Public/admin/crystal/' . $files[$k]['img'];
                    }
                    continue;
                }
            }
            $files[$k]['filename'] = basename($v, $ext);
            $files[$k]['ext'] = strtoupper(trim($ext, '.'));
            $files[$k]['dir'] = dirname($v);
            $files[$k]['url'] = $url;
            $files[$k]['time'] = filemtime ($v);
            $files[$k]['del'] = str_replace('./Public/uploads/', '', $v);
           
            
        }
        $p = I('get.p',0,'intval');
        $files = clmao_getArrPage($files, $p, 10);
        $this->files = $files;
        $this->next = $p+10;
        $this->prev = $p==0?0:($p-10);
        $this->display();
    }

    /*
     * 删除文件
     */

    public function del() {
        $file = I('get.f');
        clmao_validate_file($file);
        $file = './Public/uploads/' . $file;
        if (unlink($file)) {
            $this->redirect('index');
        }
    }

    /*
     * 添加文件
     */

    public function add() {
        $this->title='添加媒体';
        $this->display();
    }

    /*
     * 添加文件处理
     */

    public function add_process() {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('gif', 'jpg', 'jpeg', 'png', 'bmp',
            'swf', 'flv',
            'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'); // 设置附件上传类
        $upload->subName = array('date', 'Ym');
        $upload->rootPath = './Public/uploads/1/';
        // 上传单个文件 
        $info = $upload->uploadOne($_FILES['file']);
        if (!$info) {// 上传错误提示错误信息
            echo $upload->getError();
        } else {// 上传成功 获取上传文件信息
            $filePath = __ROOT__ . trim($upload->rootPath, '.') . date('Ym', time()) . '/' . $info['savename'];
            $file_path_water = $upload->rootPath . date('Ym', time()) . '/' . $info['savename'];
            $fileExt = $_FILES['file']['type'];
            if (stripos($fileExt, 'image')!==false) {
                $image = new \Think\Image();
                // 在图片右下角添加水印文字 ThinkPHP 并保存为new.jpg
                $image->open($file_path_water)->text(getSiteOption('siteName'), './Public/font/STXINGKA.TTF', 20, '#4BE732', \Think\Image::IMAGE_WATER_SOUTHEAST)->save($file_path_water);
            }
            header('content-type:text/html;charset=utf-8');
            echo "{$filePath}添加成功 <a href='{$filePath}' target='_blank'>浏览</a>";
        }
    }

}
