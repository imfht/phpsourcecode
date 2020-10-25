/*
    Enlarge for jQuery v1.1
    Abel Yao, 2013
    http://www.abelcode.com/
*/

(function($)
{
    // 默认参数
    var defaults = 
    {
        // 鼠标遮罩层样式
        shadecolor: "#FFD24D",
        shadeborder: "#FF8000",
        shadeopacity: 0.5,
        cursor: "move",
        
        // 大图外层样式
        layerwidth: 400,
        layerheight: 300,
        layerborder: "#DDD",
        fade: true,
        
        // 大图尺寸
        largewidth: 1280,
        largeheight: 960
    }
    
    // 插件入口
    var enlarge = function(option)
    {
        // 合并参数
        option = $.extend({}, defaults, option);
        
        // 循环处理
        $(this).each(function() 
        {
            var self = $(this).css("position", "relative");
        // 创建鼠标遮罩层
            var shade = $("<div>").css(
            {
                "position": "absolute",
                "left": "0px",
                "top": "0px",
                "background-color": option.shadecolor,
                "border": "1px solid " + option.shadeborder,

                "opacity": option.shadeopacity,
                "cursor": option.cursor
            });
            shade.hide().appendTo(self);

            // 创建大图和放大图层
            var large = $("<img>").css(
            {
                "position": "absolute",
                "display": "block",
                "width": option.largewidth,
                "height": option.largeheight
            });
            var layer = $("<div>").css(
            {
                "position": "absolute",
                "left": self.width() + 5,
                "top": 0,
                "background-color": "#111",
                "overflow": "hidden",
                "width": option.layerwidth,
                "height": option.layerheight,
                "border": "1px solid " + option.layerborder
            });
            large.appendTo(layer);
            layer.hide().appendTo(self);
            reLoad();

            self.on("mouseenter",reLoad);
            
            // 计算大小图之间的比例
            function reLoad(){
                var img = self.find("img:eq(0)");
                if(img.attr("source") == large.attr("src")) return;
                large.attr("src", img.attr("source"));
                imgReady(img.attr("source"), function () {
                option.largewidth = this.width;
                option.largeheight = this.height;
                var ratio =
                {
                    x: img.width() / option.largewidth,
                    y: img.height() / option.largeheight
                }
            
                // 定义一些尺寸
                var size = 
                {
                    // 计算鼠标遮罩层的大小
                    shade:
                    {
                        width: option.layerwidth * ratio.x - 2,
                        height: option.layerheight * ratio.y - 2
                    }
                }
                shade.css({
                    "width": size.shade.width,
                    "height": size.shade.height
                });
           
               large.css(
                {
                    "width": option.largewidth,
                    "height": option.largeheight
                });
                
                // 不可移动的半径范围
                var half = 
                {
                    x: size.shade.width / 2,
                    y: size.shade.height / 2
                }
            
                // 有效范围
                var area = 
                {
                    width: self.innerWidth() - shade.outerWidth(),
                    height: self.innerHeight() - shade.outerHeight()
                }
            
                // 对象坐标
                var offset = self.offset();
                
                // 显示效果
                var show = function()
                {
                    //large.attr("src", img.attr("source"));
                    offset = self.offset();
                    shade.show();
                    layer.show();
                }
            
                // 隐藏效果
                var hide = function()
                {
                    shade.hide();
                    layer.hide();
                }
                
                // 绑定鼠标事件
                self.mousemove(function(e)
                {
                    // 鼠标位置
                    var x = e.pageX - offset.left;
                    var y = e.pageY - offset.top;

                    // 判断是否在有效范围内
                    x = x - half.x;
                    y = y - half.y;
                    
                    if(x < 0) x = 0;
                    if(y < 0) y = 0;
                    if(x > area.width) x = area.width;
                    if(y > area.height) y = area.height;
                    
                    // 遮罩层跟随鼠标
                    shade.css(
                    {
                        left: x,
                        top: y
                    });
                    
                    // 大图移动到相应位置
                    large.css(
                    {
                        left: (0 - x / ratio.x),
                        top: (0 - y / ratio.y)
                    });

                })
                .mouseenter(show)
                .mouseleave(hide);
                });
            }

        });
    }
    
    // 扩展插件
    $.fn.extend(
    {
        enlarge: enlarge
    });
})(jQuery)

// 更新：
// 05.27: 1、保证回调执行顺序：error > ready > load；2、回调函数this指向img本身
// 04-02: 1、增加图片完全加载后的回调 2、提高性能

/**
 * 图片头数据加载就绪事件 - 更快获取图片尺寸
 * @version	2011.05.27
 * @author	TangBin
 * @see		http://www.planeart.cn/?p=1121
 * @param	{String}	图片路径
 * @param	{Function}	尺寸就绪
 * @param	{Function}	加载完毕 (可选)
 * @param	{Function}	加载错误 (可选)
 * @example imgReady('http://www.google.com.hk/intl/zh-CN/images/logo_cn.png', function () {
        alert('size ready: width=' + this.width + '; height=' + this.height);
    });
 */
var imgReady = (function () {
    var list = [], intervalId = null,

    // 用来执行队列
    tick = function () {
        var i = 0;
        for (; i < list.length; i++) {
            list[i].end ? list.splice(i--, 1) : list[i]();
        };
        !list.length && stop();
    },

    // 停止所有定时器队列
    stop = function () {
        clearInterval(intervalId);
        intervalId = null;
    };

    return function (url, ready, load, error) {
        var onready, width, height, newWidth, newHeight,
            img = new Image();
        
        img.src = url;

        // 如果图片被缓存，则直接返回缓存数据
        if (img.complete) {
            ready.call(img);
            load && load.call(img);
            return;
        };
        
        width = img.width;
        height = img.height;
        
        // 加载错误后的事件
        img.onerror = function () {
            error && error.call(img);
            onready.end = true;
            img = img.onload = img.onerror = null;
        };
        
        // 图片尺寸就绪
        onready = function () {
            newWidth = img.width;
            newHeight = img.height;
            if (newWidth !== width || newHeight !== height ||
                // 如果图片已经在其他地方加载可使用面积检测
                newWidth * newHeight > 1024
            ) {
                ready.call(img);
                onready.end = true;
            };
        };
        onready();
        
        // 完全加载完毕的事件
        img.onload = function () {
            // onload在定时器时间差范围内可能比onready快
            // 这里进行检查并保证onready优先执行
            !onready.end && onready();
        
            load && load.call(img);
            
            // IE gif动画会循环执行onload，置空onload即可
            img = img.onload = img.onerror = null;
        };

        // 加入队列中定期执行
        if (!onready.end) {
            list.push(onready);
            // 无论何时只允许出现一个定时器，减少浏览器性能损耗
            if (intervalId === null) intervalId = setInterval(tick, 40);
        };
    };
})();