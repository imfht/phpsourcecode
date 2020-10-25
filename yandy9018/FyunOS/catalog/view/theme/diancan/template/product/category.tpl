<?php echo $header; ?>
<body>
<style>
.dibu {
	width:100%;
    background: none repeat scroll 0% 0% rgba(255, 118, 118, 1);
    margin:0 auto;
    position:fixed;
    bottom:0;
    text-align:right;

}
.dibu a{
padding: 1em 1em;
text-decoration: none;
color: rgba(255, 255, 255, 1);
font-family:"Microsoft YaHei",微软雅黑;
	font-size:18px;
	font-weight: bold;
}
</style>
 <script>

var page=2;	

var listener = function(){
$$(document).on('tap','.lgadd > button',function(e) {
            var pel = $(e.target.parentNode);
            var id = parseInt(pel.parents('.chanpin').children('input').attr('value'));
            var num = parseInt(pel.children('input').attr('value'));
            //alert(id)
            if ($(e.target).html().indexOf('+') != -1)
                num++;
            else if (num > 0)
                num--;
            pel.children('input').attr('value', num);
            
            //写入cookie，数据结构是 id-num,id-num,id-num,如
            //14-5,1-4,6-2 Id 14 1 6的菜品分别点了5 4 2份
            var ckcart = $.cookie('cart') || '';
            var p = ckcart.indexOf(id + '-');
            if (p != -1) {
                var ed = ckcart.indexOf(',', p);
                var oldVal = ckcart.substr(p, ed - p + 1);
                ckcart = ckcart.replace(oldVal, num == 0 ? '' : id + '-' + num + ',');
				

            }
            else {
                ckcart += id + '-' + num + ',';
				
            }

            $.cookie('cart', ckcart, { expires: 7, path: '/' });
			
			refreshTotal();

        });
};


var readcart = function(){
        var ckcart = $.cookie('cart') || '';
        $('.chanpin').each(function (e) {
            var id = $(this).children('input').attr('value');
            var p = ckcart.indexOf(id + '-');
            if (p == -1)
                return;
            p = ckcart.indexOf('-', p) + 1;
            var ed = ckcart.indexOf(',', p);
            $(this).find('.lgadd > input').attr('value',ckcart.substr(p, ed - p));
        });
};

var listenerku = function () {
		  var lgminus = $(".lgminus");
		  var VarInven = $(".invenfont");
		  var jian = $(".jian");
		  var VarAddtext = $(".addtext");
		  
	   $('.chanpin .goodssbox').each(function(){
			 var AddTextE =parseInt(  $(this).find(VarAddtext).val() );
			var invenfontE =parseInt(  $(this).find(VarInven).text() );//库存
			if(AddTextE > 0){
				$(this).find(jian).removeClass("yincang");
				}
				if( AddTextE >= invenfontE ){
				$(this).find(lgminus).addClass("colordd");
				 $(this).find(lgminus).attr('disabled','disabled');	
				}
			});	
		$$(document).on("tap",".chanpin .goodssbox",function(){
			 $(this).find(jian).removeClass("yincang");
			var invenfont = parseInt(  $(this).find(VarInven).text() );//库存
		    var AddText =parseInt(  $(this).find(VarAddtext).val() );// +1
			 if(  AddText >= invenfont ){
			  $(this).find(VarAddtext).val(invenfont);
			  alert("没货啦！");
			  $(this).find(lgminus).attr('disabled',true);
			 	$(this).find(lgminus).addClass("colordd");
			 }else if( AddText <  invenfont  ){
				$(this).find(lgminus).removeClass("colordd");
				 $(this).find(lgminus).attr('disabled',false);
			 }
			 
		});	

		
 };


	
	
	  $(document).on("ready",function(){
		//alert(2)
		//infroll();
        readcart();
        listener();
		refreshTotal();
		listenerku();
	});	

