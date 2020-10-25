<?php
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组

	$sql="SELECT * FROM `".TB_PREFIX."download` WHERE channelId=".$params['id'];
	$sb = new sqlbuilder('mdt',$sql,'ordering DESC,id DESC',$db,downloadCount,true,URLREWRITE ? '/' : './');
	if(!empty($sb->results))
	{
		foreach($sb->results as $k =>$v)
	    {
	    	if(!empty($v['filePath'])) $sb->results[$k]['filePath']=$tag['path.root'].$v['filePath'];
	    }
		$tag['data.results']=$sb->results;
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();	
		}
	}
	$sb=null;
}
function view()
{
	global $db;
	global $params;
	global $tag;
	$sql='SELECT * FROM '.TB_PREFIX.'download WHERE id='.$params['args'];
	$download = $db->get_row($sql);
	if(!empty($download)){
		if(!empty($download->filePath)){
			$download->filePath =$tag['path.root'].$download->filePath;
		}
	}
	$tag['data.row']=(array)$download;
	unset($download);
}
function download()//提供下载
{
	global $db;
	global $params;
	$sql='SELECT * FROM '.TB_PREFIX.'download WHERE id='.$params['args'];
	$download = $db->get_row($sql);
	if(!empty($download->filePath)){
		$tempfile= explode('/',$download->filePath); //分割下载地址信息
		if($tempfile[0]!='http:')
		{
			$file =$download->filePath;
		}
		else
		{
			redirect($download->filePath);
		}
	}
	else
	{
		echo '<script>alert("暂无文件下载。");hostory.go(-1);</script>';
	}
	$backupfile=ABSPATH.'/'.$file;
	$filename = strtotime(date('Ymdhis'));
	$extend_2 = extend_2($file);
	if(is_file($backupfile))
	{
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$filename.'.'.$extend_2.'"');
		readfile($backupfile);
		exit;
	}
	else
	{
		exit($filename.'文件 不存在！');
	}
}
?>