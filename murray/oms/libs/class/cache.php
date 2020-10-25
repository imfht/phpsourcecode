<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 缓存类
*/

defined('INPOP') or exit('Access Denied');

class Cache{

	//构造函数
	public function __construct(){}

	//MEMCACHE读缓存
    public function memcacheCacheRead($m,$name){
		$return = $m->get($name);
        if(empty($return)){
			return false;   
        }else{   
            return $return;
        }   
    }   
  
	//执行MEMCACHE缓存
    public function memcacheCache($name,$var,$s){
		if(!$name) return false;
        $m = self::memcache();
		if(defined('IN_SAE')){
			//针对SAE执行memcache缓存
			if(empty($var)){
				return memcache_get($m, $name);   
			}else{			
				memcache_set($m, $name, $var);
				return memcache_get($m, $name);   
			} 
		}else{
			if(empty($var)){   
				return self::memcacheCacheRead($m,$name);   
			}else{
				$m->set($name,$var,0,$s);   
				return self::memcacheCacheRead($m,$name);   
			}		
		}		
    }
  
	//文件缓存
    public function fileCache($name, $var, $s){
        $filename = CACHE_PATH.DS.$name.EXT;   
        if(empty($var)){   
            if(is_file($filename)){   
                return self::readCache($filename);   
            }else return false;   
        }else{   
            if($this->writeCache($filename,$var,$s)){   
                return self::readCache($filename);   
            }else{
                exit('File cache write error！');
			}
        }   
    }   
  
	//写文件缓存
    public function writeCache($filename, $var, $s){   
        $var = array('var'=>$var,'s'=>$s);   
        $content = serialize($var);   
        $content = '<?php exit;?>'.$content;   
        fclose(fopen($filename,'w'));   
        if(file_put_contents($filename, $content)){   
            return true;   
        }else{   
            return false;   
        }   
    }   
  
	//读文件缓存
    public function readCache($filename){   
        $content = @file_get_contents($filename);   
        $var = unserialize(str_replace('<?php exit;?>', '', $content));   
        $mtime = filemtime($filename);   
        if(time()-$mtime >= $var['s']){   
            @unlink($filename);   
            return false;   
        }else{   
            return $var['var'];   
        }   
    }   
  
	//实例化MEMCACHE
    public function memcache(){
		if(defined('IN_SAE')){
			//针对SAE初始化
			$mem = memcache_init();		
		}else{
			$mem = memcache::getInstance();
		} 
        return $mem;   
    }

}
?>