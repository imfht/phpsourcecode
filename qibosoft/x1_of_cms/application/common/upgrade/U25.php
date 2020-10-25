<?php
namespace app\common\upgrade;


class U25{
    public static function up(){
	    
	    $_strA = '<?php
namespace app\qun\index;
use app\index\controller\Labelmodels AS _Labelmodels;
class Labelmodels extends _Labelmodels
{  
}';
	    
	    $_strB = '<?php
namespace app\qun\index;
use app\common\controller\index\Labelhy AS _Label;
class Labelhy extends _Label
{
}';
	    $_strC = '<?php
namespace app\qun\index;
use app\common\controller\index\Label AS _Label;
class Label extends _Label
{
}';
	    
	    $array = modules_config();
	    foreach($array AS $rs){
	        if($rs['keywords']=='search'||$rs['keywords']=='tongji'){
	            @unlink(APP_PATH.$rs['keywords'].'/index/Labelmodels.php');
	            @unlink(APP_PATH.$rs['keywords'].'/index/Labelhy.php');
	            @unlink(APP_PATH.$rs['keywords'].'/index/Label.php');
	            continue ;
	        }
	        $filename = APP_PATH.$rs['keywords'].'/index/Labelmodels.php';
	        if(!is_file($filename) || !strstr(file_get_contents($filename),"app\\".$rs['keywords']."\\")){ 
	            $strA = str_replace('qun', $rs['keywords'], $_strA);
	            file_put_contents($filename, $strA);
	        }
	        
	        $filename = APP_PATH.$rs['keywords'].'/index/Labelhy.php';
	        if(!is_file($filename) || !strstr(file_get_contents($filename),"app\\".$rs['keywords']."\\")){ 
	            $strB = str_replace('qun', $rs['keywords'], $_strB);
	            file_put_contents($filename, $strB);
	        }
	        
	        $filename = APP_PATH.$rs['keywords'].'/index/Label.php';
	        if(!is_file($filename) || !strstr(file_get_contents($filename),"app\\".$rs['keywords']."\\")){
	            $strC = str_replace('qun', $rs['keywords'], $_strC);
	            file_put_contents($filename, $strC);
	        }
	        
	    }
		  
	}
}


