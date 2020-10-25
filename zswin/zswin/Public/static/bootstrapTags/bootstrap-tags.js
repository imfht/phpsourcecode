/**
 * Bootstrap plugin - TAGS
 * @Author 上海巨人网络有限公司 应卓
 */
(function( $ ) {
   
	$.fn.tags = function(options) {
		var settings = {
            url: '',
            inputval:'',
            global: false,
			type: 'GET',
			parameterName: 'labelValue',
            max: 0, //最多标签个数(0=不限)
            clearNotFound: false //清除未找到的标签
		};
		
		options = options || {};
        $.extend(settings, options);
		
        return this.each(function () {
            var $div = $(this);
            var $menu = $div.find('.tags-menu');
            var $buttons = $div.find('div:nth-child(1)');
            var $input = $div.find('input:last');
            var menuOpend = false;
            var tags = []; //查找到的JSON标签数组
            var $tags_confirm;
            
            
            if(settings.inputval!=''){//初始赋值
            	var strs= new Array(); //定义一数组 

            	settings.inputval=String(settings.inputval);
            	//alert(typeof(settings.inputval));
            	strs=settings.inputval.split(","); //字符分割 
            	for (i=0;i<strs.length ;i++ ) 
            	{ 
            		label=strs[i];
            		value=label;
                    tags_create_new_label(label, value, $input, $div, settings.parameterName);
                    
            	} 
            
            	
              //value=label;
             // tags_create_new_label(label, value, $input, $div, settings.parameterName);
            	
            }
            
            // 当DIV被点击时焦点交给INPUT
            $div.click(function() {
            	
                $(this).find('input').focus();
                
                if($(this).find('input').val()!=''){
                	
                	
                }
                
                return true;
            });

            //输入框失去焦点时
            $input.blur(function() {
                var stc = setTimeout(function() {$menu.remove();}, 200);
                if (stc != null) {
                    clearTimeout(stc);
                }
            });
            
            //点击其他地方关闭菜单
            $('html').on('click', function(e) {
                if ($(e.target).closest($div).length < 1 && $menu.length) {
                    $menu.remove();
                }
            });
            
            //查询 - 单独提出来（主要是为了对付中文）
            var timeout;
            if (!$.support.opacity&&$.support.style&&window.XMLHttpResquest!=undefined) { //ie8 不支持绑定input事件
                $input.on('propertychange', function() {
                    clearTimeout(timeout);
                   
                    timeout = setTimeout(_doSearch, 300);
                });
            } else {
                $input.bind('input', function() {
                	
                    clearTimeout(timeout);
                    timeout = setTimeout(_doSearch, 300);
                });
            }
            function _doSearch() {
            	
                /*允许的最大个数*/
                if (settings.max > 0) {
                    if ($tags_confirm && ($tags_confirm.length + 1) > settings.max) {
                    	layer.statusinfo('最多输入'+settings.max+'个标签！','error');
                        return;
                    }
                }
                
                if (!settings.url || settings.url.length == 0) return false;
                var term = $input.val();
                if (term.length == 0) return false;
                tags = [];
                var $item = null;
               
                /*发送ajax请求*/
                $.ajax({
                    url: settings.url,
                    global: settings.global,
                    type: settings.type,
                    data: {term: term},
                    dataType: 'json',
                   
                    success: function(json) {
                    	
                        if (json.length != 0) {		// 查找到了数据
                            if (!$menu || !$menu.length) $menu = $('<ul class="tags-menu"></ul>');
                            $menu.html('').hide().appendTo($div);
                            for (var i = 0; i < json.length; i++) {
                                $item = $('<li class="tags-item">'+ json[i].label + '</li>').attr('data-value', json[i].value);
                                $item.appendTo($menu);
                                tags.push(json[i].label);
                            }
                            $menu
                                .css('top', $input.position().top + 28)
                                .css('left', $input.position().left)
                                .show();
                           //$('.tags-menu').niceScroll({styler:"fb",cursorcolor:"#ccc", cursorwidth: '4', cursorborderradius: '', background: '#404040', spacebarenabled:false, cursorborder: ''});
                            $('.tags-menu').closest("div[layoutH]").getNiceScroll().resize();
                            //得到父元素中含有layouth的元素
                            
                            
                            
                            $menu.find('li').hover(function() {
                                $(this).addClass('tags-highlight').siblings().removeClass('tags-highlight');
                                /*$menu.find('li.tags-highlight').removeClass('tags-highlight');
                                $(this).addClass('tags-highlight');*/
                            }, function() {
                                $menu.find('li').removeClass('tags-highlight');
                            });
                            
                            $menu.find('li').click(function() {
                                var label = $(this).text();
                                var value = $(this).data('value');
                                //判断重复
                                var isRepeat = false;
                                $div.find('input:hidden').each(function() {
                                    if ($(this).val() == value) {
                                        isRepeat = true;
                                        return;
                                    }
                                });
                                if (isRepeat) {
                                    $input.val('');
                                    $menu.remove();
                                    return false;
                                }
                                tags_create_new_label(label, value, $input, $div, settings.parameterName);
                                $menu.remove();
                                $input.val('');
                            });
                        }
                    }
                });
            }
     
            // 按键事件
            $input.keyup(function(event) {
            	
            	
                var keyCode = event.which;
                var isMenuPopuped = $menu.length == 1;
              
                if (keyCode == 27) {
                	// esc
                    $menu.remove();
                } else if (keyCode == 40) { // down
                    if (! isMenuPopuped) {
                        return false;
                    }
                    var $highlight = $('.tags-highlight', $menu);
                    var $first     = $menu.find('li:first');
                    if ($highlight.length == 0) {
                        $first.addClass('tags-highlight');
                    } else {
                        var $hight_next = $highlight.removeClass('tags-highlight').next();
                        if ($hight_next.length) {
                            $hight_next.addClass('tags-highlight');
                        } else {
                            $first.addClass('tags-highlight');
                        }
                    }
                    
                } else if (keyCode == 38) { // up
                    if (! isMenuPopuped) {
                        return false;
                    }
                    var $highlight = $('.tags-highlight', $menu);
                    var $last      = $menu.find('li:last');
                    if ($highlight.length == 0) {
                        $last.addClass('tags-highlight');
                    } else {
                        var $hight_prev = $highlight.removeClass('tags-highlight').prev();
                        if ($hight_prev.length) {
                            $hight_prev.addClass('tags-highlight');
                        } else {
                            $last.addClass('tags-highlight');
                        }
                    }
                } else if (keyCode == 188||keyCode == 13) {
                    /*允许的最大个数*/
                    if (settings.max > 0) {
                        if ($tags_confirm && ($tags_confirm.length + 1) > settings.max) {
                            return;
                        }
                    }
                    var label = false, value = false;
                    var $selectedItem = $menu && $menu.find('.tags-highlight');
                    if ($selectedItem && $selectedItem.length > 0) {
                        label = $selectedItem.text();
                        
                        value = $selectedItem.data('value');
                    } else {
                        label = $.trim($input.val());
                        if (label.length == 0) return false;
                        if (settings.clearNotFound) {
                        	
                            if ($.inArray(label, tags) == -1) {
                                $input.val('');
                                return false;
                            }
                        }
                        label=label.replace(',','');
                        label=label.replace('，','');
                        value = label;
                    }
                   
                    	
                   
                      
                  
                    
                   
                    if (!label){
                    	
                    	 $input.val('');
                    	return;
                    }
                    /*判断重复*/
                    var isRepeat = false;
                    $tags_confirm && $tags_confirm.length && $tags_confirm.each(function() {
                        if ($(this).val() == value) {
                            isRepeat = true;
                            return;
                        }
                        
                    });
                    if (isRepeat) {
                        $input.val('');
                        return false;
                    }
                    
                   
                   
                    
                    tags_create_new_label(label, value, $input, $div, settings.parameterName);
                    $input.val('');
                    if ($menu.length > 0) {
                        $menu.remove();
                    }
                } else if(keyCode == 8) { // backspace
                    if ($.trim($input.val().length == 0)) {
                        $menu.remove();
                        return false;
                    }
                }
            });
            
            function tags_create_new_label (label, value, appendToSelector, divSelector, parameterName) {
            	
            	
                var $btn = $('<span class="tag" data-value="' + value +'" style="margin-left: 1px; margin-top: 1px;">' + label + '&nbsp;&nbsp;<a href="javascript:void(0)" class="close tagsinput-remove-link"></a></span>');
               
                $btn.insertBefore(appendToSelector);
                    
                $btn.find('a.close').click(function() {
                    	
                        var value = $btn.data('value');
                        divSelector.find('input:hidden').each(function() {
                            if ($(this).val() == value) {
                                $(this).remove();
                                
                                return false;
                            }
                        });
                       // $('body').scrollTop(divSelector.offset().top);
                        $btn.remove();
                        $tags_confirm = $div.find('input:hidden');
                      
                    });
               
                var $hidden = $('<input type="hidden" name="' + parameterName + '">').val(value);
                $hidden.appendTo(divSelector);
                
                $tags_confirm = $div.find('input:hidden');
            }
        });
	};
})(jQuery);