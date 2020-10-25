<?php
namespace app\common\util;

class Zip{
    private  $file_count = 0 ;
    private  $datastr_len   = 0;
    private  $dirstr_len = 0;
    private  $filedata = ''; //该变量只被类外部程序访问
    private  $gzfilename;
    private  $fp;
    private  $dirstr='';
    private  $zipdir = '';  //要压缩哪个目录,如果是绝对路径的话,必须要补上
    
    /**
     * 压缩文件,可以是目录
     * @param string $zip_name 压缩包名字,包括绝对路径
     * @param string $base_path 压缩目录的上级绝对路径
     * @param string $zip_pathname 具体某个压缩文件或目录名 仅仅是目录名 所以结尾不要加斜杠
     * @return boolean|number 返回压缩成功的文件
     */
    public function run($zip_name='',$base_path='',$zip_pathname=''){
        if($this -> startfile($zip_name,$base_path)==false){
            return false;
        }
        $num = $this -> zipdir($base_path.$zip_pathname);
        $this -> createfile();
        return $num;
    }
    
    /**
     * 执行压缩文件或目录
     * @param string $dir
     * @return number
     */
    private function zipdir($dir="."){
        $sub_file_num = 0;
        if(is_file($dir)){
            if(realpath($this ->gzfilename)!=realpath($dir)){
                $this -> addfile(implode('',file($dir)),$dir);
                return 1;
            }
            return 0;
        }
        $handle = opendir($dir);
        while ($file = readdir($handle)) {
            if($file=="."||$file=="..")continue;
            if(is_dir("$dir/$file")){
                $sub_file_num += $this->zipdir("$dir/$file");
            }else {
                if(realpath($this ->gzfilename)!=realpath("$dir/$file")){
                    $this -> addfile(implode('',file("$dir/$file")),"$dir/$file");
                    $sub_file_num ++;
                }
            }
        }
        closedir($handle);
        if(!$sub_file_num) $this -> addfile("","$dir/");
        return $sub_file_num;
    }
    
