  <?php if ($logged) { ?>
  <!-- navbar -->
    <header class="navbar navbar-inverse" role="banner">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" id="menu-toggler">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $home; ?>"><img src="view/image/logo.png"></a>
        </div>
          <ul class="nav navbar-nav hidden-xs" style="padding-left:20px;">
         <li class="dropdown">
                <a href="#" class="dropdown-toggle hidden-xs hidden-sm" data-toggle="dropdown">
          <?php echo $config_name; ?> <b class="caret"></b>
                </a>
                   <ul class="dropdown-menu ">
                   <div style="margin:5px;">
                   扫描二维码访问系统
                    <hr>
                    <img src="http://qr.liantu.com/api.php?text=<?php echo $store_url; ?>&w=200"/>
                   </div>
                </ul>
            </li>
         </ul>
        <ul class="nav navbar-nav pull-right hidden-xs">

              <style>
			  .n{display:none;}
			  </style>
         <?php if ($store_status==1) { ?>
         <li class="shop_close"> <a id="shopstatus" title="店铺正在营业中"><i class="icon-pause"></i></a> <li>
         <li class="shop_open" style="display:none; background-color:#000;"> <a id="shopstatus" title="已经闭店"><i class="icon-play"></i></a> <li>
          <?php }else { ?>
         <li class="shop_close" style="display:none;"><a id="shopstatus" title="店铺正在营业中"><i class="icon-pause"></i></a> <li>
            <li class="shop_open" style="background-color:#000;"><a id="shopstatus" title="已经闭店"><i class="icon-play"></i></a> <li>
           <?php } ?>
         
            <style>
			.red{ background-color:#C03;}
			</style>
            <li class="notification-dropdown notie<?php if($newOrdersCount!=0){ ?> red<?php } ?>">
                <a href="#" class="trigger">
                    <i class="icon-bell"></i>
                    <span class="count"><b id="noc"><?php echo $newOrdersCount; ?></b></span>
                </a>
                <div class="pop-dialog">
                    <div class="pointer right">
                        <div class="arrow"></div>
                        <div class="arrow_border"></div>
                    </div>
                    <div class="body">
                        <a href="#" class="close-icon"><i class="icon-remove"></i></a>
                        <div class="notifications">
                            <h3>您当前有<b id="noc1"><?php echo $newOrdersCount; ?></b>条订单未处理</h3>
                            
                            <div id="jieshou">
                            
                       <?php foreach ($new_orders as $newOrder) { ?>
                           <span> <a href="<?php echo $newOrder['href']; ?>" class="item">
                                <i class="icon-signin"></i><?php echo $newOrder['order_id']; ?>
                                <span class="time"><i class="icon-time"></i><?php echo $newOrder['addtime']; ?></span>
                            </a></span>
                            <?php } ?>
                            
                          </div>

                         
                            <div class="footer">
                                <a href="#" class="logout">全部未处理订单</a>
                            </div>
                            
                        </div>
                    </div>
<script> 
$(function(){ 
     $('<audio id="chatAudio"><source src="view/voice/c.mp3" type="audio/mpeg"></audio>').appendTo('body');//载入声音文件
 });
 </script> 

  <?php if ($push_key){ ?>   
<script src='view/javascript/socket.io.js'></script> 
  <script src='view/javascript/yunba-1.0.1.js'></script> 

<script> 
var i = <?php echo $newOrdersCount; ?>;
var msgbus = new Yunba({server:'sock.yunba.io', port:3000, appkey:'<?php echo $push_key ?>'});
  msgbus.init(function(success){
    if(success){
       $("#fyuncloud").html("<div class='alert alert-success'><i class='icon-ok-sign'></i>正在连接云服务...<a class='close' data-dismiss='alert'>×</a></div>");  
      msgbus.connect( function(success,msg){
      if(success){
        
        dingyue();
      } else {
        alert(msg);
      }
  });   
function dingyue(){

  msgbus.subscribe( {'topic':'order_id' }, function(success,msg){
      if(success){
         $("#fyuncloud").html("<div class='alert alert-success'><i class='icon-ok-sign'></i>已连接云服务！<a class='close' data-dismiss='alert'>×</a></div>");  
      
      } 
    },function(data){
        $.ajax({ 
            type : "get", 
            url  : "index.php?route=sale/order/ajaxOrder&token=<?php echo $token;?>&order_id="+data.msg+'&m=<?php echo SNAME; ?>',  
            dataType:'html',
            success : function(data){
              i++; 
              $("#jieshou span:first").before(data);
              $("#noc,#noc1").html(i);
              $(".notie").addClass("red");
              $('#chatAudio')[0].play();
            }
        });
      });
}

    }
});
//===================================================================



//========================================================
</script> 
<?php } ?> 


</div>
            </li>
          
            <li class="dropdown">
                <a href="#" class="dropdown-toggle hidden-xs hidden-sm" data-toggle="dropdown">
                    <?php echo $logged; ?>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                   <li><a href="<?php echo $logout; ?>" >  <?php echo $text_logout; ?></a></li>
                </ul>
            </li>
            <li class="settings hidden-xs hidden-sm">
            <a role="button" href="<?php echo $custom; ?>"> <i class="icon-cog"></i></a>
               
            </li>
            <li>
         <a data-toggle="modal" href="#myModal"><i class="icon-exclamation-sign"></i></a>
          </li>
        </ul>
    </header>
    <!-- end navbar -->
      <!-- sidebar -->
    <div id="sidebar-nav">
        <ul id="dashboard-menu">
            <li id="home">
              
                <a href="<?php echo $home; ?>">
                    <i class="icon-home"></i>
                    <span><?php echo $text_dashboard; ?></span>
                </a>
            </li>            
          
          <li id="category">
                <a class="dropdown-toggle" href="#">
                    <i class="icon-group"></i>
                    <span><?php echo $text_catalog; ?></span>
                    <i class="icon-chevron-down"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?php echo $category; ?>"><?php echo $text_category; ?></a></li>
	    <li><a href="<?php echo $product; ?>"><?php echo $text_product; ?></a></li>

	  
                </ul>
            </li>
            
            <li id="serve">
                <a class="dropdown-toggle" href="#">
                    <i class="icon-edit"></i>
                    <span>服务配置</span>
                    <i class="icon-chevron-down"></i>
                </a>
                <ul class="submenu">
                  <li><a href="<?php echo $setting; ?>"><?php echo $text_setting; ?></a></li>
                    <li><a href="<?php echo $nav; ?>">菜单设置</a></li>
                      <li><a href="<?php echo $information; ?>"><?php echo $text_information; ?></a></li>
                   <hr>
                     <li><a href="<?php echo $shipping; ?>"><?php echo $text_shipping; ?></a></li>
          <li><a title="联系管理员"><font color="#999999"><?php echo $text_payment; ?></font></a></li>
           <hr>
            <li><a href="<?php echo $user; ?>"><?php echo $text_user; ?></a></li>
		<li><a href="<?php echo $user_group; ?>"><?php echo $text_user_group; ?></a></li>
        
                </ul>
            </li>
            <li id="sale">
                <a class="dropdown-toggle" href="#">
                    <i class="icon-code-fork"></i>
                    <span><?php echo $text_sale; ?></span>
                    <i class="icon-chevron-down"></i>
                </a>
                <ul class="submenu">
          <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
          <li><a href="<?php echo $banner; ?>"><?php echo $text_banner; ?></a></li> 
          <li><a href="<?php echo $customer; ?>"><?php echo $text_customer; ?></a></li>
          <li><a href="<?php echo $customer_group; ?>"><?php echo $text_customer_group; ?></a></li>
          <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li> 
          <li><a href="<?php echo $message; ?>"><?php echo $text_word; ?></a></li>
          <li class="divider"></li>
        
                </ul>
            </li>
            
  <li id="report">
                <a class="dropdown-toggle" href="#">
                   <i class="icon-signal"></i>
                    <span>数据分析</span>
                    <i class="icon-chevron-down"></i>
                </a>
                <ul class="submenu">
                  <li><a href="<?php echo $report; ?>"><?php echo $text_reports; ?></a></li>
                  <li><a href="<?php echo $report_sale_order; ?>"><?php echo $text_report_sale_order; ?></a></li>
	              <li><a href="<?php echo $report_sale_tax; ?>"><?php echo $text_report_sale_tax; ?></a></li>
	              <li><a href="<?php echo $report_sale_shipping; ?>"><?php echo $text_report_sale_shipping; ?></a></li>
	            
                  <li><a href="<?php echo $report_product_viewed; ?>"><?php echo $text_report_product_viewed; ?></a></li>
            	  <li><a href="<?php echo $report_product_purchased; ?>"><?php echo $text_report_product_purchased; ?></a></li>
                   <li><a href="<?php echo $report_customer_order; ?>"><?php echo $text_report_customer_order; ?></a></li>
	              <li><a href="<?php echo $report_customer_reward; ?>"><?php echo $text_report_customer_reward; ?></a></li>
	              <li><a href="<?php echo $report_customer_credit; ?>"><?php echo $text_report_customer_credit; ?></a></li>
                  
                </ul>
            </li>
            
            <li id="setting">
                <a class="dropdown-toggle" href="#">
                   <i class="icon-cog"></i>
                    <span><?php echo $text_system; ?></span>
                    <i class="icon-chevron-down"></i>
                </a>
                <ul class="submenu">
                 
	         	<li><a href="<?php echo $custom; ?>"><?php echo $text_custom;?></a></li>
             <li><a href="<?php echo $mail; ?>"><?php echo $text_mail;?></a></li>
                <li><a href="<?php echo $wechat; ?>"><?php echo $text_wechat;?></a></li>
                <li><a href="<?php echo $sms; ?>"><?php echo $text_sms;?></a></li>
		        <li><a href="<?php echo $language; ?>"><?php echo $text_language; ?></a></li>
              	<li><a href="<?php echo $currency; ?>"><?php echo $text_currency; ?></a></li>
              	<li><a href="<?php echo $stock_status; ?>"><?php echo $text_stock_status; ?></a></li>
              	<li><a href="<?php echo $order_status; ?>"><?php echo $text_order_status; ?></a></li>
                <li><a href="<?php echo $unit; ?>">单位管理</a></li>
              	<li><a href="<?php echo $logistics; ?>">配送员</a></li>
	          
        	 <hr>
	              <li><a href="<?php echo $country; ?>"><?php echo $text_country; ?></a></li>
	              <li><a href="<?php echo $zone; ?>"><?php echo $text_zone; ?></a></li>
	              <li><a href="<?php echo $city; ?>"><?php echo $text_city;?></a></li>
	              <li><a href="<?php echo $geo_zone; ?>"><?php echo $text_geo_zone; ?></a></li>
	            <hr>
	             <li><a href="<?php echo $tax_class; ?>"><?php echo $text_tax_class; ?></a></li>
                </ul>
            </li>  
        </ul>
    </div>
    <!-- end sidebar -->
<?php } ?>
<script type="text/javascript"><!--
 
function getURLVar(urlVarName) {
	var urlHalves = String(document.location).toLowerCase().split('?');
	var urlVarValue = '';
	
	if (urlHalves[1]) {
		var urlVars = urlHalves[1].split('&');

		for (var i = 0; i <= (urlVars.length); i++) {
			if (urlVars[i]) {
				var urlVarPair = urlVars[i].split('=');
				
				if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {
					urlVarValue = urlVarPair[1];
				}
			}
		}
	}
	
	return urlVarValue;
} 

$(document).ready(function() {
	route = getURLVar('route');
	
	if (!route) {
		$('#home').addClass('active');
	} else {
		part = route.split('/');
		
		url = part[0];
		
		if (part[1]) {
			url += '/' + part[1];
		}
		
		$('a[href*=\'' + url + '\']').parents('li[id]').addClass('active');
	    $('a[href*=\'' + url + '\']').parents('li[id]').append('<div class="pointer"><div class="arrow"></div><div class="arrow_border"></div></div>');
		$('a[href*=\'' + url + '\']').parents('li[id] ul').addClass('active');
		
	}
});
//--></script> 