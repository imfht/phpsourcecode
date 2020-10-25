<?php
//友好时间戳
function friendlyDate($time = NULL) {
    $text = '';
    $time = $time === NULL || $time > time() ? time() : intval($time);
    $t = time() - $time; //时间差 （秒）
    if ($t == 0)
        $text = '刚刚';
    elseif ($t < 60)
        $text = $t . '秒前'; // 一分钟内
    elseif ($t < 60 * 60)
        $text = floor($t / 60) . '分钟前'; //一小时内
    elseif ($t < 60 * 60 * 24)
        $text = floor($t / (60 * 60)) . '小时前'; // 一天内
    elseif ($t < 60 * 60 * 24 * 3)
        $text = floor($t/(60*60*24)) ==1 ?'昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time) ; //昨天和前天
    elseif ($t < 60 * 60 * 24 * 30)
        $text = date('m月d日 H:i', $time); //一个月内
    elseif ($t < 60 * 60 * 24 * 365)
        $text = date('m月d日', $time); //一年内
    else
        $text = date('Y年m月d日', $time); //一年以前
    return $text;
 }
//获得文章body里的外部资源(远程图片自动上传)
function downBodyImg($body){
	@set_time_limit(300);
    $basehost = "http://".$_SERVER["HTTP_HOST"];
    $img_array = array();
    @preg_match_all("/src=([\"|'|\s]{0,})(http:\/\/([^>]*)\.(gif|jpg|png|bmp))\\1/isU",$body,$img_array);
    $img_array = array_unique($img_array[2]);
    $imgUrl = 'uploads/images/'.date("Ymd", time());
    $imgPath = APP_PATH.'/'.$imgUrl;
    if(!is_dir($imgPath.'/')){
		mkdirs($imgPath);
    }
    foreach($img_array as $key=>$value){
		$value_parse=parse_url($value);
		$value_host=$value_parse['host'];
        if(preg_match("#".$basehost."#i", $value_host)){
            continue;
        }
        if(!preg_match("#^http:\/\/#i", $value)){
            continue;
        }
		$itype = substr($value, -4, 4);
        if(!preg_match("#\.(jpg|gif|png|bmp)#i", $itype)){
            continue;
        }
		//文件名，使用MD5加密防止重复采集
		$file_name=substr(md5($value),0,16).$itype;
		$fileurl = __ROOT__.'/'.$imgUrl.'/'.$file_name;
		if(!is_file($imgPath.'/'.$file_name)){
			$html=file_get_contents($value);
			write($imgPath.'/'.$file_name,$html);
        }
        $body = str_replace($value, $fileurl, $body);
    }
    return $body;
}
//获取内容url
function get_show_url($id,$page=''){
	if(config('web_url_model')==3 && config('web_url_route_on')){
		$rules=$page ? config('web_url_route_show_p') : config('web_url_route_show');
		$url=str_replace(array('{page}','{id}'),array($page,$id),$rules);
		$url=__ROOT__.'/'.$url.'.'.config('URL_PATH_SUFFIX');
	}else{
		$url=url('Article/show?id='.$id);
	}
	return $url;
}
//获取列表url
function get_list_url($id,$page=''){
	if(config('web_url_model')==3 && config('web_url_route_on')){
		$rules=$page ? config('web_url_route_list_p') : config('web_url_route_list');
		$url=str_replace(array('{page}','{id}'),array($page,$id),$rules);
		$url=__ROOT__.'/'.$url.'.'.config('URL_PATH_SUFFIX');
	}else{
		$page= $page ? '&p='.$page : '';
		$url=url('Article/lists?id='.$id.$page);
	}
	return $url;
}
//去除空白
function moveBlank($str){
	return preg_replace('~[\r\n\t\s ]+~','',$str);
}
//获取所有子分类id,  rerurn array
function getSonCid($tree){
	foreach($tree as $k=>$vo){
		$arrid[]=$vo['id'];
		if(isset($vo['children'])){
			$arr=getSonCid($vo['children']);
			if(is_array($arr)) $arrid=array_merge($arrid,$arr);
		}
	}
	return $arrid;
}
function getHashDir($key, $level = 2) {
	$hash_dir = array();
	$hash_arr = str_split(sha1($key), 2);
	for($i = 0; $i < $level; $i++) {
		$hash_dir[] = $hash_arr[$i];
	}
	$dir = str_replace('\\', '/', implode(DIRECTORY_SEPARATOR, $hash_dir));
	return $dir;
}
//树形分类
function channel_option_tree($data,$pid=0,$id=0,$n=0){
	$html = '';
	if($pid>0){
		$rep='|─';
	}else{
		$rep='-';
	}
	$rep= str_repeat("&nbsp;&nbsp;",$n).$rep;
	foreach($data as $k => $v){
		if($v['pid'] == $pid){//如果父级PID等于pid
			if($v['id']==$id){
				$html .= "<option value='".$v['id']."' selected='selected'>".$rep.$v['cname']."</option>";
			}else{
				$html .= "<option value='".$v['id']."'>".$rep.$v['cname']."</option>";
			}
			$html .= channel_option_tree($data, $v['id'],$id,$n+1);
		}
	}
	return $html;
}
//分页
function get_page_css($currentPage,$totalPages,$halfPer=5,$url,$pagego){
	$linkPage='';
    $linkPage .= ( $currentPage > 1 )
        ? '<a href="'.str_replace('!page!',1,$url).'">首页</a><a href="'.str_replace('!page!',($currentPage-1),$url).'">上一页</a>' 
        : '';
    for($i=$currentPage-$halfPer,$i>1||$i=1,$j=$currentPage+$halfPer,$j<$totalPages||$j=$totalPages;$i<$j+1;$i++){
        $linkPage .= ($i==$currentPage)?'<span>'.$i.'</span>':'<a href="'.str_replace('!page!',$i,$url).'">'.$i.'</a>'; 
    }
    $linkPage .= ($currentPage<$totalPages && $totalPages>$halfPer)? '<i>...</i><a href="'.str_replace('!page!',$totalPages,$url).'">'.$totalPages.'</a><a href="'.str_replace('!page!',($currentPage+1),$url).'">下一页</a>'
        : '';
	if(!empty($pagego)){
		$linkPage .='&nbsp;<input type="input" name="page"/><input type="button" value="跳 转" onclick="'.$pagego.'"/>';
	}
    return str_replace('-1'.RE_SUFFIX,RE_SUFFIX,str_replace('index1'.RE_SUFFIX,'',$linkPage));
}
function getDataTree($rows, $id = 'id', $pid = 'pid', $child = 'son', $root = 0) {
	$tree = array(); // 树
	if (is_array($rows)) {
		$array = array();
		foreach ($rows as $key => $item) {
			$array[$item[$id]] = &$rows[$key];
		} 
		foreach($rows as $key => $item) {
			$parentId = $item[$pid];
			if ($root == $parentId) {
				$tree[] = &$rows[$key];
			} else {
				if (isset($array[$parentId])) {
					$parent = &$array[$parentId];
					$parent[$child][] = &$rows[$key];
				} 
			} 
		} 
	}
	if(empty($tree)) $tree=$rows;
	return $tree;
}
function class_list_tree($data, $pid = 0, $n = 0) {
	$html = '';
	$rep = str_repeat("&nbsp;&nbsp;", $n) . $rep;
	foreach($data as $k => $v) {
		if ($v['pid'] == $pid) {
			if ($n > 0) {
				$c = $pid;
				//$style = " class='c{$c}' style='display:none;'";
				$rep = '<span style="color:#CCC">'.str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", ($n-1)).'|— </span>';
				$add = '';
				//if (isset($v['son'])) $rep = $rep . '<img id="icoc' . $v['id'] . '" width="11" height="11" src="public/images/open_ico.gif" onclick=\'closec("c' . $v['id'] . '");\' style="cursor:pointer">';
			} 
			if ($v['pid'] == 0) {
				//$rep = '<img id="icoc' . $v['id'] . '" width="11" height="11" src="public/images/open_ico.gif" onclick=\'closec("c' . $v['id'] . '");\' style="cursor:pointer">';
			} 
			$add = "<a href='".url('Admin/Arctype/edit?pid='.$v['id'])."'>添加子分类</a>";
			$html .= "<tr onmouseover=this.bgColor='#EDF8FE'; onmouseout=this.bgColor='#ffffff'; bgcolor='#ffffff'><td align='center' height='25'>{$v['id']}</td>";
			$html .= "<td>{$rep}<a href='".url('Admin/Arctype/edit?id='.$v['id'])."'>{$v['cname']}</a>" . ($v['isshow']?'':'<font color=red>[隐]</font>') . "</td>";
			$html .= "<td align='center'>{$add} <a href='".url('Admin/Arctype/edit?id='.$v['id'])."'>更改</a> <a onclick='return confirm(\"确定删除?删除后不可恢复!\");' href='".url('Admin/Arctype/del?id='.$v['id'])."'>删除</a></td>";
			$html .= "<td align='center'><input class='text' style='width:50px' type='text' name='order[{$v['id']}]' value='{$v['order']}'></td></tr>";
			if (isset($v['son'])) {
				$html .= class_list_tree($v['son'], $v['id'], $n + 1);
			} 
		} 
	} 
	return $html;
}
//数组value经过urlencode,支持多维数组
function array_urlencode($arr){
	if(is_array($arr)){
		foreach($arr as $k=>$v){
			$arr[$k]=array_urlencode($v);
		}
	}else{
		$arr=urlencode($arr);
	}
	return $arr;
}
function utf2gbk($str){
	if($str=='') return $str;
	if(function_exists('iconv'))
		$str=iconv("utf-8","gbk//IGNORE",$str);
	return $str;
}
function gbk2utf8($str){
	if($str=='') return $str;
	if(function_exists('iconv'))
		$str=iconv("gbk","utf-8//IGNORE",$str);
	return $str;
}
function arr2file( $file,$arr,$type=2){
	if($type==2){
		$con=var_export($arr,true);
		$con="<?php\r\n".'return '.$con.';'."\r\n?>";
	}else{
		$arr=str_replace("'","\\'",$arr);
		foreach( $arr as $k=> $v ){
			$arr[$k]="'".$v."',";
		}
		$con=implode("\r\n",$arr);
		$con="<?php\r\nreturn array(\r\n".$con."\r\n);\r\n?>";
	}
	write($file,$con);
}
function hexDecode($s) {
    return preg_replace('/(\w{2})/e',"chr(hexdec('\\1'))",$s);
}
//单位换算
function convSize($size,$unit='MB'){
	if($size==0) return 0;
	$unit=strtolower($unit);
	if($unit=='gb') $n=1024*1024*1024;
	if($unit=='mb') $n=1024*1024;
	if($unit=='kb') $n=1024;
	$s=round(($size/$n),2);
	if($s==0) $s=round(($size/$n),4);
	return $s;
}
//获取文件夹大小
function getDirSize($path,&$dirsize='0'){
	$dirarr=scandirs($path);
	foreach( $dirarr as $file ){
		if( $file != '.' && $file !='..' ){
			$realdir=$path.'/'.$file;
			if( is_dir($realdir) ){
				$dirsize += getDirSize($realdir);
			}else{
				$dirsize += filesize($realdir);
			}
		}
	}
	return $dirsize;
}
function mkdirs($path, $mode=0766){
	if(is_dir($path)) return true;
	mkdir($path,$mode,true);
}
//兼容的scandir
function scandirs($dir){
	$arr=array();
	if(!function_exists('scandir')){
		$handle=@opendir($dir);
		while(($arr[]=@readdir($handle)) !== false){
		}
		@closedir($handle);
		$arr=array_filter($arr);
	}else{
		$arr=@scandir($dir);
	}
	return $arr;
}
function deldir($dir){
	if(!is_dir($dir)) return false;
	$dirarr=scandirs($dir);
	foreach($dirarr as $file){
		if($file<>'.' && $file<>'..' ){
			if(is_dir("$dir/$file")){
				deldir("$dir/$file");
			}else if(is_file("$dir/$file")){
				unlink("$dir/$file");
			}
		}
	}
	return rmdir($dir);
}
function write($path,$data,$method="w") {
	mkdirs(dirname($path));
	if( is_file($path) && !is_writable($path)){
		return false;
	}
	if($method=='w'){
		return file_put_contents($path,$data);
	}
	$fp=fopen($path,$method);
	flock($fp,LOCK_EX);
	$result=fwrite($fp,$data);
	fclose($fp);
	return $result;
}
function read($file) {
	if(!is_file($file)) return false;
	if(!is_readable($file)){
		return false;
	}
	return file_get_contents($file);
}
function get_magic($g){
	if(get_magic_quotes_gpc()){
		$g = stripslashes($g);
	}
	 return $g;
}
function test_write($d){
    $tfile = '_test.txt';
    $d = preg_replace("#\/$#", '', $d);
    $fp = @fopen($d.'/'.$tfile,'w');
    if(!$fp){ return false;
    }else{
        fclose($fp);
        $rs = @unlink($d.'/'.$tfile);
        if($rs) return true;
        else return false;
    }
}
function debug_time(){
	list($sec,$utime)=explode(' ',microtime());
	return $utime+$sec;
}
//生成字母前缀
function get_letter($s0){
	$firstchar_ord = ord(strtoupper($s0{0})); 
	if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return $s0{0}; 
	$s = $s0; 
	$asc = ord($s{0})*256+ord($s{1})-65536; 
	if($asc>=-20319 and $asc<=-20284)return "A";
	if($asc>=-20283 and $asc<=-19776)return "B";
	if($asc>=-19775 and $asc<=-19219)return "C";
	if($asc>=-19218 and $asc<=-18711)return "D";
	if($asc>=-18710 and $asc<=-18527)return "E";
	if($asc>=-18526 and $asc<=-18240)return "F";
	if($asc>=-18239 and $asc<=-17923)return "G";
	if($asc>=-17922 and $asc<=-17418)return "H";
	if($asc>=-17417 and $asc<=-16475)return "J";
	if($asc>=-16474 and $asc<=-16213)return "K";
	if($asc>=-16212 and $asc<=-15641)return "L";
	if($asc>=-15640 and $asc<=-15166)return "M";
	if($asc>=-15165 and $asc<=-14923)return "N";
	if($asc>=-14922 and $asc<=-14915)return "O";
	if($asc>=-14914 and $asc<=-14631)return "P";
	if($asc>=-14630 and $asc<=-14150)return "Q";
	if($asc>=-14149 and $asc<=-14091)return "R";
	if($asc>=-14090 and $asc<=-13319)return "S";
	if($asc>=-13318 and $asc<=-12839)return "T";
	if($asc>=-12838 and $asc<=-12557)return "W";
	if($asc>=-12556 and $asc<=-11848)return "X";
	if($asc>=-11847 and $asc<=-11056)return "Y";
	if($asc>=-11055 and $asc<=-10247)return "Z";
	return 1;
}
//压缩html大小
function filt_html_blank($str){
	$str=preg_replace("#>(\s*)(\S*)(\s*)<#", '>$2<',$str);
	$str=preg_replace("#<!--([^>]*)-->#",'',$str);
	return $str;
}
//截取字符串
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	if(function_exists("mb_substr")){
		$slice = mb_substr($str, $start, $length, $charset);
		$suffixstr=mb_strlen($str)>$length ? '...' : '';
	}elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
		$suffixstr=iconv_strlen($str,$charset)>$length ? '...' : '';
	}else{
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
		$suffixstr=strlen($str)>$length ? '...' : '';
	}
	return $suffix ? $slice.$suffixstr : $slice;
}