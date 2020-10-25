<?php
/**
 * PHP 生成zip压缩文件类 <br />
 *  支持两种形式传入文件 <br/>
 * 	1.直接传入需要打包的文件的路径 <br />
 * 	2.通过表单浏览上传多个文件进行打包 <br />
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */
namespace herosphp\files;

class PHPZip {

	/* 要压缩的文件路径 */
	private $filePath;

	/* 得到的压缩文件名称 */
	private $zipName;

	/* ZipArchive类对象的一个引用 */
	private $zip;

	/* 各个文件夹在压缩包中的相对路径, hash数组 */
	private $zipDir = array();

	/**
	 * constructor (构造函数)
	 */
	public function __construct() {
		//check the Apache server is surpport zip compress.
		if ( !@extension_loaded('zip') && !@function_exists("gzcompress") ) {
			if ( APP_DEBUG ) {
                E("当前服务器不支持压缩，请更改PHP的相关配置。");
            }
		}
		@set_time_limit(0);

        //实例化压缩类
        $this->zip = new \ZipArchive();
	}

    /**
     * 创建zip压缩文件
     * @param $src
     * @param $zip
     * @return bool
     */
    public function createZip($src, $zip) {

		if ( !file_exists($src) ) {
            if ( APP_DEBUG ) {
                E("压缩源文件不存在!");
            }
        }

        if ( !$zip ) {
            if ( APP_DEBUG ) {
                E("压缩目标文件不能为空！");
            }
        }

        $this->filePath = $src;
        $this->zipName = $zip;

		//创建压缩文件
		if ( !file_exists($this->zipName) ) {
			if ( $this->zip->open($this->zipName, \ZIPARCHIVE::CREATE) == FALSE ) {
				if ( APP_DEBUG ) {
                    E("创建压缩文件失败");
                }
			}
		} else {
			if ( $this->zip->open($this->zipName, \ZIPARCHIVE::OVERWRITE) == FALSE ) {
				if ( APP_DEBUG ) {
                    E("创建压缩文件失败");
                }
			}
		}

		if ( !is_array($this->filePath) ) {
			$this->addFilesToZip($this->filePath);
		} else {
			foreach ( $this->filePath as $_val ) {
				$this->addFilesToZip($_val);
			}
		}
		$this->zip->close();
		if ( file_exists($this->zipName) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

    /**
     * 解压文件到目标位置
     * @param $zip  zip文件
     * @param $dst  目标文件
     * @return boolean
     */
    public function extractZip($zip, $dst) {

        if ( $this->zip->open($zip) == TRUE ) {
           return $this->zip->extractTo($dst);
        }
        return false;
    }

    /**
     * method to add files to zip pack file. (添加文件到zip压缩包,如果是目录采用递归添加)
     * @param string $_files 需要打包的文件或者文件夹
     * @param string $_zipDir
     */
	protected function addFilesToZip($_files, $_zipDir = NULL) {

		if ( is_dir($_files) ) {
			if ( ($handle = opendir($_files)) != FALSE ) {
				while ( ($filename = readdir($handle)) != FALSE ) {
					if ( $filename != '..' && $filename != '.' ) {
						if ( is_dir($_files.'/'.$filename) ) {
							//在压缩文件中新建目录
							$_new_dir = $_zipDir == NULL ? $filename : $_zipDir.'/'.$filename;
							/**
							 * 此处很重要，保存每个文件夹(绝对路径)内的文件压缩后的localname,在zip压缩文件
							 * 中的相对路径,必须保存正确，否则文件将不会添加到zip中，只有文件夹。此处可以保证
							 * 文件夹打包后所有文件的相对路径(即文件目录树结构)是不变的。
							 */
							$this->zipDir[$_files.'/'.$filename] = $_new_dir;
							$this->zip->addEmptyDir($_new_dir);
							$this->addFilesToZip($_files.'/'.$filename, $_new_dir);
						} else {
							$zipName = empty($this->zipDir) ? $filename : $this->zipDir[$_files].'/'.$filename;
							$this->zip->addFile($_files.'/'.$filename, $zipName);
						}
					}
				}
				closedir($handle);
			}
		} else {
			$this->zip->addFile($_files, basename($_files));
		}
	}

}
