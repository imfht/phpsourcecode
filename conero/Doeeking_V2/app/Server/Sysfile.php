<?php
/*
 *  2017年1月12日 星期四
 *  新建操作 - sys_file 表对应
*/
namespace app\Server;
use hyang\Logic;
use think\Db;
use Exception;
class Sysfile extends Logic
{
    public $basedir;
    public $curdir;
    public $randFileNo;         // 文件随机变换码
    private $_uploadPlusData;   // 上传文件附加字段/ array
    private $_sourcePath;       // 文件资源路径
    private $_isUploadMethod;   // 文件上传方式
    public $fileid;             // 上传文件ID
    public function init(){
        $this->basedir = ROOT_PATH.'Files/';
        $this->curdir = $this->basedir.date('Ym').'/';
        $this->_isUploadMethod = false;
    }
    // 上传文件 - 处理 成功则返回 TRUE
    // $files -> 用于测试
    public function upload($files=[])
    {     
        $files = $files? $files:$_FILES;
        list($key) = array_keys($files);
        $tmpname = $files[$key]['tmp_name'];
        $size = $this->sizeUnit($files[$key]['size']);
        $no = $this->mkfileNo();
        $saveData = [
            'user_code' => $this->code,
            'file_name' => $files[$key]['name'],
            'file_type' => $files[$key]['type'],
            'file_size' => ($size? $size:$files[$key]['size']),
            'file_no'   => $no,
            'url_name'  => date('Ym').'/'.$no.($this->getFileExt($files[$key]['name']))            
        ];
        $this->_isUploadMethod = true;
        $this->_sourcePath = $tmpname;
        return $this->saveData($saveData);
    }
    public function uploadPlusData($data=null){
        if(is_array($data)){
            $oldData = $this->_uploadPlusData;
            $this->_uploadPlusData = $oldData? array_merge($data,$oldData):$data;
        }
        else return $this->_uploadPlusData;
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 2016/1/24 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	//文件单位化  $fizese 文件原始大小：	
	public function sizeUnit($fizese)
	{
		if(!$fizese)
			return false;
        $fizese = intval($fizese);
		$tmpKb = $fizese/1024;
        $size = '';
		if($tmpKb < 1){
			$size = $fizese.'Byte(B)';
		}
		else{
			$tmpMb = $tmpKb/1024;
			if($tmpMb < 1){
				$size = round($tmpKb,3).'KB';
			}
			else{
				$tmpGb = $tmpMb/1024;
				if($tmpGb < 1){
					$size = round($tmpMb,3).'MB';
				}
				else{
					$tmpTb = $tmpGb/1024;
					if($tmpTb < 1){
						$size = round($tmpGb,3).'GB';
					}
					else{
						$size = round($tmpTb,3).'TB';
					}
				}
			}
		}
		return $size;
	}
    // 文件编号生成器 - 15bit
    // ASCII ：  (48-57):(0-9),(65-90):(A-Z),(97-122):(a-z)
    public function mkfileNo()
    {        
        $no = date('ymd');  // 6bit
        $no = dechex(intval($no));  // -> 年月日 16 进制
        $no .= 'Y'.(dechex(time() - strtotime(date('Y-m-d 00:00:00'))));
        // $no .= time();  // 10bit 
        $array = [
            rand(0,9),
            chr(rand(65,90)),
            chr(rand(97,122))
        ];
        shuffle($array);
        $no .= implode('',$array);
        $this->randFileNo = $no;
        return $no;
    }
    public function getFileExt($filename)
    {
        if(substr_count($filename,'.') == 0) return '';
        $array = explode('.',$filename);
        $ext = array_pop($array);
        return ($ext? '.'.$ext:'');
    }
    // URL 中加载文件 - 基于 curl
    public function fromUrl($url,$filename=null){
        $default = '.html';
        try{          
            // 将网络中的文件保存到服务器端
            $basedir = $this->basedir.'UpFiles/'.date('Ym').'/';
            if(!is_dir($basedir)) mkdir($basedir);
            // 获取文件格式名称
            $tArr = explode('/',$url);
            $urlFileName = array_pop($tArr);  
            $ext = strrchr($urlFileName,'.');
            if(empty($filename)){                
                $filename = $urlFileName;
                $name = $urlFileName;                  
                $pref = '_'.($this->nick? $this->nick:'').'_'.time();
                if($ext){
                    $filename = str_replace($ext,$pref.$ext,$filename);
                }
                else $filename = $filename.$pref.$defaul;
            }
            else{
                $filename = ($ext && substr_count($filename,$ext) == 0)? $filename.$ext:$filename;
                $name = $filename; // 自定义时覆盖名称
            }
            $filename = $basedir.$filename;
            ob_start(); 
            readfile($url);
            $file = ob_get_contents(); 
            ob_end_clean();
            //文件大小 
            $fp2=@fopen($filename,'w');
            @fwrite($fp2,$file);
            @fclose($fp2);

            //  文件写入数据库处理
            $size = $this->sizeUnit(filesize($filename));
            $no = $this->mkfileNo();            
            $saveData = [
                'user_code' => $this->code,
                'file_name' => $name,
                'file_size' => $size,
                'file_no'   => $no,
                'url_name'  => date('Ym').'/'.$no.$ext            
            ];  
            if($ext){                      
                $parser = new \Dflydev\ApacheMimeTypes\PhpRepository;
                $mimetype = $parser->findType(str_replace('.','',$ext));
                if($mimetype) $saveData['file_type'] = $mimetype;
            }
            $this->_sourcePath = $filename;
            return $this->saveData($saveData);
        }catch(Exception $e){
            $br = "\r\n";
            $rpt = '>> 从URL连接上传文件时出错！'
            . $br .'>> 用户('.($this->nick).')'
            . $br .'>> 时间('.(date('Y-m-d H:i:s')).')'
            . $br .'>> 错误信息： '.$br.$e->getTraceAsString();
                ;
            $this->infoRpt($rpt);
            return false;
        }
    }
    // 返回 bool
    /* 上游数组字段:   [user_code,file_name,file_type,file_size,file_no,url_name]
     *
    */
    public function saveData($data){
        $checked = false;
        try{
            $oData = $this->uploadPlusData();    
            if(is_array($oData)) $data = array_merge($oData,$data);
            if(isset($data['file_own']) && empty($data['file_own'])) $data['file_own'] = $this->nick;
            $checked = Db::table('sys_file')->insertGetId($data);    
            if($checked){
                $this->fileid = $checked;
                // 文件转移
                $modeFileRight = false;
                if(!is_dir($this->curdir)) mkdir($this->curdir);
                if($this->_isUploadMethod == true) $modeFileRight = move_uploaded_file($this->_sourcePath,$this->basedir.$data['url_name']);
                else $modeFileRight = copy($this->_sourcePath,$this->basedir.$data['url_name']);
                // 如果文件上传失败，则删除当前已新增的数据库记录
                if($modeFileRight == false){
                    $map = ['file_id'=>$checked];                
                    Db::table('sys_file')->where($map)->delete();
                    return false;
                }
                return true;
            }    
        }catch(Exception $e){
            if($checked && !is_bool($checked)){
                try{
                    $map = ['file_id'=>$checked];                
                    Db::table('sys_file')->where($map)->delete();
                }catch(Exception $e2){}

                $br = "\r\n";
                $rpt = '>> 新增上传时，保存数据/转移文件时出错！'
                . $br .'>> 用户('.($this->nick).')'
                . $br .'>> 时间('.(date('Y-m-d H:i:s')).')'
                . $br .'>> 错误信息： '.$br.$e->getTraceAsString();
                    ;
                $this->infoRpt($rpt);
            }            
        }
        return $checked;
    }
    // 报告方式
    private function infoRpt($info=null){
        if($info) debugOut($info);
    }
}