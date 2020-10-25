jQuery(document).ready(function($) {
  
  /* for top navigation */
	$(" #menu ul ").css({display: "none"}); // Opera Fix
	$(" #menu li").hover(function(){
	$(this).find('ul:first').css({visibility: "visible",display: "none"}).slideDown(400);
	},function(){
	$(this).find('ul:first').css({visibility: "hidden"});
	});
  
  $('a[href=#top]').click(function(){
      $('html, body').animate({scrollTop:0}, 'slow');
      return false;
  });

 
  $(".toggle_title").toggle(
		function(){
			$(this).addClass('toggle_active');
			$(this).siblings('.toggle_content').slideDown("fast");
		},
		function(){
			$(this).removeClass('toggle_active');
			$(this).siblings('.toggle_content').slideUp("fast");
		}
	);

	$(".tabs_container").each(function(){
		$("ul.tabs",this).tabs("div.panes > div", {tabs:'li',effect: 'fade', fadeOutSpeed: -400});
	});
	$(".mini_tabs_container").each(function(){
		$("ul.mini_tabs",this).tabs("div.panes > div", {tabs:'li',effect: 'fade', fadeOutSpeed: -400});
	});
	
  
  function startAutoPlay() {
		return setInterval(function() {
		$('.sidebar-roundabout ul').roundabout_animateToNextChild();
		}, 6000);
	}
	
  /*
  * Copyright (C) 2009 Joel Sutherland.
  * Liscenced under the MIT liscense
  */

  /* Ajax Contact Form Processing */
  $('#buttonsend').click( function() {
	
	var subject = $('#subject').val();
	var email   = $('#email').val();
	var message = $('#message').val();
	var siteurl = $('#siteurl').val();
  var sendto = $('#sendto').val();		
	
	$('.loading').fadeIn('fast');
	
	if (name != "" && subject != "" && email != "" && message != "")
		{

			$.ajax(
				{
					url: siteurl+'/sendemail.php',
					type: 'POST',
					data: "name=" + name + "&subject=" + subject + "&email=" + email + "&message=" + message+ "&sendto=" + sendto,
					success: function(result) 
					{
						$('.loading').fadeOut('fast');
						if(result == "email_error") {
							$('#email').css({"background":"#FFFCFC","border-top":"1px solid #ffb6b6","border-left":"none","border-right":"1px solid #ffb6b6","border-bottom":"none"});
						} else {
							$('#name, #subject, #email, #message').val("");
							$('.success-contact').show().fadeOut(6200, function(){ $(this).remove(); });
						}
					}
				}
			);
			return false;
			
		} 
	else 
		{
			$('.loading').fadeOut('fast');
			if(subject == "") $('#subject').css({"background":"#FFFCFC","border-top":"1px solid #ffb6b6","border-left":"none","border-right":"1px solid #ffb6b6","border-bottom":"none"});
			if(email == "" ) $('#email').css({"background":"#FFFCFC","border-top":"1px solid #ffb6b6","border-left":"none","border-right":"1px solid #ffb6b6","border-bottom":"none"});
			if(message == "") $('#message').css({"background":"#FFFCFC","border-top":"1px solid #ffb6b6","border-left":"none","border-right":"1px solid #ffb6b6","border-bottom":"none"});
			return false;
		}
  });

	$('#subject, #email,#message').focus(function(){
		$(this).css({"background":"#ffffff","border-top":"1px solid #cccbcb","border-left":"none","border-right":"1px solid #cccbcb","border-bottom":"none"});
	});
        
}); 

//Kill IE 6
var ietips='<div id=\"_ietips\" style=\"display:none;background:#000;height:40px;line-height:40px;left:0; opacity:0.80; -moz-opacity:0.80; filter:alpha(opacity=80); position:fixed;bottom:0;width:100%;z-index:999; text-align:center; color:#FFF; font-size:16px;_bottom:auto; _width: 100%; _position: absolute; _top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-(parseInt(this.currentStyle.marginTop,10)||0)-(parseInt(this.currentStyle.marginBottom,10)||0)))\">\u5F53\u524D\u6D4F\u89C8\u5668\u7248\u672C\u592A\u4F4E\uFF0C\u60A8\u5C06\u65E0\u6CD5\u5B8C\u7F8E\u4F53\u9A8C\u6211\u4EEC\u7CFB\u7EDF\uFF01<a href=\"http://www.doccms.com\" target=\"_blank\">\u7A3B\u58F3CMS<\/a>\u5C06\u5168\u9762\u4E0D\u8003\u8651\u517C\u5BB9IE6\u7684\u95EE\u9898\uFF0C\u5982\u4E0D\u80FD\u6EE1\u8DB3\u60A8\u7684\u8981\u6C42\uFF0C\u8BF7<a href=\"http://www.shlcms.com\" target=\"_blank\">\u4E0B\u8F7DSHLCMS4.2<\/a>\u6765\u89E3\u51B3\21</div>';
if(jQuery.browser.version=="6.0"){jQuery("body").append(ietips);setTimeout('jQuery("#_ietips").fadeIn(2000);',1000);}