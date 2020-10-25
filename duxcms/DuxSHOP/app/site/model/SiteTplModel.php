<?php

/**
 * 站点模板
 */
namespace app\site\model;

class SiteTplModel {



    public function tplDir() {
        $tplDir = ROOT_PATH . 'theme/';
        $tplDirs = glob($tplDir . '*', GLOB_ONLYDIR);
        if(empty($tplDirs)) {
            return [];
        }
        $dirs = [];
        foreach($tplDirs as $dir) {
                $dirs[] = basename($dir);
        }
        return $dirs;
    }

    public function tplFiles($pre = '') {
        $siteConfig = target('site/SiteConfig')->getConfig();
        $tplDir = ROOT_PATH . 'theme/' . $siteConfig['tpl_name'] . '/';
        $tplDirs = list_dir($tplDir);
        if(empty($tplDirs)) {
            return [];
        }
        $files = [];
        foreach($tplDirs as $file) {
            $file = str_replace('\\', '/', $file);
            $file = str_replace($tplDir, '', $file);
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if($ext !== 'html') {
                continue;
            }

            $value = substr($file, 0, -5);

            if(!empty($pre) && strpos($value, $pre, 0) === false) {
                continue;
            }

            $value = substr($value, strlen($pre) + 1);

            if(empty($value)) {
                continue;
            }

            $files[] = [
                'name' => $file,
                'value' => $value
            ];
        }
        return $files;
    }

}