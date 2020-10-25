var App = function() {
    var assetsPath = '/assets/';
    var globalImgPath = 'img/';
    // 定义顶部 topbar-product 下拉层
    var handleTopbarProduct = function() {
        //产品与服务 下拉列表
        $('.topbar-product-btn').on('click', function (e) {
            e.stopPropagation();
            $('.topbar-product').toggleClass('open').find('.topbar-product-dropdown').toggleClass('topbar-show');
            return false
        });
    };

    // 导航区
    var handleViewFrameworkSidebarMain = function() {
        var $li = $("#accordion>li").length; //计算ul 下 li总数
        // 左侧菜单手风琴样式
        var Accordion = function(el, multiple) {
            this.el = el || {};
            this.multiple = multiple || false;
            var links = this.el.find('.sidebar-title');//点击事件
            links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
        };

        Accordion.prototype.dropdown = function(e) {
            var $el = e.data.el,
                $this = $(this),
                $next = $this.next(),
                $max = ($('.viewFramework-body').height() - 30) - ($li*40);//总UL高度-总li高度
            //console.log($max);
            $next.slideToggle();
            //添加样式
            $this.parent().toggleClass('sidebar-nav-active').find('.submenu').css({"max-height" : $max + 'px'});
            if (!e.data.multiple) {
                $el.find('.submenu').not($next).slideUp().parent().removeClass('sidebar-nav-active');//删除样式
            }
        };
        var accordion = new Accordion($('#accordion'), false); //开始执行

        // 左侧导航展开或缩小事件
        $('.viewFramework-body .sidebar-fold').on('click', function(e) {
            e.preventDefault();
            var viewFramework = $(this).closest(".viewFramework-body");
            if (viewFramework.hasClass('viewFramework-sidebar-full')) {
                // 缩小
                viewFramework.removeClass('viewFramework-sidebar-full').addClass('viewFramework-sidebar-mini');
                $(this).removeClass('icon-unfold').addClass('icon-fold');

            } else {
                // 展开
                viewFramework.removeClass('viewFramework-sidebar-mini').addClass('viewFramework-sidebar-full');
                $(this).removeClass('icon-fold').addClass('icon-unfold');
            }
        });

        // 如果 viewFramework-body 添加了 viewFramework-sidebar-mini
        if ($(".viewFramework-body").hasClass('viewFramework-sidebar-mini')) {
            $('.sidebar-fold').removeClass('icon-unfold').addClass('icon-fold');
        }

        //中间导航区事件
        $('.viewFramework-product .product-navbar-collapse').on('click', function(e) {
            e.preventDefault();
            var product = $(this).closest(".viewFramework-product");
            if (product.hasClass('viewFramework-product-col-1')) {
                product.removeClass('viewFramework-product-col-1')
            } else {
                product.toggleClass('viewFramework-product-col-1')
            }
        })
    };

    // 返回顶部
    var handleHtmlDefaultTop = function () {
        var body = $('.viewFramework-product-body'), // 获取区域
            $back_to_top = $('.syc-top'), //链接
            offset = 300, // 滚动参数
            offset_opacity = 1200, //链接透明度
            scroll_top_duration = 700; //持续时间（毫秒）
        //隐藏或显示“回到顶部”链接
        body.on('scroll',function(){
            ($(this).scrollTop() > offset) ? $back_to_top.addClass('syc-is-visible'): $back_to_top.removeClass('syc-is-visible syc-fade-out');
            if($(this).scrollTop() > offset_opacity) {
                $back_to_top.addClass('syc-fade-out');
            }
        });
        //平滑滚动到顶部
        $back_to_top.on('click', function() {
            <!--此处加入finish防止多次点击回顶部或者回底部多次触发动画的bug,也可以使用stop()来替换finish()-->
            body.finish().animate({scrollTop:0},scroll_top_duration);
        });
    }

    // 运行 init
    return {
        // 运行 init
        init: function() {
            handleTopbarProduct();
            handleViewFrameworkSidebarMain();
            handleHtmlDefaultTop();

            // 封装类 toastr 消息
            $.extend({
                sycToAjax:function (href, options, type, async) {
                    var type = type ? type : 'POST';
                    var async = async ? async : true;
                    $.ajax({
                        url: href,
                        type: type, //GET
                        async: async,    //或false,是否异步
                        data: options,
                        timeout:5000,    //超时时间
                        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text

                        success:function(data){
                            if (data.code == '1') {
                                //
                                toastr.success(data.msg+" ...");
                                setTimeout(function () {
                                    window.location.href = data.url;
                                }, 1500);
                            } else {
                                toastr.warning(data.msg+" ...");
                                setTimeout(function () {
                                    window.location.href = data.url;
                                }, 1500);
                            }
                        },
                        error:function(xhr,textStatus){
                            toastr.error("程序严重错误");
                            console.log(xhr);
                            console.log(textStatus);
                        },
                        // complete:function(){
                        //     console.log('结束')
                        // }
                    });
                },
            });
            //限制键盘只能按数字键、小键盘数字键、退格键、上下左右、TAB
            $.fn.keydowns = function () {
                this.keydown(function (e) {
                    var code = parseInt(e.keyCode); // 监控输入框keyCode事件
                    switch (true) {
                        case code >= 48 && code <= 57: return true;
                        case code >= 96 && code <= 105: return true;
                        case code == 8  || code == 9 || code == 13: return true;
                        case code >= 37 && code <= 40: return true;
                        default: return false;
                    }

                    //if (code == 9) {
                        //console.log(e);
                    //}

                    //if (code >= 96 && code <= 105 || code >= 48 && code <= 57 || code == 8 || code == 37 || code == 39) {
                    //    return true;
                    //} else {
                    //    return false;
                    //}
                });
            };
            //监控折扣输入框变动
            $.fn.watch = function (callback) {
                this.keydowns();
                return $(this).each(function () {
                    //缓存以前的值
                    $.data(this, 'originVal', $(this).val());
                    $(this).on('keyup paste', function () {
                        var originVal = $.data(this, 'originVal');
                        var currentVal = $(this).val();
                        if (currentVal=='') {
                            return false
                        }

                        if (originVal !== currentVal) {
                            $.data(this, 'originVal', $(this).val());
                            callback(currentVal);
                        }
                    });
                });
            };
        },

        // 全局弹出遮罩层 阻止
        blockUI: function(options) {
            options = $.extend(true, {}, options);
            var html = '';
            if (options.animate) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '">' + '<div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' + '</div>';
            } else if (options.iconOnly) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey-2.gif" align=""></div>';
            } else if (options.textOnly) {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><span>&nbsp;&nbsp;' + (options.message ? options.message : '加载中...') + '</span></div>';
            } else {
                html = '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey-2.gif" align=""><span>&nbsp;&nbsp;' + (options.message ? options.message : '加载中...') + '</span></div>';
            }

            if (options.target) { // element blocking
                var el = $(options.target);
                if (el.height() <= ($(window).height())) {
                    options.cenrerY = true;
                }
                el.block({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    centerY: options.cenrerY !== undefined ? options.cenrerY : false,
                    css: {
                        top: '10%',
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            } else { // page blocking
                $.blockUI({
                    message: html,
                    baseZ: options.zIndex ? options.zIndex : 1000,
                    css: {
                        border: '0',
                        padding: '0',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                        opacity: options.boxed ? 0.05 : 0.1,
                        cursor: 'wait'
                    }
                });
            }
        },

        // 全局弹出遮罩层 取消
        unblockUI: function(target) {
            if (target) {
                $(target).unblock({
                    onUnblock: function() {
                        $(target).css('position', '');
                        $(target).css('zoom', '');
                    }
                });
            } else {
                $.unblockUI();
            }
        },

        //
        getGlobalImgPath: function() {
            return assetsPath + globalImgPath;
        }
    };

}();
jQuery(document).ready(function() {
    App.init();
});

