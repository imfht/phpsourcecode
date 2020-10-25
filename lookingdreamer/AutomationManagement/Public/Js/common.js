$(function () {
    //防止登录框缓存
    $('#ajax_login').attr('src', $('#ajax_login').attr('src') + '?' + Math.random());
    //文章排行内容切换效果
    if ($('#articel .list ul')) {
        $('#articel .list ul:eq(' + $('#articel .tag .selected').parent().index() + ')').show();//初始化效果
        var articel;
    }
    $("#articel .tag b").mouseenter(function () {
        var _this = $(this);
        articel = setTimeout(function () {//设置延迟执行
            $('#articel .tag b').removeClass('selected');
            _this.addClass('selected')
            $('#articel .list ul').hide();
            $('#articel .list ul:eq(' + _this.parent().index() + ')').show();
        }, 200)
    })
    $("#articel .tag b").mouseleave(function () {
        clearTimeout(articel);
    })
    //选择标签
    $(".selectTag").click(function () {
        settagvalue($(this));
    })

    //弹出框获取更多标签
    $("#getmoretag").click(function () {
        var model = $(this).attr('model');
        model = model || 'bbs';
        var _this = $(".tagvalue");
        $.ThinkBox.load(U("Ajax/getMoreTag/model/" + model), {
            fixed: false,
            center: false,
            modal: false,
            title: '更多标签',
            drag: false,
            close: false,
            width: 400,
            x: _this.offset().left,
            y: _this.height() + _this.offset().top + 10,
            beforeShow: function () {
                settagselect();
                $(".ajaxmoretag .selectTag").click(function () {
                    settagvalue($(this));
                });
                $("#boxselecttag").click(function () {
                    settagselect();
                    $.ThinkBox.get(this).hide();
                });
            }
        })
    });

    //美化上传按钮
    function uploadStyle() {
        var i = 0;
        if ($.browser.mozilla) {
            $('.bbs-upload-list .item label').live('click', function () {
                $(this).next('input:file').click();
            });
        }
        ;
        $('.bbs-upload-list .add').click(function () {
            if ($('.bbs-upload-list label').length >= 5) {
                $.ThinkBox.error('最多只能上传5个附件!');
                return false;
            }
            i++;
            var str = '<div class="item cf mt">' +
                '<label class="fl" for="file' + i + '">&nbsp;</label>' +
                '<input class="file" id="file' + i + '" name="attach' + i + '" type="file" onchange="fileChange($(this));" />' +
                '<span class="button del fl">' +
                '<i class="ico-file-del"></i>' +
                '<a href="javascript:void(0)">删除</a></span>' +
                '</div>';
            $(this).parents('.bbs-upload-list').append(str);
            return false;
        });
        $('.bbs-upload-list').delegate('.del', 'click', function () {
            --i;
            $(this).parent().remove();
        });
    }

    //应用美化上传控件
    uploadStyle();
    //编辑页面初始化选中标签
    settagselect();
});
//美化上传控件的onchange事件
function fileChange(ele) {
    ele.prev().html(ele.val());
};
//设置标签选中状态
function settagvalue(_this) {
    var value = $(".tagvalue").val();
    var init_value_arr = value.split(" ");
    var flag = searchArrayBy(value.split(" "), _this.text());
    if (flag == true) {
        if (init_value_arr.length > 5) return;//限制5个标签
        // if(value!="") value=value;
        $(".tagvalue").val(value + _this.text() + " ");
        _this.addClass("selectedtag");

    } else {
        $(".tagvalue").val(delArray(value.split(" "), _this.text()));
        _this.removeClass("selectedtag");
    }
}
function settagselect() {
    $(".selectTag").removeClass("selectedtag");
    if ($(".tagvalue").val() != undefined) {
        var input = $(".tagvalue").val().split(" ");
        var list = $(".selectTag");
        $.each(list, function () {
            var _this = $(this);
            $.each(input, function (key, value) {
                if (value == _this.text()) {
                    _this.addClass("selectedtag");
                }
            })
        })
    }
};
//删除数组中的字符
function delArray(arr, word) {
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] == word) {
            arr.splice(i, 1);
        }
    }
    return arr.join(" ");
};
//数组中查找字符
function searchArrayBy(arr, word) {
    var flag = true;
    $.each(arr, function (key, value) {
        if (value == word) {
            flag = false;
        }
    })
    return flag;
};
//格行变色
function interColor(ele) {
    $(ele).each(function () {
        if ($(this).index() % 2 == 0) {
            $(this).addClass('odd');
        } else {
            $(this).addClass('even');
        }
    });
}
//内容向上滚动，传3个容器的名字容器最好是div box1最外面的容器，box2 为里面第一个，box3为里面第2个
function topscroll(box1, box2, box3) {
    var speed = 50;
    var scrolls = document.getElementById(box1);
    var ajaxList_div = document.getElementById(box2);
    var append = document.getElementById(box3);
    append.innerHTML = ajaxList_div.innerHTML;
    function Marquee() {
        if (append.offsetHeight - scrolls.scrollTop <= 0) {
            scrolls.scrollTop -= ajaxList_div.offsetHeight;
        }
        else {
            scrolls.scrollTop = scrolls.scrollTop + 1;
        }
    }

    var MyMar1 = setInterval(Marquee, speed);//设置定时器
    //鼠标移上时清除定时器达到滚动停止的目的
    scrolls.onmouseover = function () {
        clearInterval(MyMar1)
    }
    //鼠标移开时重设定时器
    scrolls.onmouseout = function () {
        MyMar1 = setInterval(Marquee, speed)
    }
}

