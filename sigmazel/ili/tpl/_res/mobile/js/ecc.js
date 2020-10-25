/**
 * 扩展内置对象
 */
$.extend(String.prototype, {
    decode: function() {
        return unescape(this);
    },
    encode: function() {
        return escape(this);
    },
    endsWith: function(suffix) {
        return (this.substr(this.length - suffix.length) === suffix);
    },
    isAnsi: function() {
        return /^\w+$/.test(this);
    },
    isColor: function() {
        return /^#[0-9a-fA-F]{6}$/.test(this);
    },
    isEmail: function() {
        return /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(this);
    },
    isGB: function() {
        return /^[\u4e00-\u9fa5]+$/.test(this);
    },
    isPostCode: function() {
        return /^\d{6}$/.test(this);
    },
    isIDCard: function() {
        return /^\d{15}|\d{18}$/.test(this);
    },
    isInt: function() {
        return /^[+-]?[0-9]+$/.test(this);
    },
    isIP: function() {
        return /^\d+\.\d+\.\d+\.\d+$/.test(this);
    },
    isLongDate: function() {
        return /^\d{4}-\d{1,2}-\d{1,2}\s\d{2}:\d{2}:\d{2}$/.test(this);
    },
    isNumber: function() {
        return /^(-?\d+)(\.\d+)?$/.test(this);
    },
    isPhone: function() {
        return /^\d{3}\-\d{8}|\d{4}\-\d{7}$/.test(this);
    },
	isMobile: function() {
        return /^1(3|5|7|8)\d{8,9}$/.test(this);
    },
    isShortDate: function() {
        return /^\d{4}-\d{1,2}-\d{1,2}$/.test(this);
    },
    isUniqueID: function() {
        return /^\d{15,16}$/.test(this);
    },
    startsWith: function(prefix) {
        return (this.substr(0, prefix.length) === prefix);
    },
    stripTags: function() {
        return this.replace(/<\/?[^>]+>/gi, '');
    },
    trim: function() {
        return this.replace(/^\s+|\s+$/g, '');
    },
    trimEnd: function() {
        return this.replace(/\s+$/, '');
    },
    trimStart: function() {
        return this.replace(/^\s+/, '');
    }
});

$.extend(Array.prototype, {
    add: function(item) {
        this[this.length] = item;
    },
    clear: function() {
        this.length = 0;
    },
    clone: function() {
        if (this.length === 1) {
            return [this[0]];
        } else {
            return Array.apply(null, this);
        }
    },
    contains: function(item) {
        return (this.indexOf(item) >= 0);
    },
    dequeue: function() {
        return this.shift();
    },
    each: function(method, callback) {
        for (var i = 0,
        l = this.length; i < l; i++) {
            var elt = this[i];
            if (typeof(elt) !== "undefined") {
                method.call(this, i, elt, callback);
            }
        }
    },
    indexOf: function(item, start) {
        if (typeof(item) === "undefined") {
            return - 1;
        }
        var length = this.length;
        if (length !== 0) {
            start = start - 0;
            if (isNaN(start)) {
                start = 0;
            } else {
                if (isFinite(start)) {
                    start = start - (start % 1);
                }
                if (start < 0) {
                    start = Math.max(0, length + start);
                }
            }
            for (var i = start; i < length; i++) {
                if ((typeof(this[i]) !== "undefined") && (this[i] === item)) {
                    return i;
                }
            }
        }
        return - 1;
    },
    insert: function(index, item) {
        this.splice(index, 0, item);
    },
    merge: function(items) {
        this.push.apply(this, items);
    },
    remove: function(item) {
        var index = this.indexOf(item);
        if (index >= 0) {
            this.splice(index, 1);
        }
    },
    removeAt: function(index) {
        this.splice(index, 1);
    }
});

/**
 * 侧边回上拉刷新控件，单例
 * TODO加入上拉效果
 */
$.extend($, {
	refresh: function(options){
		options = options || {};
		if(!options['id']) return;
		
		var scroll = {};
		var refreshEl;
		
		scroll.options = {
			state:1, 
			page:1, 
            pullText: '点击查看更多..', 
            refreshText: '加载中..', 
            refreshTi: false, 
            before:null, 
            callback: null, 
            loadding:false, 
            condition:''
        };
        
        scroll.disable = function(){
        	scroll.options.state = 0;
        }
        
        scroll.refresh = function(){
        	scroll.options.loadding = false;
        }
        
        $.extend(scroll.options, options);
        
        var container = $('#' + options['id']).get(0);
        
        var refreshTpl = '<div class="refresh-container">';
        refreshTpl += '<span class="refresh-label">' + scroll.options.pullText + '</span>';
        refreshTpl += '</div>';
        
        refreshEl = $(refreshTpl).appendTo(container);
        
        $('.refresh-label', refreshEl).click(function(){
        	if(scroll.options.state == 1) scroll.options.callback.call(scroll);
        });
        
		return scroll;
	}
});

/**
 * 侧边菜单控件，单例
 */
