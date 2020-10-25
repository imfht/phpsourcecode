/**侧边栏切换**/
(function($, window, document){
  var Selector = '[data-toggle="aside"]',
      $body = $('body');

  $(document).on('click', Selector, function (e) {
      e.preventDefault();      
      $body.toggleClass('aside-toggled');
  });
}(jQuery, window, document));

/**=========================================================
 * Module: datepicker,js
 * 日期时间选择器初始化
 =========================================================*/

(function($, window, document){

    var Selector = '.datetimepicker';

    $(Selector).each(function() {

      var $this = $(this),
          options = $this.data(); // allow to set options via data-* attributes
      
      $this.datetimepicker($.extend(
        options,
        { // support for FontAwesome icons
          icons: {
              time:   "fa fa-clock-o",
              date:   "fa fa-calendar",
              up:     "fa fa-arrow-up",
              down:   "fa fa-arrow-down"
          }
        }));

      // Force a dropdown hide when click out of the input
      $(document).on('click', function(){
        $this.data("DateTimePicker").hide();
      });

    });

}(jQuery, window, document));

/**=========================================================
 * Module: notify.js
 * 自动创建toggleable通知,淡出.
 * Based on Notify addon from UIKit (http://getuikit.com/docs/addons_notify.html)
 * [data-toggle="notify"]
 * [data-options="options in json format" ]
 =========================================================*/

(function($, window, document){

  var Selector = '[data-toggle="notify"]',
      autoloadSelector = '[data-onload]',
      doc = $(document);
  $(function() {
    $(Selector).each(function(){

      var $this  = $(this),
          onload = $this.data('onload');

      if(onload !== undefined) {
        setTimeout(function(){
          notifyNow($this);
        }, 800);
      }

      $this.on('click', function (e) {
        e.preventDefault();
        notifyNow($this);
      });

    });

  });

  function notifyNow($element) {
      var message = $element.data('message'),
          options = $element.data('options');

      if(!message)
        $.error('通知:没有指定的信息');
     
      $.notify(message, options || {});
  }


}(jQuery, window, document));

/**
 * 通知插件定义为jQuery插件
 * Adapted version to work with Bootstrap classes
 * More information http://getuikit.com/docs/addons_notify.html
 */

(function($, window, document){

    var containers = {},
        messages   = {},

        notify     =  function(options){

            if ($.type(options) == 'string') {
                options = { message: options };
            }

            if (arguments[1]) {
                options = $.extend(options, $.type(arguments[1]) == 'string' ? {status:arguments[1]} : arguments[1]);
            }

            return (new Message(options)).show();
        },
        closeAll  = function(group, instantly){
            if(group) {
                for(var id in messages) { if(group===messages[id].group) messages[id].close(instantly); }
            } else {
                for(var id in messages) { messages[id].close(instantly); }
            }
        };

    var Message = function(options){

        var $this = this;

        this.options = $.extend({}, Message.defaults, options);

        this.uuid    = "ID"+(new Date().getTime())+"RAND"+(Math.ceil(Math.random() * 100000));
        this.element = $([
            // @geedmo: alert-dismissable enables bs close icon
            '<div class="uk-notify-message alert-dismissable">',
                '<a class="close">&times;</a>',
                '<div>'+this.options.message+'</div>',
            '</div>'

        ].join('')).data("notifyMessage", this);

        // status
        if (this.options.status) {
            this.element.addClass('alert alert-'+this.options.status);
            this.currentstatus = this.options.status;
        }

        this.group = this.options.group;

        messages[this.uuid] = this;

        if(!containers[this.options.pos]) {
            containers[this.options.pos] = $('<div class="uk-notify uk-notify-'+this.options.pos+'"></div>').appendTo('body').on("click", ".uk-notify-message", function(){
                $(this).data("notifyMessage").close();
            });
        }
    };


    $.extend(Message.prototype, {

        uuid: false,
        element: false,
        timout: false,
        currentstatus: "",
        group: false,

        show: function() {

            if (this.element.is(":visible")) return;

            var $this = this;

            containers[this.options.pos].show().prepend(this.element);

            var marginbottom = parseInt(this.element.css("margin-bottom"), 10);

            this.element.css({"opacity":0, "margin-top": -1*this.element.outerHeight(), "margin-bottom":0}).animate({"opacity":1, "margin-top": 0, "margin-bottom":marginbottom}, function(){

                if ($this.options.timeout) {

                    var closefn = function(){ $this.close(); };

                    $this.timeout = setTimeout(closefn, $this.options.timeout);

                    $this.element.hover(
                        function() { clearTimeout($this.timeout); },
                        function() { $this.timeout = setTimeout(closefn, $this.options.timeout);  }
                    );
                }

            });

            return this;
        },

        close: function(instantly) {

            var $this    = this,
                finalize = function(){
                    $this.element.remove();

                    if(!containers[$this.options.pos].children().length) {
                        containers[$this.options.pos].hide();
                    }

                    delete messages[$this.uuid];
                };

            if(this.timeout) clearTimeout(this.timeout);

            if(instantly) {
                finalize();
            } else {
                this.element.animate({"opacity":0, "margin-top": -1* this.element.outerHeight(), "margin-bottom":0}, function(){
                    finalize();
                });
            }
        },

        content: function(html){

            var container = this.element.find(">div");

            if(!html) {
                return container.html();
            }

            container.html(html);

            return this;
        },

        status: function(status) {

            if(!status) {
                return this.currentstatus;
            }

            this.element.removeClass('alert alert-'+this.currentstatus).addClass('alert alert-'+status);

            this.currentstatus = status;

            return this;
        }
    });

    Message.defaults = {
        message: "",
        status: "normal",
        timeout: 5000,
        group: null,
        pos: 'top-center'
    };


    $["notify"]          = notify;
    $["notify"].message  = Message;
    $["notify"].closeAll = closeAll;

    return notify;

}(jQuery, window, document));


