;(function($){
	$.fn.scrolling = function(direction, settings){
		if(this.length < 1){return;};
		settings = $.extend({
			step:1,
			speed:800,
			time:2600,
			auto:true,
			plus:true,
			minus:true
		},settings);

		var scroll_obj = this;
		var scroll_box = scroll_obj.find(".box");
		var scroll_list = scroll_box.find(".list");
		var scroll_array = scroll_list.find("li");
		var scroll_num = scroll_array.length;
	
		if(scroll_num <= 1){return;};
		
		var item_width, item_height;
		item_width = scroll_array.outerWidth();
		item_height = scroll_array.outerHeight();
	
	
		var srcoll_html,scroll_run,plus_val,minus_val,scroll_val;
		if(direction=="left"||direction=="right"){
			if(item_width*scroll_num<=scroll_box.outerWidth()){return;};
			plus_val="left";
			minus_val="right";
			scroll_val=item_width;
		}else{
			if(item_height*scroll_num<=scroll_box.outerHeight()){return;};
			plus_val="top";
			minus_val="bottom";
			scroll_val=item_height;
		};
		
		srcoll_html="";
		if(scroll_obj.find(".plus").length<=0&&settings.plus){srcoll_html+="<a class='plus'></a>";};
		if(scroll_obj.find(".minus").length<=0&&settings.minus){srcoll_html+="<a class='minus'></a>";};
		scroll_obj.append(srcoll_html);
		
		var scroll_plus=scroll_obj.find(".plus");
		var scroll_minus=scroll_obj.find(".minus");
		
		scroll_list.append(scroll_list.html());
		
		var scrollAuto=function(){
			if(settings.auto){
				scroll_run=setInterval(function(){
					scrollStart(direction);
				},settings.time);
			}else{
				return;
			};
		};
		
		var scrollStart=function(d){
			controlRemove();
			scroll_list.stop(true);
			
			var scroll_max,scroll_px;
			switch(d){
			case "left":
			case "top":
				if(parseInt(scroll_list.css(plus_val))==0){
					scroll_list.css(plus_val,-(scroll_num*scroll_val));
				};
				scroll_max=0;
				scroll_px=parseInt(scroll_list.css(plus_val))+(scroll_val*settings.step);
				if(scroll_px>scroll_max){scroll_px=scroll_max};
				if(d=="left"){
					scroll_list.animate({left:scroll_px},settings.speed,function(){
						if(parseInt(scroll_list.css(plus_val))>=0){
							scroll_list.css(plus_val,-(scroll_num*scroll_val));
						};
						controlAdd();
					});
				}else{
					scroll_list.animate({top:scroll_px},settings.speed,function(){
						if(parseInt(scroll_list.css(plus_val))>=0){
							scroll_list.css(plus_val,-(scroll_num*scroll_val));
						};
						controlAdd();
					});
				};
				break;
			case "right":
			case "bottom":
				scroll_max=-(scroll_num*scroll_val);
				scroll_px=parseInt(scroll_list.css(plus_val))-(scroll_val*settings.step);
				if(scroll_px<scroll_max){scroll_px=scroll_max};
				if(d=="right"){
					scroll_list.animate({left:scroll_px},settings.speed,function(){
						if(parseInt(scroll_list.css(plus_val))<=scroll_max){
							scroll_list.css(plus_val,0);
						};
						controlAdd();
					});
				}else{
					scroll_list.animate({top:scroll_px},settings.speed,function(){
						if(parseInt(scroll_list.css(plus_val))<=scroll_max){
							scroll_list.css(plus_val,0);
						};
						controlAdd();
					});
				};
				break;
			};
		};
		
		var controlAdd=function(){
			if(settings.plus){
				scroll_plus.bind("click",function(){
					if(typeof(scroll_run)!="undefined"){clearInterval(scroll_run);};
					scrollStart(plus_val);
					scrollAuto();
				});
			};
			if(settings.minus){
				scroll_minus.bind("click",function(){
					if(typeof(scroll_run)!="undefined"){clearInterval(scroll_run);};
					scrollStart(minus_val);
					scrollAuto();
				});
			};
		};
		
		var controlRemove=function(){
			if(settings.plus){scroll_plus.unbind("click");};
			if(settings.minus){scroll_minus.unbind("click");};
		};
	
		if(settings.auto){
			scrollAuto();
			
			scroll_box.hover(function(){
				if(typeof(scroll_run)!="undefined"){clearInterval(scroll_run);};
			},function(){
				scrollAuto();
			});
		};
		controlAdd()
	};
})(jQuery);