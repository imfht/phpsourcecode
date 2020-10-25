<?php echo $header; ?>

<body style="height:100%;">
<!-- website wrapper starts -->
<div class="websiteWrapper"> 
  <!-- page wrapper starts -->
  <div class="pageWrapper homePageWrapper"> 
  
    <!-- header outer wrapper ends --> 
     <?php echo $nav; ?>
    <!-- page content wrapper starts -->
     <?php if($home_image_s==1){ ?>
    <div class="pageContentWrapper" style="background:url(<?php echo $home_image; ?>) no-repeat;background-size: 100% 100%;"> 
 <?php }else{ ?>
  <div class="pageContentWrapper"> 
   <?php } ?>
   
       <a href="" class="mainLogo"> 
       <?php echo $name; ?>
       </a> 
       
         <?php if($home_search==1){ ?>
       <div class="shopSearchFormWrapper">
            <input type="text" value="" id="shopSearchField" class="shopSearchField fieldWithIcon shopSearchFieldIcon" name="q" placeholder="搜索" />
      </div>
       <script>
		$('.shopSearchFormWrapper input[name=\'q\']').keydown(function(e) {
	if (e.keyCode == 13) {
		url = 'index.php?route=product/category&path=0&m=<?php echo SNAME; ?>';
	
		var q = $('#shopSearchField').val();
		
		if (q) {
			url += '&q=' + encodeURIComponent(q);
		}
		location = url;
		}
});

</script>
  <?php } ?>
 <?php if($home_banner==1){ ?>
<div class="swiper-container">
  <div class="swiper-wrapper">

    <?php foreach ($banners as $banner) { ?>
       <div class="swiper-slide"> <a href="<?php echo $banner['link']; ?>"><img alt="<?php echo $banner['title']; ?>" src="<?php echo $banner['image']; ?>"></a></div>
  <?php } ?>
  
<style>
.swiper-container, .swiper-slide, .swiper-slide img {
	width:100%;
  height: 200px;
  padding-bottom:20px;
}
</style>
  </div>
</div>
 <?php }else{ ?>
 <div style="height:200px;"></div>
  <?php } ?>
      <!-- shop search form wrapper starts -->


  <div style="width:80%; margin:auto;">
<div class="homebuttonbox">
 <?php foreach ($navs as $nav) { ?>
        <div class="homebuttonitem">
         <a rel="<?php echo $nav['url']; ?>" class="homebutton jsurl"><?php echo $nav['title']; ?></a>
        </div>
         <?php } ?>
      </div>
 
    </div>
   <br>


<div class="postExcerptWrapper">

          <p class="smallPostQuote">
           <b>店铺状态：</b> <?php echo $store_status; ?><br>
          <b>营业时间:</b> <font color="#FF0000"><?php echo $hoursFrom; ?>—<?php echo $hoursTo; ?></font><br>
          <b>联系电话：</b><?php echo $telephone; ?> <a href="tel:<?php echo $telephone; ?>">呼叫</a><br>
    <b>店铺地址：</b><?php echo $address; ?> <a href="<?php echo $map; ?>">地图</a>
          </p>
        </div>
      
      <!-- shop search form wrapper ends --> 

   

    </div>
    <!-- page content wrapper ends -->
 
    <div class="footer">
   <p><a target="_blank" href="http://www.fuwupu.com">&copy;服务铺提供技术支持</a></p>
    </div>
  </div>
  <!-- page wrapper ends --> 
</div>

<!-- website wrapper ends -->
<script type="text/javascript">
/*======
Use document ready or window load events
For example:
With jQuery: $(function() { ...code here... })
Or window.onload = function() { ...code here ...}
Or document.addEventListener('DOMContentLoaded', function(){ ...code here... }, false)
=======*/

window.onload = function() {
  var mySwiper = new Swiper('.swiper-container',{
    //Your options here:
    mode:'horizontal',
    loop: true
    //etc..
  });  
}

/*
Or with jQuery/Zepto
*/
$(function(){
  var mySwiper = $('.swiper-container').swiper({
    //Your options here:
    mode:'horizontal',
    loop: true
    //etc..
  });
})

</script>

      </div>
</body>

</html>

