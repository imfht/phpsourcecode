$.extend(Function.prototype, {
	_instanceof:function(instance){
		if(typeof(instance) === 'undefined' || instance === null) return false;
		if(instance instanceof this) return true;
		else return false;
	}
});

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
        return /^1\d{10}$/.test(this);
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

function Text(txt){
	this._parts = (typeof(txt) !== 'undefined'&& txt !== null && txt !== '') ? [txt.print()] : [];
	this._value = {};
	this._len = 0;
}

$.extend(Text.prototype, {
	_parts:null,
	_value:null,
	_len:null,
	append:function(txt){
		this._parts[this._parts.length] = txt;
	},
	clear:function(){
		this._parts = [];
		this._value = {};
		this._len = 0;
	},
	empty:function(){
		if(this._parts.length === 0) return true;
		return this.toString() === '';
	},
	print:function(sep){
		sep = sep || '';
		var parts = this._parts;
		if(this._len !== parts.length){
			this._value = {};
			this._len = parts.length;
		}
		var val = this._value;
		if(typeof(val[sep]) === 'undefined'){
			if(sep !== ''){
				for(var i = 0; i < parts.length; ){
					if((typeof(parts[i]) === 'undefined') || (parts[i] === '') || (parts[i] === null)) parts.splice(i, 1);
					else i++;
				}
			}
			val[sep] = this._parts.join(sep);
		}
		return val[sep];
	}
});

var _stringregex = new RegExp('["\b\f\n\r\t\\\\\x00-\x1F]', 'i');
var _serialize_withtext = function(object, txt, sort) {
    var i;
    switch (typeof object) {
        case 'object':
            if (object) {
                if (Array._instanceof(object)) {
                    txt.append('[');
                    for (i = 0; i < object.length; ++i) {
                        if (i > 0) txt.append(',');
                        _serialize_withtext(object[i], txt);
                    }
                    txt.append(']');
                } else {
                    if (Date._instanceof(object)) {
                        txt.append('"\\/Date(');
                        txt.append(object.getTime());
                        txt.append(')\\/"');
                        break;
                    }
                    var properties = [];
                    var propertyCount = 0;
                    for (var name in object) {
                        if (name.startsWith('$')) continue;
                        properties[propertyCount++] = name;
                    }
                    if (sort) properties.sort();
                    txt.append('{');
                    var needComma = false;
                    for (i = 0; i < propertyCount; i++) {
                        var value = object[properties[i]];
                        if (typeof (value) !== 'undefined' && typeof (value) !== 'function') {
                            if (needComma) txt.append(',');
                            else needComma = true;
                            _serialize_withtext(properties[i], txt, sort);
                            txt.append(':');
                            _serialize_withtext(value, txt, sort);
                        }
                    }
                    txt.append('}');
                }
            } else txt.append('null');
            break;
        case 'number':
            if (isFinite(object)) txt.append(String(object));
            break;
        case 'string':
            txt.append('"');
            if (_stringregex.test(object)) {
                var length = object.length;
                for (i = 0; i < length; ++i) {
                    var curChar = object.charAt(i);
                    if (curChar >= ' ') {
                        if (curChar === '\\' || curChar === '"') txt.append('\\');
                        txt.append(curChar);
                    } else {
                        switch (curChar) {
                            case '\b':
                                txt.append('\\b');
                                break;
                            case '\f':
                                txt.append('\\f');
                                break;
                            case '\n':
                                txt.append('\\n');
                                break;
                            case '\r':
                                txt.append('\\r');
                                break;
                            case '\t':
                                txt.append('\\t');
                                break;
                            default:
                                txt.append('\\u00');
                                if (curChar.charCodeAt() < 16) txt.append('0');
                                txt.append(curChar.charCodeAt().toString(16));
                        }
                    }
                }
            } else txt.append(object);
            txt.append('"');
            break;
        case 'boolean':
            txt.append(object.toString());
            break;
        default:
            txt.append('null');
            break;
    }
}

json_encode = function(object){
	var txt = new Text();
	_serialize_withtext(object, txt, false);
	return txt.print();
}

json_decode = function(data){
	try{
		var exp = data.replace(new RegExp('(^|[^\\\\])\\"\\\\/Date\\((-?[0-9]+)\\)\\\\/\\"','g'),"$1new Date($2)");
		return eval('('+exp+')');
	}catch(e){}
}


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
            close: false, 
            width:590, 
            height:100, 
            mheight: Math.max($(window).height(), $(document).height()), 
            title: '操作提示!',
            message: '', 
            element: '',
            button: {cancel:'关闭', submit:''}, 
            cancel: null, 
            submit: null, 
            callback: null, 
            timeout:0, 
            timeouted:null, 
            remove:true,
            top:0, 
            style:'',
            overflow:'auto',
            css:'-ilinei-dialog'
        };
        
		$.extend(params, options);
		
		function _dialog_callback(){
	    	if(typeof(params.callback) == 'function') params.callback();
	    	
	    	$('.submit', _dialog).click(function(){
				if(typeof(params.submit) == 'function') params.submit();
				if(params.remove) $(_dialog).remove();
			});
			
			$('.cancel, .close', _dialog).click(function(){
				if(typeof(params.cancel) == 'function') params.cancel($(this).attr('rel'));
				$(_dialog).remove();
			});
			
			if(!($.browser.msie && $.browser.version == '6.0')) $('#_dialog .window').css('position', 'fixed');
		}
		
		if($('#_dialog').length) $('#_dialog').remove();
		if(params.close) return;
		
		var _title_height = 50;
		var _bottom_height =  params.button.submit || params.button.cancel ? 60 : 0;
		var _content_height = _title_height + params.height + _bottom_height;
		var _contentHTML = params.message ? '<div class="message">' + params.message + '</div>' : params.element;

		var _tempHTML = '<div id="_dialog" class="' + params.css + '">';

        _tempHTML += '<div class="cover"></div>';
        _tempHTML += '<div class="window" style="' + params.style + ' width:' + params.width + 'px; height:' + (params.height + _title_height + _bottom_height) + 'px; margin-top:-' + (_content_height / 2) + 'px; margin-left:-' + (params.width / 2) + 'px;">';
        _tempHTML += '<div class="title">';
        _tempHTML += '<span class="text">' + params.title + '</span>';
        _tempHTML += '</div>';

		_tempHTML += '<div class="content" style="height:' + (params.height) + 'px; overflow:' + params.overflow + ';"></div>';
	    
	    if(_bottom_height == 60){
	    	_tempHTML += '<form>';
	    	_tempHTML += '<p class="operate">';
	    	
	    	if(params.button.cancel) _tempHTML += '<button type="button" class="button-gray cancel" rel="cancel">' + params.button.cancel + '</button>';
	    	if(params.button.submit) _tempHTML += '<button type="button" class="submit" rel="submit">' + params.button.submit + '</button>';
	    	
	    	_tempHTML += '</p>';
	    	_tempHTML += '</form>';
	    }
	    
	    _tempHTML += '</div>';
	    _tempHTML += '</div>';
	    
	    $('body').append(_tempHTML);
	    
	    var _dialog = $('#_dialog');
	    
	    $(_dialog).height(params.mheight);
	    $('.cover', _dialog).height(params.mheight);
		
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
		$.extend(options, {timeout:1500, top:50, button:{cancel:''}});
		$.dialog(options);
	}
});