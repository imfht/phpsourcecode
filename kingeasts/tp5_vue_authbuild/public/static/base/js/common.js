// 布局脚本
/*====================================
 *基于JQuery 1.9.0主框架
 *DTcms管理界面
 *作者：一些事情
====================================*/
//绑定需要浮动的表头
$(function(){
    $(".rule-single-checkbox").ruleSingleCheckbox();
    $(".rule-multi-checkbox").ruleMultiCheckbox();
    $(".rule-multi-radio").ruleMultiRadio();
    $(".rule-single-select").ruleSingleSelect();
    $(".rule-multi-porp").ruleMultiPorp();
    $(".rule-date-input").ruleDateInput();

    $.fn.serializeJson=function(){
        var serializeObj={};
        $(this.serializeArray()).each(function(){
            serializeObj[this.name]=this.value;
        });
        return serializeObj;
    };
});



//===========================工具类函数============================
//只允许输入数字
function checkNumber(e) {
    var keynum = window.event ? e.keyCode : e.which;
    if ((48 <= keynum && keynum <= 57) || keynum == 8) {
        return true;
    } else {
        return false;
    }
}

//只允许输入小数
function checkForFloat(obj, e) {
    var isOK = false;
    var key = window.event ? e.keyCode : e.which;
    if ((key > 95 && key < 106) || //小键盘上的0到9
        (key > 47 && key < 60) ||  //大键盘上的0到9
        (key == 110 && obj.value.indexOf(".") < 0) || //小键盘上的.而且以前没有输入.
        (key == 190 && obj.value.indexOf(".") < 0) || //大键盘上的.而且以前没有输入.
        key == 8 || key == 9 || key == 46 || key == 37 || key == 39) {
        isOK = true;
    } else {
        if (window.event) { //IE
            e.returnValue = false;   //event.returnValue=false 效果相同.
        } else { //Firefox
            e.preventDefault();
        }
    }
    return isOK;
}

//检查短信字数
function checktxt(obj, txtId) {
    var txtCount = $(obj).val().length;
    if (txtCount < 1) {
        return false;
    }
    var smsLength = Math.ceil(txtCount / 62);
    $("#" + txtId).html("您已输入<b>" + txtCount + "</b>个字符，将以<b>" + smsLength + "</b>条短信扣取费用。");
}

//四舍五入函数
function ForDight(Dight, How) {
    Dight = Math.round(Dight * Math.pow(10, How)) / Math.pow(10, How);
    return Dight;
}

//复选框
$.fn.ruleSingleCheckbox = function () {
    var singleCheckbox = function (parentObj) {
        //查找复选框
        var checkObj = parentObj.children('input:checkbox').eq(0);
        parentObj.children().hide();
        //添加元素及样式
        var newObj = $('<a href="javascript:;">'
            + '<i class="off">否</i>'
            + '<i class="on">是</i>'
            + '</a>').prependTo(parentObj);
        parentObj.addClass("single-checkbox");
        //判断是否选中
        if (checkObj.prop("checked") == true) {
            newObj.addClass("selected");
        }
        //检查控件是否启用
        if (checkObj.prop("disabled") == true) {
            newObj.css("cursor", "default");
            return;
        }
        //绑定事件
        newObj.click(function () {
            if ($(this).hasClass("selected")) {
                $(this).removeClass("selected");
            } else {
                $(this).addClass("selected");
            }
            checkObj.trigger("click"); //触发对应的checkbox的click事件
        });
        //绑定反监听事件
        checkObj.on('click', function () {
            if ($(this).prop("checked") == true && !newObj.hasClass("selected")) {
                alert();
                newObj.addClass("selected");
            } else if ($(this).prop("checked") == false && newObj.hasClass("selected")) {
                newObj.removeClass("selected");
            }
        });
    };
    return $(this).each(function () {
        singleCheckbox($(this));
    });
};

