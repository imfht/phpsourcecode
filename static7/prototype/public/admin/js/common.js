// Custom scripts
var $subnav = $("#side-menu"), url;
$(function () {
    /* 左边菜单高亮 */
    var regular = "/(\/(id)\/\d+)|(&id=\d+)|(\/(pid)\/\d+)|(&pid=\d+)|(\/(group)\/\d+)|(&group=\d+)/";
    highlight_subnav(window.location.pathname.replace(regular, ""));
    // MetsiMenu
    $subnav.metisMenu();
    // Collapse ibox function
    $('.collapse-link').click(function () {
        var ibox = $(this).closest('div.ibox');
        var button = $(this).find('i');
        var content = ibox.find('div.ibox-content');
        content.slideToggle(200);
        button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        ibox.toggleClass('').toggleClass('border-bottom');
        setTimeout(function () {
            ibox.resize();
            ibox.find('[id^=map-]').resize();
        }, 50);
    });

    // Close ibox function
    $('.close-link').click(function () {
        var content = $(this).closest('div.ibox');
        content.remove();
    });

    // Small todo handler
    $('.check-link').click(function () {
        var button = $(this).find('i');
        var label = $(this).next('span');
        button.toggleClass('fa-check-square').toggleClass('fa-square-o');
        label.toggleClass('todo-completed');
        return false;
    });

    // minimalize menu
    $('.navbar-minimalize').click(function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    });

    // tooltips
    $('.tooltip-demo').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });

    // Move modal to body Fix Bootstrap backdrop issu with animation.css
    $('.modal').appendTo("body");
    fix_height();
    $(window).bind("load resize click scroll", function () {
        if (!$("body").hasClass('body-small')) {
            fix_height();
        }
    });
    $("[data-toggle=popover]").popover();
});

// For demo purpose - animation css script
function animationHover(element, animation) {
    element = $(element);
    element.hover(function () {
        element.addClass('animated ' + animation);
    }, function () {
        window.setTimeout(function () {//wait for animation to finish before removing classes
            element.removeClass('animated ' + animation);
        }, 2000);
    });
}

// Minimalize menu when screen is less than 768px
$(function () {
    $(window).bind("load resize", function () {
        if ($(this).width() < 769) {
            $('body').addClass('body-small');
        } else {
            $('body').removeClass('body-small');
        }
    });
});

function SmoothlyMenu() {
    if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
        $subnav.hide();// Hide menu in order to smoothly turn on when maximize menu
        setTimeout(function () {// For smoothly turn on menu
            $subnav.fadeIn(500);
        }, 100);
    } else if ($('body').hasClass('fixed-sidebar')) {
        $subnav.hide();
        setTimeout(function () {
            $subnav.fadeIn(500);
        }, 300);
    } else {
        $subnav.removeAttr('style');// Remove all inline style from jquery fadeIn function to reset menu state
    }
}

// Dragable panels
function WinMove() {
    var element = "[class*=col]";
    var handle = ".ibox-title";
    var connect = "[class*=col]";
    $(element).sortable({
        handle: handle,
        connectWith: connect,
        tolerance: 'pointer',
        forcePlaceholderSize: true,
        opacity: 0.8
    }).disableSelection();
}
;
// Full height of sidebar
function fix_height() {
    var heightWithoutNavbar = $("body > #wrapper").height() - 61;
    $(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");
}
//自动高度
$(window).resize(function () {
    $("#page-wrapper").css("min-height", $(window).height());
}).resize();

/*layer 弹出层*/
layer.config({
    skin: 'layer-ext-espresso',
    extend: ['skin/espresso/style.css']
});
//layer通用提示框
function alert_msg(text, icon) {
    text = text || '提交成功，系统未返回信息';
    icon = icon || 0;
    layer.msg(text, {
        icon: icon,
        offset: 60,
        shift: 0
    });
}

//ajax get请求
$('.ajax-get').on('click', function () {
    var target;
    if ($(this).hasClass('confirm')) {
        if (!confirm('确认要执行该操作吗?')) {
            return false;
        }
    }
    if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
        $.get(target).success(function (data) {
            if (data.code === 1) {
                data.url ? alert_msg(data.msg + ' 页面即将自动跳转~', 1) : alert_msg(data.msg, 1);
                setTimeout(function () {
                    data.url ? location.href = data.url : '';
                }, 1500);
            } else {
                alert_msg(data.msg, 0);
                setTimeout(function () {
                }, 1500);
            }
        });
    }
    return false;
});
//ajax post submit请求
$('.ajax-post').on('click', function () {
    var target, query, form;
    var target_form = $(this).attr('target-form');
    var that = this;
    var nead_confirm = false;
    if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
        form = $('.' + target_form);
        if ($(this).attr('hide-data') === 'true') { //无数据时也可以使用的功能
            form = $('.hide-data');
            query = form.serialize();
        } else if (form.get(0) == undefined) {
            query = null;
        } else if (form.get(0).nodeName == 'FORM') {
            if ($(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            if ($(this).attr('url') !== undefined) {
                target = $(this).attr('url');
            } else {
                target = form.get(0).action;
            }
            query = form.serialize();
        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
            form.each(function (k, v) {
                if (v.type == 'checkbox' && v.checked == true) {
                    nead_confirm = true;
                }
            });
            if (nead_confirm && $(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            query = form.serialize();
        } else {
            if ($(this).hasClass('confirm')) {
                if (!confirm('确认要执行该操作吗?')) {
                    return false;
                }
            }
            query = form.find('input,select,textarea').serialize();
        }
        $(that).prop('disabled', true);
        $.post(target, query).success(function (data) {
            status_load(data, that);
        });
    }
    return false;
});

function status_load(data, that) {
    if (data.code == 1) {
        data.url ? alert_msg(data.msg + ',页面即将自动跳转~', 1) : alert_msg(data.msg, 1);
        setTimeout(function () {
            $(that).prop('disabled', false);
            data.url ? location.href = data.url : '';
        }, 1500);
    } else {
        alert_msg(data.msg, 0);
        setTimeout(function () {
            $(that).prop('disabled', false);
        }, 1500);
    }
}
//全选的实现
$('.check-all').on('ifChecked', function (event) {
    $('input[name="ids[]"]').iCheck('check');
});
$('.check-all').on('ifUnchecked', function (event) {
    $('input[name="ids[]"]').iCheck('uncheck');
});
//导航高亮
function highlight_subnav(url) {
    $subnav.find('a[href="' + url + '"]').closest('li').addClass("active").closest('ul').addClass("in").attr("aria-expanded", "true");
}
// 通用返回
$('.retreat').on('click', function () {
    history.back(-1);
    return false;
});
function validateAjax(form, button) {
    var form = $(form);
    var button = $(button);
    button.prop('disabled', true);
    $.post(form.attr("action"), form.serialize(), function (data) {
        status_load(data, button);
    }, "json");
}
//全选的实现
$(".check-all").click(function () {
    $(".ids").prop("checked", this.checked);
});
$(".ids").click(function () {
    var option = $(".ids");
    option.each(function (i) {
        if (!this.checked) {
            $(".check-all").prop("checked", false);
            return false;
        } else {
            $(".check-all").prop("checked", true);
        }
    });
});