</script>
<!-- website wrapper starts -->
<div class="websiteWrapper" id="content"> 
  <!-- page wrapper starts -->
  <div class="pageWrapper portfolioTwoPageWrapper"> 
 <?php echo $nav; ?>

   
    <!-- header outer wrapper ends --> 
    
    <!-- portfolio wrapper starts -->

    <div class="pageContentWrapper">
   
      <h3 class="pageTitle">菜单<span style="float:right"><a href="#modal" class="second fenlei">分类</a> <a href="#" class="sousuo">搜索</a></span></h3>
        <!-- shop search form wrapper starts -->
      <div class="shopSearchFormWrapper" style="margin-bottom:0px; display:none;">
        
            <input type="text" value="<?php echo $q; ?>" id="shopSearchField" class="shopSearchField fieldWithIcon shopSearchFieldIcon" name="q" placeholder="搜索" />

      
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
      <!-- shop search form wrapper ends --> 
       <?php if ($products) { ?>      
 <?php foreach ($products as $product) { ?>      
      <!-- portfolio item starts  -->
      <div class="portfolioOneItemWrapper chanpin">
      <input type="hidden"  value="<?php echo $product['product_id']; ?>"  name="product_id">
       <a href="<?php echo $product['href']; ?>" class="portfolioOneItemImageWrapper">
       <img src="<?php echo $product['thumb']; ?>">
     
       </a>
        <?php if ($product['tag']) { ?>
       <div id="i"><span><?php echo $product['tag']; ?></span></div>
         <?php } ?>
         
        <div class="portfolioOneItemInfoWrapper">
          <h4 class="portfolioOneItemTitle"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a><span style="float:right; font-size:18px; font-weight:bold; color:#F60"><?php echo $product['price']; ?>/<?php echo $product['unit']; ?></span></h4>
      
               <?php if ($product['reward']>0) { ?>
                 <p>
                   积分：送<?php echo $product['reward']; ?>分<br>  
                   </p>
                 <?php } ?>

          

        </div>
        
      <?php if ($storeType == 1){ ?>
        <div class="portfolioOneItemButtonsWrapper goodssbox"> 
         <span  class="inventory fr"  style="display: none;">库存<font  class="invenfont"><?php echo $product['quantity']; ?></font> </span>
        <span class="lgadd fr">
         <button type="button" class="lgminus jia">+</button> 
         <button type="button" class="lgplus jian yincang">-</button>  
          <input type="text"  value="0"  name="t1"  size="2"  class="addtext"  maxlength="3" id="tt"  datatype="Number"  readonly="readonly"  msg="必须为数字">
        </span>
        
        </div>
        <?php } ?>
      </div>
      <!-- portfolio item ends --> 
      
      
        <?php } ?>
         <div id="more">
         <?php echo $more; ?>
         </div>
        <?php }else{ ?>
        没有数据，请稍后再来！
       <?php } ?>
   
    </div>
    <!-- portfolio wrapper ends --> 
    
   <!-- footer wrapper starts -->
     <?php if($this->customer->isLogged()){ ?>
    <div class="dibu"><a href="#" class="incart">已选 <font class="count"></font> 现在结算</a> </div>
    <?php }else{ ?>
     <div class="dibu"><a rel="<?php echo $login; ?>" href="#" class="jsurl">已选 <font class="count"></font> 现在结算</a> </div>
      <?php } ?>
    <!-- footer wrapper ends -->
   
    
  </div>
  <!-- page wrapper ends --> 
  
   <style>
	 #modal { display: none; }

            #modal a {color: rgba(255, 255, 255, 1); font-weight: bold; padding: 5px 10px; border: none; }

           #modal p{ background:#666; margin-top:20px; text-align:center; color:#000;}
		   #modal p a{ height:auto;}
			

</style>

       <div id="modal">
           <h3 class="pageTitle">分类</h3>
           <ul>
           <li><a class="jsurl" rel="index.php?route=product/category&path=0&m=<?php echo SNAME; ?>">全部</a></li>
		  <?php foreach ($categories as $category) { ?>
			<li><a class="jsurl" rel="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
         <?php } ?>
           
		   </ul>
            <p><a href="javascript:$.pageslide.close()">关闭</a></p>
        </div>
</div>
<!-- website wrapper ends -->
<script>
  $$('.sousuo').tap(function() { 
   $$('.shopSearchFormWrapper').show();
  
  });
 $$('#more').tap(function() { 
 
// $('#links').addClass("ui-state-disabled");
 $('#links').html('加载中...');
 $.ajax({ 
					type : "GET", 
					url  : "index.php?route=product/category&path=<?php echo $this->request->get['path']; ?>&ajxa=1&page="+page+"&m=<?php echo SNAME; ?>",  
					success : function(result){
						if(result&&result!=1){
							page++;
							 $(".portfolioOneItemWrapper:last").after(result);
							 
							 readcart();
							 listenerku();
							 // $('#links').removeClass("ui-state-disabled");
							  $('#links').html('下一页');
							}else{
								$('#links').html('没有数据了');
								$('#more').fadeOut(500);
								}
							 
					}, 
					error: function() { alert("error"); }
				}); 
				
        }); 
		</script>

    <script src="catalog/view/javascript/fyun/slide/jquery.pageslide.min.js"></script>


 <script>

   



     $(".second").pageslide({ direction: "left", modal: true });
    </script>
    
    
    

</body>
</html>

