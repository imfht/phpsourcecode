<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/dt-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style>
*{ padding:0; margin:0;}
img{ border:none;}
.newcalllist{ width:100%; float:left; padding-bottom:15px;}
.newcalllist h2{ font-size:16px; margin-bottom:10px; padding:0 10px;}
.newcalllist h2 a{ float:right; font-family:"宋体"; color:#999; font-size:12px; font-weight:normal;}
.newcalllist ul{ width:100%; float:left; border-top:1px solid #e1e1e1; list-style:none;}
.newcalllist ul li{ width:100%; height:100%; float:left; border:1px solid #e1e1e1; border-top:none; padding:15px 0;}
.newcalllist span{ width:15%; height:25px; padding:10px 0 0 15px; float:left;}
.newcalllist #textnews{ width:80%; float:left; color:#999;}
.newcalllist strong{ width:95%; height:28px; color:#1E6BC5; font-size:16px; margin-bottom:10px; display:block; border-bottom:1px dashed #ddd;}
.newcalllist p{ width:95%;margin:0; padding:0; height:300px;}
#picnewslist a{color:#333; font-size:13px;}
#picnewslist a:hover{color:#005EAE;}
#textnews a{color:#1E6BC5; font-size:14px;}
#textnews a:hover{color:#26B170;}
.details{ width:90%; height:45px; line-height:25px;;overflow:hidden; background:#F8F8FF; border: 1px solid #DDD; margin-bottom: 15px;  padding: 10px; font-size:14px; text-indent:28px;}
</style>
<?php 
if(!empty($tag['data.results']))
{
		?>
		<div class="newcalllist">
			<ul>
			<?php 
			foreach ($tag['data.results'] as $k=>$data)
			{
			?>
             		<li>
                    	<span><?php echo date('Y-m-d',strtotime($data['dtTime'])); ?></span>
                        <div id="textnews">
                        	<a href="<?php echo sys_href($params['id'],'view',$data['id'])?>" title="<?php echo $v['title']; ?>"><strong><?php echo $data['title']; ?></strong></a>
							<div class="details"><?php echo $data['description']; ?></div>
							<p><a href="<?php echo sys_href($params['id'],'view',$data['id'])?>" title="<?php echo $v['title']; ?>"><img src="<?php echo ispic($data['picture']); ?>" width="400" height="300" /></a></p>
                        </div>
					</li>
             	     
			<?php	
			}
			?>
            	</ul>
             </div>
		<div style="float:right" id="articeBottom"><?php if(!empty($tag['pager.cn'])) echo $tag['pager.cn']; ?></div>
	     <?php
}			
?>
