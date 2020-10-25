<?php
/**
 * 文件上传类, 支持多文件上传不重名覆盖。支持base64编码文件上传
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */
namespace herosphp\files;

class FileUpload {

    /**
     * @var array 上传文件配置参数
     */
    protected $config = array(
        //上传文件的根目录
        'upload_dir' => __DIR__,
        //允许上传的文件类型
        'allow_ext' => 'jpg|jpeg|png|gif|txt|pdf|rar|zip|swf|bmp|c|java|mp3',
        //图片的最大宽度, 0没有限制
        'max_width' => 0,
        //图片的最大高度, 0没有限制
        'max_height' => 0,
        //文件的最大尺寸
        'max_size' =>  1024000,     /* 文件size的最大 1MB */
     );

    /**
     * @var array 上传文件的信息
     * local_name => 上传文件的客户端名称
     * file_size => 文件大小
     * file_name => 上传文件的新的文件名
     * file_ext => 上传文件的后缀名称
     * file_type => 本次上传的mine类型
     * is_image => 是否是图片
     * image_width => 图片宽度
     * image_height => 图片高度
     * file_path => 文件的绝对路径
     * file_name => 文件名(带后缀)
     * file_ext => 文件后缀
     * raw_name => 文件名(不带后缀)
     */
    protected $fileInfo = array();

    /**
     * @var string 上传文件的后缀名
     */
    protected $extension = '';

    /**
     * @var int 上传的错误代码
     */
    protected $errNum = 0;

    /**
     * @var array 上传的状态信息
     */
    protected static $_UPLOAD_STATES = array(
        '0'		=> 	'文件上传成功',
        '1'		=> 	'文件超出大小限制',
        '2'		=> 	'文件的大小超过了HTML表单指定值',
        '3'		=> 	'文件只有部分被上传',
        '4'		=> 	'文件没有被上传',
        '5'		=> 	'不允许的文件类型',
        '6'		=> 	'上传目录创建失败',
        '7'		=> 	'文件保存时出错',
        '8'		=> 	'base64解码IO错误',
        '9'		=> 	'文件尺寸超出了限制',
        '10'		=> 	'文件没有上传到服务器',
	);

    /**
     * constructor
     * @param        array() $_config
     * @notice        $_config keys  upload_dir, allowExt, max_szie
     */
	public function __construct($_config) {

		if ( !isset($_config) ) return FALSE;

		$this->config = array_merge($this->config, $_config);
		ini_set('upload_max_filesize', ceil( $this->config['max_size'] / (1024*1024) ).'M');
	}

    /**
     * upload file method.
     * @param        sting $_field name of form elements.
     * @param        bool $_base64
     * @return       mixed false or file info array.
     */
	public function upload($_field, $_base64 = false) {

        if ( !$this->checkUploadDir() ) {
            $this->errNum = 6;
            return false;
        }

		if ( $_base64 ) {
			$_data = $_POST[$_field];
			return $this->makeBase64Image( $_data );
		}

		$_localFile = $_FILES[$_field]['name'];
        if ( !$_localFile ) {
            $this->errNum = 10;
            return false;
        }
		$_tempFile = $_FILES[$_field]['tmp_name'];//原来是这样
        //$_tempFile = str_replace('\\\\', '\\', $_FILES[$_field]['tmp_name']);//MAGIC_QUOTES_GPC=OFF时，做了这样处理：$_FILES = daddslashes($_FILES);图片上传后tmp_name值变成 X:\\Temp\\php668E.tmp，结果move_uploaded_file() 函数判断为不合法的文件而返回FALSE。
		$_error_no = $_FILES[$_field]['error'];
        $this->fileInfo['file_type'] = $_FILES[$_field]['type'];
        $this->fileInfo['local_name'] = $_localFile;
        $this->fileInfo['file_size'] = $_FILES[$_field]['size'];

        $this->errNum = $_error_no;
        if ( $this->errNum == 0 ) {
            $this->checkFileType($_localFile);
            if ( $this->errNum == 0 ) {
                $this->checkFileSize($_tempFile);
                if ( $this->errNum == 0 ) {
                    if ( is_uploaded_file($_tempFile) ) {
                        $_new_filename = $this->getFileName($_localFile);
                        $this->fileInfo['file_path'] = $this->config['upload_dir'].DIRECTORY_SEPARATOR.$_new_filename;
                        if ( move_uploaded_file($_tempFile, $this->fileInfo['file_path']) ) {

                            $_filename = $_new_filename;
                            $this->fileInfo['file_name'] = $_filename;
                            $pathinfo = pathinfo($this->fileInfo['file_path']);
                            $this->fileInfo['file_ext'] =  $pathinfo['extension'];
                            $this->fileInfo['raw_name'] = $pathinfo['filename'];

                            return $this->fileInfo;

                        } else {
                            $this->errNum = 7;
                        }
                    }
                }
            }
        }

		return false;

	}