(function ($) {
    //图片切换
    $.fn.attentSwitch = function (options) {
        var slider = $(this);
        var width = parseInt(slider.children().outerWidth());
        slider.defaults = {
            event: 'click',
            button: true,
            num: 4,
            speed: 1000,
            direct: ''
        };
        slider.init = function () {
            slider.vars = $.extend({}, slider.defaults, options);
            if (slider.vars.button != false) {
                slider.createButton();
            }
            ;
            if (slider.vars.direct != '') {
                slider.children().eq(slider.vars.direct.attr('index')).addClass('selected');
                slider.children().bind('mouseover', {
                    left: slider.vars.direct.css('margin-left')
                }, slider.direct);
            }
            if (slider.children().length > slider.vars.num) {
                slider.button.find('.next').addClass('selected');
                slider.button.children().bind(slider.vars.event, slider.scroll);
                slider.css('position', 'relative');
                slider.children().each(function () {
                    $(this).css({
                        'position': 'absolute',
                        'top': '0',
                        'left': $(this).index() * width
                    });
                });
            }
        }
        slider.direct = function (left) {
            var _this = $(this);
            var left = parseInt(left.data.left);
            _this.siblings().removeClass('selected');
            _this.addClass('selected');
            slider.vars.direct.stop(true, false).animate({
                'margin-left': left + _this.index() * width - Math.abs(parseInt(slider.css('margin-left')))
            }, 300);
        };
        slider.scroll = function () {
            var left = parseInt(slider.css('margin-left'));
            var leg = width * slider.vars.num;
            var rema = slider.children().length - parseInt(slider.children().length / slider.vars.num) * slider.vars.num;
            if ($(this).hasClass('prev')) {
                slider.button.children().filter('.next').addClass('selected');
                if (Math.abs(left) > 0) {
                    if (Math.abs(left) <= leg) {
                        $(this).removeClass('selected');
                    }
                    ;
                    if (rema != 0 && Math.abs(left) == width * rema) {
                        leg = width * rema;
                    }
                    ;
                    if (!slider.is(':animated')) {
                        slider.animate({
                            'margin-left': left + leg
                        }, slider.vars.speed);
                    }
                    ;
                    return false;
                }
                ;
            } else {
                slider.button.children().filter('.prev').addClass('selected');
                if (Math.abs(left) < (slider.children().length - slider.vars.num - rema) * width) {
                    if (Math.abs(left) + leg >= (slider.children().length - slider.vars.num - rema) * width) {
                        $(this).removeClass('selected');
                    }
                    ;
                    if (rema != 0 && (Math.abs(left) + leg) / width == slider.children().length - rema) {
                        leg = width * rema;
                    }
                    ;
                    if (!slider.is(':animated')) {
                        slider.animate({
                            'margin-left': left - leg
                        }, slider.vars.speed);
                    }
                    ;
                    return false;
                }
                ;
            }
        };
        slider.createButton = function () {
            slider.button = $(
                '<div class="button">' +
                    '<span class="prev" title="上一页">&nbsp;</span>' +
                    '<span class="next" title="下一页">&nbsp;</span>' +
                    '</div>'
            );
            slider.after(slider.button);
            return slider.button;
        };
        slider.init();
    };
    $.fn.caseSwitch = function (options) {
        var slider = $(this);
        var width = parseInt(slider.children().outerWidth());
        slider.defaults = {
            event: 'click',
            button: true,
            num: 4,
            speed: 1000,
            before: function () {
            }
        };
        slider.init = function () {
            slider.vars = $.extend({}, slider.defaults, options);
            if (slider.vars.button != false) {
                slider.createButton();
            }
            ;
            if (slider.children().length > 0) {
                slider.css('position', 'relative');
                slider.children().each(function () {
                    $(this).css({
                        'position': 'absolute',
                        'top': '0',
                        'left': $(this).index() * width
                    });
                });
            }
            if (slider.children().length > slider.vars.num) {
                slider.button.find('.next').addClass('selected');
                slider.button.children().bind(slider.vars.event, slider.scroll);
            }
        }
        slider.scroll = function () {
            var left = parseInt(slider.css('margin-left'));
            var leg = width * slider.vars.num;
            var rema = slider.children().length - parseInt(slider.children().length / slider.vars.num) * slider.vars.num;
            if ($(this).hasClass('prev')) {
                slider.button.children().filter('.next').addClass('selected');
                if (Math.abs(left) > 0) {
                    if (Math.abs(left) <= leg) {
                        $(this).removeClass('selected');
                    }
                    ;
                    if (rema != 0 && Math.abs(left) == width * (slider.vars.num - rema)) {
                        leg = width * (slider.vars.num - rema);
                    }
                    ;
                    if (!slider.is(':animated')) {
                        slider.animate({
                            'margin-left': left + leg
                        }, slider.vars.speed);
                    }
                    ;
                    return false;
                }
                ;
            } else {
                slider.button.children().filter('.prev').addClass('selected');
                if (Math.abs(left) <= (slider.children().length - slider.vars.num - rema) * width) {
                    if (Math.abs(left) >= (slider.children().length - slider.vars.num - rema) * width) {
                        $(this).removeClass('selected');
                    }
                    ;
                    if (rema != 0 && Math.abs(left) == leg) {
                        leg = width * (slider.vars.num - rema);
                    }
                    ;
                    if (!slider.is(':animated')) {
                        slider.animate({
                            'margin-left': left - leg
                        }, slider.vars.speed);
                    }
                    ;
                    return false;
                }
                ;
            }
        };
        slider.createButton = function () {
            slider.button = $(
                '<div class="button">' +
                    '<span class="prev" title="上一页">&nbsp;</span>' +
                    '<span class="next" title="下一页">&nbsp;</span>' +
                    '</div>'
            );
            slider.after(slider.button);
            return slider.button;
        };
        slider.init();
    };
    //首页新闻特效
    $.fn.newsSlider = function (options) {
        var slider = $(this);
        var setTime;
        var num = slider.children().length;
        var index;
        slider.defaults = {
            speed: 5000,
            event: 'click',
            current: 0
        };
        slider.init = function () {
            slider.vars = $.extend({}, slider.defaults, options);
            index = slider.vars.current;
            if (num > 1) {
                slider.createNav();
                slider.css({
                    'position': 'relative',
                    'width': slider.children().width() + 'px',
                    'height': slider.children().height() + 'px',
                    'overflow': 'hidden'
                });
                slider.children().css({
                    'position': 'absolute',
                    'top': '0px',
                    'left': '0px'
                });
                slider.nav.children().each(function () {
                    $(this).bind(slider.vars.event, {
                        index: $(this).index()
                    }, slider.handPlay);
                });
                if (slider.children().eq(slider.vars.current).is(':hidden')) {
                    slider.children().eq(slider.vars.current).show();
                }
                ;
                slider.autoPlay(index);
            }
            ;

        };
        slider.handPlay = function (index) {
            index = index.data.index;
            slider.showImg(index);
            clearInterval(setTime);
            setTimeout(function () {
                slider.autoPlay(index);
            });
        };
        slider.autoPlay = function (index) {
            setTime = setInterval(function () {
                index++;
                if (index == num) {
                    index = 0;
                }
                ;
                slider.showImg(index);
            }, slider.vars.speed);
        };
        slider.showImg = function (index) {
            slider.children().filter(':visible').stop(true, true).fadeOut(600, function () {
                slider.children().eq(index).fadeIn(600);
            });
            slider.nav.children().removeClass('selected').eq(index).addClass('selected');
        };
        slider.createNav = function () {
            var ele = '';
            for (var i = 0; i < num; i++) {
                ele += '<a href="javascript:void(0);">' + (i + 1) + '</a>';
            }
            ;
            slider.nav = $('<div class="nav">' + ele + '</div>');
            slider.nav.children().eq(slider.vars.current).addClass('selected');
            slider.after(slider.nav);
            return slider.nav;
        };
        slider.init();
    };
    //列表隔行变色
    $.fn.listBgColor = function (options) {
        var _default = {
            over: true
        };
        var _vars = $.extend({}, _default, options);
        if ($(this).length > 0) {
            $(this).each(function () {
                if ($(this).index() % 2) {
                    $(this).addClass('odd');
                } else {
                    $(this).addClass('even');
                }
            });
        }
        if (_vars.over) {
            $(this).hover(
                function () {
                    $(this).addClass('hover');
                },
                function () {
                    $(this).removeClass('hover');
                }
            )
        }
        ;
        return false;
    };
    //鼠标移到元素上切换样式
    $.fn.mouseOverStyle = function (name) {
        $(this).hover(
            function () {
                $(this).addClass(name);
            },
            function () {
                $(this).removeClass(name);
            }
        )
    };
//标签排序
})(jQuery);
//返回顶部效果
function scrollTop() {
    var ele = $('<div class="index-scroll"><a href="javascript:void(0);" title="返回顶部">&nbsp;</a></div>');
    var left = $('.footer-center').offset().left + $('.footer-center').width() + 12;
    ele.css({
        'position': 'fixed',
        'bottom': '42px',
        'left': left,
        'display': 'none'
    });
    ele.click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 1000);
    });
    $(window).resize(function () {
        left = $('.footer-center').offset().left + $('.footer-center').width() + 12;
        ele.css('left', left);
    });
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            ele.fadeIn(500);
        } else {
            ele.fadeOut(500);
        }
    });
    $('body').append(ele);
};
//判断是否登录
function isLogin() {
    var flag = false;
    $.ajax({
        type: "POST",
        url: U("Ajax/isLogin"),
        async: false,
        success: function (msg) {
            if (msg == '1') {
                dialogLogin();
                flag = false;
            } else {
                flag = true;
            }
        }
    })
    return flag;
}


