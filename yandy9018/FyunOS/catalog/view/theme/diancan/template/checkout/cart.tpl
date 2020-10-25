

<script type="text/javascript">
  var payment = function () {
	
	  if(round2(Payme,2) >= round2(CarAllCash,2)){
			$('#payme').html("余额：<font style='color:red;font-weight:bold;'>"+round2(Payme,2)+" </font>元，扣款 <font style='color:blue;font-weight:bold;'>"+round2(CarAllCash,2)+"</font>元" );
			
			}else if((round2(Payme,2) < round2(CarAllCash,2)) && round2(Payme,2) > 0){
				var last = (round2(CarAllCash,2) - round2(Payme,2));
				$('#payme').html("余额：<font style='color:red;font-weight:bold;'>"+round2(Payme,2)+" </font> 元，剩余 <font style='color:red;font-weight:bold;'>"+ round2(last,2) +"</font> 元货到付款" );
				
		  }else{
				  $('#payme').html(" <font style='color:red;font-weight:bold;'>"+ round2(CarAllCash,2) +"</font> 元货到付款" );
			}
			
			
			
		 if(round2(Payme,2) >= round2(MoToFixed,2)){
			$('#paymea').html("余额：<font style='color:red;font-weight:bold;'>"+round2(Payme,2)+" </font>元，扣款 <font style='color:blue;font-weight:bold;'>"+round2(MoToFixed,2)+"</font>元" );
			
			}else if((round2(Payme,2) < round2(MoToFixed,2)) && round2(Payme,2) > 0){
				var last = (round2(MoToFixed,2) - round2(Payme,2));
				$('#paymea').html("余额：<font style='color:red;font-weight:bold;'>"+round2(Payme,2)+" </font> 元，剩余 <font style='color:red;font-weight:bold;'>"+ round2(last,2) +"</font> 元付现金" );
				
		  }else{
				  $('#paymea').html(" <font style='color:red;font-weight:bold;'>"+ round2(MoToFixed,2) +"</font> 元付现金" );
			  }
	 
 };
 
 
 var peisong = function () {
 if( PackingFee && PackingFee != 0){
							
						CarAllCash = round2(MoToFixed,2) + round2(parseFloat(PackingFee),2);
						$('.song').html(PackingFee);
						
						$('.carfee').html("配送费 <font style='color:red;font-weight:bold;'>" + PackingFee + " </font>元 + 订单 <font style='color:red;font-weight:bold;'>" + round2(MoToFixed,2) + " </font> 元  = ");

}else{
					  CarAllCash = round2(MoToFixed,2);
					  $('.song').html(PackingFee);
					 $('.carfee').html("免配送费 + 订单 <font style='color:red;font-weight:bold;'> " + round2(MoToFixed,2) + " </font> 元 = ");
					
					
					}
					   $('.ding').html(round2(MoToFixed,2));
					  // alert(MoToFixed);
					   $('.cartotal').html(CarAllCash);
					
 };
 
function changeZone(zoneid){ 
$('select[name=\'city_id\']').load("index.php?route=common/localisation/city&zone_id="+zoneid+"&m=<?php echo SNAME; ?>",function(){
	 document.all.city_id.options[0].selected=true;
	// $('select[name=\'city_id\']').selectmenu('refresh', true); 
});
}

var MoToFixed =<?php echo $moToFixed; ?>;
var CarOnSale=0;
var CarAllCash=<?php echo $carAllCash; ?>;
var PackingFee= <?php echo $shipping_cost; ?>;
var Payme = <?php echo $balance; ?>;
var istopcart = false;
<?php if(!$telephone){ ?> 
 var Telephone = 1;
 <?php }else{ ?>
   var Telephone = 2;
  <?php } ?>   

function changefee(s){ 
if( s ){
	$('#peisong').load("index.php?route=common/localisation/geo_zone&v=1&city_id="+s+"&m=<?php echo SNAME; ?>");


var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
   		 s=xmlhttp.responseText;
		 //alert($('.cartotal').html(round2(CarAllCash,2)));
	  
		PackingFee=s;
		peisong();
		payment();
		
	 
	  
    }
  }

