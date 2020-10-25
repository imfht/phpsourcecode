<?php

/**
 * 系统首页
 */

namespace app\system\admin;

class UpdateAdmin extends \app\system\admin\SystemAdmin {

    protected $verDir;
    protected $verFile;
    protected $verInfo;

    /**
     * 当前模块参数
     */
    protected function _infoModule() {
        return array(
            'info' => array(
                'name' => '系统更新',
                'description' => '针对整个系统进行更新升级',
            )
        );
    }

    /**
     * 系统信息
     */
    public function index() {
        $this->assign('verInfo', \dux\Config::get('dux.use_ver'));
        $this->systemDisplay();
    }


    /**
     * 升级版本
     */
    public function update() {
        $file = request('post', 'file');
        if(empty($file)) {
            $this->error('更新文件不存在！');
        }
        $this->download($file);
        $this->unzip();
        $this->detectVer();
        $this->moveVer();
        $this->runSql();
        $this->success('更新安装成功，请重新系统，以便正常使用！');
    }

    /**
     * 下载版本
     */
    private function download($url) {
        $dir = ROOT_PATH . 'data/cache/download/';
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                $this->error('安装临时目录创建失败，请确认data目录有写入权限！');
            }
        }
        $data = \dux\lib\Http::doGet($url, 20);
        $fileName = explode('/', $url);
        $fileName = end($fileName);
        $dirName = explode('.', $fileName);
        $fileDir = $dir . $dirName[0] . '/';
        if (!is_dir($fileDir)) {
            if (!@mkdir($fileDir, 0777, true)) {
                $this->error('安装临时目录创建失败，请确认data目录有写入权限！');
            }
        }
        $file = $dir . $fileName;
        if (!@file_put_contents($file, $data)) {
            $this->error('更新下载失败，请确认data目录有写入权限！');
        }
        $this->verDir = $fileDir;
        $this->verFile = $file;
    }

    /**
     * 解压版本
     */
    private function unzip() {
        $zip = new \dux\lib\Zip();
        if (!$zip->decompress($this->verFile, $this->verDir)) {
            $this->error('更新文件解压失败！');
        }
    }

    /**
     * 检测版本信息
     */
    private function detectVer() {
        $file = file_get_contents($this->verDir . 'update.json');
        $info = json_decode($file, true);
        if (empty($info)) {
            $this->error('未发现更新信息，请检查安装包！');
        }
        $verInfo = \dux\Config::get('dux.use_ver');
        if (strtotime($info['ver_date']) <= ($verInfo['ver_date'])) {
            $this->error('更新版本低于当前系统版本,更新失败');
        }
        $this->verInfo = $info;
    }

    /**
     * 复制更新文件
     */
    private function moveVer() {
        if (!copy_dir($this->verDir . 'src', ROOT_PATH)) {
            $this->error('移动更新失败，请确保所有目录有写入权限！');
        }
    }

    /**
     * 导入sql文件
     */
    private function runSql() {
        $sqlFile = $this->verDir . 'install.sql';
        if (!is_file($sqlFile)) {
            return true;
        }
        $config = \dux\Config::get('dux.database');
        $install = new \dux\lib\Install();
        $sqlList = $install->mysql($sqlFile, $this->verInfo['sql_prefix'], $config['prefix']);
        $model = target('system/SystemConfig');
        $model->beginTransaction();
        foreach ($sqlList as $sql) {
            if ($model->execute($sql) === false) {
                $this->error('更新数据导入失败，请重新安装！');
            }
        }
        $model->commit();
    }
}