    /**
     * 接收base64位参数，转存图片
     * @param $_base64_data
     * @return bool|string
     */
    protected function makeBase64Image($_base64_data) {

		$_img = base64_decode($_base64_data);
        $this->fileInfo['local_name'] = time().".png";
		$_filename = $this->getFileName($this->fileInfo['local_name']);
        $this->fileInfo['file_name'] = $_filename;
        $this->fileInfo['file_path'] = $this->config['upload_dir'].DIRECTORY_SEPARATOR.$_filename;
		if ( file_put_contents($this->fileInfo['file_path'], $_img) && file_exists($this->fileInfo['file_path']) ) {

            $size = getimagesize($this->fileInfo['file_path']);
            if ( ($this->config['max_width'] > 0 && $size[0] > $this->config['max_width'])
                || ($this->config['max_height'] > 0 && $size[1] > $this->config['max_height']) )  {

                $this->errNum = 9;
                return false;

            }
            $this->fileInfo['image_width'] = $size[0];
            $this->fileInfo['image_height'] = $size[1];
            //初始化mimeType
            $this->fileInfo['file_type'] = "image/png";
            $this->fileInfo['is_image'] = 1;
            $this->fileInfo['file_size'] = filesize($this->fileInfo['file_path']);

            $pathinfo = pathinfo($this->fileInfo['file_path']);
            $this->fileInfo['file_ext'] =  $pathinfo['extension'];
            $this->fileInfo['raw_name'] = $pathinfo['filename'];

            return $this->fileInfo;
		}
		$this->errNum = 8;

        return false;
	}

    /**
     * 获取新的文件名
     * @param $filename
     * @return string
     */
    public function getFileName($filename) {

		$_ext = $this->getFileExt($filename);
		return time().'-'.mt_rand(100000, 999999).'.'.$_ext;

	}

    /**
     * 检测上传目录
     * @return bool
     */
    protected function checkUploadDir() {
		if ( !file_exists($this->config['upload_dir']) ) {
			return self::makeFileDirs($this->config['upload_dir']);
		}
		return true;
	}

    /**
     * 创建多级目录
     * @param $path
     * @return bool
     */
    public static function makeFileDirs($path) {
        //必须考虑 "/" 和 "\" 两种目录分隔符
        $files = preg_split('/[\/|\\\]/s', $path);
        $_dir = '';
        foreach ($files as $value) {
            $_dir .= $value.DIRECTORY_SEPARATOR;
            if ( !file_exists($_dir) ) {
                mkdir($_dir);
            }
        }
        return true;

    }

    /**
     * 检测文件类型是否合法
     * @param $filename
     * @return boolean
     */
    protected function checkFileType($filename) {

        if ( $this->config['allow_ext'] == '*' ) {
            return true;
        }
		$_ext = strtolower(self::getFileExt($filename));
        $_allow_ext = explode("|", $this->config['allow_ext']);
		if ( !in_array($_ext, $_allow_ext) ) {
			$this->errNum = 5;
            return false;
		}
        return true;
	}

    /**
     * 获取文件名后缀
     * @param $filename
     * @return string
     */
    protected function getFileExt($filename) {
        $_pos = strrpos($filename, '.');
        return strtolower(substr($filename , $_pos+1));
    }

    /**
     * 检查文件大小是否合格
     * @param $filename
     */
    protected function checkFileSize($filename) {

        if ( filesize($filename) > $this->config['max_size'] ) {
            $this->errNum = 1;
        }

        //如果是图片还要检查图片的宽度和高度是否超标
        $size = getimagesize($filename);
        if ( $size != false ) {

            $this->fileInfo['is_image'] = 1;
            if ( ($this->config['max_width'] > 0 && $size[0] > $this->config['max_width'])
                || ($this->config['max_height'] > 0 && $size[1] > $this->config['max_height']) )  {
                $this->errNum = 9;
            }
            $this->fileInfo['image_width'] = $size[0];
            $this->fileInfo['image_height'] = $size[1];

        } else {

            $this->fileInfo['is_image'] = 0;

        }

	}

    /**
     * get upload message
     * @return       string
     */
	public function getUploadMessage() {
        if ( $this->errNum == 9 ) {
            return "尺寸超出{$this->config['max_width']}x{$this->config['max_height']}";
        }
		return self::$_UPLOAD_STATES[$this->errNum];
	}

}
