<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style type="text/css">
*{ padding:0; margin:0;}
ul,ol,li{ list-style:none;}
img{ border:none;}
a{ text-decoration:none;}
.jobinfo{ width:98%; float:left; padding:20px 15px 0 15px;}
.jobinfotop{ width:100%; height:40px; float:left; border-bottom:1px solid #ccc;}
.jobinfotop h1{ font-size:20px; font-family:"微软雅黑"; font-weight:normal; color:#666; float:left; padding-left:10px; display:block;}
.jobinfotop span{ float:right; padding:15px 10px 0 0; font-size:12px; color:#999;}
.jbinfor{ width:95%; float:left; padding:20px 0 20px 35px;}
.jbinfor ul li{ width:50%; float:left; font-size:14px; line-height:35px;}
.jobdescrip{ width:100%; float:left; background:url(images/tips_bg.jpg) top repeat-x; padding-bottom:20px;}
.jobdescrip h4{ padding:5px 0 0 20px; font-size:14px; height:40px;}
.jobdescrip p{ padding-left:50px; float:left; line-height:30px;}
.jobjoin{ width:100%; text-align:center; padding-bottom:20px; float:left;}
.jbjionbt{ width:98%; height:50px; float:left; text-align:right;}
.jbjionbt a{ padding:0.2em 1em; margin-right:15px;-moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: #7FBF4D; background-image: -moz-linear-gradient(center top , #7FBF4D, #63A62F); border-color: #63A62F #63A62F #5B992B; border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; box-shadow: 0 1px 0 0 #96CA6D inset; font-family:"微软雅黑"; color: #FFFFFF; font: 12px; text-align: center; text-shadow: 0 -1px 0 #4C9021; width:80px; cursor:pointer;}
.jbjionbt a:hover { background-color: #76B347; background-image: -moz-linear-gradient(center top , #76B347, #5E9E2E); box-shadow: 0 1px 0 0 #8DBF67 inset; cursor: pointer;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
</style>
<div class="jobinfo">
<?php
if(!empty($tag['data.results']))
{
	foreach($tag['data.results'] as $k=>$data)
	{
		?>
        <div class="jobinfotop"><h1><?php echo $data['title']; ?></h1></div>
        <div class="jbinfor">
            <ul>
                <li><b>招聘人数：</b><?php echo $data['requireNum']; ?></li>
                <li><b>工作性质：</b><?php echo $data['jobKind']; ?></li>
                <li><b>工作经验：</b><?php echo $data['experience']; ?></li>
                <li><b>工作地点：</b><?php echo $data['address']; ?></li>
                <li><b>学历要求：</b><?php echo $data['educational']; ?></li>
                <li><b>工资待遇：</b><?php echo $data['salary']; ?></li>
                <li><b>发布日期：</b><?php echo $data['dtTime']; ?></li>
                <li><b>截止日期：</b><?php echo $data['lastTime']; ?></li>
            </ul>
        </div>
        <div class="jbjionbt"><a href="<?php echo sys_href($data['channelId'],'view',$data['id'])?>">详情</a><a href="<?php echo sys_href($data['channelId'],"job_send",$data['id'])?>">应聘此职位</a></div>
		<?php
	}
	?>
	<div id="articeBottom">
		<div id="apartPage"><?php if(!empty($tag['pager.cn']))echo $tag['pager.cn']; ?></div>
	</div>
	<?php
}
else
{
	echo '暂无招聘。';
}
?>
</div>