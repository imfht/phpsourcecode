<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'plus/CIClass.abstract.php';

Class Uploader extends \CIPlus\CIClass {
    /// region <<< upload config >>>
    protected $file_save_path = '';
    protected $temp_save_path = '';
    protected $create_usage_folder = TRUE;
    protected $usages = [];
    // usage
    private $usage = '';
    private $approved_ext = [];
    private $max_file_size = 0;
    private $max_file_num = 1;
    private $fetch_hash = TRUE;
    private $rename = '';
    /// endregion
    
    private $custom_folder; // 自定义文件夹名称
    private $custom_name; // 自定义文件名
    
    public $url = '/';
    
    protected $callback_obj;
    protected $callback_func;
    
    public function __construct(array $params = array()) {
        parent::__construct($params);
        $this->loadConf('uploader');
    }
    
    /**
     * 自定义子文件夹
     * @param $folder
     * @return $this
     */
    public function folder($folder) {
        $this->custom_folder = $folder;
        return $this;
    }
    
    /**
     * 自定义文件名
     * @param $name
     * @return $this
     */
    public function name($name) {
        $this->custom_name = $name;
        return $this;
    }
    
    /**
     *  以 FILES 类型上传
     * @param $files :文件
     * @param string $usage : 文件用途（参考配置文件）
     * @return bool
     */
    public function file($files, $usage = 'image') {
        $this->_setUsage($usage);
        $keys = array_keys($files);
        $num = count($keys);
        if ($num > $this->max_file_num) $this->_error('illegalNumber');
        for ($i = 0; $i < $num; $i++) {
            if (!$this->_isUploadedFile($files[$keys[$i]])) $this->_error('illegalFile');
            $this->_uploadProcess($files[$keys[$i]]);
        }
        return true;
    }
    
    /**
     * 以 base64 字节流上传
     * @param $stream : 文件完整的base64编码字节流
     * @param $usage : 文件用途（参考配置文件）
     * @return bool
     */
    public function base64($stream, $usage = 'image') {
        if (empty($stream)) $this->_error('illegalFile');
        $this->_setUsage($usage);
        $stream = explode(',', $stream);
        $info = explode(';', $stream[0]);
        $mime = explode(':', $info[0]);
        $mime = $mime[1];
        $base64 = $stream[1];
        $tmpFile = $this->_base64SaveAsTmpFile($base64);
        $ext = $this->_base64GetExtFromMime($mime);
        $file['name'] = end(explode(DIRECTORY_SEPARATOR, $tmpFile)) . $ext;
        $file['mime'] = $mime;
        $file['tmp_name'] = $tmpFile;
        $file['error'] = 0;
        $file['size'] = filesize($tmpFile);
        $this->_uploadProcess($file);
        return true;
    }
    
    /**
     * 将 base64 写入临时文件
     * @param $base64
     * @return bool|string
     */
    private function _base64SaveAsTmpFile($base64) {
        $tmpFile = tempnam($this->temp_save_path, 'TMP0');
        $file = fopen($tmpFile, 'w+');
        fwrite($file, base64_decode($base64));
        fclose($file);
        return $tmpFile;
    }
    
    // 通过文件 base64 的 mime 类型判断文件扩展名
    private function _base64GetExtFromMime($mime) {
        $mimes = get_mimes();
        $ext = array_search($mime, $mimes);
        if (empty($ext)) {
            foreach ($mimes as $k => $v) {
                if (is_array($v)) {
                    if (in_array($mime, $v)) {
                        $ext = $k;
                        break;
                    }
                }
            }
        }
        return $ext;
    }
    
    /**
     * 文件上传进程
     * @param $file
     */
    private function _uploadProcess($file) {
        if (!$this->_isLegalExt($file)) $this->_error('illegalExt');
        if (!$this->_isLegalSize($file)) $this->_error('illegalSize');
        if (!$this->_isExistUploadPath()) $this->_error('nonexistentPath');
        if (!$this->_isWritablePath()) $this->_error('notWritablePath');
        $this->_getFileHash($file);
        $this->_setName($file);
        $this->_getFileInfo($file);
        if (!$this->_moveFileToSavePath($file)) $this->_error('notWritablePath');
    }
    
    /**
     * 设置上传文件用途类型
     * @param $usage
     */
    private function _setUsage($usage) {
        if (key_exists($usage, $this->usages)) {
            $this->usage = $usage;
            foreach ($this->usages[$usage] as $k => $v) {
                if (property_exists($this, $k)) {
                    $this->$k = $v;
                }
            }
        } else {
            $this->_error();
        }
    }
    
    /**
     * 是否为正规途径上传的文件
     * @param $file
     * @return bool
     */
    private function _isUploadedFile(&$file) {
        return is_uploaded_file($file['tmp_name']);
    }
    
    /**
     * 是否为合法的文件扩展类型
     * @param $file
     * @return bool
     */
    private function _isLegalExt(&$file) {
        $f = explode('.', $file['name']);
        $ext = strtolower(end($f));
        if (in_array($ext, $this->approved_ext)) {
            $file['ext'] = $ext;
            return true;
        }
        return false;
    }
    
    /**
     * 是否为合法的文件大小
     * @param $file
     * @return bool
     */
    private function _isLegalSize(&$file) {
        return $file['size'] <= $this->max_file_size;
    }
    
    /**
     * 是否存在文件上传路径
     * @return bool
     */
    private function _isExistUploadPath() {
        if ($this->create_usage_folder) {
            $this->file_save_path .= $this->usage . DIRECTORY_SEPARATOR;
            $this->url .= $this->usage . '/';
            $this->_mkdir($this->file_save_path);
        }
        if (!empty($this->custom_folder)) {
            $this->file_save_path .= $this->custom_folder . DIRECTORY_SEPARATOR;
            $this->url .= $this->custom_folder . '/';
            $this->_mkdir($this->file_save_path);
        }
        return is_dir($this->file_save_path);
    }
    
    /**
     * 文件上传路径是否可写
     * @return bool
     */
    private function _isWritablePath() {
        return is_writable($this->file_save_path);
    }
    
    /**
     * 创建文件夹
     * @param $path
     */
    private function _mkdir($path) {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }
    
    private function _getFileHash(&$file) {
        if ($this->fetch_hash) {
            $hash = sha1_file($file['tmp_name']);
            $file['hash'] = $hash;
            return $hash;
        }
        return false;
    }
    
    /**
     * 设置上传文件名称
     * @param $file
     * @return bool
     */
    private function _setName(&$file) {
        if (!empty($this->custom_name)) {
            $file['origin_name'] = $file['name'];
            $file['name'] = $this->custom_name . '.' . $file['ext'];
        } elseif (!empty($this->rename)) {
            $type = '_rename_' . strtolower($this->rename);
            $name = $this->$type($file) . '.' . $file['ext'];
            $file['origin_name'] = $file['name'];
            $file['name'] = $name;
        }
        $this->url .= $file['name'];
        return true;
    }
    
    // 返回文件散列
    private function _rename_hash(&$file) {
        if (empty($file['hash'])) {
            $hash = sha1_file($file['tmp_name']);
        } else {
            $hash = $file['hash'];
        }
        return $hash;
    }
    
    // 返回时间戳
    private function _rename_time(&$file) {
        return date('YmdHis', time()) . rand(100, 999);
    }
    
    /**
     * 获取上传文件信息
     * @param $file
     */
    private function _getFileInfo(&$file) {
        switch ($this->usage) {
            case 'image':
                $this->_getImageSize($file);
                break;
        }
    }
    
    // 获取上传图片的尺寸
    private function _getImageSize(&$file) {
        $size = getimagesize($file['tmp_name']);
        if (isset($size)) {
            $file['width'] = $size[0];
            $file['height'] = $size[1];
        }
    }
    
    /**
     * 将文件移动到指定的文件目录
     * @param $file
     * @return bool
     */
    private function _moveFileToSavePath(&$file) {
        $ufo = $this->file_save_path . $file['name'];
        if (rename($file['tmp_name'], $ufo) === false) {
            return FALSE;
        }
        @unlink($file['tmp_name']);
        @chmod($ufo, 0755);
        return TRUE;
    }
    
    /**
     * 错误处理
     * @param string $type
     */
    private function _error($type = '') {
        if (is_object($this->callback_obj) && method_exists($this->callback_obj, $this->callback_func)) {
            $method = $this->callback_func;
            $this->callback_obj->$method($type);
        } else {
            ob_end_clean();
            http_response_code(500);
            exit($type);
        }
    }
}