(function($){
    $.ikphp = $.ikphp || {version: "v1.0.0"},
    $.extend($.ikphp, {
        util: {
            getStrLength: function(str) {
                str = $.trim(str);
                var length = str.replace(/[^\x00-\xff]/g, "**").length;
                return parseInt(length / 2) == length / 2 ? length / 2: parseInt(length / 2) + .5;
            },
            empty: function(str) {
                return void 0 === str || null === str || "" === str
            },
            isURl: function(str) {
                return /([\w-]+\.)+[\w-]+.([^a-z])(\/[\w-.\/?%&=]*)?|[a-zA-Z0-9\-\.][\w-]+.([^a-z])(\/[\w-.\/?%&=]*)?/i.test(str) ? !0 : !1
            },
            isEmail: function(str) {
                return /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(str);
            },
            minLength: function(str, length) {
                var strLength = $.qupai.util.getStrLength(str);
                return strLength >= length;
            },
            maxLength: function(str, length) {
                var strLength = $.qupai.util.getStrLength(str);
                return strLength <= length;
            },
            redirect: function(uri, toiframe) {
                if(toiframe != undefined){
                    $('#' + toiframe).attr('src', uri);
                    return !1;
                }
                location.href = uri;
            },
            base64_decode: function(input) {
                var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
                var output = "";
                var chr1, chr2, chr3 = "";
                var enc1, enc2, enc3, enc4 = "";
                var i = 0;
                //if(typeof input.length=='undefined')return '';
                if(input.length%4!=0){
                    return "";
                }
                var base64test = /[^A-Za-z0-9\+\/\=]/g;
                
                if(base64test.exec(input)){
                    return "";
                }
                
                do {
                    enc1 = keyStr.indexOf(input.charAt(i++));
                    enc2 = keyStr.indexOf(input.charAt(i++));
                    enc3 = keyStr.indexOf(input.charAt(i++));
                    enc4 = keyStr.indexOf(input.charAt(i++));
                    
                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;
                    
                    output = output + String.fromCharCode(chr1);
                    
                    if (enc3 != 64) {
                        output+=String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output+=String.fromCharCode(chr3);
                    }
                    
                    chr1 = chr2 = chr3 = "";
                    enc1 = enc2 = enc3 = enc4 = "";
                
                } while (i < input.length);
                return output;
            }
        },
		ui:{
				decode_img: function(context) {
					$('.J_decode_img', context).each(function(){
						var uri = $(this).attr('data-uri')||"";
						$(this).attr('src', uri);  
					});
				}
		}
    });
})(jQuery);	
/**
 * @name 瀑布流效果
 * @author QQ：160780470
 * @url http://www.ikphp.com
 */
(function(){

    $.ikphp.wall = {
        settings : {
            container: '#J_waterfall', //容器
            item_unit: '.J_item', //商品单元
            loading_bar: '#J_wall_loading', //加载条
            page_bar: '#J_wall_page',
            ajax_url: null, //请求地址
            distance: 500, //高度微调
            spage: 1,
            max_spage: 3
        },
        init: function(options){
            options && $.extend($.ikphp.wall.settings, options);
            var s = $.ikphp.wall.settings;
            s.ajax_url = $(s.container).attr('data-uri'); 
            var distance = $(s.container).attr('data-distance');
            if(distance != void(0)){
                s.distance = distance;
            }
            //使用masonry插件
			$.ikphp.ui.decode_img($(s.container));
            $(s.container)[0] && $(s.container).imagesLoaded( function(){
                $(s.container).masonry({
                    itemSelector: s.item_unit
                });
                $(s.item_unit).animate({opacity: 1});
            });
            $.ikphp.wall.is_loading = !1;
            $(window).bind('scroll', $.ikphp.wall.lazy_load);
        },
        //加载
        lazy_load: function(){ 
            var s = $.ikphp.wall.settings,
                st = $(document).height() - $(window).scrollTop() - $(window).height();
            if (!$.ikphp.wall.is_loading && $(s.loading_bar)[0] && st <= s.distance) {
                $.ikphp.wall.is_loading = !0;
                $.ikphp.wall.loader();
            }
        },
        //加载状态
        is_loading: !0,
        //执行加载
        loader: function(){
            var s = $.ikphp.wall.settings;
            $(s.loading_bar).show();
            $.ajax({
                url: s.ajax_url,
                data: {sp: s.spage},
                type: 'GET',
                dataType: 'json',
                success: function(result){
                    if(result.status == 1){
                        var html = $(result.data.html).css({opacity: 0});
                        $.ikphp.ui.decode_img(html);
                        html.find('.J_img').imagesLoaded(function(){
                            html.animate({opacity: 1});
                            $(s.container).append(html).masonry('appended', html, true, function(){
                                $(s.loading_bar).hide(); //隐藏加载条
                                $.ikphp.wall.is_loading = !1; //可以继续加载
                                s.spage += 1; //页码加1
                                if(s.spage >= s.max_spage || !result.data.isfull){
                                    $(s.page_bar).show(); //子页加载完毕
                                    $(window).unbind('scroll', $.ikphp.wall.lazy_load);
                                }
                                !result.data.isfull && $(s.loading_bar).remove();
                            });
                        });
                    }else{
					    tips(result.msg);
                    }
                }
            });
        }
    }
    $.ikphp.wall.init({distance:500, max_spage:10});

})(jQuery);
