<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace addons\security\controller;

use app\common\controller\Base;
use app\common\widget\Widget;
use think\facade\App;
use think\Facade\Env;

class Admin extends Base
{
    protected function initialize()
    {
        //调用admin/Base控制器的初始化
        action('admin/Base/initialize');
    }

    /*
     * 安全文件列表
     */
    public function securityList()
    {
        $security_dir = Env::get('root_path') . 'data/security/';
        if (!file_exists($security_dir)) {
            @mkdir($security_dir);
        }
        $finger_files = list_file($security_dir, '*.finger');
        foreach ($finger_files as &$finger_file) {
            $finger_file['check_href'] = addon_url('security://Admin/securityCheck', ['file' => md5($finger_file['filename'])]);
            $finger_file['del_href']   = addon_url('security://Admin/securityDel', ['file' => md5($finger_file['filename'])]);
        }
        //表格字段
        $fields       = [
            ['title' => '文件名称', 'field' => 'filename'],
            ['title' => '文件大小', 'field' => 'size'],
            ['title' => '上传时间', 'field' => 'mtime', 'type' => 'datetime']
        ];
        $right_action = [
            'check' => ['field' => 'check_href', 'title' => '检测', 'icon' => 'ace-icon fa fa-check bigger-130', 'class' => 'red'],
            'del'   => ['field' => 'del_href', 'title' => '删除', 'icon' => 'ace-icon fa fa-close bigger-130', 'class' => 'red confirm-rst-url-btn', 'extra_attr' => 'data-info="你确定要彻底删除安全文件吗？"']
        ];
        $widget       = new Widget();
        return $widget
            ->addToparea([], [['title' => '重生成安全文件', 'id' => 'security_generate', 'type' => 'a', 'attr' => ['class' => 'btn btn-xs btn-danger', 'icon_l' => 'ace-icon fa fa-bolt bigger-110', 'href' => addon_url('security://Admin/securityGenerate')]]])
            ->addtable($fields, '', $finger_files, $right_action)
            ->setButton()
            ->fetch();
    }

    /*
     * 安全文件生成
     */
    public function securityGenerate()
    {
        $security_dir = Env::get('root_path') . 'data/security/';
        if (!file_exists($security_dir)) {
            @mkdir($security_dir);
        }
        $filename = $security_dir . 'file_finger_' . date('YmdHi') . '_' . random(10) . '.finger';
        $f        = fopen($filename, 'w');
        fwrite($f, "GENE: RCF V" . App::version() . "\n");
        fwrite($f, "TIME: " . date('Y-m-d H:i:s') . "\n");
        fwrite($f, "ROOT: \n");
        $files_md5 = [];
        foreach ([
                     //检测目录
                     'vendor',
                     'app',
                     'extend',
                     'public',
                     'thinkphp',
                     'addons'
                 ] as $dir) {
            foreach ($this->securityFilefingerGenerate('./' . $dir . '/', $dir . '/') as $file_md5) {
                $files_md5 [] = $file_md5;
                fwrite($f, $file_md5 [1] . '|' . $file_md5 [0] . "\n");
            }
        }
        fclose($f);
        $this->success('成功生成安全文件', addon_url('security://Admin/securityList'));
    }

    /*
     * 安全文件删除
     */
    public function securityDel()
    {
        $security_dir = Env::get('root_path') . 'data/security/';
        if (!file_exists($security_dir)) {
            $this->error('文件不存在', addon_url('security://Admin/securityList'));
        }
        $file = input('file');
        foreach (list_file($security_dir, '*.finger') as $f) {
            if (md5($f ['filename']) == $file) {
                @unlink($f ['pathname']);
            }
        }
        $this->success('成功删除', addon_url('security://Admin/securityList'));
    }