/**=========================================================
 * Module: play-animation.js
 * Provides a simple way to run animation with a trigger
 * Targeted elements must have 
 *   [data-toggle="play-animation"]
 *   [data-target="Target element affected by the animation"] 
 *   [data-play="Animation name (http://daneden.github.io/animate.css/)"]
 *
 * Requires animo.js
 =========================================================*/
 
(function($, window, document){

  var Selector = '[data-toggle="play-animation"]';

  $(function() {
    
    var $scroller = $('body, .wrapper');

    // Parse animations params and attach trigger to scroll
    $(Selector).each(function() {
      var $this     = $(this),
          offset    = $this.data('offset'),
          delay     = $this.data('delay')     || 100, // milliseconds
          animation = $this.data('play')      || 'bounce';
      
      if(typeof offset !== 'undefined') {
        
        // test if the element starts visible
        testAnimation($this);
        // test on scroll
        $scroller.scroll(function(){
          testAnimation($this);
        });

      }

      // Test an element visibilty and trigger the given animation
      function testAnimation(element) {
          if ( !element.hasClass('anim-running') &&
              $.Utils.isInView(element, {topoffset: offset})) {
          element
            .addClass('anim-running');

          setTimeout(function() {
            element
              .addClass('anim-done')
              .animo( { animation: animation, duration: 0.7} );
          }, delay);

        }
      }

    });

    // Run click triggered animations
    $(document).on('click', Selector, function(e) {

      var $this     = $(this),
          targetSel = $this.data('target'),
          animation = $this.data('play') || 'bounce',
          target    = $(targetSel);

      if(target && target) {
        target.animo( { animation: animation } );
      }
      
    });

  });

}(jQuery, window, document));

/**=========================================================
 * Module: portlet.js
 * 拖放任何面板改变其位置
 * The Selector should could be applied to any object that contains
 * panel, so .col-* element are ideal.
 =========================================================*/

(function($, window, document){

  // Component is optional
  if(!$.fn.sortable) return;

  var Selector = '[data-toggle="portlet"]';

  $(function(){

    $( Selector ).sortable({
      connectWith:  Selector,
      items:        'div.panel',
      handle:       '.portlet-handler',
      opacity:      0.7,
      placeholder:  'portlet box-placeholder',
      cancel:       '.portlet-cancel',
      forcePlaceholderSize: true,
      iframeFix:  false,
      tolerance:  'pointer',
      helper:     'original',
      revert:     200,
      forceHelperSize: true,

    }).disableSelection();

  });

}(jQuery, window, document));