    /**
     * 返回文件的修改时间格式.
     * 只为本类内部函数调用.
     * @param number $unixtime
     * @return boolean
     */
    private function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);
        
        if ($timearray['year'] < 1980) {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        }
        
        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
        ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    }
    
    /**
     * 初始化文件,建立文件目录,
     * 并返回文件的写入权限.
     * @param string $zipfile 压缩文件
     * @param string $zipdir 这里要把具体压缩的某个目录写上,给后面替换掉.因为压缩包里不能有绝对路径
     * @return boolean
     */
    public function startfile($zipfile = 'temp.zip',$zipdir=''){
        $this->gzfilename=$zipfile;
        $this->zipdir=$zipdir;
//         $mypathdir=array();
//         do{
//             $mypathdir[] = $zipfile = dirname($zipfile);
//         }while($zipfile != '.');
//         @end($mypathdir);
//         do{
//             $zipfile = @current($mypathdir);
//             @mkdir($zipfile);
//         }while(@prev($mypathdir));
        //error_reporting(7);
        if($this->fp=fopen($this->gzfilename,"w")){
            return true;
        }
        return false;
    }
    
    /**
     * 添加一个文件到 zip 压缩包中.
     * @param unknown $data 文件内容
     * @param unknown $name 目录名,是相对路径,不能是绝对路径
     * @return unknown
     */
    public function addfile($data, $name){
        $name     = str_replace([$this->zipdir,'\\'], ['','/'], $name);
        if(strrchr($name,'/')=='/') return $this->adddir($name);        
        $dtime    = dechex($this->unix2DosTime());
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
        . '\x' . $dtime[4] . $dtime[5]
        . '\x' . $dtime[2] . $dtime[3]
        . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');
        
        $unc_len = strlen($data);
        $crc     = crc32($data);
        $zdata   = gzcompress($data);
        $c_len   = strlen($zdata);
        $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
        
        //新添文件内容格式化:
        $datastr  = "\x50\x4b\x03\x04";
        $datastr .= "\x14\x00";            // ver needed to extract
        $datastr .= "\x00\x00";            // gen purpose bit flag
        $datastr .= "\x08\x00";            // compression method
        $datastr .= $hexdtime;             // last mod time and date
        $datastr .= pack('V', $crc);             // crc32
        $datastr .= pack('V', $c_len);           // compressed filesize
        $datastr .= pack('V', $unc_len);         // uncompressed filesize
        $datastr .= pack('v', strlen($name));    // length of filename
        $datastr .= pack('v', 0);                // extra field length
        $datastr .= $name;
        $datastr .= $zdata;
        $datastr .= pack('V', $crc);                 // crc32
        $datastr .= pack('V', $c_len);               // compressed filesize
        $datastr .= pack('V', $unc_len);             // uncompressed filesize
        
        
        fwrite($this->fp,$datastr);	//写入新的文件内容
        $my_datastr_len = strlen($datastr);
        unset($datastr);
        
        //新添文件目录信息
        $dirstr  = "\x50\x4b\x01\x02";
        $dirstr .= "\x00\x00";                	// version made by
        $dirstr .= "\x14\x00";                	// version needed to extract
        $dirstr .= "\x00\x00";                	// gen purpose bit flag
        $dirstr .= "\x08\x00";                	// compression method
        $dirstr .= $hexdtime;                 	// last mod time & date
        $dirstr .= pack('V', $crc);           	// crc32
        $dirstr .= pack('V', $c_len);         	// compressed filesize
        $dirstr .= pack('V', $unc_len);       	// uncompressed filesize
        $dirstr .= pack('v', strlen($name) ); 	// length of filename
        $dirstr .= pack('v', 0 );             	// extra field length
        $dirstr .= pack('v', 0 );             	// file comment length
        $dirstr .= pack('v', 0 );             	// disk number start
        $dirstr .= pack('v', 0 );             	// internal file attributes
        $dirstr .= pack('V', 32 );            	// external file attributes - 'archive' bit set
        $dirstr .= pack('V',$this->datastr_len ); // relative offset of local header
        $dirstr .= $name;
        
        $this->dirstr .= $dirstr;	//目录信息
        
        $this -> file_count ++;
        $this -> dirstr_len += strlen($dirstr);
        $this -> datastr_len += $my_datastr_len;
    }
    
    private function adddir($name){
        $name = str_replace("\\", "/", $name);
        $datastr = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00";
        
        $datastr .= pack("V",0).pack("V",0).pack("V",0).pack("v", strlen($name) );
        $datastr .= pack("v", 0 ).$name.pack("V", 0).pack("V", 0).pack("V", 0);
        
        fwrite($this->fp,$datastr);	//写入新的文件内容
        $my_datastr_len = strlen($datastr);
        unset($datastr);
        
        $dirstr = "\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00";
        $dirstr .= pack("V",0).pack("V",0).pack("V",0).pack("v", strlen($name) );
        $dirstr .= pack("v", 0 ).pack("v", 0 ).pack("v", 0 ).pack("v", 0 );
        $dirstr .= pack("V", 16 ).pack("V",$this->datastr_len).$name;
        
        $this->dirstr .= $dirstr;	//目录信息
        
        $this -> file_count ++;
        $this -> dirstr_len += strlen($dirstr);
        $this -> datastr_len += $my_datastr_len;
    }
    
    /**
     * 创建文件
     */
    public function createfile(){
        //压缩包结束信息,包括文件总数,目录信息读取指针位置等信息
        $endstr = "\x50\x4b\x05\x06\x00\x00\x00\x00" .
                pack('v', $this -> file_count) .
                pack('v', $this -> file_count) .
                pack('V', $this -> dirstr_len) .
                pack('V', $this -> datastr_len) .
                "\x00\x00";
                
                fwrite($this->fp,$this->dirstr.$endstr);
                fclose($this->fp);
    }
}