xmlhttp.open("GET","index.php?route=common/localisation/geo_zone&v=2&city_id="+s+"&m=<?php echo SNAME; ?>",true);
xmlhttp.send();
}

}			
</script>

<style>
#tabs {
 overflow: hidden;
  width: 100%;
  margin: 0;
  padding: 0;
  list-style: none;
}

#tabs li {
  margin: 0 .5em 0 0;
}

#tabs a {
  position: relative;
  background: #ddd;
  background-image: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#ddd));
  background-image: -webkit-linear-gradient(top, #fff, #ddd);
  background-image: -moz-linear-gradient(top, #fff, #ddd);
  background-image: -ms-linear-gradient(top, #fff, #ddd);
  background-image: -o-linear-gradient(top, #fff, #ddd);
  background-image: linear-gradient(to bottom, #fff, #ddd);  
  padding: .7em 1em;
  float: left;
  text-decoration: none;
  color: #444;
  text-shadow: 0 1px 0 rgba(255,255,255,.8);
  -moz-box-shadow: 0 2px 2px rgba(0,0,0,.4);
  -webkit-box-shadow: 0 2px 2px rgba(0,0,0,.4);
  box-shadow: 0 2px 2px rgba(0,0,0,.4);
}

#tabs a:hover,
#tabs a:hover::after,
#tabs a:focus,
#tabs a:focus::after {
  background: #fff;
}

#tabs a:focus {
  outline: 0;
}

#tabs a::after {
  content:'';
  position:absolute;
  z-index: 1;
  top: 0;
  right: -.5em;  
  bottom: 0;
  width: 1em;
  background: #ddd;
  background-image: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#ddd));
  background-image: -webkit-linear-gradient(top, #fff, #ddd);
  background-image: -moz-linear-gradient(top, #fff, #ddd);
  background-image: -ms-linear-gradient(top, #fff, #ddd);
  background-image: -o-linear-gradient(top, #fff, #ddd);
  background-image: linear-gradient(to bottom, #fff, #ddd);  
  -moz-box-shadow: 2px 2px 2px rgba(0,0,0,.4);
  -webkit-box-shadow: 2px 2px 2px rgba(0,0,0,.4);
  box-shadow: 2px 2px 2px rgba(0,0,0,.4);
  -webkit-transform: skew(10deg);
  -moz-transform: skew(10deg);
  -ms-transform: skew(10deg);
  -o-transform: skew(10deg);
  transform: skew(10deg);
}

#tabs #current a {
  background: #fff;
  z-index: 3;
}

#tabs #current a::after {
  background: #fff;
  z-index: 3;
}

/* ------------------------------------------------- */

#content1 {
    background: #fff;
    padding: 2em;
    position: relative;
    z-index: 2;	
    -moz-box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
    -webkit-box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
    box-shadow: 0 -2px 3px -2px rgba(0, 0, 0, .5);
}

/* ------------------------------------------------- */

#about {
    color: #999;
}

#about a {
    color: #eee;
}

</style>

  <!-- page wrapper starts -->
      <!-- page title starts -->
      <h3 class="pageTitle">餐车</h3>
      <!-- page title ends -->
      
      <!-- checkout form starts -->
    
       <?php foreach ($products as $product) { ?>
       
        <!-- checkout product starts -->
        <div class="checkoutProductWrapper">
         <a href="" class="checkoutProductImageWrapper"><img src="<?php echo $product['thumb']; ?>" class="checkoutProductImage" alt=""></a>
          <div class="checkoutProductInfoWrapper"> <a href="" class="checkoutProductTitle"><?php echo $product['name']; ?></a>
            <div class="checkoutProductButtonsWrapper">
          <input type="hidden" value="<?php echo $product['product_id']; ?>" class="product_id">
          <input type="hidden" value="<?php echo $product['quantity1']; ?>" class="nsum">
          <input type="hidden" class="fjf" value="<?php echo $product['reward']; ?>">
          <input type="hidden" class="fdj" value="<?php echo $product['price']; ?>">
          
              <span class="checkoutProductPrice"><?php echo $product['price']; ?>元/<?php echo $product['unit']; ?> × <?php echo $product['quantity1']; ?>
                  </span>
                  
<a href="#" class="checkoutRemoveProductButton"></a>
              </div>
          </div>
        </div>
        <!-- checkout product ends --> 

         <?php } ?>
       
       <ul id="tabs">
    <li><a href="#" name="tab1">店内下单</a></li>
    <?php if ($this->config->get('config_distribution')==1) { ?> 
    <li><a href="#" name="tab2">外卖配送</a></li>
      <?php } ?>   
</ul>

<div id="content1"> 
  
    <div id="tab1">
<form id="form1">
  <input type="hidden" name="type" value="1">
  <fieldset>
       	<label for="name1"></label>
		<input type="text" name="firstname" id="name" placeholder="在这里输入您的姓名/称呼" value="<?php echo $name; ?>" data-clear-btn="true">
             
                     <?php if($telephone){ ?>  
                  <div class="formFieldWrapper">
					<label for="telephone">手机号码:</label>
					<input readonly="readonly" type="text" name="telephone" id="telephone" placeholder="在这里输入您电话"  value="<?php echo $telephone; ?>">
			    </div>
                         
               
<?php }else { ?> 

    
             <!-- login form wrapper starts -->
            <div class="formFieldWrapper">
              <label for="loginNameField">手机号码:</label>
              <input type="text" value="" id="telephone" class="loginNameField fieldWithIcon userFieldIcon" name="telephone" />
            </div>
            
       <div class="columnWrapper oneHalf">
       <div class="formFieldWrapper">
            
              <label for="loginPasswordField">验证码:</label>
              <input type="text" value="" id="codeRand" class="loginPasswordField fieldWithIcon passwordFieldIcon" name="codeRand" />
              
            </div>
      </div>
      
      
      <div class="columnWrapper oneHalf lastColumn">
     <div id="btn" class="formFieldWrapper">
      <label for="kong"></label>
      <a id="cr" href="#" class="buttonWrapper buttonBlue cr">获取</a>
      </div>
      </div>



      <!-- login form wrapper ends -->
                <?php } ?> 
                  <div class="clear"></div>
                 <div class="formFieldWrapper">
                 <label for="seat">位置:</label>
                 
    <select id="seat" class="seat" name="seat">
     <option value="">请选择桌号</option>
    <?php foreach ($seat as $i) { ?>
    <option value="<?php echo $i; ?>"> <?php echo $i; ?>号桌</option>
    <?php } ?>
    </select>
        </div>
             <!-- checkout info wrapper starts -->
        <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">商品数量:<font color="#FF0000" class="count"></font> 个</span> </div>
           <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">订单金额:<font color="#FF0000" class="ding"></font> 元</span> </div>
            <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">获得积分:<font color="#FF0000" class="alljf"></font> 分</span> </div>
           
              <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">本次消费: <font color="#FF0000" class="ding"></font> 元</span> </div>
       
        <!-- checkout info wrapper ends -->
         <div class="postExcerptWrapper">
          <p class="smallPostQuote" id="paymea"></p>

        </div>
        <div class="checkoutButtonsWrapper">
            <?php if ($store_status==1) {?>     
        <a href="#" id="confirm" class="singleProductPurchaseButton">确认下单</a>
             <?php }else{ ?>     
               <a href="#" id="" class="singleProductPurchaseButton">店铺已关闭</a>
                 <?php } ?>  
        </div>
         </fieldset>
    </form>
    </div>
     

    <div id="tab2">
 <form id="form2">
   <input type="hidden" name="type" value="2">
  <fieldset> 
       		<label for="name1"></label>
					<input data-corners="false" type="text" name="firstname" id="name1" placeholder="在这里输入您的姓名/称呼" value="<?php echo $name; ?>" data-clear-btn="true">
             
                     <?php if($telephone){ ?>  
                  <div class="formFieldWrapper">
                  <label for="telephone1">手机号码:</label>
					<input class="ui-state-disabled" data-corners="false" type="text" readonly="readonly" name="telephone" id="telephone1" placeholder="在这里输入您电话"  value="<?php echo $telephone; ?>">
			    </div>
                         
               
<?php }else { ?> 

    
             <!-- login form wrapper starts -->
            <div class="formFieldWrapper">
              <label for="loginNameField">手机号码:</label>
              <input type="text" value="" id="telephone" class="loginNameField fieldWithIcon userFieldIcon" name="telephone" />
            </div>
            
       <div class="columnWrapper oneHalf">
       <div class="formFieldWrapper">
            
              <label for="loginPasswordField">验证码:</label>
              <input type="text" value="" id="codeRand" class="loginPasswordField fieldWithIcon passwordFieldIcon" name="codeRand" />
              
            </div>
      </div>
      
      
      <div class="columnWrapper oneHalf lastColumn">
     <div id="btn" class="formFieldWrapper">
      <label for="kong"></label>
      <a id="cr" href="#" class="buttonWrapper buttonBlue cr">获取</a>
      </div>
      </div>



      <!-- login form wrapper ends -->
                <?php } ?> 
                   <div class="columnWrapper oneHalf">
 
              <div class="formFieldWrapper">
              <label for="zone_id">区域</label>

     <select id="zone_id" name="zone_id" onChange="changeZone(this.options[this.options.selectedIndex].value)">
               </select>  
           
      </div>
      
         </div>
      
      <div class="columnWrapper oneHalf lastColumn">
  <div class="formFieldWrapper">
         
              
      
       <label for="city_id"></label>
     <select  id="city_id" name="city_id" onChange="changefee(this.options[this.options.selectedIndex].value)" >
    
                  </select>         
          </div>
      </div>
      <script>
 $('select[name=\'zone_id\']').load('index.php?route=common/localisation/zone&country_id=<?php echo $country_id; ?>&zone_id=<?php echo $zone_id; ?>&m=<?php echo SNAME; ?>',function(){
	  //$('select[name=\'zone_id\']').selectmenu('refresh', true); 
	  });

    $('select[name=\'city_id\']').load('index.php?route=common/localisation/city&zone_id=<?php echo $zone_id; ?>&city_id=<?php echo $city_id; ?>&m=<?php echo SNAME; ?>',function(){
		 //$('select[name=\'city_id\']').selectmenu('refresh', true); 
		
		});
</script>
      <div class="clear"></div>
        <div class="formFieldWrapper">
              <label for="registerUserNameField">地址:</label>
              <input type="text"  value="<?php echo $address; ?>" id="address" class="registerUserNameField fieldWithIcon addressFieldIcon" name="address" />
            </div>
            
          <!-- checkout info wrapper starts -->
        <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">商品数量:<font color="#FF0000" class="count"></font> 个</span> </div>
           <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">订单金额:<font color="#FF0000" class="ding"></font> 元</span> </div>
            <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">获得积分:<font color="#FF0000" class="alljf"></font> 分</span> </div>
             <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">配送费用: <font color="#FF0000" class="song"></font> 元</span> </div>
              <div class="checkoutInfoWrapper"> <span class="checkoutProductsNumber">本次消费:<font color="#FF0000" class="cartotal"></font> 元</span> </div>
       
        <!-- checkout info wrapper ends -->
        <div class="postExcerptWrapper">
          <p class="smallPostQuote" id="payme"></p>
        
             <p> <span class="smallPostQuoteAuthor" id="peisong"> - <?php echo $shipping_time; ?></span></p>
           
        </div>
        <div class="checkoutButtonsWrapper">
        
           <?php if ($store_status==1) {?>     
        <a href="#" id="confirm1" class="singleProductPurchaseButton">确认下单</a>
             <?php }else{ ?>     
               <a href="#" id="" class="singleProductPurchaseButton">店铺已关闭</a>
                 <?php } ?>  
        </div>
          </fieldset>
    </form>
    </div>
   
  

            
    
    
      <!-- checkout form ends -->
    </div>
    <!-- page content wrapper ends -->
    

    
 