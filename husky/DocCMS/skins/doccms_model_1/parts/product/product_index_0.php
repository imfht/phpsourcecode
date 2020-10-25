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
<style type="text/css">
<!--
.doc_pro_list { font-size:12px; }
.doc_pro_list li { clear: both; height:175px; padding:0 120px 0 210px; position:relative; list-style:none; border-bottom:1px solid #ccc; }
.doc_pro_list .doc_colorbox { position:absolute; left:10px; top:20px; width:160px; height:120px; padding:5px; border:1px #ccc solid; }
.doc_pro_list .doc_colorbox img { width:160px; height:120px; }
.doc_pro_list .doc_plug { width:100px; height:60px; position:absolute; right:5px; top:50px; }
.doc_pro_list .doc_plug a { display:block; height:28px; line-height:28px; padding-left:20px; }
.doc_pro_list .doc_detailed { background:url(<?php echo $tag['path.skin'];
?>res/images/product_05.png) no-repeat left center;
}
.doc_pro_list .doc_cart { background: url(<?php echo $tag['path.skin'];
?>res/images/product_06.png) no-repeat left center;
}
.doc_pro_list h3 { margin:0; width:100%; height:40px; line-height:40px; padding-top:5px; overflow:hidden; }
.doc_pro_list h3 a { font-weight:bold; font-size:13px; color:#0CF; }
.doc_pro_list p { width:100%; padding:0 0 8px 0; line-height:24px; height:70px; overflow:hidden; margin:0; }
#articeBottom { font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%; }
.details { height:90px; overflow:hidden; line-height:25px; }
.details h2 { font-size:12px; font-weight:normal; }
-->
</style>
<link rel="stylesheet" href="<?php echo $tag['path.skin']; ?>res/css/colorbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo $tag['path.skin'];?>res/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $tag['path.skin'];?>res/js/jquery.colorbox.js"></script>
<script>
	$(document).ready(function(){
		$(".doc_colorbox").colorbox({rel:'colorbox', transition:"fade"});
	});
</script>
<!--
以下为订单系统，如有需要自行去掉注释
<div style="margin-bottom:15px; height:25px;">
	<span class="pic_more"><a href="<?php echo sys_href($request['p'],'product_basket')?>" title="查看"><img src="<?php echo $tag['path.skin']; ?>res/images/basket/cart.jpg" width="118" height="25" /></a></span>
</div>
-->
<div class="doc_pro_list">
  <ul>
    <?php
    
	if( !empty( $tag['data.results'] ) )
	{	
		foreach($tag['data.results'] as $k =>$data)
	    {
		  ?>
    <li> <a class="doc_colorbox" href="<?php echo $data['originalPic']?>" title="<?php echo $data['title']; ?>"><img src="<?php echo $data['smallPic']; ?>" width="160" height="120" alt="<?php echo $data['description'];?>"></a>
      <h3><a href="<?php echo sys_href($data['channelId'],'product',$data['id'])?>"><?php echo $data['title']; ?></a></h3>
      <p><?php echo $data['description']; ?></p>
      <div class="doc_plug"> <a href="<?php echo sys_href($data['channelId'],'product',$data['id'])?>" class="doc_detailed">详细参数</a> <a href="<?php echo sys_href($data['channelId'],'product_intobasket',$data['id'])?>" class="doc_cart">加入购物车</a> </div>
    </li>
    <?php
        }
    }
    else
    {
        echo '<br />暂无产品列表。';
    }
?>
  </ul>
</div>
<div class="clear"></div>
<div  id="articeBottom">
  <?php if(!empty($tag['pager.cn'])) echo $tag['pager.cn']; ?>
</div>
