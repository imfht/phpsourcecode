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
<!--
*{ margin:0; padding:0;}
#crsnews{ width:99%; float:left; padding:10px 10px;}
.picnews{ width:100%; float:left; height:160px;}
.picnews ul li{ width:50%; height:160px; float:left; list-style:none;}
.picnews ul li a{ display:block;}
.newspic{ width:174px; height:124px; float:left;}
.picnews ul li img{ width:160px; height:110px; float:left; padding:5px; border:1px solid #ddd;}
.picnewslist{ width:160px; padding-left:6px; float:left; line-height:24px; color:#999;}
.picnewslist a{ width:160px; float:left; color:#000; font-size:14px; height:72px;}
.picnewslist span{ width:160px; height:48px; float:left;overflow:hidden;}
.newcalllist{ width:100%; float:left; padding-bottom:15px; font-size:12px;}
.newcalllist h2{ font-size:16px; margin-bottom:10px; padding:0 10px;}
.newcalllist h2 a{ float:right; font-family:"宋体"; color:#999; font-size:12px; font-weight:normal;}
.newcalllist ul{ width:99%; float:left; border-top:1px solid #e1e1e1; list-style:none;}
.newcalllist ul li{ height:90px; border:1px solid #e1e1e1; border-top:none; padding:10px 0 10px 15px;}
.newcalllist span{ width:100px; height:25px; padding-top:10px; float:left;}
.newcalllist .textnews{ width:83%; float:left; color:#999;}
.newcalllist strong{ width:100%; height:28px; float:left; color:#08c; font-size:14px;}
.newcalllist p{ width:100%; height:55px; line-height:25px; float:left;overflow:hidden; font-size:12px;}
#picnewslist a{color:#333; font-size:13px;}
#picnewslist a:hover{color:#005EAE;}
.textnews a:hover{color:#005EAE;}
.clear{ clear:both;}
-->
</style>
<div id="crsnews">
	
<?php 
if(!empty($tag['data.results']))
{
	foreach($tag['data.results'] as $kk=>$vv)
	{
		$channelId	= $vv['channelId']; //栏目id 
		$channel	= $vv['channel'];   //栏目中文名
		$results	= $vv['results'];   //列表数据
		if(!empty($results))
		{
		?>
            <div class="newcalllist">
            <h2><a href="<?php echo sys_href($channelId)?>">更多&gt;</a><?php echo $channel; ?></h2>
            <ul>
			<?php 
			foreach ($results as $k=>$data)
			{
			?>
                <li>
                    <span><?php echo date('Y-m-d',strtotime($data['dtTime'])); ?></span>
                    <div class="textnews">
                    <a title="<?php echo $v['title']; ?>" href="<?php echo sys_href($data['channelId'],'list',$data['id'])?>"><strong><?php echo $data['title']; ?> </strong></a>
                    <p>新闻摘要：<?php echo $data['description']; ?></p>
                    </div>
                </li>
                 
			<?php	
			}
			?>
           </ul>
           </div> 
           <?php 
		}
	}
}			
?>
</div>
<div class="clear"></div>