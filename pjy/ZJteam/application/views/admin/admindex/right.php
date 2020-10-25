<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="<?php echo base_url()?>Public/admin_style/css/admin.css" type="text/css"/>
<title>PJY后台管理</title>
</head>
<body>
    <table cellpadding="0" cellspacing="1" class="table_list">
        <caption>系统信息</caption>
        <tr>
            <td width="40%">域名</td>
            <td><?php echo $_SERVER['SERVER_NAME']?></td>
        </tr>
        <tr>
            <td>服务端信息</td>
            <td><?php echo $_SERVER['SERVER_SOFTWARE']?></td>
        </tr>
        <tr>
            <td>PHP版本</td>
            <td><?php echo PHP_VERSION?></td>
        </tr>
        <tr>
            <td>MYSQL版本</td>
            <td><?php echo $this->db->version();?></td>
        </tr>
        <tr>
            <td>GD库版本</td>
            <td><?php 
					if(function_exists('gd_info')){
					$this->gd = gd_info();
					echo $gdinfo = $this->gd['GD Version'];
					}else{
						echo $gdinfo = '<span style="color:red">未知</span>';
					}
		?></td>
        </tr> 
        <tr>
            <td>文件上传</td>
            <td><?php 		
				if(ini_get('file_uploads')){
				$umfs = ini_get('upload_max_filesize');
				$pms = ini_get('post_max_size');
				echo '允许 | 文件:'.$umfs.' | 表单: '.$pms;
			}else{
				echo '<span style="color:red">禁止</span>';
			}
		?></td>
        </tr>  
        <tr>
            <td>远程文件获取</td>
            <td><?php 		
				if(ini_get('allow_url_fopen')){
					echo '支持';
				}else{
					echo '<span style="color:red">不支持</span>';
				}
		?></td>
        </tr>  
        <tr>
            <td>脚本执行时间</td>
            <td><?php echo ini_get('max_execution_time').'秒'?></td>
        </tr>         
</table>
<table cellpadding="0" cellspacing="1" class="table_list">
 <caption>PJY制作</caption>
 <tr>
  <td width="40%">官方网站：</td>
  <td><a href="http://pjy.strikingly.com/" target="_blank">PJY的个人网站</a></td>
 </tr>
 <tr>
  <td>QQ：</td>
  <td><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=731401082&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:9118847:42" alt="PJY" title="PJY"></a></td>
 </tr>
 <tr>
  <td>Email：</td>
  <td><a href="731401082@qq.com">731401082@qq.com</a></td>
 </tr>
</table>    
</body>
</noframes></html>