/**=========================================================
 * Module: sidebar-menu.js
 * Provides a simple way to implement bootstrap collapse plugin using a target 
 * next to the current element (sibling)
 * Targeted elements must have [data-toggle="collapse-next"]
 =========================================================*/
(function($, window, document){

  var collapseSelector = '[data-toggle="collapse-next"]',
      colllapsibles    = $('.sidebar .collapse').collapse({toggle: false}),
      toggledClass     = 'aside-toggled',
      $body            = $('body'),
      phone_mq         = 768; // media querie

  $(function() {

    $(document)
      .on('click', collapseSelector, function (e) {
          e.preventDefault();
          
          if ($(window).width() > phone_mq &&
              $body.hasClass(toggledClass)) return;

          // Try to close all of the collapse areas first
          colllapsibles.collapse('hide');
          // ...then open just the one we want
          var $target = $(this).siblings('ul');
          $target.collapse('show');

      })
      // Submenu when aside is toggled
      .on('click', '.sidebar > .nav > li', function() {

        if ($body.hasClass(toggledClass) &&
          $(window).width() > phone_mq) {

            $('.sidebar > .nav > li')
              .not(this)
              .removeClass('open')
              .end()
              .filter(this)
              .toggleClass('open');
        }

      });

  });


}(jQuery, window, document));

/**=========================================================
 * Module: sparkline.js
 * SparkLines Mini Charts
 =========================================================*/

(function($, window, document){

  var Selector = '.inlinesparkline';

  // Match color with css values to style charts
  var colors = {
        primary:         '#5fb5cb',
        success:         '#27ae60',
        info:            '#22bfe8',
        warning:         '#ffc61d',
        danger:          '#f6504d'
    };

  // Inline sparklines take their values from the contents of the tag 
  $(Selector).each(function() {

      var $this = $(this);
      var data = $this.data();

        if(data.barColor && colors[data.barColor])
          data.barColor = colors[data.barColor];

      var options = data;
      options.type = data.type || 'bar'; // default chart is bar

      $(this).sparkline('html', options);

  });

}(jQuery, window, document));

/**=========================================================
 * Module: table-checkall.js
 * 表检查所有复选框
 =========================================================*/

(function($, window, document){
  
  var Selector = 'th.check-all';

  $(Selector).on('change', function() {
    var $this = $(this),
        index= $this.index() + 1,
        checkbox = $this.find('input[type="checkbox"]'),
        table = $this.parents('table');
    // Make sure to affect only the correct checkbox column
    table.find('tbody > tr > td:nth-child('+index+') input[type="checkbox"]')
      .prop('checked', checkbox[0].checked);

  });

}(jQuery, window, document));

/**=========================================================
 * Module: tooltips.js
 * 初始化 Bootstrap 工具提示与自动布局
 =========================================================*/

(function($, window, document){

  $(function(){

    $('[data-toggle="tooltip"]').tooltip({
    	
      container: 'body',
      placement: function (context, source) {
                    //return (predictTooltipTop(source) < 0) ?  "bottom": "top";
                    var pos = "top";
                    if(predictTooltipTop(source) < 0)
                      pos = "bottom";
                    if(predictTooltipLeft(source) < 0)
                      pos = "right";
                    return pos;
                }
    });

  });

  // Predicts tooltip top position 
  // based on the trigger element
  function predictTooltipTop(el) {
    var top = el.offsetTop;
    var height = 40; // asumes ~40px tooltip height

    while(el.offsetParent) {
      el = el.offsetParent;
      top += el.offsetTop;
    }
    return (top - height) - (window.pageYOffset);
  }

  // Predicts tooltip top position 
  // based on the trigger element
  function predictTooltipLeft(el) {
    var left = el.offsetLeft;
    var width = el.offsetWidth;

    while(el.offsetParent) {
      el = el.offsetParent;
      left += el.offsetLeft;
    }
    return (left - width) - (window.pageXOffset);
  }

}(jQuery, window, document));


/**=========================================================
 * Module: user-block-status.js
 * 用于下拉栏的变化
 * 用户状态
 =========================================================*/