//多项复选框
$.fn.ruleMultiCheckbox = function() {
    var multiCheckbox = function(parentObj){
        parentObj.addClass("multi-checkbox"); //添加样式
        parentObj.children().hide(); //隐藏内容
        var divObj = $('<div class="boxwrap"></div>').prependTo(parentObj); //前插入一个DIV
        parentObj.find(":checkbox").each(function(){
            var indexNum = parentObj.find(":checkbox").index(this); //当前索引
            var newObj = $('<a href="javascript:;">' + parentObj.find('label').eq(indexNum).text() + '</a>').appendTo(divObj); //查找对应Label创建选项
            if($(this).prop("checked") == true){
                newObj.addClass("selected"); //默认选中
            }
            //检查控件是否启用
            if($(this).prop("disabled") == true){
                newObj.css("cursor","default");
                return;
            }
            //绑定事件
            $(newObj).click(function(){
                if($(this).hasClass("selected")){
                    $(this).removeClass("selected");
                    //parentObj.find(':checkbox').eq(indexNum).prop("checked",false);
                }else{
                    $(this).addClass("selected");
                    //parentObj.find(':checkbox').eq(indexNum).prop("checked",true);
                }
                parentObj.find(':checkbox').eq(indexNum).trigger("click"); //触发对应的checkbox的click事件
                //alert(parentObj.find(':checkbox').eq(indexNum).prop("checked"));
            });
        });
    };
    return $(this).each(function() {
        multiCheckbox($(this));
    });
}

//多项选项PROP
$.fn.ruleMultiPorp = function() {
    var multiPorp = function(parentObj){
        parentObj.addClass("multi-porp"); //添加样式
        parentObj.children().hide(); //隐藏内容
        var divObj = $('<ul></ul>').prependTo(parentObj); //前插入一个DIV
        parentObj.find(":checkbox").each(function(){
            var indexNum = parentObj.find(":checkbox").index(this); //当前索引
            var liObj = $('<li></li>').appendTo(divObj)
            var newObj = $('<a href="javascript:;">' + parentObj.find('label').eq(indexNum).text() + '</a><i></i>').appendTo(liObj); //查找对应Label创建选项
            if($(this).prop("checked") == true){
                liObj.addClass("selected"); //默认选中
            }
            //检查控件是否启用
            if($(this).prop("disabled") == true){
                newObj.css("cursor","default");
                return;
            }
            //绑定事件
            $(newObj).click(function(){
                if($(this).parent().hasClass("selected")){
                    $(this).parent().removeClass("selected");
                }else{
                    $(this).parent().addClass("selected");
                }
                parentObj.find(':checkbox').eq(indexNum).trigger("click"); //触发对应的checkbox的click事件
                //alert(parentObj.find(':checkbox').eq(indexNum).prop("checked"));
            });
        });
    };
    return $(this).each(function() {
        multiPorp($(this));
    });
}

//多项单选
$.fn.ruleMultiRadio = function() {
    var multiRadio = function(parentObj){
        parentObj.addClass("multi-radio"); //添加样式
        parentObj.children().hide(); //隐藏内容
        var divObj = $('<div class="boxwrap"></div>').prependTo(parentObj); //前插入一个DIV
        parentObj.find('input[type="radio"]').each(function(){
            var indexNum = parentObj.find('input[type="radio"]').index(this); //当前索引
            var newObj = $('<a href="javascript:;">' + parentObj.find('label').eq(indexNum).text() + '</a>').appendTo(divObj); //查找对应Label创建选项
            if($(this).prop("checked") == true){
                newObj.addClass("selected"); //默认选中
            }
            //检查控件是否启用
            if($(this).prop("disabled") == true){
                newObj.css("cursor","default");
                return;
            }
            //绑定事件
            $(newObj).click(function(){
                $(this).siblings().removeClass("selected");
                $(this).addClass("selected");
                parentObj.find('input[type="radio"]').prop("checked",false);
                parentObj.find('input[type="radio"]').eq(indexNum).prop("checked",true);
                parentObj.find('input[type="radio"]').eq(indexNum).trigger("click"); //触发对应的radio的click事件
                //alert(parentObj.find('input[type="radio"]').eq(indexNum).prop("checked"));
            });
        });
    };
    return $(this).each(function() {
        multiRadio($(this));
    });
}

