<?php
class Coll
{
	/*
	*  得到一中字符中的超链接地址
	*/
	function geturl($str,$host="",$path="",$urlin="",$urllist=""){ 
	   if (preg_match("/href(\s+|)=(\s+|)(“|‘|'|\"|)(\s+|)(.*?)(”|’|'|\"|\s+|>)/ies",$str,$result)){
		   $str=str_replace($result[0],"",$str);
		   $pathbase=$path;
		   //把邮件链接和 script 过滤掉
		   if(strpos($result[5],"mailto:")!==false || strpos($result[5],"script:")!==false ){
			   $url="";
			   return $this->geturl($str,$host,$path,$urlin,$urllist);
		   }
		   else $url=$result[5];
		   if(strpos($url,"http")!==0) {
				 //判断链接是否为相对目录 并计算出完整url
				 $path1=strpos($url,"../",0);
				 while($path1!==false) {
					 if(substr($path,strlen($path)-1,strlen($path))=="/") $path=substr($path,0,strlen($path)-1);
					 $url=substr($url,strpos($url,"../")+3,strlen($url));    
					 //echo $url.strripos($path,"/")."\n";    
					 $path=substr($path,0,strripos($path,"/"));
					 $path1=strpos($url,"../",0);
				 }
				 if($path!='/') $url = str_replace($path,"",$url);
				 if(strpos($url,'/')!==0) $url= str_replace("//","/",$path."/".$url);
				 $url= str_replace("//","/",$url);
				 $url=$host.$url;
		   }
		   if($urlin=='' || strpos($url,$urlin)!==false) {
			   $urllist .=$url."\n";
		   }
		  return $this->geturl($str,$host,$pathbase,$urlin,$urllist);
		}
		return $urllist;
	}
	
	
	/*
	*  read a file
	*/
	function read($url){
		 $handle =@ fopen ($url, "r"); //读取代远程文件，需先打开文件
		 $contents = "";
		 if (!$handle) return false; 
		 while (!feof($handle)) {
			   $contents .= fread($handle, 102400);
		 }
		 @fclose($handle);
		 return $contents; 
	}
	
	/*
	*  write a file
	*/
	function write($tempname,$str){
		 $oldhtml=read($tempname);
		 $isinto=strripos($oldhtml,$str);
		 if ($isinto===false){
		 $root=is_file($tempname)?"a":"w";
		 $fp = @fopen($tempname,$root);
		 if (!fwrite($fp,$str)); //将信息写入文件
			 flock($fp,LOCK_UN);        
			 fclose($fp);
		 }
		 return true;
	}
	
	function microtime_float(){
	 list($usec, $sec) = explode(" ", microtime());
	 return ((float)$usec + (float)$sec);
	}
	
	
	/*
	* 过滤掉一些HTML标签
	*/
	function tohtml($str){
		 if($str=="") return "";
		 $str=str_replace("\r","",$str);
		 $str=str_replace("\n","<br />",$str);
		 $str=str_replace("'","’",$str);
		 while(strpos($str,"<br />")===0){
			 $str=substr($str,6,strlen($str));
		 }
		 return $str;
	}
	
	
	