(function($, window, document){

  var Selector =  '.user-block-status';

  $(document).on('click', Selector, function(e) {

    // avoids conflict with menu collapse
    e.stopPropagation();

    var $this = $(this),
        html = $this.find('.dropdown-menu > li > a').filter(e.target).html(), // the status clicked
        btn  = $this.find('.btn'); // the button to display status
    
    // Update button status
    btn.html(html);

    // Update picture status indicator
    $('.user-block .user-block-picture .user-block-status').html(html);
    
    // Since we stopPropagation dropdown must be closed manually
    if($this.hasClass('open'))
      btn.dropdown('toggle');
    
  });

}(jQuery, window, document));

/**=========================================================
 * Module: utils.js
 * jQuery Utility functions library 
 * 改编自UIKit的核心
 =========================================================*/

(function($, window, doc){

    "use strict";
    
    var $html = $("html"), $win = $(window);

    $.support.transition = (function() {

        var transitionEnd = (function() {

            var element = doc.body || doc.documentElement,
                transEndEventNames = {
                    WebkitTransition: 'webkitTransitionEnd',
                    MozTransition: 'transitionend',
                    OTransition: 'oTransitionEnd otransitionend',
                    transition: 'transitionend'
                }, name;

            for (name in transEndEventNames) {
                if (element.style[name] !== undefined) return transEndEventNames[name];
            }
        }());

        return transitionEnd && { end: transitionEnd };
    })();

    $.support.animation = (function() {

        var animationEnd = (function() {

            var element = doc.body || doc.documentElement,
                animEndEventNames = {
                    WebkitAnimation: 'webkitAnimationEnd',
                    MozAnimation: 'animationend',
                    OAnimation: 'oAnimationEnd oanimationend',
                    animation: 'animationend'
                }, name;

            for (name in animEndEventNames) {
                if (element.style[name] !== undefined) return animEndEventNames[name];
            }
        }());

        return animationEnd && { end: animationEnd };
    })();

    $.support.requestAnimationFrame = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.oRequestAnimationFrame || function(callback){ window.setTimeout(callback, 1000/60); };
    $.support.touch                 = (
        ('ontouchstart' in window && navigator.userAgent.toLowerCase().match(/mobile|tablet/)) ||
        (window.DocumentTouch && document instanceof window.DocumentTouch)  ||
        (window.navigator['msPointerEnabled'] && window.navigator['msMaxTouchPoints'] > 0) || //IE 10
        (window.navigator['pointerEnabled'] && window.navigator['maxTouchPoints'] > 0) || //IE >=11
        false
    );
    $.support.mutationobserver      = (window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver || null);

    $.Utils = {};

    $.Utils.debounce = function(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    $.Utils.removeCssRules = function(selectorRegEx) {
        var idx, idxs, stylesheet, _i, _j, _k, _len, _len1, _len2, _ref;

        if(!selectorRegEx) return;

        setTimeout(function(){
            try {
              _ref = document.styleSheets;
              for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                stylesheet = _ref[_i];
                idxs = [];
                stylesheet.cssRules = stylesheet.cssRules;
                for (idx = _j = 0, _len1 = stylesheet.cssRules.length; _j < _len1; idx = ++_j) {
                  if (stylesheet.cssRules[idx].type === CSSRule.STYLE_RULE && selectorRegEx.test(stylesheet.cssRules[idx].selectorText)) {
                    idxs.unshift(idx);
                  }
                }
                for (_k = 0, _len2 = idxs.length; _k < _len2; _k++) {
                  stylesheet.deleteRule(idxs[_k]);
                }
              }
            } catch (_error) {}
        }, 0);
    };

    $.Utils.isInView = function(element, options) {

        var $element = $(element);

        if (!$element.is(':visible')) {
            return false;
        }

        var window_left = $win.scrollLeft(),
            window_top  = $win.scrollTop(),
            offset      = $element.offset(),
            left        = offset.left,
            top         = offset.top;

        options = $.extend({topoffset:0, leftoffset:0}, options);

        if (top + $element.height() >= window_top && top - options.topoffset <= window_top + $win.height() &&
            left + $element.width() >= window_left && left - options.leftoffset <= window_left + $win.width()) {
          return true;
        } else {
          return false;
        }
    };

    $.Utils.options = function(string) {

        if ($.isPlainObject(string)) return string;

        var start = (string ? string.indexOf("{") : -1), options = {};

        if (start != -1) {
            try {
                options = (new Function("", "var json = " + string.substr(start) + "; return JSON.parse(JSON.stringify(json));"))();
            } catch (e) {}
        }

        return options;
    };

    $.Utils.events       = {};
    $.Utils.events.click = $.support.touch ? 'tap' : 'click';

    $.langdirection = $html.attr("dir") == "rtl" ? "right" : "left";

    $(function(){

        // Check for dom modifications
        if(!$.support.mutationobserver) return;

        // Install an observer for custom needs of dom changes
        var observer = new $.support.mutationobserver($.Utils.debounce(function(mutations) {
            $(doc).trigger("domready");
        }, 300));

        // pass in the target node, as well as the observer options
        observer.observe(document.body, { childList: true, subtree: true });

    });

    // add touch identifier class
    $html.addClass($.support.touch ? "touch" : "no-touch");

}(jQuery, window, document));
/**
 * 供了一个起点运行插件和其他脚本
 */
