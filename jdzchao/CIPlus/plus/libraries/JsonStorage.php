<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.abstract.php';

/**
 * Class JsonStorage
 * ===================================================================
 * 保存Json为文件
 * 读取本地Json文件
 * ===================================================================
 * Version 1.0.0
 * Create by LeeNux @ 2016-3-2
 * Update by LeeNux @ 2016-3-2
 */
Class JsonStorage extends \CIPlus\CIClass {
    protected $save_path = '';
    protected $save_extension = '';
    protected $security_extension = "";
    
    public function __construct(array $params = array()) {
        parent::__construct($params);
        // 优先加载系统配置
        $this->loadConf('json_storage');
        // 加载用户配置，可覆盖之前配置
    }
    
    /**
     * 读取json文件
     * @param $fileName :文件名
     * @param $toArray :是否转为数组
     * @return bool|mixed|string
     */
    public function read($fileName, $toArray = true) {
        $file_path = $this->save_path . $fileName . '.' . $this->save_extension;
        if (file_exists($file_path)) {
            $json = file_get_contents($file_path);
            if ($toArray) {
                $json = json_decode($json, true);
            }
            return $json;
        } else {
            return false;
        }
    }
    
    /**
     * 写入json文件
     * @param $fileName
     * @param $str
     * @return bool
     */
    public function write($fileName, $str) {
        // 将数组转换为json
        if (is_array($str)) {
            $str = json_encode($str);
        } // 将对象转换为json
        elseif (is_object($str)) {
            $str = json_encode($str);
        }
        $file_path = $this->save_path . $fileName . '.' . $this->save_extension;
        $file = fopen($file_path, 'w');
        if (file_exists($file_path)) {
            fwrite($file, $str);
            @chmod($file, 0755);
            fclose($file);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 读取需要保护的json文件
     * @param $fileName
     * @param bool $toArray
     * @return bool|mixed|string
     */
    public function load($fileName, $toArray = true) {
        $file_path = $this->save_path . $fileName . '.' . $this->security_extension;
        if (file_exists($file_path)) {
            $json = trim(substr(file_get_contents($file_path), 15));
            if ($toArray) {
                $json = json_decode($json, true);
            }
            return $json;
        } else {
            return false;
        }
    }
    
    /**
     * 写入需要保护的json文件
     * @param $fileName
     * @param $str
     * @return bool
     */
    public function save($fileName, $str) {
        // 将数组转换为json
        if (is_array($str)) {
            $str = json_encode($str);
        } // 将对象转换为json
        elseif (is_object($str)) {
            $str = json_encode($str);
        }
        $file_path = $this->save_path . $fileName . '.' . $this->security_extension;
        $file = fopen($file_path, 'w');
        if (file_exists($file_path)) {
            fwrite($file, "<?php exit();?>\n" . $str);
            @chmod($file, 0755);
            fclose($file);
            return true;
        } else {
            return false;
        }
    }
    
}
