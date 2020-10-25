<?php
namespace app\ebcms\controller;
class Store extends \app\ebcms\controller\Common
{

    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch();
        } elseif (request()->isPost()) {
            $param = [
                'page'  =>  input('page'),
                'rows'  =>  input('rows'),
                'q'     =>  input('q'),
            ];
            $res = \ebcms\Server::store('apps', $param);
            if (!$res['code']) {
                $this->error($res['msg'], '', $res['data']);
            }
            $data = $res['data'];
            $this->success('', '', $data);
        }
    }

    // 预安装表单
    public function preins(){
        if (request() -> isGet()) {
            $app_id = input('app_id');
            $param = [
                'app_id' => $app_id
            ];
            if ($myapp = \think\Db::name('app')->where('app_id', $app_id)->find()) {
                $param['version']   =   $myapp['version'];
            }
            $res = \ebcms\Server::store('appInfo', $param);
            if ($res['code']) {

                // 新安装的时候 检测是否冲突
                if (!$myapp) {
                    if ($app = \think\Db::name('app') -> where('name', $res['data']['name']) -> find()) {
                        $this -> error('和【' . $app['title'].'】存在冲突，无法安装！');
                    }
                }

                $this -> assign('app',$res['data']);
                $this -> assign('version',$res['data']['version']);
                return $this -> fetch();
            }else{
                $this -> error($res['msg']);
            }
        }elseif (request() -> isPost()) {
            // 检测是否已经购买
            $res = \ebcms\Server::store('checkBuy', input());
            if ($res['code']) {
                $this -> success($res['msg']);
            }else{
                $this -> error($res['msg']);
            }
        }
    }

    // 安装
    public function install()
    {
        $param = input();
        if ($myapp = \think\Db::name('app')->where('app_id', $param['app_id'])->find()) {
            $param['version']   =   $myapp['version'];
        }
        if (request() -> isPost()) {
            $param['do'] = true;
        }
        $res = \ebcms\Server::store('install', $param);
        if (!$res['code']) {
            $this->error($res['msg']);
        }
        $version = $res['data'];
        $app = $version['app'];

        $patch_path = RUNTIME_PATH . '/patch/';
        $file_path = \ebcms\Config::get('system.base.backup_path') . '/file/';
        $patchfile = $patch_path . md5($app['id'] . '_' . $version['version'] . '_' . substr(\think\Config::get('safe_code'), 6)) . '.zip';

        if (request()->isGet()) {
            if (!is_dir($patch_path)) {
                if (is_writable(dirname($patch_path))) {
                    mkdir($patch_path, 0755, true);
                } else {
                    $this->error('无法创建文件夹，请检查' . dirname($patch_path) . '的权限！错误代码：9000');
                }
            } elseif (!is_writable($patch_path)) {
                $this->error($patch_path . '文件夹不存在或权限不足！请修改！错误代码：9001');
            }
            if (!is_dir($file_path)) {
                if (is_writable(dirname($file_path))) {
                    mkdir($file_path, 0755, true);
                } else {
                    $this->error('无法创建文件夹，请检查' . dirname($file_path) . '的权限！错误代码：9002');
                }
            } elseif (!is_writable($file_path)) {
                $this->error($file_path . '文件夹不存在或权限不足！请修改！错误代码：9003');
            }
            if (!is_file($patchfile) || $version['file_sha1'] != md5(file_get_contents($patchfile))) {
                if (false === $downfile = $this -> download($version['file'])) {
                    $this->error('文件下载失败！请重试！错误代码：9004');
                }
                if ($version['file_sha1'] != sha1($downfile)) {
                    $this->error('文件校验失败！请重新下载！错误代码：9005');
                }
                if (is_file($patchfile) && !is_writable($patchfile)) {
                    $this->error($patchfile . '文件已经存在！请修改该文件权限！错误代码：9006');
                }
                file_put_contents($patchfile, $downfile);
            }
            $files = [];
            $errors = [];
            if (true !== $msg = $this->checkzip($patchfile, $files, $errors)) {
                $this -> error($msg);
            }
            $this->assign('files', $files);
            $this->assign('error', $errors ? 1 : 0);
            $this->assign('errors', array_unique($errors));
            $this->assign('version', $version);
            $this->assign('app', $app);
            return $this->fetch();
        } elseif (request()->isPost()) {

            // 校验压缩文件
            $files = [];
            $errors = [];
            if (true !== $msg = $this->checkzip($patchfile, $files, $errors)) {
                $this -> error($msg);
            }
            if ($errors) {
                $this->error('文件权限校验失败！错误代码：9007');
            }

            // 备份文件
            $backup_path = $file_path . $app['id'] . '/' . $version['version'] . '/';
            $this->backup_files($files, $backup_path);

            // 更新文件
            if (true !== $msg = $this->update_files($patchfile)) {
                $this->rollback_files($backup_path);
                $this->error('发生错误：'.$msg);
            }

            // 如果存在执行文件
            if (isset($files['application/' . $app['name'] . '/install/ebcms.php'])) {
                // 检测执行文件是否存在
                $exec_file = APP_PATH . $app['name'] . '/install/ebcms.php';
                if (!is_file($exec_file)) {
                    $this->rollback_files($backup_path);
                    $this->error('安装文件不完整！错误代码：9009');
                }
                // 执行安装
                include $exec_file;
                if (function_exists('ebcms_install')) {
                    if (true !== $msg = ebcms_install()) {
                        $this->rollback_files($backup_path);
                        $this->error('安装失败！<br>' . $msg . '<br>错误代码：9779');
                    }
                }
            }
            
            // 更新数据库
            if ($myapp) {
                // 更新内容
                \think\Db::name('app')->where('app_id', $app['id'])->setField('version', $version['version']);
            }else{
                $data = [
                    'app_id'        =>  $app['id'],
                    'title'         =>  $app['title'],
                    'name'          =>  $app['name'],
                    'version'       =>  $version['version'],
                    'update_time'   =>  time(),
                    'create_time'   =>  time(),
                    'status'        =>  1,
                ];
                \think\Db::name('app')->insert($data);
            }
            \think\Hook::add('app_end', 'app\\ebcms\\behavior\\Clearcache');
            @unlink($patchfile);
            $this->success('安装成功！');
        }
    }

    // 检查权限
    private function checkzip($filename, &$files, &$errors)
    {
        $rs = zip_open($filename);
        if (!is_resource($rs)) {
            return $this -> zip_error($rs);
        }
        while ($dir_rs = zip_read($rs)) {
            if (zip_entry_open($rs, $dir_rs)) {
                $file = zip_entry_name($dir_rs);
                if (0 === strpos($file, 'public')) {
                    $pathfile = '.' . substr($file, 6);
                    $file_title = $pathfile;
                } elseif (0 === strpos($file, 'templates/default')) {
                    $_path = \ebcms\Config::get('home.site.theme')?:'default';
                    $pathfile = ROOT_PATH . 'templates/' . $_path . '/' . substr($file, 18);
                    $file_title = 'templates/' . $_path . '/' . substr($file, 18);
                } else {
                    $pathfile = ROOT_PATH . $file;
                    $file_title = $file;
                }
                if (pathinfo($pathfile, PATHINFO_EXTENSION)) {
                    if (is_file($pathfile)) {
                        if (is_writable($pathfile)) {
                            $files[$file_title] = true;
                        } else {
                            $errors[] = $pathfile;
                            $files[$file_title] = false;
                        }
                    } else {
                        $files[$file_title] = $this->checkpath(dirname($pathfile), $errors);
                    }
                }
                zip_entry_close($dir_rs);
            }
        }
        zip_close($rs);
        return true;
    }

    private function checkpath($path, &$errors)
    {
        if (is_dir($path)) {
            if (is_writable($path)) {
                return true;
            } else {
                $errors[] = $path;
                return false;
            }
        } else {
            return $this->checkpath(dirname($path), $errors);
        }
    }

    private function update_files($filename)
    {
        $rs = zip_open($filename);
        if (!is_resource($rs)) {
            return $this -> zip_error($rs);
        }
        while ($dir_rs = zip_read($rs)) {
            if (zip_entry_open($rs, $dir_rs)) {
                $file = zip_entry_name($dir_rs);
                $file_size = zip_entry_filesize($dir_rs);
                if (0 === strpos($file, 'public')) {
                    $filename = '.' . substr($file, 6);
                } elseif (0 === strpos($file, 'templates/default')) {
                    $_path = \ebcms\Config::get('home.site.theme')?:'default';
                    $filename = ROOT_PATH . 'templates/' . $_path . '/' . substr($file, 18);
                } else {
                    $filename = ROOT_PATH . $file;
                }
                if (pathinfo($filename, PATHINFO_EXTENSION)) {
                    $file_size = zip_entry_filesize($dir_rs);
                    $file_content = zip_entry_read($dir_rs, $file_size);
                    file_put_contents($filename, $file_content);
                } else {
                    if (!is_dir($filename)) {
                        mkdir($filename, 0755, true);
                    }
                }
                zip_entry_close($dir_rs);
            }
        }
        zip_close($rs);
        return true;
    }

    private function backup_files($files, $path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        foreach ($files as $file => $v) {
            if (0 === strpos($file, 'public')) {
                $filename = '.' . substr($file, 6);
            } else {
                $filename = ROOT_PATH . $file;
            }
            if (is_file($filename)) {
                $backfile = str_ireplace(['/', '\\'], '____', $file);
                file_put_contents($path . $backfile . '.php', file_get_contents($filename));
            }
        }
    }

    private function rollback_files($path)
    {
        $files = scandir($path);
        foreach ($files as $key => $value) {
            if ($value == '.' || $value == '..') {
                continue;
            }
            if (0 === strpos($value, 'public')) {
                $backto = './' . substr(str_replace('____', '/', $value), 7, -4);
            } else {
                $backto = ROOT_PATH . substr(str_replace('____', DS, $value), 0, -4);
            }
            file_put_contents($backto, file_get_contents($path . $value));
        }
        return true;
    }

    private function zip_error($errno) {
        $zipFileFunctionsErrors = array(
            'ZIPARCHIVE::ER_MULTIDISK' => 'Multi-disk zip archives not supported.',
            'ZIPARCHIVE::ER_RENAME' => 'Renaming temporary file failed.',
            'ZIPARCHIVE::ER_CLOSE' => 'Closing zip archive failed', 
            'ZIPARCHIVE::ER_SEEK' => 'Seek error',
            'ZIPARCHIVE::ER_READ' => 'Read error',
            'ZIPARCHIVE::ER_WRITE' => 'Write error',
            'ZIPARCHIVE::ER_CRC' => 'CRC error',
            'ZIPARCHIVE::ER_ZIPCLOSED' => 'Containing zip archive was closed',
            'ZIPARCHIVE::ER_NOENT' => 'No such file.',
            'ZIPARCHIVE::ER_EXISTS' => 'File already exists',
            'ZIPARCHIVE::ER_OPEN' => 'Can\'t open file', 
            'ZIPARCHIVE::ER_TMPOPEN' => 'Failure to create temporary file.', 
            'ZIPARCHIVE::ER_ZLIB' => 'Zlib error',
            'ZIPARCHIVE::ER_MEMORY' => 'Memory allocation failure', 
            'ZIPARCHIVE::ER_CHANGED' => 'Entry has been changed',
            'ZIPARCHIVE::ER_COMPNOTSUPP' => 'Compression method not supported.', 
            'ZIPARCHIVE::ER_EOF' => 'Premature EOF',
            'ZIPARCHIVE::ER_INVAL' => 'Invalid argument',
            'ZIPARCHIVE::ER_NOZIP' => 'Not a zip archive',
            'ZIPARCHIVE::ER_INTERNAL' => 'Internal error',
            'ZIPARCHIVE::ER_INCONS' => 'Zip archive inconsistent', 
            'ZIPARCHIVE::ER_REMOVE' => 'Can\'t remove file',
            'ZIPARCHIVE::ER_DELETED' => 'Entry has been deleted',
        );
        $errmsg = 'unknown';
        foreach ($zipFileFunctionsErrors as $constName => $errorMessage) {
            if (defined($constName) and constant($constName) === $errno) {
                return 'Zip File Function error: '.$errorMessage;
            }
        }
        return 'Zip File Function error: unknown';
    }

    // 启用 停用
    public function status(){
        \think\Db::transaction(function(){
            \think\Db::name('app') -> where(['id'=>input('id')]) -> setField('status',input('value')?1:0);
            \think\Hook::add('app_end', 'app\\ebcms\\behavior\\Clearcache');
        });
        $this -> success('操作成功！');
    }

    // 卸载
    public function uninstall()
    {
        \think\Hook::add('app_end', 'app\\ebcms\\behavior\\Clearcache');

        // 密码验证
        $manager = \think\Db::name('manager') -> find(\think\Session::get('manager_id'));
        if ($manager['password'] !== \ebcms\Func::crypt_pwd(input('password'), $manager['email'])) {
            $this->error('密码错误！');
        }

        // 禁止卸载核心
        $app = \think\Db::name('app') -> where('id',input('id')) -> find();
        if ($app['name'] == 'ebcms') {
            $this -> error('核心禁止卸载');
        }

        // 判断文件和函数是否完整
        $file = APP_PATH . $app['name'] . '/install/ebcms.php';
        if (!is_file($file)) {
            $this -> error('卸载文件丢失！');
        }
        include $file;
        if (!function_exists('ebcms_uninstall')) {
            $this -> success('卸载文件错误！');
        }

        $res = ebcms_uninstall();
        if (true === $res) {
            \think\Db::transaction(function(){
                \think\Db::name('app') -> where('id', input('id')) -> delete();
            });
            $this -> success('卸载成功！');
        }else{
            $this -> error('卸载失败：' . $res);
        }
    }

    private function download($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($ch);
        if (false !== $res) {
            curl_close($ch);
            list($header, $data) = explode("\r\n\r\n", $res);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }

}