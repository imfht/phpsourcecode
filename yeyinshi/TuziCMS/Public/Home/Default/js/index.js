	 
// 检测css3 transition
function supportsTransitions() {
    var b = document.body || document.documentElement;
    var s = b.style;
    var p = 'transition';
    if(typeof s[p] == 'string') {return true; }

    // Tests for vendor specific prop
    v = ['Moz', 'Webkit', 'Khtml', 'O', 'ms'],
    p = p.charAt(0).toUpperCase() + p.substr(1);
    for(var i=0; i<v.length; i++) {
      if(typeof s[v[i] + p] == 'string') { return true; }
    }
    return false;
}

// 首页幻灯片
$(function(){
	$(".homebanner1 .nums").find("li:first").addClass("curr");
	function loadImage(url, callback)
	{
		var img = new Image(); //创建一个Image对象，实现图片的预下载
		img.src = url;

		if (img.complete)
		{ // 如果图片已经存在于浏览器缓存，直接调用回调函数
			callback.call(img);
			return; // 直接返回，不用再处理onload事件
		}
		img.onload = function ()
		{ //图片下载完毕时异步调用callback函数。
			callback.call(img);//将回调函数的this替换为Image对象
		};
	};

	var img_count = $(".homebanner1 .imgs").find("img").length;

	var transitionAutoChange = function(activePos) {
		if (typeof activePos === 'undefined') activePos = 0;
		var pos = activePos + 1;
		if (pos >= img_count) {
			pos = 0;
		}
		$(".homebanner1 .nums li").eq(pos).trigger('click');
	};
	function addChangeEvent()
	{
		var transitionTimeOut = false;
		var transitionInterval = false;
		var transitioning = false;
		$(".homebanner1 .imgs li").eq(0).addClass('active');
		transitionInterval = setInterval(transitionAutoChange, 7000);

		$(".homebanner1 .nums li").bind("click", function(){
			clearInterval(transitionInterval);
			var pos = $(".homebanner1 .nums li").index(this);
			var activePos = $(".homebanner1 .nums li").index($(".homebanner1 .nums .curr"));
			if (pos == activePos) return ;
			if (transitioning) return;
			transitioning = true;

			var colorArr = ['#2d90e2', '#38aa30', '#fe8f00', '#fa522d'];
			var color = colorArr[pos];
			$('.banner').stop().animate({backgroundColor: color});

			var active = $(".imgs li").eq(activePos);
			var next = $(".imgs li").eq(pos);
			if (!next[0]) {
				transitioning = false;
				return;
			}
			if (supportsTransitions()) {
				active.fadeTo(600,0.1,function(){
            		active.css('opacity', 1);
	        	});
			}
	        if (pos > activePos) {
	        	var type = "next";
	        } else {
	        	var type = "prev";
	        }

	        var direction = type == 'next' ? 'left' : 'right';
	        next.addClass(type);
	        next[0].offsetWidth;// force reflow
	        active.addClass(direction);
	        next.addClass(direction);
	      	$(".homebanner1 .nums li").eq(activePos).removeClass('curr');
	        $(".homebanner1 .nums li").eq(pos).addClass('curr');
	        clearTimeout(transitionTimeOut);
	        if (supportsTransitions()) {
		        transitionTimeOut = setTimeout(function(){
		          	next.removeClass([type, direction].join(' ')).addClass('active');
		          	active.removeClass(['active', direction].join(' '));
		          	transitionInterval = setInterval(function(){
		          		transitionAutoChange(pos);
		          	}, 5000);
		          	transitioning = false;
		        }, 1000);
	    	} else {
	    		transitionTimeOut = setTimeout(function(){
	    			active.removeClass(['active', direction].join(' ')).css({opacity: 0});
	    			next.css({opacity:0}).animate({opacity:1},2000, function(){
	    				next.removeClass([type, direction].join(' ')).addClass('active');
	    				transitionInterval = setInterval(function(){
			          		transitionAutoChange(pos);
			          	}, 5000);
			        	transitioning = false;
	    			});
		        }, 100);
	    	}
		});
	};

	$(".homebanner1 .imgs").find("img").each(function(i){
		var _this = $(this);
		var url = _this.attr("data-src");
		loadImage(url, function(){
			_this.attr("src", url);
			if ((i + 1) == img_count) {
				addChangeEvent();
			}
		});
		if (!supportsTransitions() && i > 0) {
			_this.parent().parent().css({'opacity': 0});
		}
	});

});

// 客户案例
$(function(){
	if (!supportsTransitions()) {
		$("#tab-cont-1").find(".item").css({'opacity': 0});
		$("#tab-cont-1").find(".item").eq(0).css({'opacity': 1});
	}
	$("#tab-title-1").find("li:first").find("a").addClass("tabs-focus");
	var tab_num = $("#tab-title-1 li").length;

	function addChangeEvent()
	{
		var transitionTimeOut = false;
		var transitioning = false;
		$("#tab-cont-1").find(".item").eq(0).addClass('active');

		$("#tab-title-1 li").bind("click", function(){
			var pos = $("#tab-title-1 li").index(this);
			var activePos = $("#tab-title-1 li").index($("#tab-title-1 .tabs-focus").parent());
			if (pos == activePos) return ;
			if (transitioning) return;
			transitioning = true;

			var active = $("#tab-cont-1").find(".item").eq(activePos);
			var next = $("#tab-cont-1").find(".item").eq(pos);
			if (!next[0]) {
				transitioning = false;
				return;
			}
			if (supportsTransitions()) {
				active.fadeTo(600,0.1,function(){
            		active.css('opacity', 1);
	        	});
			}
	        if (pos > activePos) {
	        	var type = "next";
	        } else {
	        	var type = "prev";
	        }
	        var direction = type == 'next' ? 'left' : 'right';
	        next.addClass(type);
	        next[0].offsetWidth;// force reflow
	        next.addClass(direction);
	      	$("#tab-title-1 li").eq(activePos).find("a").removeClass('tabs-focus');
	        $("#tab-title-1 li").eq(pos).find("a").addClass('tabs-focus');
	        clearTimeout(transitionTimeOut);
	        if (supportsTransitions()) {
                active.addClass(direction);
		        transitionTimeOut = setTimeout(function(){
		          	next.removeClass([type, direction].join(' ')).addClass('active');
		          	active.removeClass(['active', direction].join(' '));
		          	transitioning = false;
		        }, 1000);
	    	} else {
	    		transitionTimeOut = setTimeout(function(){
	    			active.removeClass(['active', direction].join(' ')).css({opacity: 0});
	    			next.css({opacity:0}).animate({opacity:1},1000, function(){
	    				next.removeClass([type, direction].join(' ')).addClass('active');
	    				transitioning = false;
	    			});
		        }, 100);
	    	}
		});
	};
	addChangeEvent();
});


