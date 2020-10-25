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
img{ border:none;}
#productlist{ width:98%; float:left; font-size:12px;}
.probox{float:left; width:210px; background:url(<?php echo $tag['path.skin']; ?>res/images/probox_bg3.gif) 5px 6px no-repeat; height: 100%; margin:20px 10px 0 5px; border:1px solid #ccc; padding:10px 0;}
.probox img{ margin:10px 0 0 20px; width:172px; height:129px; float:left;}
.probox h3{ font-size:12px;text-align:center; display:block; font-size:14px; height:30px; line-height:30px; overflow:hidden; float:left;}
.proinfo{ padding:20px 0 0 10px; text-align:left; float:left;}
.proinfo p{ width:180px; line-height:28px; overflow:hidden; float:left;text-overflow:ellipsis; white-space:nowrap;}
.detail{ background:url(<?php echo $tag['path.skin']; ?>res/images/details.gif) no-repeat; float:right; width:80px; height:23px;}
.details{ width:90%; height:70px; line-height:25px;;overflow:hidden; margin-bottom: 15px; text-indent:24px;}
.details h2{ font-size:14px;}
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
</style>
<link rel="stylesheet" href="<?php echo $tag['path.skin']; ?>res/css/colorbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo $tag['path.skin'];?>res/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $tag['path.skin'];?>res/js/jquery.colorbox.js"></script>
<script>
	$(document).ready(function(){
		$(".colorbox").colorbox({rel:'colorbox', transition:"fade"});
	});
</script>
<!--
以下为订单系统，如有需要自行去掉注释
<div style="margin-bottom:15px; height:25px;">
	<span class="pic_more"><a href="<?php echo sys_href($request['p'],'product_basket')?>" title="查看"><img src="<?php echo $tag['path.skin']; ?>res/images/basket/cart.jpg" width="118" height="25" /></a></span>
</div>
-->
<div id="productlist">
<?php
	if( !empty( $tag['data.results'] ) )
	{	
		foreach($tag['data.results'] as $k =>$data)
	    {
		  ?>
        <div class="probox">
            <a class="colorbox" href="<?php echo $data['originalPic']?>" title="<?php echo $data['title']; ?>"><img src="<?php echo $data['smallPic']; ?>" alt="<?php echo $data['title']; ?>" width="140" height="105" /></a>
            <div class="proinfo">
                <h3><a href="<?php echo sys_href($data['channelId'],'product',$data['id'])?>"><?php echo $data['title']; ?></a></h3>
                <div class="details"><?php echo $data['description']; ?></div>
                <p><a href="<?php echo sys_href($data['channelId'],'product',$data['id'])?>" class="detail"></a></p>
            </div>
        </div>
		 <?php
        }
    }
    else
    {
        echo '<br />暂无产品列表。';
    }
?>
  <div class="clear"></div>
  <div  id="articeBottom"><?php if(!empty($tag['pager.cn'])) echo $tag['pager.cn']; ?></div>
 </div> 