//JS U方法的实现
//TODO:目前只是简单的分隔符替换
function U(url) {
    if (!url)return;
    var sep = '-', suffix = '.html';
    return '/' + url.replace(/\//g, sep) + suffix;
}

//编辑器菜单按钮
var KE_OPTIONS = {
    "items": [
        'source', 'fullscreen', '|', 'undo', 'redo', '|', 'preview',
        'plainpaste', 'wordpaste', '|', 'insertorderedlist', 'insertunorderedlist',
        'fontsize', '|', 'forecolor', 'bold', 'italic', 'underline', '|',
        'image', 'table', 'hr', 'emoticons', 'code', 'pagebreak',
        'link', 'unlink', '|', 'about'
    ],
    "cssPath": "/Public/Home/Css/editor.css",
    "wellFormatMode": false,
    "uploadJson": U('common/upload')

};


//下拉列表美化
(function ($) {
    $.fn.ThinkSelect = function (options) {
        var self = this;

        /* 默认配置 */
        var defaults = {
            "style": "default", //默认皮肤
            "showType": "slide", //下拉列表显示方式 [slide - 滑动, fade - 淡入淡出, none - 无效果]
            "speed": "fast", //下拉列表关闭速度 [showType 为 slide,fade 时有效]
            "keys": true, //是否开启键盘支持
            "reset": false //显示前是否刷新下拉列表 [下拉列表需要动态添加选项时开启]
        };

        /* 配置当前SELECT */
        var options = $.extend({}, defaults, options || {});

        var zIndex = 2011;

        /* 返回当前SELECT对象 */
        return this.each(function () {
            var _this = $(this);

            /* 获取原SELECT列表属性 */
            var size = {
                "width": options.width || _this.width(),
                "height": options.height || _this.height()
            };
            _this.hide(); //隐藏原SELECT

            _this.isMultiple = _this.attr('multiple') ? true : false; //是否多选

            /* 创建ThinkSelect */
            var select = $("<div/>")
                .addClass("ThinkSelect " + options.style)
                .css({
                    "position": "relative",
                    "width": size.width,
                    "height": size.height,
                    "line-height": size.height + "px",
                    "z-index": zIndex++
                })
                .click(function (event) {
                    event.stopPropagation()
                })
                .insertAfter(_this);

            /* 给ThinkSelect 添加事件 */
            select.toTop = function () {
                toTop.call(this)
            };

            /* 创建显示框 */
            var box = $("<a/>")
                .text(_this.isMultiple ? _this.attr("title") || "可多选" : _this.find("option:selected").text())
                .attr("href", "javascript:void(0);")
                .css({
                    "display": "block",
                    "overflow": "hidden",
                    'white-space': 'normal',
                    "height": size.height
                })
                .click(function () {
                    if ($.browser.webkit) this.focus(); //谷歌浏览器
                    if (list.is(":visible")) {
                        var hover = list.find(".hover");
                        if (hover.length) {
                            hover.removeClass("hover");
                            hover.click();
                        } else {
                            list._hide()
                        }
                    } else {
                        select.toTop();
                        list._show();
                    }
                })
                .appendTo(select);

            /* 创建下拉列表 */
            var list = $("<ul/>")
                .hide()
                .addClass(_this.isMultiple ? "multiple" : "")
                .css({
                    "white-space": "nowrap",
                    "position": "absolute"
                })
                .appendTo(select);

            /* 给list 添加事件 */
            list._show = function (callback) {
                show.call(this, callback)
            };
            list._hide = function (callback) {
                hide.call(this, callback)
            };
            list._set = function (type) {
                set.call(this, type)
            };
            list._list = function () {
                li.call(this, _this)
            };

            /* 将原SELECT列表 option 添加到ThinkSelect list 中 */
            list._list();

            /* 在页面中点击其他位置关闭列表 */
            $(document).click(function () {
                list._hide(function () {
                    this.find(".hover").removeClass("hover")
                })
            });

            /* 开启键盘支持 */
            if (options.keys) {
                select.keydown(function (event) {
                    if (list.is(":visible")) { //下拉列表可见
                        switch (event.keyCode) {
                            case 38://向上
                                list._set(0);
                                return false;
                                break;
                            case 40://向下
                                list._set(1);
                                return false;
                                break;
                        }
                    }
                });
            }
        });

        /* 创建下拉列表 */
        function li(select) {
            var list = this;
            var box = list.parent().find("a");
            select.find("option").each(function () {
                var li = $("<li/>")
                    .addClass(this.selected ? "selected" : "") //给选中项添加样式
                    .attr("thinkval", this.value) //设置当前li的值
                    .text(this.innerHTML) //设置当前li显示的文本
                    .mouseover(function () {
                        $(this).addClass("hover"); //给当前li添加鼠标经过样式
                    })
                    .mouseout(function () {
                        $(this).removeClass("hover"); //给当前li卸载鼠标经过样式
                    })
                    .click(function () {
                        var val = $(this).attr("thinkval"); //当前点击值
                        var option = select.find("option[value='" + val + "']"); //和当前值对应的option
                        if (select.isMultiple) { //多选
                            option.attr("selected", option[0].selected ? false : true);
                            $(this).toggleClass("selected");
                        } else { // 单选
                            var oldVal = select.val(), change = false;
                            if (oldVal !== val) { //是否更改了当前SELECT的值
                                option.attr("selected", true); //给原select设置新的值
                                list.find(".selected").removeClass("selected"); //卸载原li选中样式
                                $(this).addClass("selected"); //给新选中的li添加样式
                                box.text(this.innerHTML); //把选中的li中的文本放入显示框
                                change = true;
                            }

                            /* 关闭下拉列表 */
                            list._hide(function () {
                                box.focus(); //关闭后让显示框获取焦点
                                change && select.change(); //触发原select change事件
                            });
                        }
                    })
                    .appendTo(list);
            });

            /* 设置list的宽度 */
            list.css("width", "auto"); //把list的宽度设为自动便于获取到变化后的准确值
            list.css("width", Math.max(list.parent().width(), list.width()));
        }

        /* 显示下拉列表 */
        function show(callback) {
            /* 关闭其他显示的列表 */
            $(".ThinkSelect").find("ul:visible").hide();

            /* 判断列表显示位置 */
            var select = this.parent(), height = this.height();
            var viewsize = select[0].getBoundingClientRect();
            var bottom = $(window).height() - viewsize.bottom;
            if (height > bottom && height < viewsize.top) {
                this.css({
                    "top": "",
                    "bottom": select.height()
                });
            } else {
                this.css({
                    "top": select.height(),
                    "bottom": ""
                });
            }

            var list = this;
            var _callback = function () {
                if ($.isFunction(callback))
                    callback.call(list)
            };

            /* 重新获取下拉列表，用于动态的select */
            if (options.reset) {
                this.find("li").remove();
                this._list();
            }

            /* 根据指定的方式显示下拉列表 */
            if (options.showType == "slide") {
                this.slideDown(options.speed, _callback);
            } else if (options.showType == "fade") {
                this.fadeIn(options.speed, _callback);
            } else {
                this.show();
                _callback();
            }
        }

        /* 隐藏下拉框 */
        function hide(callback) {
            var list = this;
            var _callback = function () {
                if ($.isFunction(callback))
                    callback.call(list)
            };
            if (options.showType == "slide") {
                this.slideUp(options.speed, _callback);
            } else if (options.showType == "fade") {
                this.fadeOut(options.speed, _callback);
            } else {
                this.hide();
                _callback();
            }
        }

        /* 设置当前选中的选项 */
        function set(type) {
            var thisLi = this.find(".hover"), allLi = this.find("li");
            var index = thisLi.index();
            if (index == -1) {
                type ? allLi.first().addClass("hover") : allLi.last().addClass("hover");
            } else {
                thisLi.removeClass("hover");
                type ? thisLi.next().addClass("hover") : thisLi.prev().addClass("hover");
            }
        }

        /* 调整层叠位置 */
        function toTop() {
            this.css("z-index", zIndex++);
        }
    }
})(jQuery)

//审核
$(function () {
    $('#ThinkAudit').click(function () {
        $.post(
            '/Ajax/audit',
            {
                'model': $(this).attr('model'),
                'id': $(this).attr('cid')
            },
            function (data) {
                if (data.status) $.ThinkBox.success(data.info);
                else $.ThinkBox.error(data.info);
            },
            'json'
        );

    });
})