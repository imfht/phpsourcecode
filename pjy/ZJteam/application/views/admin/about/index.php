<?php include 'application/views/admin/public/head.php'?>
<head>
	<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/ueditor.all.js"></script>

	<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>Public/ueditor/lang/zh-cn/zh-cn.js"></script>
	<link rel="stylesheet" href="<?php echo base_url();?>Public/ueditor/themes/default/css/ueditor.css" />
	<script type="text/javascript">
		var ue = UE.getEditor('editor');
	</script>

</head>
<body>
    <form name="content" method="post" action="<?php echo site_url('admin/updateAbout');?>"/>
    <input type="hidden" name="id" value="<?php echo $about->id;?>">
    <table cellpadding="0" cellspacing="1" class="table_list">
        <caption>关于我们</caption>
		<tr>
            <td colspan="2" height=300><textarea id="editor" name="editor" style="width:100%;height:800px;"><?php echo $about->content;?></textarea></td>
        </tr>
    </table>
    <div style="text-align:center"><button name="sub" type="submit">提 交</button></div>
</form>
</body>
</html>