// 客户感言首页
var banner_currId=0;
var banner_currId1=0;
var imgCount,imgCount1;
var timeout,timeout1;
var interval=8000;
$(function(){
	   imgCount=$(".khgy .imgs li").length;
	   $(".khgy .imgs li:gt(0)").hide();
	   $(".khgy .nums li:eq(0)").addClass("curr");
	   timeout=setTimeout("banner_change(1)",interval);
	   $(".khgy .nums li").click(function(){
			var cid=parseInt($(this).attr("id"));
			clearTimeout(timeout);
			banner_change(cid);
		});

        imgCount1=$(".khgyal .imgs li").length;
        $(".khgyal .imgs li:gt(0)").hide();
        $(".khgyal .nums li:eq(0)").addClass("curr");
        timeout1=setTimeout("banner_change1(1)",interval);
        $(".khgyal .nums li").click(function(){
            var cid=parseInt($(this).attr("id"));
            clearTimeout(timeout1);
            banner_change1(cid);
        });
});

function banner_change(currId){
	banner_currId=currId;
	$(".khgy .imgs li:visible").css("display","none");
	$(".khgy .imgs li").eq(currId).fadeIn(800);
	$(".khgy .nums li").removeClass("curr");
	$(".khgy .nums li").eq(currId).addClass("curr");
	banner_currId++;
	if(banner_currId>=imgCount) banner_currId=0;
	timeout=setTimeout("banner_change(banner_currId)",interval);
}

function banner_change1(currId){
    banner_currId1=currId;
    $(".khgyal .imgs li:visible").css("display","none");
    $(".khgyal .imgs li").eq(currId).fadeIn(800);
    $(".khgyal .nums li").removeClass("curr");
    $(".khgyal .nums li").eq(currId).addClass("curr");
    banner_currId1++;
    if(banner_currId1>=imgCount1) banner_currId1=0;
    timeout1=setTimeout("banner_change1(banner_currId1)",interval);
}

// JavaScript Document
(function($){
    var goToTopTime;
    $.fn.goToTop=function(options){
        var opts = $.extend({},$.fn.goToTop.def,options);
        var $window=$(window);
        $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body'); // opera fix
        //$(this).hide();
        var $this=$(this);
        clearTimeout(goToTopTime);
        goToTopTime=setTimeout(function(){
            var controlLeft;
            if ($window.width() > opts.pageHeightJg * 2 + opts.pageWidth) {
                controlLeft = ($window.width() - opts.pageWidth) / 2 + opts.pageWidth + opts.pageWidthJg;
            }else{
                controlLeft = $window.width()- opts.pageWidthJg-$this.width();
            }
            var cssfixedsupport=$.browser.msie && parseFloat($.browser.version) < 7;//判断是否ie6

            var controlTop=$window.height() - $this.height()-opts.pageHeightJg;

            controlTop=cssfixedsupport ? $window.scrollTop() + controlTop : controlTop;
            var shouldvisible=( $window.scrollTop() >= opts.startline )? true : false;

            if (shouldvisible){
                $this.stop().show();
            }else{
                $this.stop().hide();
            }

            $this.css({
                position: cssfixedsupport ? 'absolute' : 'fixed',
                top: controlTop,
                left: controlLeft
            });
        },30);

        $(this).click(function(event){
            $body.stop().animate( { scrollTop: $(opts.targetObg).offset().top}, opts.duration);
            $(this).blur();
            event.preventDefault();
            event.stopPropagation();
        });
    };

    $.fn.goToTop.def={
        pageWidth:960,//页面宽度
        pageWidthJg:15,//按钮和页面的间隔距离
        pageHeightJg:130,//按钮和页面底部的间隔距离
        startline:130,//出现回到顶部按钮的滚动条scrollTop距离
        duration:3000,//回到顶部的速度时间
        targetObg:"body"//目标位置
    };
})(jQuery);

//切换效果
$(function() {
	function cmstopTabs(o) {
		var tit = $(o['title']),
		cont = $(o['cont']),
		tabsty = o['tabStyle'];
		var tits = tit.find('li a'),
		conts = cont.find('>div');
		tits.click(function() {
			var index = tits.index($(this));
			$(this).addClass(tabsty).parent().siblings().find('a').removeClass(tabsty);
			$(conts[index]).show().siblings().hide();
			return false;
		});
	}
	cmstopTabs({
		tabStyle: 'tabs-focus',
		title: '#tab-title-2',
		cont: "#tab-cont-2"
	});
});
