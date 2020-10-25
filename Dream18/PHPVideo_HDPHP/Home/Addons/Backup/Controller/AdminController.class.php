<?php

/**
 * 数据库备份模块
 * Class BackupControl
 * @author 向军 <houdunwangxj@gmail.com>
 */
class AdminController extends AddonAuthController
{

    //备份列表
    public function index()
    {
        $file = Dir::tree("Backup");
        $dir = array();
        foreach ($file as $f) {
            if (is_dir($f['path'])) {
                $dir[] = $f;
            }
        }
        $this->assign("dir", $dir);
        $this->display();
    }

    //数据备份
    public function backup_db()
    {
        $result = Backup::backup(
            array(
                'size' => 200,//分卷大小
                'structure' => false,//表结构
                'dir' => 'Backup/' . date("Ymdhis") . '/',//备份目录
                'step_time' => 1,//备份时间间隔
            )
        );
        if ($result === false) {
            //备份发生错误
            $this->error(Backup::$error, addon_url('index'));
        } else {
            if ($result['status'] == 'success') {
                //备份完成
                $this->success($result['message'], addon_url('index'));
            } else {
                //备份过程中
                $this->success($result['message'], $result['url'], 0.2);
            }
        }
    }

    //配置数据备份
    public function backup()
    {
        $this->assign("table", M()->getAllTableInfo());
        $this->display();
    }

    //还原数据
    public function recovery()
    {
        $dir    = "Backup/" . Q("dir");
        $result = Backup::recovery(array('dir' => $dir));
        if ($result === false) {//还原发生错误
            $this->error(Backup::$error, addon_url('index'));
        } else {
            if ($result['status'] == 'success') {//还原完毕
                $this->success($result['message'], addon_url('index'));
            } else {//备份运行中...
                $this->success($result['message'], $result['url'], 0.2);
            }
        }
    }

    //优化表
    public function optimize()
    {
        if (!empty($_POST['table'])) {
            foreach ($_POST['table'] as $t) {
                M()->optimize($t);
            }
            $this->success('优化表成功');
        }
    }

    //修复表
    public function repair()
    {
        if (!empty($_POST['table'])) {
            foreach ($_POST['table'] as $t) {
                M()->repair($t);
            }
            $this->success('修复表成功');
        }

    }

    //删除备份目录
    public function del()
    {
        $dir = Q('dir', '', 'trim');
        if (Dir::del('Backup/' . $dir)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }

}