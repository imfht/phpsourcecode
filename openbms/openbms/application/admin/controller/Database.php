<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Database extends AdminBase
{
    protected $backup;

    protected function _initialize()
    {
        parent::_initialize();
        $this->backup = new \core\Backup([
            'path'     => './data/',// 数据库备份路径
            'part'     => 20971520, // 数据库备份卷大小
            'compress' => 0,        // 数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 9,        // 数据库备份文件压缩级别 1普通 4 一般  9最高
        ]);
    }

    public function index()
    {
        return $this->fetch('index', ['list' => $this->backup->dataList()]);
    }

    // 导入表
    public function import()
    {
        if ($this->request->isPost()) {
            $file  = $this->backup->getFile('timeverif', $this->request->param('time'));
            $start = $this->backup->setFile($file)->import(0);
            if ($start === 0) {
                insert_admin_log('恢复了数据');
                $this->success('还原成功');
            } else {
                $this->error('还原失败');
            }
        }
        return $this->fetch('import', ['list' => $this->backup->fileList()]);
    }

    // 备份表
    public function backup($table)
    {
        if ($this->request->isPost()) {
            $file = ['name' => date('Ymd-His'), 'part' => 1];
            foreach ($table as $v) {
                if ($this->backup->setFile($file)->backup($v, 0) !== 0) {
                    $this->error('备份失败');
                }
            }
            insert_admin_log('备份了数据');
            $this->success('备份成功');
        }
    }

    // 优化表
    public function optimize($table)
    {
        if ($this->request->isPost()) {
            if ($this->backup->optimize($table)) {
                insert_admin_log('优化了数据');
                $this->success('优化成功');
            } else {
                $this->error('优化失败');
            }
        }
    }

    // 修复表
    public function repair($table)
    {
        if ($this->request->isPost()) {
            if ($this->backup->repair($table)) {
                insert_admin_log('修复了数据');
                $this->success('修复成功');
            } else {
                $this->error('修复失败');
            }
        }
    }

    // 下载备份文件
    public function download($time)
    {
        insert_admin_log('下载了数据');
        $this->backup->downloadFile($time);
    }

    // 删除备份文件
    public function del($time)
    {
        if ($this->request->isPost()) {
            if ($this->backup->delFile($time)) {
                insert_admin_log('删除了数据');
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }
}