(function($, window, document){

	if (typeof $ === 'undefined') { throw new Error('This application\'s JavaScript requires jQuery'); }
	$(window).load(function() {
		$('.scroll-content').slimScroll({
        	height: '260px'
    	});
    	$('.sidebar').slimScroll({
        	height: '100%',
    	});    
  	});

	$(function() {
	    // 抑制空链接
	    $('a[href="#"]').each(function(){
	      this.href = 'javascript:void(0);';
	    });	
	    $("[data-toggle=popover]").popover();	
	});

	/*开关切换*/
	$(".switch-enable").click(function(){
	   	var parent = $(this).parent('.switch-wrapper');
	    $('.switch-disable',parent).removeClass('selected');
		$(this).addClass('selected');
		parent.prev('input').val('1');   
	});
	$(".switch-disable").click(function(){
		var parent = $(this).parents('.switch-wrapper');
		$('.switch-enable',parent).removeClass('selected');
		$(this).addClass('selected');
		parent.prev('input').val('0'); 
	});
		
    //ajax get请求
    $('.ajax-get').click(function(){
        var target;
        var that = this;
        if ( $(this).hasClass('confirm') ) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            $.get(target).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        topAlert(data.info + ' 页面即将自动跳转~','success');
                    }else{
                        topAlert(data.info,'success');
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    topAlert(data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1500);
                }
            });

        }
        return false;
    });

	//ajax post submit请求
	$('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $('.'+target_form);                   
            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();            	
            }else if (form.get(0)==undefined){
            	return false;
            }else if ( form.get(0).nodeName=='FORM' ){            	
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                	target = $(this).attr('url');

                }else{
                	target = form.get(0).action;
                }                 
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {                
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            }else{
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
                if (data.status) {					
                    if (data.url) {
                        topAlert(data.info + ' 页面即将自动跳转~','success');
                    }else{
                        topAlert(data.info ,'success');
                    }
					
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    topAlert(data.info);
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1500);
                }
            });
        }
        return false;
    });

	//ajax 删除请求
	$('.btn-del').click(function () {
		var $this=$(this), url = $this.attr('url'), msgType="success";
		 $.getJSON(url,function(data){
		 	if (0 == data.status){
		 		msgType = "danger";	 		
		 	}else{
		 		$this.parents('tr').remove();
		 	}
		 	topAlert(data.info,msgType);		 		
		});		
	});
	
	
}(jQuery, window, document));

//导航高亮
function highlight_subnav(url,url2){
	if(url2 == null) url2 = url;
    $('#head-menu').find('a[href="'+url+'"]').closest('li').addClass('current');
    $('.side-sub-menu').find('a[href="'+url2+'"]').closest('li').addClass('current');  	
}

//顶部消息提示
function topAlert(message,type) {
	var $status = "danger";
  	if ('success' == type){$status = 'success'}
    if(!message)
        $.error('通知:没有指定的信息');     
      $.notify(message, {status:$status});
}

/*时间搜索显示*/
$('.search-dropdown').click(function (e) {
	if ($(this).next(".search-dropdown-con").is(":hidden")) {
		$(this).next(".search-dropdown-con").show();
		$(this).find("i").addClass("fa-chevron-up");
		e.stopPropagation();
	}else{
    	$(this).find("i").removeClass("fa-chevron-up");
    }

});

$(".search-dropdown-con").click(function(e) {
	e.stopPropagation();
});
$(document).click(function() {
	$(".search-dropdown-con").hide();
	 $(".search-dropdown").find("i").removeClass("fa-chevron-up");
});