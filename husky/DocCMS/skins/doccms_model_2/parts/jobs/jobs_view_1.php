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
<?php $data=$tag['data.row']; ?>
<style type="text/css">
*{ padding:0; margin:0;}
ul,ol,li{ list-style:none;}
img{ border:none;}
a{ text-decoration:none;}
.jobinfo{ width:98%; float:left; padding:20px 0 0 15px;}
.jobinfotop{ width:100%; height:40px; float:left; border-bottom:1px solid #ccc;}
.jobinfotop h1{ font-size:20px; font-family:"微软雅黑"; font-weight:normal; color:#666; float:left; padding-left:10px; display:block;}
.jobinfotop span{ float:right; padding:15px 10px 0 0; font-size:12px; color:#999;}
.jbinfor{ width:95%; float:left; padding:20px 0 20px 35px;}
.jbinfor ul li{ width:50%; float:left; font-size:14px; line-height:35px;}
.jobdescrip{ width:100%; float:left; background:url(images/tips_bg.jpg) top repeat-x; padding-bottom:20px;}
.jobdescrip h4{ padding:5px 0 0 20px; font-size:14px; height:40px;}
.jobdescrip p{ padding-left:50px; float:left; line-height:30px;}
.jobjoin{ width:100%; text-align:center; padding-bottom:20px; float:left;}
</style>
<div id="jobinfo">
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
	<div class="jobdescrip">
    	<h4>职位描述及要求：</h4>
        <p>
        	<?php echo $data['content']; ?>
		</p>
    </div>
	<div class="jobdescrip">
    	<h4>联系方式：</h4>
        <div class="jbinfor">
            <ul>
                <li><b>联系电话：</b><?php echo $data['telphone']; ?></li>
                <li><b>E-Mail：</b><?php echo $data['email']; ?></li>
            </ul>
    	</div>
    </div>
    <div class="jobjoin"><a href="<?php echo sys_href($data['channelId'],"job_send",$data['id'])?>"><img src="<?php echo $tag['path.skin']; ?>res/images/jonin.jpg" width="200" height="50" /></a></div>
</div>