//单选下拉框
$.fn.ruleSingleSelect = function () {
    var singleSelect = function (parentObj) {
        parentObj.addClass("single-select"); //添加样式
        parentObj.children().hide(); //隐藏内容
        var divObj = $('<div class="boxwrap"></div>').prependTo(parentObj); //前插入一个DIV
        //创建元素
        var titObj = $('<a class="select-tit" style="height:27px;line-height:24px;" href="javascript:;"><span></span><i></i></a>').appendTo(divObj);
        var itemObj = $('<div class="select-items"><ul></ul></div>').appendTo(divObj);
        var arrowObj = $('<i class="arrow"></i>').appendTo(divObj);
        var selectObj = parentObj.find("select").eq(0); //取得select对象
        //遍历option选项
        selectObj.find("option").each(function (i) {
            var indexNum = selectObj.find("option").index(this); //当前索引
            var liObj = $('<li>' + $(this).text() + '</li>').appendTo(itemObj.find("ul")); //创建LI
            if ($(this).prop("selected") == true) {
                liObj.addClass("selected");
                titObj.find("span").text($(this).text());
            }
            //检查控件是否启用
            if ($(this).prop("disabled") == true) {
                liObj.css("cursor", "default");
                return;
            }
            //绑定事件
            liObj.click(function () {
                $(this).siblings().removeClass("selected");
                $(this).addClass("selected"); //添加选中样式
                selectObj.find("option").prop("selected", false);
                selectObj.find("option").eq(indexNum).prop("selected", true); //赋值给对应的option
                titObj.find("span").text($(this).text()); //赋值选中值
                arrowObj.hide();
                itemObj.hide(); //隐藏下拉框
                selectObj.trigger("change"); //触发select的onchange事件
                //alert(selectObj.find("option:selected").text());
            });
        });
        //设置样式
        //titObj.css({ "width": titObj.innerWidth(), "overflow": "hidden" });
        //itemObj.children("ul").css({ "max-height": $(document).height() - titObj.offset().top - 62 });

        //检查控件是否启用
        if (selectObj.prop("disabled") == true) {
            titObj.css("cursor", "default");
            return;
        }
        //绑定单击事件
        titObj.click(function (e) {
            e.stopPropagation();
            if (itemObj.is(":hidden")) {
                //隐藏其它的下位框菜单
                $(".single-select .select-items").hide();
                $(".single-select .arrow").hide();
                //位于其它无素的上面
                arrowObj.css("z-index", "999");
                itemObj.css("z-index", "999");
                //显示下拉框
                arrowObj.show();
                itemObj.show();
            } else {
                //位于其它无素的上面
                arrowObj.css("z-index", "");
                itemObj.css("z-index", "");
                //隐藏下拉框
                arrowObj.hide();
                itemObj.hide();
            }
        });
        //绑定页面点击事件
        $(document).click(function (e) {
            selectObj.trigger("blur"); //触发select的onblure事件
            arrowObj.hide();
            itemObj.hide(); //隐藏下拉框
        });
    };
    return $(this).each(function () {
        singleSelect($(this));
    });
}

//日期控件
$.fn.ruleDateInput = function() {
    var dateInput = function(parentObj){
        parentObj.wrap('<div class="date-input"></div>');
        parentObj.before('<i></i>');
    };
    return $(this).each(function() {
        dateInput($(this));
    });
}

