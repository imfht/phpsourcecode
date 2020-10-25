<?php defined('BASEPATH') or exit ('No direct script access allowed');

require_once CIPLUS_PATH . 'CIClass.abstract.php';

class Thumbnail extends \CIPlus\CIClass {
    protected $image_save_path;
    protected $temp_save_path;

    protected $thumb_save;
    protected $thumb_folder;
    protected $thumb_factor;
    protected $thumb_default;

    protected $cache_time;

    private $image_path;
    private $image_name;
    private $thumb_path;

    public function __construct(array $params = array()) {
        parent::__construct($params);
        $this->loadConf('thumbnail');
    }

    // 显示图片
    public function show($size, $name, $dim) {
        $this->image_path = $this->image_save_path . $name;
        $this->image_name = $name;

        if ($size !== 'origin') {
            $this->resizeImage($size, $dim);
        } else {
            $this->thumb_path = $this->image_path;
        }
        $this->showImage();
    }

    // 判断图片是否存在
    public function exist($name) {
        $path = $this->image_save_path . $name;
        return file_exists($path);
    }

    // 创建缩略图
    private function resizeImage($size, $dim) {
        $thumb_folder = $this->image_save_path . $this->thumb_folder . DIRECTORY_SEPARATOR;
        if (!is_dir($thumb_folder)) {
            mkdir($thumb_folder);
        }
        $size = is_numeric($size) ? $size : $this->thumb_default;
        $thumb_folder = $thumb_folder . $size . DIRECTORY_SEPARATOR;
        if (in_array($size, $this->thumb_factor)) {
            if (!is_dir($thumb_folder)) {
                mkdir($thumb_folder);
            }
            $this->thumb_path = $thumb_folder . $this->image_name;
            if (!file_exists($this->thumb_path)) {
                $this->createThumb($size, $dim, true);
            }
        } else {
            $this->createThumb($size, $dim, false);
        }
    }

    // 显示缩略图（或原图）
    private function showImage() {
        if ($this->cache_time > 0) {
            $this->CI->output->cache($this->cache_time);
        }
        $type = explode('.', $this->image_name);
        $this->CI->output->set_content_type(end($type))->set_output(file_get_contents($this->thumb_path));
        $this->CI->output->_display();
        if (!$this->thumb_save) {
            @unlink($this->thumb_path);
        }
        exit;
    }

    // 创建缩略图
    private function createThumb($size, $dim, $save = false) {
        if (!$save) {
            $this->thumb_path = $this->temp_save_path . $this->image_name;
            $this->thumb_save = false;
        }
        $config['image_library'] = 'gd2';
        $config['source_image'] = $this->image_path;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['file_permissions'] = 0664;
        $config['width'] = $size;
        $config['height'] = $size;
        $config['master_dim'] = $dim;
        $config['new_image'] = $this->thumb_path;
        $config['thumb_marker'] = '';
        $this->CI->load->library('image_lib', $config);
        return $this->CI->image_lib->resize();
    }
}