<?php
checkme(10);
function index(){
}
function export()
{
	global $db,$request,$sizelimit,$startrow;
	$tables=$request['tables'];
	$sizelimit=$request['sizelimit'];
	if($request['dosubmit'])
	{
		$fileid = isset($request['fileid']) ? $request['fileid'] : 1;
		if($fileid==1 && $tables) 
		{
			if(!isset($tables) || !is_array($tables)) 
				echo "<script>alert('请选择要备份的数据表!');window.history.go(0);</script>";
			$random = mt_rand(100000, 999999);
			cache_write('bakup_tables.php', $tables);
		}
		else
		{
			if(!$tables = cache_read('bakup_tables.php')) 
				echo "<script>alert('请选择要备份的数据表!');window.history.go(-1);</script>";
		}
		$sqldump = '';
		$tableid = isset($request['tableid']) ? $request['tableid'] - 1 : 0;
		$startfrom = isset($request['startfrom']) ? intval($request['startfrom']) : 0;
		$tablenumber = count($tables);
		for($i = $tableid; $i < $tablenumber && strlen($sqldump) < $sizelimit * 1024; $i++)
		{
			$sqldump .= sql_dumptable($tables[$i], $startfrom, strlen($sqldump));
			$startfrom = 0;
		}
		
		if(trim($sqldump))
		{
			$sqldump = "#Realure.cn Created\n# --------------------------------------------------------\n\n\n".$sqldump;
			$tableid = $i;
			$random = isset($request['random']) ? $request['random'] : $random;
			$filename = DB_DBNAME.'_'.date('Ymd').'_'.$random.'_'.$fileid.'.sql';
			$fileid++;

			$bakfile = '../temp/data/'.$filename;
			if(!is_writable('../temp/data/')) 
				message('数据无法备份到服务器!请检查 ./data 目录是否可写。', $forward);
			file_put_contents($bakfile, $sqldump);
			//echo 'wer';
			//exit;
			echo '<script>alert("备份文件'.$filename.'写入成功!");window.location.href="?m=system&s=bakup&a=export&sizelimit='.$sizelimit.'&tableid='.$tableid.'&fileid='.$fileid.'&startfrom='.$startrow.'&random='.$random.'&dosubmit=1";</script>';
		}
		else
		{
		   cache_delete('bakup_tables.php');
		   echo '<script>alert("数据库备份完毕!");window.location.href="?m=system&s=bakup&a=export";</script>';
		    //message('数据库备份完毕!');
		}
		exit;
	}
}
function import()
{
	global $db,$request;
	$pre=$request['pre'];
	if($request['dosubmit'])
	 {
		if($request['filename'] && fileext($request['filename'])=='sql')
		{
			$filepath = ABSPATH.'/temp/data/'.$filename;
			if(!is_file($filepath)) 
				  echo '<script>alert("文件不存在!");window.history.go(-1);</script>';
			$sql = file_get_contents($filepath);
			sql_execute($sql);
			  echo '<script>alert("'.$filename.'中的数据已经成功导入到数据库!");window.history.go(-1);</script>';
		}
		else
		{
			$fileid = isset($request['fileid']) ? $request['fileid'] : 1;
			$filename = $request['pre'].$fileid.'.sql';
			$filepath = ABSPATH.'/temp/data/'.$filename;
			if(is_file($filepath))
			{
				$sql = file_get_contents($filepath);//将整个文件读入一个字符串
				sql_execute($sql);
				$fileid++;
				echo '<script>alert("数据文件'.$filename.'导入成功!");window.location.href="?m=system&s=bakup&a=import&pre='.$pre.'&fileid='.$fileid.'&dosubmit=1";</script>';		
						
			}
			else
			{
				echo '<script>alert("数据库恢复成功!");window.location.href="?m=system&s=bakup&a=import";</script>';
			}
		}
	 }
}
function download()
{
	global $request;
	if(!empty($request['filename']))
	{
		file_down(ABSPATH.'/temp/data/'.$request['filename']);
	}
	else
	{
		 echo '<script>alert("文件名不能为空!");window.history.go(-1);</script>';
	}
}
function delete()
{
	global $request;
	if(count($request['filenames'])==0)
	{
		echo '<script>alert("没有选数据表!");window.history.go(-1);</script>';
		return false;
	}
	if(is_array($request['filenames'])&&count($request['filenames'])>0)
	 {
		 foreach($request['filenames'] as $filename)
		 {
			 if(fileext($filename)=='sql')
			 {
				 @unlink('../temp/data/'.$filename);
			 }
		 }
	 }
	 else
	 {
		 if(fileext($request['filenames'])=='sql')
		 {
			 @unlink('../temp/data/'.$request['filenames']);
		 }
	 }
	 echo '<script>alert("文件删除成功!");window.history.go(-1);</script>';
}
function uploadsql()
{
	global $request;
	$uploadfile=basename($_FILES['uploadfile']['name']);
	if($_FILES['userfile']['size']>$request['max_file_size'])
		echo '<script>alert("您上传的文件超出了2M的限制!");window.history.go(-1);</script>';
	if(fileext($uploadfile)!='sql') 
		echo '<script>alert("只允许上传sql格式文件!");window.history.go(-1);</script>';
	$savepath = ABSPATH.'/temp/data/'.$uploadfile;
	if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $savepath))
	{
		 echo '<script>alert("数据库SQL脚本文件上传成功!");window.history.go(-1);</script>';
	}
	else
	{
		echo '<script>alert("数据库SQL脚本文件上传失败!");window.history.go(-1);</script>';
	}
}
function file_down($file,$filename='')
{
	if(is_file($file))
	{
		$filename = $filename ? $filename : basename($file);
		$filetype = fileext($filename);
		$filesize = filesize($file);
		header('Cache-control: max-age=31536000');
		header('Expires: '.gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
		header('Content-Encoding: none');
		//header('Content-Length: '.$filesize);
		header('Content-Disposition: attachment; filename='.$filename);
		header('Content-Type: '.$filetype);
		readfile($file);
	}
	else
	{
		echo '<script>alert("文件不存在!");window.history.go(-1);</script>';
	}
	exit;
}
function fileext($filename)
{
	return trim(substr(strrchr($filename, '.'), 1));
}
function repair()
{
	global $request,$db;
	 if($request['dosubmit'])
	 {
	 	if(count($request['tables'])==0)
			echo '<script>alert("您未选数据表!");window.history.go(-1);</script>';
		 $tables = is_array($request['tables']) ? @implode(',',$request['tables']) : $request['tables'];
		 $operation=isset($request['operation'])?$request['operation']:$operation;
		 if($tables && in_array($operation,array('repair','optimize','drop'))) 
		 	mysql_query("$operation TABLE $tables");
		echo '<script>alert("数据表操作成功!");window.history.go(-1);</script>';
	 }
}
function cache_write($file, $string, $type = 'array')
{
	if(is_array($string))
	{
		$type = strtolower($type);
		if($type == 'array')
		{
			$string = "<?php\n return ".var_export($string,TRUE).";\n?>";
		}
		elseif($type == 'constant')
		{
			$data='';
			foreach($string as $key => $value) $data .= "define('".strtoupper($key)."','".addslashes($value)."');\n";
			$string = "<?php\n".$data."\n?>";
		}
	}
	file_put_contents('../temp/data/'.$file, $string);
}
function cache_read($file, $mode = 'i')
{
	$cachefile = ABSPATH.'/temp/data/'.$file;
	@chmod($cachefile, 0666);
	if(!is_file($cachefile)) return array();
	return $mode == 'i' ? include $cachefile : file_get_contents($cachefile);
}
function cache_delete($file)
{
	return @unlink(ABSPATH.'/temp/data/'.$file);
}
function sql_dumptable($table, $startfrom = 0, $currsize = 0)
{
	global $db, $sizelimit, $startrow;

	if(!isset($tabledump)) $tabledump = '';
	$offset = 100;
	if(!$startfrom)
	{
		$tabledump = "DROP TABLE IF EXISTS $table;\n";
		$createtable = mysql_query("SHOW CREATE TABLE $table");
		$create = mysql_fetch_row($createtable);
		$tabledump .= $create[1].";\n\n";
	}
	$tabledumped = 0;
	$numrows = $offset;
	while($currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset)
	{
		$tabledumped = 1;
		$rows = mysql_query("SELECT * FROM $table LIMIT $startfrom, $offset");
		$numfields = mysql_num_fields($rows);
		$numrows = mysql_num_rows($rows);
		while ($row = mysql_fetch_row($rows))
		{
			$comma = "";
			$tabledump .= "INSERT INTO $table VALUES(";
			for($i = 0; $i < $numfields; $i++)
			{
				/*mysql_escape_string --  转义一个字符串用于 mysql_query */
				$tabledump .= $comma."'".mysql_escape_string($row[$i])."'";
				$comma = ",";
			}
			$tabledump .= ");\n";
		}
		$startfrom += $offset;
	}
	$startrow = $startfrom;
	$tabledump .= "\n";
	return $tabledump;
}
function sql_execute($sql)
{
	global $db;
    $sqls = sql_split($sql);
	if(is_array($sqls))
    {
		foreach($sqls as $sql)
		{
			if(trim($sql) != '') 
			{
				mysql_query($sql);
			}
		}
	}
	else
	{
		mysql_query($sqls);
	}
	return true;
}
function sql_split($sql)
{
	global $db;
	$sql = preg_replace("/TYPE=(InnoDB|MyISAM)( DEFAULT CHARSET=[^; ]+)?/", "TYPE=\\1 DEFAULT CHARSET=utf8",$sql);
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query)
	{
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach($queries as $query)
		{
			$str1 = substr($query, 0, 1);
			if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
		}
		$num++;
	}
	return($ret);
}
function message($msg,$url_forward='./',$ms=1250)
{
	global $charset;
	return ABSPATH.'/admini/views/system/bakup/message.php';
    exit; 
}
function clean(){}
function aclean(){
	global $db,$tmp;
	//获得当前数据库的表信息
	$tmp['sql']='SHOW TABLE STATUS ;';
	$tmp['rs']=$db->get_results($tmp['sql']);
	if(!empty($tmp['rs'])){
		//获得表类型信息
		foreach ($tmp['rs'] as $k=>$v){
			$tmp['tablesinfo'][$k][]=$v->Name;
			if(!empty($v->Engine)){
				$tmp['tablesinfo'][$k][]=1;//基本表
			}else{
				$tmp['tablesinfo'][$k][]=0;//视图
			}
		}
		//print_r($tmp['tablesinfo']);exit;
		//读取频道栏目存入数组
		$tmp['sql']='SELECT id FROM '.TB_PREFIX.'menu  WHERE isExternalLinks=0  ORDER BY id ASC';
		$tmp['menu']=$db->get_col($tmp['sql']);
		//if(empty($tmp['menu']))$tmp['menu'][]='0';
		if(empty($tmp['menu'])){
			$tmp['delsql']=truncatedata($tmp['tablesinfo']);//处理每个表的数据
		}else{
			$tmp['delsql']=deletedata($tmp['tablesinfo'],$tmp['menu']);//处理每个表的数据
		}
		//echo $tmp['delsql'];
		$db->hide_errors();//屏蔽 Query is empty
		$db->query($tmp['delsql'],true);
		$tmp['msg']= '清除数据完毕！';
	}else{
		$tmp['msg']= '数据表尚未创建，请确认！';
	}
	echo '<script>alert("'.$tmp['msg'].'");history.go(-1);</script>';
}
function uclean(){//用户自定义清除 
	global $db,$tmp,$request;
	//获得当前表信息
	if(empty($request['tables'])){
		echo '请选择要清理的表';exit;
	}else{
		foreach ($request['tables'] as $k=>$v){//tabletype
			$tmp['tablesinfo'][$k][]=$v;
			$tmp['tablesinfo'][$k][]=$request['tabletype'][$k];
		}
	}
	//print_r($tmp['tablesinfo']);exit;
	//读取频道栏目存入数组
	$tmp['sql']='SELECT id FROM '.TB_PREFIX.'menu  WHERE isExternalLinks=0  ORDER BY id ASC';
	$tmp['menu']=$db->get_col($tmp['sql']);
	if(empty($tmp['menu']))$tmp['menu'][]='0';
	$tmp['delsql']=deletedata($tmp['tablesinfo'],$tmp['menu']);//处理每个表的数据
		//echo $tmp['delsql'];
	$db->hide_errors();//屏蔽 Query is empty
	$db->query($tmp['delsql'],true);
	
	redirect($_SERVER['HTTP_REFERER']);//返回当前页
}
function deletedata($tablesinfo,$menu){
	global $db;
	foreach ($tablesinfo as $k=>$v){
		if($v[1]){
			$sql='DESCRIBE '.$v[0].' channelId;';
			$rs=$db->get_row($sql);
			if(!empty($rs)){
				$sql='SELECT distinct channelId FROM '.$v[0].' ORDER BY channelId ASC';
				$targetTable=$db->get_col($sql);
				if(!empty($targetTable)){
				$targetDiff=array_diff($targetTable,$menu);//返回两个数组的差集数组。
				if(!empty($targetDiff)){
					$targetDiff=@implode(',',$targetDiff);
					$delsql.='DELETE FROM '.$v[0].' WHERE channelId IN('.$targetDiff.');';					
					}
				}
			}
		}
	}
	return $delsql;
}
function truncatedata($tablesinfo){
	$arr=array(TB_PREFIX.'models_reg',TB_PREFIX.'user');//禁止清空的表
	foreach ($tablesinfo as $k=>$v){
		if($v[1] ){
			if(!in_array($v[0],$arr))$delsql.='TRUNCATE TABLE `'.$v[0].'`;';//直接清空表		
		}
	}
	return $delsql;
}
?>