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
form{ background:#EDFBFF; padding-bottom:20px; border:1px solid #ccc; border-bottom:none;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
.poll{ width:99%; float:left; border-bottom:1px solid #ccc;}
.poll p{ line-height:35px; font-size:14px; padding-left:20px;}
.poll .polltitle{ height:40px; margin-bottom:10px; background:url(<?php echo $tag['path.skin']; ?>res/images/poll_title.jpg) repeat-x; border-bottom:1px solid #ccc;}
.jbjionbt a{ padding:0.2em 1em; margin-right:15px;-moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: #7FBF4D; background-image: -moz-linear-gradient(center top , #7FBF4D, #63A62F); border-color: #63A62F #63A62F #5B992B; border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; box-shadow: 0 1px 0 0 #96CA6D inset; font-family:"微软雅黑"; color: #FFFFFF; font: 12px; text-align: center; text-shadow: 0 -1px 0 #4C9021; width:80px; cursor:pointer;}
.jbjionbt a:hover { background-color: #76B347; background-image: -moz-linear-gradient(center top , #76B347, #5E9E2E); box-shadow: 0 1px 0 0 #8DBF67 inset; cursor: pointer;}
.creatbt { background-color: #ECECEC; background-image: -moz-linear-gradient(#F4F4F4, #ECECEC); border: 1px solid #D4D4D4; border-radius: 0.2em 0.2em 0.2em 0.2em; color: #333333; cursor: pointer; display: inline-block; font: 12px; outline: medium none; overflow: visible; padding: 0.5em 1em; position: relative; text-decoration: none; text-shadow: 1px 1px 0 #FFFFFF; white-space: nowrap; margin-right:10px;}
.creatbt:hover, .creatbt:focus, .creatbt:active { background-color: #3072B3; background-image: -moz-linear-gradient(#599BDC, #3072B3); border-color: #3072B3 #3072B3 #2A65A0; color: #FFFFFF; text-decoration: none; text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.3); }
-->
</style>
<div class="poll">
<?php
if( !empty( $tag['data.results'] ) )
{
	foreach($tag['data.results'] as $k =>$data)
	{
			?>
      <form method="post" action="<?php echo sys_href($data['channelId'],'poll_send',$data['id']);?>">
      <p class="polltitle"><span style=" font-weight:bold;"><?php echo $data['title']; ?></span> （<?php echo date('Y年m月d日',strtotime($data['dtTime'])); ?>开始投票）
       <?php
			if( !empty( $data['children'] ) )
			{
				foreach($data['children'] as $children_data)
				{
				?>
              </p>
              <p style=" padding-left:40px;"><input type="<?php echo $data['choice']=='a'?'radio':'checkbox'?>" name="choice<?php echo $data['choice']=='a'?'':'[]'?>" value="<?php echo $children_data['id']; ?>" <?php echo $children_data['isdefault']=='a'?'checked':'';?>>
              <?php echo $children_data['choice'] ?>
		<?php 
				}
			}
		?>
        </p>
        <p align="center">
          <input type="submit" value="投票" class="creatbt">
          &nbsp;&nbsp;&nbsp;
          <input type="button" value="查看" class="creatbt" onclick="window.location.href='<?php echo sys_href($data['channelId'],'poll',$data['id']);?>'">
        </p>
      </form>
      <?php
	}
}
else
{
	echo '暂无投票列表。';
}
?></div>
<div id="articeBottom">
  <?php if(!empty($tag['pager.cn'])) echo $tag['pager.cn']; ?>
</div>