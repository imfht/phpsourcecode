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
<?php $data=$tag['data.row'];?>
<link rel="stylesheet" type="text/css" href="<?php echo $tag['path.skin']; ?>res/css/demo.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $tag['path.skin']; ?>res/css/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $tag['path.skin']; ?>res/css/elastislide.css" />
<script>
var _mobanUrl="<?php echo $tag['path.skin']; ?>";
</script>
<script type="text/javascript" src="<?php echo $tag['path.skin']; ?>res/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $tag['path.skin']; ?>res/js/jquery.tmpl.min.js"></script>
<script type="text/javascript" src="<?php echo $tag['path.skin']; ?>res/js/jquery.elastislide.js"></script>
<script type="text/javascript" src="<?php echo $tag['path.skin']; ?>res/js/gallery.js"></script>
<script id="img-wrapper-tmpl" type="text/x-jquery-tmpl">	
    <div class="rg-image-wrapper">
        {{if itemsCount > 1}}
            <div class="rg-image-nav">
                <a href="#" class="rg-image-nav-prev">Previous Image</a>
                <a href="#" class="rg-image-nav-next">Next Image</a>
            </div>
        {{/if}}
        <div class="rg-image"></div>
        <div class="rg-loading"></div>
        <div class="rg-caption-wrapper">
            <div class="rg-caption" style="display:none;">
                <p></p>
            </div>
        </div>
    </div>
</script>
<style type="text/css">
.rg-caption-wrapper p{ color:#fff;}
</style>
<div id="rg-gallery" class="rg-gallery">
    <div class="rg-thumbs">
        <!-- Elastislide Carousel Thumbnail Viewer -->
        <div class="es-carousel-wrapper">
            <div class="es-nav">
                <span class="es-nav-prev">Previous</span>
                <span class="es-nav-next">Next</span>
            </div>
            <div class="es-carousel">
                <ul>
                 <?php 
			$originalPic = explode('|',$data['originalPic']);
			$middlePic   = explode('|',$data['middlePic']);
			$smallPic    = explode('|',$data['smallPic']);
			for($i=0;$i<count($originalPic);$i++)
			{
		  ?>
          <li><a href="#"><img src="<?php echo ispic($smallPic[$i])?>" data-large="<?php echo ispic($originalPic[$i])?>" data-description="<?php echo $data['description']; ?>"/></a></li>
        <?php
		    }?>
                
                </ul>
            </div>
        </div>
        <!-- End Elastislide Carousel Thumbnail Viewer -->
    </div><!-- rg-thumbs -->
</div><!-- rg-gallery -->
