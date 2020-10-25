<?php
namespace Addon\Develop\Control\Controller;
use Core\AddonController;
use Core\Model\Addon;
use Core\Model\Utility;
use Core\Util\File;

class PublishController extends AddonController {
    private $coreTables = array(
        'mb_core_paylogs',
        'core_settings',
        'ex_addon_entries',
        'ex_addons',
        'mmb_groups',
        'mmb_mapping_fans',
        'mmb_mapping_ucenter',
        'mmb_members',
        'mmb_profiles',
        'platform_alipay',
        'platform_weixin',
        'platforms',
        'rp_processors',
        'rp_replies',
        'usr_acl',
        'usr_resources',
        'usr_roles',
        'usr_users',
    );
    
    private $coreDatas = <<<'DAT'
    $sql['datas'][] = "INSERT INTO `mb_mmb_groups` (`title`, `remark`, `orderlist`, `isdefault`) VALUES ('普通会员', '系统默认的会员组', 0, 1);";
DAT;

    
    private $ignores = array(
        '/^\/\.gitignore$/i',
        '/^\/addons\/.*$/i',
        '/^\/attachment\/.*$/i',
        '/^\/source\/Conf\/config\.inc\.php$/i',
        '/^\/source\/Conf\/install\.lock$/i',
        '/^\/source\/Data\/.*$/i',
    );
    
    public function execAction() {
        if(IS_POST) {
            $u = new Utility();
            $schemas = array();
            foreach($this->coreTables as $table) {
                $schemas[] = $u->dbTableSchema($table);
            }
            $install = file_get_contents(ADDON_CURRENT_PATH . 'Data/install.php');
            $install = str_replace('//{init-db-schemas}', serialize($schemas), $install);
            $install = str_replace('//{$init-db-datas}', trim($this->coreDatas), $install);
            $zip = new \ZipArchive();
            $tmpFile = ADDON_CURRENT_PATH . 'Data/package.zip';
            @unlink($tmpFile);
            $zip->open($tmpFile, \ZipArchive::CREATE);
            $release = I('post.release');
            $ver = <<<DOC
<?php
define('MB_VERSION', '1.0.0');
define('MB_RELEASE', '{$release}');
DOC;
            file_put_contents(MB_ROOT . 'source/Conf/version.inc.php', $ver);
            $files = File::tree(MB_ROOT);
            foreach($files as $file) {
                $local = substr($file, strlen(MB_ROOT));
                $isIgnore = false;
                foreach($this->ignores as $ig) {
                    if(preg_match($ig, $local)) {
                        $isIgnore = true;
                        break;
                    }
                }
                if(!$isIgnore) {
                    if(substr($local, -4) == '.php' && !preg_match('/^\/source\/ThinkPHP\/.*$/i', $local)) {
                        $content = $this->trimComments($file);
                        if(preg_match('/^\/source\/const\.inc\.php$/i', $local)) {
                            $content = preg_replace('/^.*define\(\'APP_DEBUG\'.*$\n/m', '', $content);
                        }
                        $zip->addFromString("upload{$local}", $content);
                    } else {
                        $zip->addFile($file, "upload{$local}");
                    }
                }
            }

            $zip->addEmptyDir('upload/addons');
            $zip->addEmptyDir('upload/attachment/qr');
            $zip->addEmptyDir('upload/attachment/media/alipay');
            $zip->addEmptyDir('upload/source/Data/Logs/Api');
            $zip->addEmptyDir('upload/source/Data/Logs/Wander');
            $zip->addEmptyDir('upload/source/Data/Logs/Bench');
            $zip->addEmptyDir('upload/source/Data/Logs/App');
            $zip->addEmptyDir('upload/source/Data/Runtime/Web');
            $zip->addEmptyDir('upload/source/Data/Runtime/App');
            $zip->addFromString('upload/install.php', $install);
            $zip->close();
            $version = MB_VERSION;
            $filename = "MicroBuilder-V{$version}-Release({$release})";
            header('content-type: application/zip');
            header('content-disposition: attachment; filename="' . $filename . '.zip"');
            readfile($tmpFile);
            @unlink($tmpFile);
        }
        $release = date('YmdHi', TIMESTAMP - TIMESTAMP % 1800 + 1800);
        $this->assign('release', $release);
        $this->display('Publish/exec');
    }
    
    private function trimComments($src, $header = '') {
        if(is_file($src)) {
            if(!$src = file_get_contents($src)) {
                return false;
            }
        }
        $tokens = token_get_all($src);
        if(empty($header)) {
            $header = <<<DOC
/**
 * [MicroBuilder System] Copyright (c) 2014 MICROBUILDER.CN
 * MicroBuilder is NOT a free software, it under the license terms, visited http://www.microb.cn/ for more details.
 */
DOC;
        }
        $header = trim($header);
        $des = '';
        foreach($tokens as $token) {
            if(is_array($token)) {
                list($tn, $ts) = $token; // tokens: number, string, line
                if($tn == T_COMMENT || $tn == T_DOC_COMMENT) {
                } else {
                    $des .= $ts;
                    if($tn == T_OPEN_TAG) {
                        $des .= $header;
                        $des .= "\n";
                    }
                }
            } else {
                $des .= $token;
            }
        }
        return $des;
    }
    
    public function addonsAction() {
        $addons = array();
        $path = MB_ROOT . 'addons/';
        if (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($addonpath = readdir($handle))) {
                    if($addonpath != '.' && $addonpath != '..') {
                        $define = Addon::getAddon($addonpath, true);
                        if(!is_error($define)) {
                            $addons[] = $define;
                        }
                    }
                }
            }
        }
        if(IS_POST) {
            $a = I('post.addon');
            $addons = coll_key($addons, 'name');
            $addon = $addons[$a];
            if(!empty($addon)) {
                $zip = new \ZipArchive();
                $tmpFile = ADDON_CURRENT_PATH . 'Data/package.zip';
                @unlink($tmpFile);
                $zip->open($tmpFile, \ZipArchive::CREATE);
                $root = MB_ROOT . "addons/{$a}";
                $files = File::tree($root);
                foreach($files as $file) {
                    $local = substr($file, strlen($root));
                    if(substr($local, -4) == '.php') {
                        if(I('post.trim') != '') {
                            $content = $this->trimComments($file, I('post.license'));
                        }
                        $zip->addFromString("{$a}{$local}", $content);
                    } else {
                        $zip->addFile($file, "{$a}{$local}");
                    }
                }
                $zip->close();
                $version = MB_VERSION;
                $filename = "{$a}-v{$addon['version']} (for MB-{$version})";
                header('content-type: application/zip');
                header('content-disposition: attachment; filename="' . $filename . '.zip"');
                readfile($tmpFile);
                @unlink($tmpFile);
            }
        }
        
        $this->assign('addons', $addons);
        $this->display('addons');
    }
}