    /*
     * 安全检测
     */
    public function securityCheck()
    {
        $security_dir = Env::get('root_path') . 'data/security/';
        if (!file_exists($security_dir)) {
            $this->error('文件不存在', addon_url('security://Admin/securityList'));
        }
        $md5_file = null;
        $file     = input('file');
        foreach (list_file($security_dir, '*.finger') as $f) {
            if (md5($f ['filename']) == $file) {
                $md5_file = $f ['pathname'];
                break;
            }
        }
        if (null != $md5_file) {
            if (!file_exists($md5_file) || !is_file($md5_file)) {
                $this->error('文件不存在', addon_url('security://Admin/securityList'));
            }
            $lines = explode("\n", file_get_contents($md5_file));
            if (count($lines) < 3) {
                $this->error('安全文件错误1', addon_url('security://Admin/securityList'));
            }
            if (!preg_match('/^GENE: RCF V.*?$/', $lines [0]) || !preg_match('/^TIME: \\d+\\-\\d+\\-\\d+ \\d+:\\d+:\\d+$/', $lines [1]) || !preg_match('/^ROOT: ([\\/\\.]*)/', $lines [2])) {
                $this->error('安全文件错误2', addon_url('security://Admin/securityList'));
            }
            $finger_file_root = trim(substr($lines [2], 5));
            $basedir          = str_replace('\\', '/', rtrim(realpath($finger_file_root), '\\/')) . '/';
            unset($lines[0], $lines[1], $lines[2]);
            $error_msgs         = [];
            $file_should_exists = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line) {
                    $l = explode('|', $line);
                    if (count($l) == 2) {
                        $file                       = trim($l [1]);
                        $md5                        = trim($l [0]);
                        $file_should_exists [$file] = $md5;
                        if (file_exists($filename = $basedir . $file)) {
                            if ($md5 != md5_file($filename)) {
                                $error_msgs []['error'] = '文件被篡改 : ' . $file;
                            }
                        } else {
                            $error_msgs []['error'] = '缺少文件 : ' . $file;
                        }
                    } else {
                        $error_msgs []['error'] = '错误行 : ' . $line;
                    }
                }
            }
            if (!$error_msgs) {
                $error_msgs[]['error'] = '当前系统没有发生文件被修改问题。';
            }
            //表格字段
            $fields       = [
                ['title' => '文件检测情况', 'field' => 'error']
            ];
            $right_action = [
            ];
            $widget       = new Widget();
            return $widget
                ->addToparea([], [['title' => '返回安全文件列表', 'type' => 'a', 'attr' => ['class' => 'btn btn-xs btn-primary', 'icon_l' => 'ace-icon fa fa-rotate-left bigger-110', 'href' => addon_url('security://Admin/securityList')]], ['title' => '重生成安全文件', 'id' => 'securityGenerate', 'type' => 'a', 'attr' => ['class' => 'btn btn-xs btn-danger', 'icon_l' => 'ace-icon fa fa-bolt bigger-110', 'href' => addon_url('security://Admin/securityGenerate')]]])
                ->addtable($fields, '', $error_msgs, $right_action)
                ->setButton()
                ->fetch();
        } else {
            $this->error('文件不存在', addon_url('security://Admin/securityList'));
        }
    }

    //安全文件生成
    private function securityFilefingerGenerate($dir = '', $prefix = '')
    {
        static $allow_file_exts = [
            'php'  => true,
            'js'   => true,
            'html' => true,
            'htm'  => true
        ];
        $file_arrs = [];
        foreach (list_file($dir) as $file) {
            if ($file ['isDir']) {
                $file_arrs = array_merge($file_arrs, $this->securityFilefingerGenerate($file ['pathname'] . '/', $prefix . $file ['filename'] . '/'));
            } else if ($file ['isFile']) {
                if (isset($allow_file_exts[$file['ext']])) {
                    $file_saved   = $prefix . str_replace('\\', '/', $file ['filename']);
                    $file_arrs [] = [
                        $file_saved,
                        md5_file($file ['pathname'])
                    ];
                }
            }
        }
        return $file_arrs;
    }
}
