<?php
/**
 * SaeDisk class file.
 *
 * @author Biner <huanghuibin@gmail.com>
 * @link http://yiidemo.sinaapp.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

class SaeDisk #implements SaeDiskInterface
{
    private $_domain;
    private $_storage;

    private $_mimeTypes = array(
        //applications
        'ai'    => 'application/postscript',
        'eps'   => 'application/postscript',
        'exe'   => 'application/octet-stream',
        'doc'   => 'application/vnd.ms-word',
        'xls'   => 'application/vnd.ms-excel',
        'ppt'   => 'application/vnd.ms-powerpoint',
        'pps'   => 'application/vnd.ms-powerpoint',
        'pdf'   => 'application/pdf',
        'xml'   => 'application/xml',
        'odt'   => 'application/vnd.oasis.opendocument.text',
        'swf'   => 'application/x-shockwave-flash',
        // archives
        'gz'    => 'application/x-gzip',
        'tgz'   => 'application/x-gzip',
        'bz'    => 'application/x-bzip2',
        'bz2'   => 'application/x-bzip2',
        'tbz'   => 'application/x-bzip2',
        'zip'   => 'application/zip',
        'rar'   => 'application/x-rar',
        'tar'   => 'application/x-tar',
        '7z'    => 'application/x-7z-compressed',
        // texts
        'txt'   => 'text/plain',
        'php'   => 'text/x-php',
        'html'  => 'text/html',
        'htm'   => 'text/html',
        'js'    => 'text/javascript',
        'css'   => 'text/css',
        'rtf'   => 'text/rtf',
        'rtfd'  => 'text/rtfd',
        'py'    => 'text/x-python',
        'java'  => 'text/x-java-source',
        'rb'    => 'text/x-ruby',
        'sh'    => 'text/x-shellscript',
        'pl'    => 'text/x-perl',
        'sql'   => 'text/x-sql',
        // images
        'bmp'   => 'image/x-ms-bmp',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'png'   => 'image/png',
        'tif'   => 'image/tiff',
        'tiff'  => 'image/tiff',
        'tga'   => 'image/x-targa',
        'psd'   => 'image/vnd.adobe.photoshop',
        //audio
        'mp3'   => 'audio/mpeg',
        'mid'   => 'audio/midi',
        'ogg'   => 'audio/ogg',
        'mp4a'  => 'audio/mp4',
        'wav'   => 'audio/wav',
        'wma'   => 'audio/x-ms-wma',
        // video
        'avi'   => 'video/x-msvideo',
        'dv'    => 'video/x-dv',
        'mp4'   => 'video/mp4',
        'mpeg'  => 'video/mpeg',
        'mpg'   => 'video/mpeg',
        'mov'   => 'video/quicktime',
        'wm'    => 'video/x-ms-wmv',
        'flv'   => 'video/x-flv',
        'mkv'   => 'video/x-matroska'
        );

    protected $filesInfo = array();
    /*
    * 构造函数
    */
    function __construct($domain = 'uploads',$k='',$sk='')
    {
        $this->_domain = $domain;
        $this->_storage = new SaeStorage($k,$sk);
    }

    /*
    * 获取文件列表 默认会将文件做筛选，不显示子目录的文件
        *.txt 表示搜索 所有*.txt
        * / 表示搜索 所有目录 （假设以/结尾则是目录）
        aaa/ 表示搜索 aaa目录下的文件 
    */
    public function get_list($dir_name,$show_all = false,$show_dir = false)
    {
        if (substr($dir_name, 0,1) == DIRECTORY_SEPARATOR) {
            $dir_name = substr($dir_name, 1,strlen($dir_name));
        }
        if (substr($dir_name, -1) == DIRECTORY_SEPARATOR) {
            #$dir_name = substr($dir_name, 0, -1);
        }
        $prefix = $dir_name;
        $prefix = str_replace(' ','',$prefix);
        if(empty($prefix))
        {
            $prefix = '*';
        }
        
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        $ls = $s->getList( $domain,$prefix);
        if(!empty($ls))
        {
            //排除本身
            if(!$show_all)
            {
                foreach($ls as $key => $one)
                {
                    //不显示子目录
                    $tmp = str_replace($ls[0],'',$one);
                    
                    $bo = strpos($tmp,'/');
                    $lenth = strlen($tmp);
                    #var_dump($bo);var_dump($lenth);var_dump($tmp);echo '<br/><br/>';
                    if($bo > 1 AND $lenth > $bo+1)
                    {
                        unset($ls[$key]);
                    }
                }
            }
            unset($ls[0]);

            if($show_dir == false)
            {
                foreach($ls as $key => $one)
                {
                    if(substr($one, -1) == DIRECTORY_SEPARATOR)
                    {

                        unset($ls[$key]);
                        continue;                       
                    }
                }
            }
        }
        #var_dump($prefix);var_dump($ls);
        return $ls;
    }

    /*
    * 目录名
    $fold 为 true 则不遍历子目录
    return 数组结构
     array(
        'dirNum'=>''
        'dirNum'=>''
        'dirs'=>array()
        'files'=>array()
     );
    */
    public function get_files($dir_name,$fold = true)
    {
        $dir_name = $this->formart_dir($dir_name);
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();

        $ls = $s->getListByPath($domain,$dir_name,1000,0,$fold);
        $rs = $ls['files'];
        if(!empty($rs))
        {
            $arr = explode("/",$dir_name);
            $auto_sina_name = $dir_name."/".end($arr);

            foreach($rs as $key=> $tmp)
            {
                $fullName =  $tmp['fullName'];
                if(substr($fullName, -1) == DIRECTORY_SEPARATOR or $fullName == $auto_sina_name)
                {
                    unset($rs[$key]);
                    continue;   
                }
                # 和谐掉新浪自创的文件名 - -
                $mine = $this->getMimeType($tmp['Name']);
                if($tmp['length'] == 26 and empty($mine))
                {
                    unset($rs[$key]);
                    continue;   
                }

                $rs[$key]['fileName'] = $tmp['fullName'];
            }
        }
        return $rs;

    }

    /*
    * 获得文件夹列表
    */
    public function get_dirs($dir_name,$fold  = true)
    {
        $dir_name = $this->formart_dir($dir_name);
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        $ls = $s->getListByPath($domain,$dir_name,1000,0,$fold);
        $rs = $ls['dirs'];
        if(!empty($rs))
        {
            foreach($rs as $key=> $tmp)
            {
                $rs[$key]['fileName'] = $tmp['fullName'];
            }
        }
        return $rs;
    }
    
    /*
    * 上传文件
    */
    public function upload_file($destFileName,$srcFileName,$attr)
    {
        if(empty($srcFileName))
        {
            return false;
        }
        if(empty($attr['type']))
        {
            //根据后缀名获得文件类型
            $attr['type'] = $this->getMimeType($destFileName);
        }

        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        
        $rs = $s->upload($domain, $destFileName, $srcFileName, $attr);
        $err = $s->errmsg();
        return $rs;
    }
    /*
    * 获得文件资料
        $attr = array(
            'type'=>'文件类型',
            'length'=>文件长度,
            'datetime'=>'添加时间'
        )
        Array
        (
            [fileName] => bbb/222.txt #文件名
            [length] => 1 #文件长度
            [datetime] => 1307091828 #添加时间
            [type] => text/plain #文件类型
        )
     * 目前支持的文件属性
     *  - expires: 浏览器缓存超时，功能与Apache的Expires配置相同
     *  - encoding: 设置通过Web直接访问文件时，Header中的Content-Encoding。
     *  - type: 设置通过Web直接访问文件时，Header中的Content-Type。

    */
    public function get_file_info($file_name)
    {
            $info = $this->filesInfo[$file_name];
        if(empty($info))
        {
            $domain = $this->getAssetsDomain();
            $s = $this->getDiskStorage();
            $info = $s->getAttr($domain,$file_name);
            if(!empty($info) AND empty($info['type']))
            {
                $info['type'] = $this->getMimeType($file_name);
            }
            $this->filesInfo[$file_name] = $info;
        }

        return $info;
    }
    /*
    * 检测是否目录
    */
    public function is_dir($dir_name)
    {
        if($dir_name == '' OR $dir_name =='/')
        {
            return true;
        }
        if (substr($dir_name, -1) == DIRECTORY_SEPARATOR) {
            return true;
        }
        return false;

        $info = $this->get_file_info($dir_name);
        return $info['type'] == 'directory';
    }
    /*
    * 创建目录
    */
    public function create_dir($dir_name)
    {
        if(empty($dir_name))
        {
            return false;
        }
        $attr = array('type'=>'good');
        #目录名加上 / 则为目录
        $file_name = $dir_name.DIRECTORY_SEPARATOR;
        #WARING 我觉得新浪抄了我的创意.... 目录
        $content = 'this is a sae dir';

        $rs = $this->write_file($file_name ,$content ,$attr);
        return $rs;
    }
    /*
    * 创建文件
    */
    public function create_file($file_name,$content = ' ')
    {
        $rs = $this->write_file($file_name ,$content);
        return $rs;
    }
    /*
    * 写文件
    */
    public function write_file($file,$content,$attr = array())
    {
        if(empty($file))
        {
            return false;
        }
        
        if (substr($file, 0,1) == DIRECTORY_SEPARATOR) {
            $file = substr($file, 1,strlen($file));
        }

        $file = str_replace('//','/',$file);

        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        if(empty($attr['type']))
        {
            $attr['type'] = $this->getMimeType($file);
        }
        if(empty($content))
        {
            $content = ' ';
        }
        #$file = str_replace(' ','',$file);

        $rs = $s->write($domain, $file ,$content ,-1, $attr);
        return $rs;
    }
    /*
    * 文件复制
    */
    public function copy_file($old_name,$new_name)
    {
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        //判断是否存在
        /*
        $tx = $this->get_file_info($new_name);
        if($tx)
        {
            return false;
        }
        */
        //读取数据
        $attr = $s->getAttr($domain,$old_name);

        $content = $this->read_file($old_name);
        $exists = $this->write_file($new_name,$content,$attr);
        if($exists)
        {
            #$exists = $this->delete_file($old_name);
        }
        return $exists;
    }
    /*
    * 文件重命名
    */
    public function rename_file($old_name,$new_name)
    {
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        //判断是否存在
        /*
        $tx = $this->get_file_info($new_name);
        if($tx)
        {
            return false;
        }
        */
        //读取数据
        $attr = $s->getAttr($domain,$old_name);
        $content = $this->read_file($old_name);
        $exists = $this->write_file($new_name,$content,$attr);
        if($exists)
        {
            $exists = $this->delete_file($old_name);
        }
        return $exists;
    }
    /*
    * 文件夹重命名
    */
    public function rename_dir($old_dir,$new_dir)
    {
        $old_dir = $this->formart_dir($old_dir);
        $new_dir = $this->formart_dir($new_dir);

        
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        $list = $s->getList($domain,$old_dir);
                

        #$ls = $this->get_list($old_dir,true);
        if(!empty($list))
        {
            $length = strlen($old_dir);

            foreach($list as $old_name)
            {
                $rel_name = substr($old_name, $length);
                $new_name = $new_dir.$rel_name;
                $exists = $this->rename_file($old_name,$new_name);
                #var_dump($new_name);
            }
        }
        #$exists = $this->rename_file($old_dir,$new_dir);
        #var_dump($old_dir);var_dump($new_dir);$s = $this->errmsg();var_dump($s);
        return $exists;
    }
    /*
    * 移动文件
    */
    public function move_file($old_name,$new_name)
    {
        return $this->rename_file($old_name,$new_name);
    }
    /*
    * 读取文件内容
    */
    public function read_file($file)
    {
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        $content = $s->read($domain,$file);
        return $content;
    }
    /*
    * 文件是否存在
    */
    public function file_exists($file = '')
    {
	$domain = $this->getAssetsDomain();
      	$s = $this->getDiskStorage();
        $exists = $s->fileExists($domain,$file);
        return $exists;
    }
    /*
    * 文件夹是否存在
    */
    public function dir_exists($dir = '')
    {        
        $dir_name = $this->formart_dir($dir);
      	$dir = $dir_name."/";
        
        $exists = $this->file_exists($dir);  
        return $exists;
    }
    
    /*
    * 删除目录
    */
    public function delete_dir($dir)
    {
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        $dir_name = $this->formart_dir($dir);

        $list = $s->getList($domain,$dir_name);

        if(!empty($list))
        {
            foreach($list as $one)
            {
                $exists = $this->delete_file($one);
            }
        }
        #$exists = $s->upload($domain, $old_name,$new_name);
        #$exists = $this->delete_file($dir);
        return $exists;
    }
    /*
    * 删除文件
    */
    public function delete_file($file)
    {
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        $exists = $s->delete($domain, $file);
        return $exists;
    }
    /*
    * 文件夹是否有子目录
    */
    public function hasChildren($dir_name)
    {
        $dir_name = $this->formart_dir($dir_name);
        $s = $this->getDiskStorage();
        $domain = $this->getAssetsDomain();
        $ls = $s->getListByPath($domain,$dir_name);
        return $ls['dirNum']>0;
    }
    /*
    * 获得domain
    */
    private function getAssetsDomain()
    {
        return $this->_domain;
    }
    /*
    * 获得STORAGE 类
    */
    private function getDiskStorage()
    {
        return $this->_storage;
    }
    /*
    * 根据文件名获得 mime信息
    */
    public function getMimeType($file)
    {
        //获得后缀名
        $hx = '';
        $extend =explode(".", $file);
        if(!empty($extend))
        {
            $va=count($extend)-1;
            $hx= $extend[$va];
        }
        $type = '';
        //根据后缀名获得 mimetype
        if(!empty($this->_mimeTypes[$hx]))
        {
            $type = $this->_mimeTypes[$hx];
        }
        return $type;
    }
    /*
    * 获得web访问路径
    */
    public function getWebUrl($path = '')
    {
        $domain = $this->getAssetsDomain();
        $s = $this->getDiskStorage();
        $url = $s->getUrl($domain,$path);
        return $url;
    }
    /*
    * 错误代码
    */
    public function errmsg()
    {
        return $this->getDiskStorage()->errmsg();
    }
    /*
    * 目录格式化
    */
    public function formart_dir($dir_name)
    {
        $dir_name = trim($dir_name, DIRECTORY_SEPARATOR);
        return $dir_name;
     }
}

/**
 * SaeInterface , public interface of all sae client apis
 *
 * all sae client classes must implement these method for setting accesskey and secretkey , getting error infomation.
 * @package sae
 **/
interface SaeDiskInterface
{
    public function errmsg();
    public function errno();

    public function upload_share_file($file);
    public function get_list($dir_name);
    public function get_quota();
    public function upload_with_md5($file);
    public function get_file_info($file_name);
    public function create_dir($dir_name,$path);
    public function delete_dir($dir_name,$path);
    public function delete_file($file_name,$path);
    public function copy_file($file_source, $file_to, $new_name);
    public function move_file($file_source, $file_to, $new_name);
    public function rename_file($file, $new_name);

    public function rename_dir($dir_name, $new_name);
    public function move_dir($dir_source, $dir_to, $new_name);
}
