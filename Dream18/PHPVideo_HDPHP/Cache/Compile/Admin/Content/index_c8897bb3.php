<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>栏目视频列表</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
    <script type="text/javascript" src="http://localhost/PHPUnion/Home/Admin/Theme/Content/ztree/jquery.ztree.all-3.5.min.js"></script>
<link type="text/css" rel="stylesheet" href="http://localhost/PHPUnion/Home/Admin/Theme/Content/ztree/zTreeStyle.css"/>
<style>
    .l{position: absolute;left:0px;width:200px;top:0px;bottom:5px;border:1px solid #ddd;border-radius: 4px 4px 0 0;box-shadow: none;overflow-x:scroll;}
    .r{position: absolute;left:210px;right:0px;top:0px;bottom:5px;border:1px solid #ddd;border-radius: 4px 4px 0 0;box-shadow: none;}
</style>
<script>
    var setting = {};
    function getZtree()
    {
       $('#ztree').hide();
        $.post("<?php echo U('getCateZtree');?>",'',function(data)
        {
            if(data)
            {
                $.fn.zTree.init($("#ztree"), setting, data);
            }
            // $('#ztree').slideDown(80);
            $('#ztree').show();
        },'json')
    }
    $(document).ready(function()
    {
        getZtree();
    });
</script>
<div class="l">
    <ul class="ztree" id="ztree"></ul>
</div>
<div class="r">
    <iframe id="myFrameId" src="<?php echo U('Admin/Index/welcome');?>" name="z_content" scrolling="auto" frameborder="0" width="100%" height="100%"></iframe>
</div>
</body>
</html>