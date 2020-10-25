<?php echo $header; ?>

<body> 
<script>
$$('#jiaru').tap(function() {
            var id =  <?php echo $product_id ?>;
            var num=0;
            //写入cookie，数据结构是 id-num,id-num,id-num,如
            //14-5,1-4,6-2 Id 14 1 6的菜品分别点了5 4 2份
            var ckcart = $.cookie('cart') || '';
            var p = ckcart.indexOf(id + '-');
			num++;
            if (p != -1) {
                var ed = ckcart.indexOf(',', p);
                var oldVal = ckcart.substr(p, ed - p + 1);
                ckcart = ckcart.replace(oldVal, num == 0 ? '' : id + '-' + num + ',');

            }
            else{
                ckcart += id + '-' + num + ',';
            }

            $.cookie('cart', ckcart, {expires:7,path:'/'});
			$$("#jiaru").hide();
			$$("#progressBar").html('已加入');
							  $$("#progressBar").show();
							  $("#progressBar").delay(500);
							  $("#progressBar").fadeOut(500); 
			
});
			
</script>
<!-- website wrapper starts -->
<div class="websiteWrapper"> 
  <!-- page wrapper starts -->
  <div class="pageWrapper loginPageWrapper"> 
   <?php echo $nav; ?>
    <!-- header outer wrapper ends --> 
    
    <!-- page content wrapper starts -->
    
    <div class="pageContentWrapper"> 
      
      <!-- page title starts -->
      <h3 class="pageTitle"><?php echo $heading_title; ?></h3>
      <!-- page title ends -->
      <div style="height: 175px;" class="singleProductGallery"> 
      <img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image"   />
      
       </div>
      
        <style>
		  #price1{
			  font-size:24px;
			  padding-bottom:20px;
			  line-height:30px;
			  }
			  </style>
         <?php if ($storeType == 1){ ?>
             <div id="price1">￥<?php echo $price; ?> / <?php echo $unit; ?></div> <div id="jiaru"><a href="#" class="singleProductPurchaseButton">加入结算</a></div>
          <?php }else{ ?>
          <div id="price1">￥<?php echo $price; ?> / <?php echo $unit; ?></div>
      
             <?php } ?>
      <div class="accordionItemWrapper"> <a href="" class="accordionButton currentAccordion"><span class="accordionButtonIcon"></span><span class="accordionButtonTitle">详细介绍</span></a>
          <div style="display: block;" class="accordionContentWrapper">
            <div class="accordionContent">
        <p><?php echo $description; ?></p>
            </div>
          </div>
        </div>
      
    
        
    </div>
    
    </div>
    
    </div>


	</body>
</html>