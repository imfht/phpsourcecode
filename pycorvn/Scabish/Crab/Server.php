<?php
namespace Scabish\Crab;

use PHPImageWorkshop\ImageWorkshop;

/**
 * Scabish\Crab\Server
 * Crab服务端，接收上传的图片或文件，生成任意尺寸图片
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2016-6-7
 */
class Server {
    
    const VERSION = 0;
    
    private static $_instance;
    
    private $_width = 0; // 缩略图宽高
    private $_height = 0;
    
    public $_baseUri;
    private $_basePath;
    
    private function __construct() {}
    
    public function __clone() {}
    
    public static function Instance() {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 运行框架
     * @throws Exception
     */
    public function Run($basePath) {
        #$this->CheckSign() || $this->Response(false, 'Authentication failed');
        
        $this->_basePath = $basePath;
        $this->ParseBaseURI();
        
        spl_autoload_register(array(__CLASS__, 'Autoload')); // Registers the autoloader
        
        $this->IsUpload() ? $this->SaveFile() : $this->HandleFile();
    }
    
    protected function IsUpload() {
        return isset($_FILES['file']);
    }
    
    protected function SaveFile() {
        $file = $_FILES['file'];
        $file['error'] && $this->Response(false, 'file upload failed');
        $info = getimagesize($file['tmp_name']);
        if($info) { // 图片
            list($_, $extension) = explode('/', strtolower($info['mime']));
        } else { // 其他文件
            $extension = substr($file['name'], strrpos($file['name'], '.') + 1);
        }
        
        $filePath = date('Y/m/d').'/'.md5(microtime()).'.'.$extension;
        $fullPath = $this->_basePath.'/image/'.$filePath;
        $fullDir = dirname($fullPath);
        (file_exists($fullDir) && is_dir($fullDir)) || mkdir($fullDir, 0777, true);
        rename($file['tmp_name'], $fullPath);
        chmod($fullPath, 0555);
        
        $this->Response(true, 'http://'.$_SERVER['HTTP_HOST'].$this->_baseUri.'/image/'.$filePath);
    }
    
    private function ParseBaseURI() {
        $self = strtolower(trim($_SERVER['SCRIPT_NAME'], '/'));
        $baseUri = substr($self, 0, strpos($self, 'index.php'));
        $this->_baseUri = $baseUri ? '/'.trim($baseUri, '/') : '';
    }
    
    protected function HandleFile() {
        $uri = $_SERVER['REQUEST_URI'];
        if(false !== ($paramPos = strpos($uri, '-'))) {
            $param = substr($uri, $paramPos, strrpos($uri, '.') - $paramPos);
            $pattern = substr_count($param, '-') > 1 ? '/^\-(.+)\-(\d+)*$/' : '/^\-(.+)$/';
            preg_match($pattern, $param, $matches);
            if(isset($matches[1])) $this->_width = intval($matches[1]);
            if(isset($matches[2])) $this->_height = intval($matches[2]);
        }
        $fileName = preg_replace('/\-.*(?=\.)/', '', $uri);
        $file = $_SERVER['DOCUMENT_ROOT'].'/'.ltrim($fileName, '/');
        if(file_exists($file) && is_file($file)) {
            $info = getimagesize($file);
            $info ? $this->GetThumb($file) : $this->Download($file);
        }
    }
    
    protected function Download($file) {
        $fp = fopen($file, "rb");
        $file_size = filesize($file);
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length:".$file_size);
        header("Content-Disposition: attachment; filename=".basename($file));
        $buffer = 1024;
        $file_count = 0;
        while(!feof($fp) && $file_count < $file_size){
            $file_con = fread($fp,$buffer);
            $file_count += $buffer;
            echo $file_con;
        }
        fclose($fp);
    }
    
    protected function GetThumb($file) {
        $info = getimagesize($file);
        if(!$this->_width && !$this->_height) { // origin
            $imagePath = $file;
        } else {
            if(!$this->_width) {
                $this->_width = intval($this->_height*$info[0]/$info[1]);
            }
            if(!$this->_height) {
                $this->_height = intval($this->_width*$info[1]/$info[0]);
            }
            $imagePath = $this->GetThumbName($file);
            $w = $info[0];
            $h = $info[1];
            $scaleW = $w/$this->_width;
            $scaleH = $h/$this->_height;
            $scale = $scaleW > $scaleH ? $scaleH : $scaleW;
            $w = intval($w/$scale);
            $h = intval($h/$scale);
            if(!(file_exists($imagePath) && is_file($imagePath))) {
                $layer = ImageWorkshop::initFromPath($file);
                $layer->resizeInPixel($w, $h, false);
                $layer->cropInPixel($this->_width, $this->_height, 0, 0, 'MM');
                $layer->save(dirname($imagePath), basename($imagePath));
            }
        }
        
        $image = file_get_contents($imagePath);
        header('Cache-Control: max-age=86400');
        header('Content-type: '.$info['mime']);
        echo $image;
        
    }
    public function GetThumbName($file) {
        $info = getimagesize($file);
        list($_, $extension) = explode('/', strtolower($info['mime']));
        preg_match('/\/image(.+)(\.\w+)$/', $file, $match);
        
        return $this->_basePath.'/image/'.trim($match[1], '/').'-'.($this->_width ? : '').($this->_height ? '-'.$this->_height : '').'.'.$extension;
    }
    
    /**
     * 返回json信息
     * @param boolean $status 结果状态
     * @param mixed $data 返回数据
     */
    public function Response($status, $data = '') {
        header('Content-type: application/json; charset=utf-8');
        die(json_encode([
            'status' => $status ? 1 : 0, // 成功状态码
            'data' => $data, // 数据
        ]));
    }
    
    private function CheckSign() {
        // @todo check $_GET[id],$_GET[time],$_GET[sign]
        
        return true; 
    }
    
    public static function Autoload($class) {
        $fileName = '';
        $className = $class;
        if (false !== ($lastNsPos = strripos($className, '\\'))) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', '/', $namespace).'/';
        }
        $fileName .= str_replace('_', '/', $className).'.php';
        $fullFileName = __DIR__.'/'.$fileName;
        if (file_exists($fullFileName)) {
            require $fullFileName;
        } else {
            throw new \Exception('Class "'.$class.'" does not exist.');
        }
    }
        
    public function __destruct() {
        
    }
}