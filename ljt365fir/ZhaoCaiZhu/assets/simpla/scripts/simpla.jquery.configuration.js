$(document).ready(function(){
$(".ajax").colorbox({inline: true,iframe:true, width:"90%", height:"90%"});
	var atime=240;
	var setTgTime;//鼠标在NAVli上悬停的计时器。
	var setHoverTime;//鼠标离开后的计时器。
	var navcurr=$("#main-nav li a.current");
	var navli=$("#main-nav>li");
		navcurr.parent().addClass('sm').find("ul").slideDown(atime); // Slide down the current menu item's sub menu
		navli.click(function () {
			}
		).hover(
			function () {
			var self=$(this);
				if(self.attr('class')!='sm'){
		       clearTimeout(setHoverTime);
				setTgTime=setTimeout(function(){
				self.siblings().find("ul").slideUp(atime);
				self.find("ul").slideToggle(atime); 
				},120);
				}
				$(this).find('a.nav-top-item').stop().animate({ paddingRight: "25px" }, 200);
			}, 
			function () {
				var self=$(this);
				if(self.attr('class')!='sm'){
				clearTimeout(setTgTime);
				self.stop();
				setHoverTime=setTimeout(function(){navli.find("ul").slideUp(atime);navcurr.attr('rel','open');navcurr.parent().find("ul").stop().slideDown(atime); },500);
				}
				$(this).find('a.nav-top-item').stop().animate({ paddingRight: "15px" });
			}
		);
		
		$("#main-nav li a.no-submenu").click( // When a menu item with no sub menu is clicked...
			function () {
				window.location.href=(this.href); // Just open the link instead of a sub menu
				return false;
			}
		); 

    // Sidebar Accordion Menu Hover Effect:
		

    //Minimize Content Box
		
		$(".content-box-header h3").css({ "cursor":"s-resize" }); // Give the h3 in Content Box Header a different cursor
		$(".closed-box .content-box-content").hide(); // Hide the content of the header if it has the class "closed"
		$(".closed-box .content-box-tabs").hide(); // Hide the tabs in the header if it has the class "closed"
		
		$(".content-box-header h3").click( // When the h3 is clicked...
			function () {
			  $(this).parent().next().toggle(); // Toggle the Content Box
			  $(this).parent().parent().toggleClass("closed-box"); // Toggle the class "closed-box" on the content box
			  $(this).parent().find(".content-box-tabs").toggle(); // Toggle the tabs
			}
		);

    // Content box tabs:
		
		$('.content-box .content-box-content div.tab-content').hide(); // Hide the content divs
		$('ul.content-box-tabs li a.default-tab').addClass('current'); // Add the class "current" to the default tab
		$('.content-box-content div.default-tab').show(); // Show the div with class "default-tab"
		
		$('.content-box ul.content-box-tabs li a').click( // When a tab is clicked...
			function() { 
				$(this).parent().siblings().find("a").removeClass('current'); // Remove "current" class from all tabs
				$(this).addClass('current'); // Add class "current" to clicked tab
				var currentTab = $(this).attr('href'); // Set variable "currentTab" to the value of href of clicked tab
				$(currentTab).siblings().hide(); // Hide all content divs
				$(currentTab).show(); // Show the content div with the id equal to the id of clicked tab
				//return false; 
			}
		);

    //Close button:
		
		$(".close").click(
			function () {
				$(this).parent().fadeTo(400, 0, function () { // Links with the class "close" will close parent
					$(this).slideUp(400);
				});
				return false;
			}
		);

    // Alternating table rows:
		
		$('tbody tr:even').addClass("alt-row"); // Add class "alt-row" to even table rows

    // Check all checkboxes when the one in a table head is checked:
		
		$('.check-all').click(
			function(){
				$(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));   
			}
		);

    // Initialise Facebox Modal window:
		

    // Initialise jQuery WYSIWYG:
		
		$(".wysiwyg").wysiwyg(); // Applies WYSIWYG editor to any textarea with the class "wysiwyg"

});
  
  
  