$(function(){
    $(".member_group").on("click", function() {
        var a = $(this).attr("data-url");
        parent.layer.open({
            type: 2,
            title: !1,
            area: ['560px','660px'],
            closeBtn: 0,
            content: a,
            success: function(b, d) {
                var c = parent.layer.getChildFrame("body", d);
                c.find(".btnOK").on("click",
                    function() {
                        parent.layer.close(d);
                        var b = c.find("#GroupForm").serializeArray(),
                            e = c.attr("data-uid");
                        $.post(a, {
                                fromData: b,
                                uid: e
                            },
                            function(a) {
                                parent.layer.msg(a.info, {
                                    time: 2E3
                                });
                                a.url && "" != a.url && setTimeout(function() {
                                        location.href = a.url
                                    },
                                    2E3);
                                "" == a.url && setTimeout(function() {
                                        location.href = a.url
                                    },
                                    1E3)
                            })
                    });
                c.find(".btnCancel,.close").on("click",
                    function() {
                        parent.layer.close(d)
                    })
            }
        })
    });


    /* 监听提交 [ 旧 ] */
    $('.ajaxSubmit').on('click', function(event) {
        $("#btnSubmit").attr("disabled",true);
        a = "undefined" == typeof $(this).attr("data-url") ? $("#formData") : $(this);
        var b = "undefined" == typeof $(this).attr("data-url") ? a.attr("action") : $(this).attr("data-url"),
            d = "undefined" == typeof $(this).attr("binding-data") ? "POST": "GET",
            c = "undefined" == typeof $(this).attr("data-url") ? "": $(this).attr("binding-data");
        bindSubmit(a, b, d, c)
    })
    /* 监听提交 [ 新 ] */
    $('[ke-form]').on('submit', function (eve) {
        eve.preventDefault()
        var submit = $(this).find('[type="submit"]')
        submit.attr('disabled', true)
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: $(this).serialize(),
            complete: function () {
                submit.attr('disabled', false)
            },

            success: function (result) {
                if( result.code == 1  ){
                    parent.layer.open({
                        type: 1,
                        title: !1,
                        closeBtn: 0,
                        scrollbar: !1,
                        shade: 0,
                        time: 2E3,
                        offset: "50px",
                        shift: 5,
                        content: '<div class="HTooltip bounceInDown animated" style="width:350px;padding:7px;text-align:center;position:fixed;right:7px;background-color:#5cb85c;color:#fff;z-index:100001;box-shadow:1px 1px 5px #333;-webkit-box-shadow:1px 1px 5px #333;font-size:14px;">' + result.msg + "</div>"
                    })
                    setTimeout(function(){
                        $("#btnSubmit").attr("disabled",false);
                    },2E3);
                    result.url && "" != result.url && setTimeout(function() {
                            location.href = result.url;
                        },
                        2E3)
                }else{
                    parent.layer.open({
                        type: 1,
                        title: !1,
                        closeBtn: 0,
                        scrollbar: !1,
                        shade: 0,
                        time: 2E3,
                        offset: ["50px", "100%"],
                        shift: 6,
                        content: '<div class="HTooltip bounceInDown animated" style="width:350px;padding:7px;text-align:center;position:fixed;right:7px;background-color:#D84C31;color:#fff;z-index:100001;box-shadow:1px 1px 5px #333;-webkit-box-shadow:1px 1px 5px #333;font-size:14px;">' + result.msg + "</div>"
                    });
                    //添加定时器，主要为了不让顾客重复点击
                    setTimeout(function(){
                        $("#btnSubmit").attr("disabled",false);
                    },2E3);
                }
            }
        })
    })

})

function bindSubmit(a, b, d, c) {
    $(a).ajaxSubmit({
        url: b,
        type: d,
        data: {
            data: c
        },
        success: function(a, b) {
            if( a.code == 1  ){
                parent.layer.open({
                    type: 1,
                    title: !1,
                    closeBtn: 0,
                    scrollbar: !1,
                    shade: 0,
                    time: 2E3,
                    offset: "50px",
                    shift: 5,
                    content: '<div class="HTooltip bounceInDown animated" style="width:350px;padding:7px;text-align:center;position:fixed;right:7px;background-color:#5cb85c;color:#fff;z-index:100001;box-shadow:1px 1px 5px #333;-webkit-box-shadow:1px 1px 5px #333;font-size:14px;">' + a.msg + "</div>"
                })
                setTimeout(function(){
                    $("#btnSubmit").attr("disabled",false);
                },2E3);
                a.url && "" != a.url && setTimeout(function() {
                        location.href = a.url;
                    },
                    2E3)
            }else{
                parent.layer.open({
                    type: 1,
                    title: !1,
                    closeBtn: 0,
                    scrollbar: !1,
                    shade: 0,
                    time: 2E3,
                    offset: ["50px", "100%"],
                    shift: 6,
                    content: '<div class="HTooltip bounceInDown animated" style="width:350px;padding:7px;text-align:center;position:fixed;right:7px;background-color:#D84C31;color:#fff;z-index:100001;box-shadow:1px 1px 5px #333;-webkit-box-shadow:1px 1px 5px #333;font-size:14px;">' + a.msg + "</div>"
                });
                //添加定时器，主要为了不让顾客重复点击
                setTimeout(function(){
                    $("#btnSubmit").attr("disabled",false);
                },2E3);
            }
        }
    })
}