/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*//////////////////// Variables Start                                                                                    */
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
var $ = jQuery.noConflict();
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*//////////////////// Variables End                                                                                      */
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/



/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*//////////////////// Document Ready Function Starts                                                                     */
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
jQuery(document).ready(function($){
			
	
	
	// initial settings start
	var mainMenuHeight = $('.mainMenuOuterWrapper').outerHeight();
	var shoppingCartHeight = $('.shoppingCartWrapper').outerHeight();
	
	var headerSectionAnimation = 'complete';
	var currentHeaderSection = 'none';
	var tempDelay = 0;
	
	$('.mainMenuOuterWrapper').css('margin-top', -mainMenuHeight);
	$('.shoppingCartWrapper').css('margin-top', -shoppingCartHeight);
	
	var windowWidth = $(window).width() - 48;
		
	var lightboxInitialWidth = windowWidth;
	var lightboxInitialHeight = 220;
	// initial settings end


     
	// main menu and shopping cart functions start
	function headerSection(section, sectionButton){
		
		mainMenuHeight =  $('.mainMenuOuterWrapper').outerHeight();
		shoppingCartHeight = $('.shoppingCartWrapper').outerHeight();
		
		if(headerSectionAnimation == 'complete' && currentHeaderSection != sectionButton){
			
			headerSectionAnimation = 'incomplete';
			
			if(currentHeaderSection == 'shoppingCart' && sectionButton == 'mainMenu'){
				
				tempDelay = 600;
				$('.shoppingCartWrapper').stop(true, true).animate({'margin-top': -shoppingCartHeight}, 600, 'easeOutQuart', function(){
					
					currentHeaderSection = 'none'; 
					$('.shoppingCartWrapper').css('display', 'none'); 
					$(section).css('display', 'block');
					$('.mainMenuOuterWrapper').css('margin-top', -mainMenuHeight);
					$('.shoppingCartWrapper').css('margin-top', -shoppingCartHeight);
					backToTop();
					
				});
			
			} else if(currentHeaderSection == 'mainMenu' && sectionButton == 'shoppingCart'){
			   
			    tempDelay = 600;
				$('.mainMenuOuterWrapper').stop(true, true).animate({'margin-top': -mainMenuHeight}, 600, 'easeOutQuart', function(){
					
					currentHeaderSection = 'none'; 
					$('.mainMenuOuterWrapper').css('display', 'none'); 
					$(section).css('display', 'block');
					$('.mainMenuOuterWrapper').css('margin-top', -mainMenuHeight);
					$('.shoppingCartWrapper').css('margin-top', -shoppingCartHeight);
					backToTop();
				
				});
			
			} else {
				tempDelay = 0;	
				$(section).css('display', 'block');
			}
			
			$(section).stop(true, true).delay(tempDelay).animate({'margin-top': 0}, 600, 'easeInQuart', function(){headerSectionAnimation = 'complete'; currentHeaderSection = sectionButton;});
		
		} else if(headerSectionAnimation == 'complete' && currentHeaderSection == sectionButton){
			
			headerSectionAnimation = 'incomplete';
			
			if(currentHeaderSection == 'mainMenu'){
				var tempSectionHeight = -mainMenuHeight;
			} else if(currentHeaderSection == 'shoppingCart'){
				var tempSectionHeight = -shoppingCartHeight;
			};
			
			$(section).stop(true, true).animate({'margin-top': tempSectionHeight}, 600, 'easeOutQuart', function(){headerSectionAnimation = 'complete'; currentHeaderSection = 'none'; backToTop();});
			
		};
		
	};	
	
	$$('.mainMenuButton').singleTap(function(){
		
		headerSection('.mainMenuOuterWrapper', 'mainMenu');
		
		return false;
		
	});
	
	$$('#shoppingCartButton,#topcart').tap(function(){
		headerSection('.shoppingCartWrapper', 'shoppingCart');
		return false;
		
	});
	$$('#topcart').tap(function(){
		$('.shoppingCartButton').attr('id','CartButtonNoid');
	});

	// main menu and shopping cart functions end
	
	
	
	// gallery functions start
	$('.singleProductGalleryMenu > li > a').click(function(){
		
		var galleryIndex = $(this).parent().index();
		
		$('.currentSingleProductGalleryMenuItem').removeClass('currentSingleProductGalleryMenuItem');
		$(this).parent().addClass('currentSingleProductGalleryMenuItem');
		
		$('.currentSingleProductGalleryItem').removeClass('currentSingleProductGalleryItem');
		$('.singleProductGallery > .singleProductGalleryItem').eq(galleryIndex).addClass('currentSingleProductGalleryItem');
		
		return false;
		
	});
	
	function adaptSingleProductGallery(){
	
		$('.singleProductGallery').css('height', $('.singleProductGallery .currentSingleProductGalleryItem').outerHeight());
		
	};
	adaptSingleProductGallery();
	
	$('.singleProductGallery .currentSingleProductGalleryItem').load(function(){
		
		adaptSingleProductGallery();
		
	});
	// gallery functions end
	
	
	
	// adapt main menu function starts
	function adaptMainMenu(){
		
		if(currentHeaderSection == 'none'){
			
			$('.mainMenuOuterWrapper').css('margin-top', -$('.mainMenuOuterWrapper').height());
			$('.shoppingCartWrapper').css('margin-top', -$('.shoppingCartWrapper').height);
			
		};
		
	};
	// adapt main menu function ends
	
	
	
    
	
	
	// adapt portfolio function starts
	function adaptPortfolio(){
		
		$('.portfolioTwoItemsWrapper').css('width', $('.portfolioTwoPageWrapper').width() - 12 - 48);
		$('.portfolioTwoFilterableItemsWrapper').css('width', $('.portfolioTwoFilterablePageWrapper').width() - 12 - 48);
		
		var portfolioTwoItemWidth = ($('.portfolioTwoPageWrapper').width() - 96 - 36)/2;
		var portfolioTwoFilterableItemWidth = ($('.portfolioTwoFilterablePageWrapper').width() - 96 - 36)/2;
		
		$('.portfolioTwoItemWrapper').css('width', portfolioTwoItemWidth);
		$('.portfolioTwoFilterableWrapper .portfolioFilterableItemWrapper').css('width', portfolioTwoFilterableItemWidth);
		
	};
	
	adaptPortfolio();
	// adapt portfolio function ends
		
	
	
	// filterable portfolio functions start
	$('#portfolioMenuWrapper > li > a').click(function(){
		
		var filterVal = $(this).attr('data-type');
		
		if(filterVal != 'all'){
			
			$('.currentPortfolioFilter').removeClass('currentPortfolioFilter');
			
			$(this).addClass('currentPortfolioFilter');
			
			$('.portfolioFilterableItemWrapper').each(function(){
	            
				var itemCategories = $(this).attr("data-type").split(",");
				  
				if($.inArray(filterVal, itemCategories) > -1){
					
					$(this).addClass('filteredPortfolioItem');
					
					$('.filteredPortfolioItem').stop(true, true).animate({opacity:1}, 300, 'easeOutCubic');
					
				}else{
						
					$(this).removeClass('filteredPortfolioItem');
					
					if(!$(this).hasClass('filteredPortfolioItem')){
						
						$(this).stop(true, true).animate({opacity:0.3}, 300, 'easeOutCubic');
					
					};
					
				};
					
			});
		
		}else{
			
			$('.currentPortfolioFilter').removeClass('currentPortfolioFilter');
			
			$(this).addClass('currentPortfolioFilter');
			
			$('.filteredPortfolioItem').removeClass('filteredPortfolioItem');
			
			$('.portfolioFilterableItemWrapper').stop(true, true).animate({opacity:1}, 300, 'easeOutCubic');
			
		}
			
		return false;
	
	});
	// filterable portfolio functions end
	
     $$('.shoppingCartProductButtonsWrapper > a').tap(function (e) {
	  
            var pel = $(e.target.parentNode);
            var id = parseInt(pel.children('.product_id:input').attr('value'));
            var num=0;
            pel.children('.nsum:input').attr('value',num);

            //写入cookie，数据结构是 id-num,id-num,id-num,如
            //14-5,1-4,6-2 Id 14 1 6的菜品分别点了5 4 2份
            var ckcart = $.cookie('cart') || '';
            var p = ckcart.indexOf(id + '-');
			
            if (p != -1) {
                var ed = ckcart.indexOf(',', p);
                var oldVal = ckcart.substr(p, ed - p + 1);
                ckcart = ckcart.replace(oldVal, num == 0 ? '' : id + '-' + num + ',');

            }
            else{
                ckcart += id + '-' + num + ',';
            }

            $.cookie('cart', ckcart, {expires:7,path:'/'});
           refreshTotal();//刷新总数
	       refreshSumMoney();//刷新金额
		$(this).parent().parent().parent().fadeOut(300);
	
        });
	
	// drop-down widget function starts
	$('.drop-downText').click(function(){
		
		if(!($(this).parent().hasClass('drop-downActive'))){
		
			$('.drop-downWrapper').each(function(){
			
				if($(this).hasClass('drop-downActive')){
					$(this).removeClass('drop-downActive');
					$(this).find('> .drop-downItemsWrapper').stop(true, true).animate({height: 'hide'}, 300, 'easeOutCubic');
				};
			
			});
			
			$(this).parent().addClass('drop-downActive');
			
			$(this).parent().find('> .drop-downItemsWrapper').stop(true, true).animate({height: 'show'}, 300, 'easeOutCubic');
		
		} else {
			
			$(this).parent().find('> .drop-downItemsWrapper').stop(true, true).animate({height: 'hide'}, 300, 'easeOutCubic', function(){$(this).parent().removeClass('drop-downActive');});
			
		};
		
		return false;
		
	});
	
	$('.drop-downItem').click(function(){
		
		$(this).parent().parent().removeClass('drop-downActive');
			
		$(this).parent().parent().find('> .drop-downItemsWrapper').stop(true, true).animate({height:'hide'}, 300, 'easeOutCubic');
			
		$(this).parent().parent().find('.drop-downField').val($(this).attr('data-value'));
		
		var tempLabel = $(this).parent().parent().find('.drop-downText').attr('data-label');
		
		$(this).parent().parent().find('.drop-downText').text(tempLabel + ' (' + $(this).text() + ')');
		
	});
	// drop-down widget function ends
	
	
	
	// alert box widget function starts
	$('.alertBoxButton').click(function(){
		
		$(this).parent().fadeOut(300, function(){$(this).remove();});
		
		return false;
		
	});
	// alert box widget function ends
	
	
	
	// accordion widget function starts
	$('.accordionButton').click(function(e){
		 
		if($(this).hasClass('currentAccordion')){
			
			 $(this).parent().find('.accordionContentWrapper').stop(true, true).animate({height:'hide'}, 300, 'easeOutCubic', function(){$(this).parent().find('.accordionButton').removeClass('currentAccordion');});
			 
		}else{
			 
			$(this).parent().find('.accordionContentWrapper').stop(true, true).animate({height:'show'}, 300, 'easeOutCubic', function(){$(this).parent().find('.accordionButton').addClass('currentAccordion');});
		 
        };
		 
		return false;
		
	});
	// accordion widget function ends

	
	
	// back to top functions starts
	function backToTop(){
		
		$('body, html').stop(true, true).animate({scrollTop:0}, 1200,'easeOutCubic'); 
		
	};
	
	$('.backToTopButton').click(function(){
								   
	    backToTop();
		
		return false;
	
    });
	// back to top functions ends 
	
	
	
	// window resize functions start
	$(window).resize(function(){
		
		windowWidth = $(window).width() - 48;
		
		lightboxInitialWidth = windowWidth;
		
		adaptMainMenu();
		
		adaptPortfolio();
		
		adaptSingleProductGallery();
									
	});
	// window resize functions end
	
	
	
	// nivo slider functions start
	$('#mainSlider').nivoSlider({
		
		controlNav: false,
		prevText: '',
        nextText: '' 
		
	});
	
	$('.previousSlideButton').click(function(){
		
		$('#mainSlider').find('a.nivo-prevNav').click();
		return false;
            
	});
	
	$('.nextSlideButton').click(function(){	
	
		$('#mainSlider').find('a.nivo-nextNav').click();
		return false;
			
	});

 
  var refreshSumMoney = function () {
           var money = 0,sumjf = 0;
           $('.checkoutProductButtonsWrapper').each(function () {

               var price = parseFloat($(this).find('.fdj').attr('value'));
               var djf = parseFloat($(this).find('.fjf').attr('value'));
               var num = parseFloat($(this).find('.nsum').attr('value'));
               if (!isNaN(price) && !isNaN(num))
                   money += price * num;
 	       if (!isNaN(djf) && !isNaN(num))
               	   sumjf+= djf * num;
           });
		  //alert(money);
           $('.allzj').html(money.toFixed(2));
	   $('.alljf').html(round2(sumjf,0));
	   if(!istopcart){
	        MoToFixed =  money;//所有订单总金额
                   peisong();	
                   payment();
				 
	   }
    };

	
		
var bdconfirm = function(){
$$('#confirm').tap(function () {

	var formData = $('#form1').serialize(); 					 
	  $.ajax({ 
		  type : "post", 
		  url  : "index.php?route=checkout/confirm&m="+sname,  
		  data : formData, 
		  dataType:'json',
		  beforeSend:function(){
			  	
				if($('#name').val()==""){
				$$("#progressBar").html('请填写姓名');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
					 return false; 
					 }
					 
					if($('#seat').val()==""){
				$$("#progressBar").html('请选择桌号');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
					 return false; 
					 }
					 
				if($('#telephone').val()==""){
				$$("#progressBar").html('请填写电话');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
					 return false; }	
				if(Telephone ==1){
					  if($('input[name=\'codeRand\']').val()=="")
					  {
						   $$("#progressBar").html('请填写验证码');
						  $$("#progressBar").show();
						  $("#progressBar").delay(500);
						  $("#progressBar").fadeOut(500); 
						  return false; 
					   }
					
					
					}
					
					
				 $$("#progressBar").html('正在下单');
				  $$("#progressBar").show();
			  
		  }, //发送请求
		  success : function(json){
				  if(json['captcha']=="N"){
			 $$("#progressBar").html('验证码错误');
						  $$("#progressBar").show();
						  $("#progressBar").delay(500);
						  $("#progressBar").fadeOut(500); 
					  }else{
					$('.pageContentWrapper').html(json['output']);
					$$("#progressBar").html('正在下单');
				    $$("#progressBar").hide();
						  }
				  
				  },
	  });
	 return false;	
});
$$('#confirm1').tap(function () {		
var formData = $('#form2').serialize(); 
 $.ajax({ 
		  type : "post", 
		  url  : "index.php?route=checkout/confirm&m="+sname,  
		  data : formData, 
		  dataType:'json',
		  beforeSend:function(){
				if($('#name1').val()==""){
				$$("#progressBar").html('请填写姓名');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
					 return false; }
				if($('#telephone1').val()==""){
				$$("#progressBar").html('请填写电话');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
					
					 return false; }	
			   if($('select[name=\'zone_id\']').val()==""){
				   $$("#progressBar").html('选择区域');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
				   
				    return false; }
			   if($('select[name=\'city_id\']').val()==""){
				   
				   $$("#progressBar").html('选择街道');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
				
				return false; }
			   if($('input[name=\'address\']').val()==""){
				   $$("#progressBar").html('填写地址');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
				   
				    return false; }
					if(Telephone ==1){
					  if($('input[name=\'codeRand\']').val()=="")
					  {
						  $$("#progressBar").html('填写验证码');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
						  return false; 
					   }
					}
				 $$("#progressBar").html('正在下单');
				  $$("#progressBar").show();
			  
		  }, //发送请求
		  success : function(json){
				  if(json['captcha']=="N"){
						$$("#progressBar").html('验证码错误');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
					  }else{
						  $('.pageContentWrapper').html(json['output']);
						   $$("#progressBar").html('正在下单');
						  $$("#progressBar").hide();
						  }
				  
				  },
	  });
								
								
return false;	
}); 

 $$('#cr').tap(function() { 
   $$("#progressBar").html('正在获取...');
   $$("#progressBar").show();
			if($('input[name=\'telephone\']').val()){
			//$('input[name=\'telephone\']').attr("disabled","disabled");
				 $.ajax({ 
					type : "POST", 
					url  : "index.php?route=common/ajaxCaptcha&telephone="+$('input[name=\'telephone\']').val()+"&m="+sname,  
					success : function(result){
						  if (result == "Y") {
							  $$("#progressBar").html('已发送！');
							  $$("#progressBar").show();
							  $("#progressBar").delay(1000);
							  $("#progressBar").fadeOut(500); 
							  //$('input[name=\'codeRand\']').removeClass("ui-state-disabled");
							   //$('input[name=\'telephone\']').addClass("ui-state-disabled");
							 startCount();//开始倒计时
							
						  }
						  else {
							 $$("#progressBar").html('错误！请重新获取！');
							  $$("#progressBar").show();
							  $("#progressBar").delay(1000);
							  $("#progressBar").fadeOut(500); 
						  }
					}, 
					error: function() { alert("error"); }
				}); 
			} else{
				$$("#progressBar").html('请填写手机号码');
							  $$("#progressBar").show();
							  $("#progressBar").delay(500);
							  $("#progressBar").fadeOut(500); 
				}

        }); 
		

		
		
 $$('#cr1').tap(function() { 
   $$("#progressBar").html('正在获取...');
   $$("#progressBar").show();
			if($('input[name=\'telephone\']').val()){
			//$('input[name=\'telephone\']').attr("disabled","disabled");
				 $.ajax({ 
					type : "POST", 
					url  : "index.php?route=common/ajaxCaptcha&telephone="+$('input[name=\'telephone\']').val()+"&m="+sname,  
					success : function(result){
						  if (result == "Y") {
							  $$("#progressBar").html('已发送！');
							  $$("#progressBar").show();
							  $("#progressBar").delay(1000);
							  $("#progressBar").fadeOut(500); 
							  //$('input[name=\'codeRand\']').removeClass("ui-state-disabled");
							   //$('input[name=\'telephone\']').addClass("ui-state-disabled");
							 startCount();//开始倒计时
							
						  }
						  else {
							 $$("#progressBar").html('错误！请重新获取！');
							  $$("#progressBar").show();
							  $("#progressBar").delay(1000);
							  $("#progressBar").fadeOut(500); 
						  }
					}, 
					error: function() { alert("error"); }
				}); 
			} else{
				$$("#progressBar").html('请填写手机号码');
							  $$("#progressBar").show();
							  $("#progressBar").delay(500);
							  $("#progressBar").fadeOut(500); 
				}

        }); 
		
		//验证码有效期倒计时
  var Time=300;//5分钟
  //定义定时器的id
  var listenid;
  var count=Time;
  
  function startCount(){
	  $('.cr').attr('id','noid');
	    $('.cr1').attr('id','noid1');
	 //$("#cr").addClass("ui-state-disabled");
	 //$("#cr1").addClass("ui-state-disabled");
	 $('#noid').html("(" +count + ")秒后获取");
	  $('#noid1').html("(" +count + ")秒后获取");
	  count=count-1;
	  listenid=setTimeout("startCount()",1000)
	   if(count<0){
		  count=Time;
		  stopCount();
	 //$('input[name=\'codeRand\']').addClass("ui-state-disabled");
	//$('#cr').removeClass("ui-state-disabled");
	//$('#cr1').removeClass("ui-state-disabled");
	  $('.cr').attr('id','cr');
	  $('.cr1').attr('id','cr1');
	   $('#cr').html("获取验证码");
	    $('#cr1').html("获取验证码");
	  // $('#cr1').html("获取验证码");
	  }
  }
//停止计时
  function stopCount(){
	clearTimeout(listenid);
  }
}



var carttab = function(){

 $("#content1").find("[id^='tab']").hide(); // Hide all content
    $("#tabs li:first").attr("id","current"); // Activate the first tab
    $("#content1 #tab1").fadeIn(); // Show first tab's content
    
    $('#tabs a').click(function(e) {
        e.preventDefault();
        if ($(this).closest("li").attr("id") == "current"){ //detection for current tab
        
		 //return;       
        }
        else{             
          $("#content1").find("[id^='tab']").hide(); // Hide all content
          $("#tabs li").attr("id",""); //Reset id's
          $(this).parent().attr("id","current"); // Activate this
          $('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
		
        }
 });
		
}
$$('.checkoutProductButtonsWrapper > a').tap(function (e) {
	  
            var pel = $(e.target.parentNode);
            var id = parseInt(pel.children('.product_id:input').attr('value'));
            var num=0;
            pel.children('.nsum:input').attr('value',num);

            //写入cookie，数据结构是 id-num,id-num,id-num,如
            //14-5,1-4,6-2 Id 14 1 6的菜品分别点了5 4 2份
            var ckcart = $.cookie('cart') || '';
            var p = ckcart.indexOf(id + '-');
			
            if (p != -1) {
                var ed = ckcart.indexOf(',', p);
                var oldVal = ckcart.substr(p, ed - p + 1);
                ckcart = ckcart.replace(oldVal, num == 0 ? '' : id + '-' + num + ',');

            }
            else{
                ckcart += id + '-' + num + ',';
            }

            $.cookie('cart', ckcart, {expires:7,path:'/'});
            refreshTotal();//刷新总数
	        refreshSumMoney();//刷新金额
		$(this).parent().parent().parent().fadeOut(300);
	
        });
	
$$('.incart,#topcart').tap(function() {
        $$("#progressBar").show();
		$$(".websiteWrapper").style("opacity","0.3");
		$('.pageContentWrapper').load("index.php?route=checkout/cart&m="+sname,function(){
		refreshTotal();
		refreshSumMoney();
		carttab();
		bdconfirm();
		$$("#progressBar").hide();
		$$(".websiteWrapper").style("opacity","1");
		$$('.dibu').hide();
});
});

	
	// lightbox functions start
	
	
	
	
	// lightbox functions end



});


/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*//////////////////// Document Ready Function Ends                                                                       */
/*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/