	/*
	* 获取字符串中的图片
	* $str 一串字符
	* $f   是否把图片复制到本来，$f=1 复制图片到本地来
	*/
	function getImages($str,$f="",$host="",$path="/"){
		 if(preg_match("/<(IMG|img)(\s+|)(.*?)src(\s+|)=(\s+|)(“|‘|'|\"|)(\s+|)(.*?)(.jpg|.gif|.png|.bmp|.jpeg|.JPG|.GIF)(.*?)>/ies",$str,$rs)){
		 
		 $imgpath=$rs[8].$rs[9];
		 if(strpos($imgpath,"http")!==0) {
			 //判断链接是否为相对目录 并计算出完整url
			 $path1=strpos($imgpath,"../",0);
			 while($path1!==false) {
				 if(substr($path,strlen($path)-1,strlen($path))=="/") $path=substr($path,0,strlen($path)-1);
				 $imgpath=substr($imgpath,strpos($imgpath,"../")+3,strlen($imgpath));    
			 //     echo $imgpath.strripos($path,"/")."\n";    
				 $path=substr($path,0,strripos($path,"/"));
				 $path1=strpos($imgpath,"../",0);
			 }
			 if($path!='/') $imgpath = str_replace($path,"",$imgpath);
			 if(strpos($imgpath,'/')!==0) $imgpath= str_replace("//","/",$path."/".$imgpath);
			 $imgpath= str_replace("//","/",$imgpath);
			 $parseHost=parse_url($host);
			 $host=$parseHost["scheme"]."://".$parseHost["host"];
			 $imgpath=$host.$imgpath;
			 echo $imgpath;
		 }    
	
		 //将图片复制到本地
		 if($f=="1"){
			 $uppaths="images";
			 $dir=date("Y-m",time());
			 if(is_dir($uppaths)!=TRUE) mkdir($uppaths,0777);
			 if(is_dir($uppaths."/".$dir)!=TRUE) mkdir($uppaths."/".$dir,0777);
			 $name=substr($rs[8],(strripos($rs[8],"/",0)+1),strlen($rs[8]));  
			 $name=$uppaths."/".$dir."/".$name.$rs[9];    
			 if(copy($imgpath,$name));
			 $imgpath=$name;
		 }    
	
		 $imgpath=str_replace($rs[8].$rs[9],$imgpath,$rs[0]);
		 $imgpath=str_replace("<","{",$imgpath);
	
		 $str =str_replace($rs[0],$imgpath,$str);
		 return getImages($str,$f,$host,$path);
	  }else{
		 return   $str;
	  }
	}
	
	
	//转义正则表达式字符串
	function change_match_string($str){
		 //注意，以下只是简单转义
		$old=array("/","$");
		$new=array("\/","\$");
		$str=str_replace($old,$new,$str);
		return $str;
	}
	//获取匹配内容
	function fetch_match_contents($begin,$end,$c){
		$begin=change_match_string($begin);
		$end=change_match_string($end);
	//\s 匹配任何空白字符，包括空格、制表符、换页符等等。等价于[ \f\n\r\t\v]。 
	//\S 匹配任何非空白字符。等价于 [^ \f\n\r\t\v]
		$str="/{$begin}([\s\S]*?){$end}/im";
		if(@preg_match($str,$c,$rs)){
			return $rs[1];
		}else {
			return "";
		}
	}
	//获取特定区域的HTML
	function fetch_match_all($html,$rules,$arr){
		
	}
	
	
	//得到内容开始写
	function getcontent($planarArr,$str,$host){
		 if(is_array($planarArr)){
			foreach($planarArr as $key=>$rs){
				$keyName=$rs["optToFiled"];
				$keyValue="";
				if($rs["optType"]==1){//判断是否是用户自己填写，还是通过抓取来获取得到值的
					$keyValue=fetch_match_contents($rs["optStart"],$rs["optEnd"],$str);
	
				}else if($rs["optType"]==0){
					$keyValue=$rs["optStart"];
				}
				
				if($rs["optImageMode"]==1){
					$keyValue = getImages($keyValue,$rs["optImageServer"],$host,$path);//抓取图片 
					$keyValue = str_replace("{img","<img",$keyValue);
					$keyValue = str_replace("{IMG","<img",$keyValue); 
				}
				if($rs["optIsNull"]==0 && empty($keyValue)){
					$errors=1;
				}else{
					$sql[$keyName]=$keyValue;	
				}
			}
			$sql=($errors==1)?"":$sql;
			return $sql;
		 }
		 if(empty($title)){
			$sql="";
		 }elseif(empty($content)){
			$sql="";
		 }else{
			 $sql=array("title"=>$title,"content"=>$content,"author"=>$author,"tel"=>$tel,"web"=>$web,"ser"=>$ser,"add"=>$add,"con"=>$con);
				 return $sql;
		 }
		
	
	}
	

	
}
?>