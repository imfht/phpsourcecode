<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
 
/**
 * 文件系统类
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    liu21st <liu21st@gmail.com>
 */
class filesys {//类定义开始

	private $path;
	private $name;
	private $link;
	private $size;
	private $addtime;
	private $type;

    private $config =   array(
        'root' => '',
		'charset'=>'GBK',
    );

    public function __get($name){
        if(isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }

    public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    public function __isset($name){
        return isset($this->config[$name]);
    }
    
   //$mode为0时:带返回上级目录,为1时:不带返回上级目录
	public function path($path,$mode=0){
		$info1 = array();
		$info2 = array();
		$info3 = array();
		if($this->config['charset']=='UTF-8'){
			$path = iconv($this->config['charset'],'GBK',$path);
		}
		$base = str_replace("\\", '/',getcwd());
		if($mode==0){
			foreach($this->ylistPath($path) as $p){
				if($this->checkType($p)=='del'){
					continue;
				}
				if($this->checkType($p)=='top'){
					$this->name = 'last';
					$son = stristr(dirname($path),$this->config['root']);
					if($son){
						$this->link = $son;
					}else{
						$this->link = str_replace($base,'',$path);
					}
					if($this->config['charset']=='UTF-8'){
						$info1[] = array('name'=>iconv('GBK',$this->config['charset'],$this->name),'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>iconv('GBK',$this->config['charset'],$this->link));
					}else{
						$info1[] = array('name'=>$this->name,'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>$this->link);
					}
				}elseif($this->checkType($path.'/'.$p)=='dir'){
					$this->name = $p;
					$son = stristr($path.'/'.$p,$this->config['root']);
					if($son){
						$this->link = $son;
					}else{
						$this->link = str_replace($base,'',$path.'/'.$p);
					}
					$this->addtime = filemtime($path.'/'.$p);
					if($this->config['charset']=='UTF-8'){
						$info2[] = array('name'=>iconv('GBK',$this->config['charset'],$this->name),'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>iconv('GBK',$this->config['charset'],$this->link));
					}else{
						$info2[] = array('name'=>$this->name,'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>$this->link);
					}
				}else{
					$type = $this->checkType($p);
					$this->name = $p;
					$son = stristr($path.'/'.$p,$this->config['root']);
					if($son){
						$this->link = $son;
					}else{
						$this->link = str_replace($base,'',$path.'/'.$p);
					}
					$this->size = round(filesize($path.'/'.$p)/1024,2);
					$this->addtime = filemtime($path.'/'.$p);
					if($this->config['charset']=='UTF-8'){
						$info3[] = array('name'=>iconv('GBK',$this->config['charset'],$this->name),'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>iconv('GBK',$this->config['charset'],$this->link));
					}else{
						$info3[] = array('name'=>$this->name,'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>$this->link);
					}
				}
			}
			return array_merge($info1,$info2,$info3);
			unset($info1,$info2,$info3);
		}elseif($mode==1){
			foreach($this->nlistPath($path) as $p){
				if($this->checkType($path.'/'.$p)=='dir'){
					$this->name = $p;
					$son = stristr($path.'/'.$p,$this->config['root']);
					if($son){
						$this->link = strstr($son,'/');
					}else{
						$this->link = str_replace($base,'',$path.'/'.$p);
					}
					$this->addtime = filemtime($path.'/'.$p);
					if($this->config['charset']=='UTF-8'){
						$info1[] = array('name'=>iconv('GBK',$this->config['charset'],$this->name),'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>iconv('GBK',$this->config['charset'],$this->link));
					}else{
						$info1[] = array('name'=>$this->name,'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>$this->link);
					}
				}else{
					$type = $this->checkType($p);
					$this->name = $p;
					$son = stristr($path.'/'.$p,$this->config['root']);
					if($son){
						$this->link = $son;
					}else{
						$this->link = str_replace($base,'',$path.'/'.$p);
					}
					$this->size = round(filesize($path.'/'.$p)/1024,2);
					$this->addtime = filemtime($path.'/'.$p);
					if($this->config['charset']=='UTF-8'){
						$info2[] = array('name'=>iconv('GBK',$this->config['charset'],$this->name),'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>iconv('GBK',$this->config['charset'],$this->link));
					}else{
						$info2[] = array('name'=>$this->name,'size'=>$this->size,'addtime'=>$this->addtime,'type'=>$this->type,'link'=>$this->link);
					}
				}
			}
			return array_merge($info1,$info2);
			unset($info1,$info2);
		}
	}
	
	//检测是文件类型
	private function checkType($file){
		if($file=='.'){
			return $this->type = 'del';
		}
		if($file=='..'){
			return $this->type = 'top';
		}elseif(is_dir($file)){
			return $this->type = 'dir';
		}elseif(strtolower(strrchr($file,'.'))=='.jpg' || strtolower(strrchr($file,'.'))=='.jpeg' || strtolower(strrchr($file,'.'))=='.gif' || strtolower(strrchr($file,'.'))=='.bmp' || strtolower(strrchr($file,'.')=='.png')){
			return $this->type = 'img';
		}elseif(strtolower(strrchr($file,'.'))=='.html' || strtolower(strrchr($file,'.'))=='.htm'){
			return $this->type = 'htm';
		}elseif(strtolower(strrchr($file,'.'))=='.php'){
			return $this->type = 'php';
		}elseif(strtolower(strrchr($file,'.'))=='.css'){
			return $this->type = 'css';
		}elseif(strtolower(strrchr($file,'.'))=='.swf'){
			return $this->type = 'fla';
		}elseif(strtolower(strrchr($file,'.'))=='.bak'){
			return $this->type = 'bak';
		}elseif(strtolower(strrchr($file,'.'))=='.js'){
			return $this->type = 'js';
		}elseif(strtolower(strrchr($file,'.'))=='.txt' || strtolower(strrchr($file,'.'))=='.bak' || strtolower(strrchr($file,'.'))=='.inc' || strtolower(strrchr($file,'.'))=='.ca'){
			return $this->type = 'txt';
		}elseif(strtolower(strrchr($file,'.'))=='.rar' || strtolower(strrchr($file,'.'))=='.zip'){
			return $this->type = 'rar';
		}else{
			return $this->type = 'un';
		}
	}
	
	//获取文件夹列表---带返回上级目录
	public function yListPath($path){
		if(!$path){
			return 0;
		}
		$dir = dir($path);
		$arr_path = array();
		while(($file = $dir->read()) !== false){
			if($file!='.'){
				$arr_path[] = $file;
			}	
		}
		$dir->close();
		return $arr_path;
	}
	//获取文件夹列表---不带返回上级目录
	public function nListPath($path){
		if(!$path){
			return 0;
		}
		$dir = dir($path);
		$arr_path = array();
		while(($file = $dir->read()) !== false){
			if($file!='.' && $file!='..'){
				$arr_path[] = $file;
			}	
		}
		$dir->close();
		return $arr_path;
	}
	
	//删除文件或目录
	private function fileLoop($arr,$top_path){
		foreach($arr as $loop){
			$this->delFile($top_path.'/'.$loop,1);
		}
	}
	
	public function delFile($path,$mode=0){
		if($this->config['charset']=='UTF-8' && $mode==0){
			$path = iconv($this->config['charset'],'GBK',$path);
		}
		$info = '';
		if(is_dir($path)){
			$top = @rmdir($path);
			if(!$top){
				$this->fileLoop($this->nListPath($path),$path);
			}
		}else{
			$info = @unlink($path);	
		}
		$dir = @rmdir($path);
		
		$top_path = dirname($path);
		$dirt = @rmdir($top_path);
		return 1;
	}
	
	//获取文件及信息
	private function scanLoop($path,$fix){
		$this->scan($path,$fix,1);
	}
	
	private $scan = array();
	public function scan($path,$fix,$mode=0){
		if($this->config['charset']=='UTF-8' && $mode==0){
			$path = iconv($this->config['charset'],'GBK',$path);
		}
		if(is_dir($path)){
			$arr_dir = $this->nListPath($path);
			$fix = trim($fix);
			$fix = preg_replace("/\s/","",$fix);
			$arr_fix = explode('|',$fix);
			$arr_fix = array_filter($arr_fix);
			foreach($arr_dir as $a){
				$real_path = $path.'/'.$a;
				if(is_dir($real_path)){
					$this->scanLoop($real_path,$fix);
				}else{
					$real_fix = strtolower(strrchr($a,'.'));
					$real_time = filemtime($real_path);
					if($fix){
						if(in_array($real_fix,$arr_fix)){
							if($this->config['charset']=='UTF-8'){
								$this->scan[] = array(
									'path'=>iconv('GBK',$this->config['charset'],$real_path),
									'fix'=>$real_fix,
									'time'=>$real_time
								);
							}else{
								$this->scan[] = array(
									'path'=>$real_path,
									'fix'=>$real_fix,
									'time'=>$real_time
								);
							}
						}else{
							continue;
						}
					}else{
						if($this->config['charset']=='UTF-8'){
							$this->scan[] = array(
								'path'=>iconv('GBK',$this->config['charset'],$real_path),
								'fix'=>$real_fix,
								'time'=>$real_time
							);
						}else{
							$this->scan[] = array(
								'path'=>$real_path,
								'fix'=>$real_fix,
								'time'=>$real_time
							);
						}
					}	
				}
			}
			return $this->scan;
			unset($this->scan);
		}
	}
	
	//获取目录大小---请慎用,大大降低效率
	private function dirLoop($arr,$top_path){
		foreach($arr as $loop){
			$newpath = $top_path.'/'.$loop;
			$this->dirSize($newpath,1);
		}
	}
	var $info;
	public function dirSize($path,$mode=0){
		if($this->config['charset']=='UTF-8' && $mode==0){
			$path = iconv($this->config['charset'],'GBK',$path);
		}
		if(is_dir($path)==1){
			$this->dirLoop($this->nListPath($path),$path);
		}else{
			$this->info += filesize($path);	
		}
		$size = round($this->info/1024,2);
		return $size;
	}
	
	public function clearInfo(){
		$this->info = 0;
	}
	
	//修改文件或目录名
	public function reName($old_name,$new_name){
		if($this->config['charset']=='UTF-8'){
			$old_name = iconv($this->config['charset'],'GBK',$old_name);
			$new_name = iconv($this->config['charset'],'GBK',$new_name);
		}
		$info = rename($old_name,$new_name);
		if(!$info){
			return 0;
		}else{
			return 1;
		}
	}

	//移动文件或目录名
	public function moveFile($old_path,$new_path,$name){
		if($this->config['charset']=='UTF-8'){
			$old_name = iconv($this->config['charset'],'GBK',$old_name);
			$new_name = iconv($this->config['charset'],'GBK',$new_name);
			$name = iconv($this->config['charset'],'GBK',$name);
		}
		if(!file_exists($new_path)){
			mkdir($new_path,0777);
		}
		if(file_exists($old_path.$name)){
			$info = rename($old_path.$name,$new_path.$name);
			if(!$info){
				return 0;
			}else{
				return 1;
			}
		}else{
			return 0;
		}
	}
	
	//复制文件或目录名
	public function copyFile($old_path,$new_path,$name){
		if($this->config['charset']=='UTF-8'){
			$old_name = iconv($this->config['charset'],'GBK',$old_name);
			$new_name = iconv($this->config['charset'],'GBK',$new_name);
			$name = iconv($this->config['charset'],'GBK',$name);
		}
		if(!file_exists($new_path)){
			mkdir($new_path,0777);
		}
		if(file_exists($old_path.$name)){
			$info = copy($old_path.$name,$new_path.$name);
			if(!$info){
				return 0;
			}else{
				return 1;
			}
		}else{
			return 0;
		}
	}
	
	//获取文件内容
	public function getFile($path){
		if($this->config['charset']=='UTF-8'){
			$path = iconv($this->config['charset'],'GBK',$path);
		}
		$info = file_get_contents($path);
		return $info;
	}
	
	//创建/编辑文件名
	public function putFile($path,$data){
		if($this->config['charset']=='UTF-8'){
			$path = iconv($this->config['charset'],'GBK',$path);
			//$data = iconv($this->config['charset'],'GBK',$data);
		}
		$info = file_put_contents($path,$data);
		if($info===0 || $info){
			return 1;
		}else{
			return 0;
		}
	}
	
	//创建/编辑目录名
	public function putDir($path,$mode=0777){
		if($this->config['charset']=='UTF-8'){
			$path = iconv($this->config['charset'],'GBK',$path);
		}
		if(file_exists($path)){
			$info = 0;
		}else{
			$info = mkdir($path,$mode,true);
		}
		
		if(!$info){
			return 0;
		}else{
			return 1;
		}
	}
	
	//远程w文件保存到本地
	public function saveFIle($url,$newpath,$ext=array('.jpg','.gif','.png'),$maxszie=-1){
		if(!$url || !$newpath){
			return 0;		//链接或目录为空
		}
		if(!file_exists($newpath)){
			mkdir($newpath,0777,true);
		}
		$fix = strrchr($url, ".");
		$fix = strtolower($fix);
		$filename = uniqid().$fix;
		$realname = $newpath.'/'.$filename;
		if(is_string($ext) && strstr($ext,'|')){
			$ext = preg_replace("/\s/","",$ext);
			$ext = explode('|',trim($ext));
		}elseif(is_array($ext)){
			$ext = $ext;
		}else{
			$ext = array('.jpg','.gif','.png');
		}
		if(!in_array($fix,$ext)){
			return -1;		//格式不合法
		}
		
		ob_start();
		readfile($url);
		$file = ob_get_contents();
		ob_end_clean();
		$size = strlen($file);
		if($maxszie!=-1){
			if($size>$maxszie){
				return -2;
			}	
		}
		$fp = @fopen($realname,"a");
		fwrite($fp,$file);
		fclose($fp);
		$arr = array(
			'filename'=>$filename,
			'size'=>$size,
			'realname'=>$realname,
			'fix'=>$fix
		);
		return $arr;
	}
}