$.extend($, {
	aside: function(options){
		var params = {
			id: '', 
			icon: '', 
			section: '', 
			callback: null
		};
		
		$.extend(params, options);
		
		if(!params.id || !params.icon || !params.section) return;
		
		var _aside = $('#' + params.id);
		var _icon = $('#' + params.icon);
		var _section = $('#' + params.section);
		
	    function _showAsideMenu(){
	    	$(_aside).addClass('active');
	    	$('.container', _aside).css({height:Math.max($(document).height(), $(window).height()) + 'px'});
	    	$(_aside).animate({right : '0'}, 'fast');
	    }
	    
	    function _hideAsideMenu(){
	    	$(_aside).animate({right : '-' + $(_aside).width() + 'px'}, 'fast', function(){
	    		$(_aside).removeClass('active');
	    	});
	    }
	    
	    $(_icon).click(_showAsideMenu);
	    $('.back', _aside).click(_hideAsideMenu);
        $(_aside).on('swipeRight', _hideAsideMenu);
        
        if(typeof(params.callback) == 'function') params.callback();
	}
});

/**
 * 侧边回顶部控件，单例
 */
$.extend($, {
	gotop: function(options){
		var params = {
			id: '', 
			bottom: '', 
			scroll: null
		};
		
		$.extend(params, options);
		
		if(!params.id) return;
		
		var _gotop = $('#' + params.id);
		
		function _toggleGoTop(){
			if($(window).scrollTop() > 0) $(_gotop).show();
			else $(_gotop).hide();
			
			if(typeof(params.scroll) == 'function') params.scroll();
		}
		
		if(params.bottom) $(_gotop).css('bottom', params.bottom);
		
		$(window).scroll(_toggleGoTop);
		
		$(_gotop).click(function(event){
			event.stopPropagation(); 
			$.scrollTo({top:0, duration:200}); 
			return false;
		});
	}
});

/**
 * 弹出框控件，单例
 */
$.extend($, {
	dialog:function(options){
		var params = {
			data: {}, 
            href: '', 
            close: '', 
            message: '', 
            element: '', 
            button: {cancel:'', submit:''}, 
            cancel: null, 
            submit: null, 
            callback: null, 
            mheight: Math.max($(window).height(), $(document).height()), 
            timeout:0, 
            timeouted:null, 
            remove:true,
            top:0, 
            max:false
        };
        
		$.extend(params, options);
		
		function _dialog_callback(){
	    	if(typeof(params.callback) == 'function') params.callback();
	    	
	    	$('.submit', _dialog).click(function(){
				if(typeof(params.submit) == 'function') params.submit();
				if(params.remove) $(_dialog).remove();
			});
			
			$('.cancel', _dialog).click(function(){
				if(typeof(params.cancel) == 'function') params.cancel();
				$(_dialog).remove();
			});
			
		    var _cheight = $('.content', _dialog).height();
		    $('.window', _dialog).css({height:_cheight + 'px', marginTop:'-' + (_cheight / 2  + params.top) + 'px', position:'fixed'});
		}
		
		if($('#_dialog').length) $('#_dialog').remove();
		if(params.close) return;
		
		var _contentHTML = params.message ? '<div class="message">' + params.message + '</div>' : params.element;
		
		var _tempHTML = '<div id="_dialog" class="dialog">';
		_tempHTML += '<div class="cover"></div>';
		_tempHTML += params.max ? '<div class="window window-max fadeIn fasted">' : '<div class="window fadeIn fasted">';
	    _tempHTML += '<div class="content"></div>';
	    
	    if(params.button.submit || params.button.cancel){
	    	_contentHTML += '<div class="button">';
	    	
	    	if(params.button.cancel) _contentHTML += '<a class="cancel">' + params.button.cancel + '</a>';
	    	if(params.button.submit) _contentHTML += '<a class="pure-submit submit">' + params.button.submit + '</a>';
	    	
	    	_contentHTML += '</div>';
	    }
	    
	    _tempHTML += '</div>';
	    _tempHTML += '</div>';
	    
	    $('body').append(_tempHTML);
	    
	    var _dialog = $('#_dialog');
	    
	    $(_dialog).height(params.mheight);
	    
	    $('.cover', _dialog).height(params.mheight).click(function(){
	    	if(params.remove) $(_dialog).remove();
	    });
		
	    if(params.href){
	    	$.get(params.href, params.data, function(data){
	    		$('.content', _dialog).html(data + _contentHTML);
	    		_dialog_callback();
	    	});
	    }else {
	    	$('.content', _dialog).html(_contentHTML);
	    	_dialog_callback();
	    }
	    
	    if(params.timeout){
		    setTimeout(function(){
				 $(_dialog).animate('hide', function(){
		            if(typeof(params.timeouted) == 'function') params.timeouted();
		            
		             _dialog.hide().remove();
				 });
			}, params.timeout);
	    }
	}
});

/**
 * 消息控件，单例
 */
$.extend($, {
	toast: function(options){
		$.extend(options, {timeout:1500, top:50});
		$.dialog(options);
	}
});

/**
 * 图片放大控件，单例
 */
$.extend($, {
	image_zoom: function(img){
		if(typeof(window.WeixinJSBridge) != 'undefined'){
			var _images = new Array();
			
			var _parent = arguments.length > 1 ? arguments[1] : $(img).parent();
			if(typeof(_parent) == 'function') _images = _parent();
			else if(typeof(_parent) == 'string') _images = _parent.split(',');
			else{
				$('img', _parent).each(function(index, item){
					if(!$(item).hasClass('swiper-slide-duplicate')) _images.push($(item).attr('data'));
				});
			}
			
			WeixinJSBridge.invoke('imagePreview', {'current': $(img).attr('data'), 'urls': _images});
			return;
		}else{
			var _image = new Image();
			_image.src = $(img).attr('data');
			_image.onload = function(){
				$.dialog({max:true, element:'<img class="pure-u-1" src="' + this.src + '"/>'});
			}
		}
    }
});