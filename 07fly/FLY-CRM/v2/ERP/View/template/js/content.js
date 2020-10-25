// var $parentNode = window.parent.document;

// function $childNode(name) {
//     return window.frames[name]
// }

// // tooltips
// $('.tooltip-demo').tooltip({
//     selector: "[data-toggle=tooltip]",
//     container: "body"
// });

// // 使用animation.css修改Bootstrap Modal
// $('.modal').appendTo("body");

// $("[data-toggle=popover]").popover();


//判断当前页面是否在iframe中
//if (top == this) {
//    var gohome = '<div class="gohome"><a class="animated bounceInUp" href="index.html?v=4.0" title="返回首页"><i class="fa fa-home"></i></a></div>';
//    $('body').append(gohome);
//}


//为true输出日志
var debug = true;

/**
 * 打印日志
 */
function log(data) {
    if (debug) {
        if (typeof (data) == "object") {
            console.log(JSON.stringify(data)); //console.log(JSON.stringify(data, null, 4));
        } else {
            console.log(data);
        }
    }
}

//animation.css
function animationHover(element, animation) {
    element = $(element);
    element.hover(
        function () {
            element.addClass('animated ' + animation);
        },
        function () {
            //动画完成之前移除class
            window.setTimeout(function () {
                element.removeClass('animated ' + animation);
            }, 2000);
        });
}

//拖动面板
//function WinMove() {
//    var element = "[class*=col]";
//    var handle = ".ibox-title";
//    var connect = "[class*=col]";
//    $(element).sortable({
//            handle: handle,
//            connectWith: connect,
//            tolerance: 'pointer',
//            forcePlaceholderSize: true,
//            opacity: 0.8,
//        })
//        .disableSelection();
//};
//初始化一些效果
$(function () {

    //禁用输入框提示
    $("input[type='text']").attr('autocomplete','off');

    //下拉框初始样式
    $('.chosen-select').chosen({search_contains: true});

    //实现全选反选
    $(".checkboxCtrl").on('click', function () {
        $("tbody input[class='checkboxCtrlId']:checkbox").prop("checked", $(this).prop('checked'));
    });

    //全局返回
    $(".btn-back-reply").on('click', function () {
        window.history.go(-1);
    });

    //checkbox 选中样式
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    //操作说明打开
    $("body").on("click", ".btn-help-detail", function () {
        type = $(this).attr('data-type');
        parent.layer.open({
            type: 2,
            title: '操作说明',
            closeBtn: 1,
            anim: 0,
            shadeClose: true,
            area: ['50%', '50%'],
            content: '/index.php/sysmanage/HelpDoc/help_doc/type/' + type + '/',
            end: function () {
            }
        });
        return false;
    });

	//单击操作
	$("body").on("click", ".ajax-open", function () {
		var target = $(this).attr("data-url");
		var id = $(this).attr("data-id");
		if (typeof (id) != "undefined" && id != 0) {
			var target = target + "?id=" + id;
		}
		var title = $(this).attr("data-title");
		log(target);
		layer.open({
			type: 2,
			title: title,
			shadeClose: true,
			fixed: false, //不固定
			area: ['90%', '90%'],
			content: target,
			end: function () {
				turnPage(1);//页面加载时初始化分页
			}
		});
		return false;
	});

	//单击操作
	$("body").on("click", ".ajax-get", function () {
		var target = $(this).attr("data-url");
		var id = $(this).attr("data-id");
		if (typeof (id) != "undefined" && id != 0) {
			var target = target + "?id=" + id;
		}
        var ids =$(this).attr('data-ids');
        //是否设置了参数字段
        if( typeof(ids)!="undefined" && ids!=0 ){
            var ids=($.param(eval('('+ids+')'),true));
            var target=target+"?"+ids;
        }
		$.ajax({
			type: "POST",
			url: target,
			data:{"id":id},
			dataType:"json",
			success: function(data){
				if(data.statusCode=='200'){
					layer.msg('操作成功', {icon: 1});
					turnPage(1);
				}
			},
			complete: function () {//完成响应
			}
		});
	});

    $("body").on("blur", ".ajax-sort", function() {
        var target = $(this).attr("data-url");
        var sort =$(this).val();
        var id = $(this).attr("data-id");
        if (typeof (id) != "undefined" && id != 0) {
            var target = target + "?id=" + id;
        }
        $.ajax({
            type: "POST",
            url: target,
            data:{"sort":sort,"id":id},
            dataType:"json",
            success: function(data){
                if(data.statusCode=='200'){
                    layer.msg('操作成功', {icon: 1});
                    turnPage(1);
                }
                log(data.message);
            }
        });
    });

    //启用关闭
    $("body").on("click", ".ajax-checkbox", function () {
        var target;
        var val=0;
        var chk = $(this).prop('checked');
        log(chk);
        var id=$(this).attr('data-id');

        if(chk){ val=1; }

        if ((target = $(this).attr('href')) || (target = $(this).attr('url')) || (target = $(this).attr('data-url'))) {
            $.post(target, {id: id, value: val}, function (data) {
                if(data.statusCode=='200'){
                    layer.msg('操作成功', {icon: 1});
                } else {
                    layer.msg(data.msg, {icon: 5});
                }

            }, "json");
        }
    });
    //树形目录展开，折叠
    $("body").on("click", ".treeClassBody lable", function () {
        var UL = $(this).parent().siblings("ul");
        $(this).html('');
        if (UL.css("display") == "none") {
            UL.css("display", "block");
            $(this).html('[-]');
        } else {
            UL.css("display", "none");
            $(this).html('[+]');
        }
    });
    //时间选择器
//	$('.searchDateRange .input-daterange').datepicker({
//		keyboardNavigation: false,
//		forceParse: false,
//		autoclose: true
//	});
    //表格行超出之后隐藏
    $("body").on("click", ".overflow-td", function () {
        var that = $(this);
        var cont = $(this).html();
        //小tips
        layer.tips(cont, that, {
            tips: [4, '#3595CC'],
            time: 9000
        });

    });

    //日期选择插件yyyy-mm-dd
    $(".datepicker").datepicker({
//		language: "zh-CN",
//		autoclose: true,//选中之后自动隐藏日期选择框
//		clearBtn: true,//清除按钮
//		todayBtn: true,//今日按钮
//		format: "yyyy-mm-dd",
        startView: 2,
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        clearBtn: true,//清除按钮
        autoclose: true

    });

    //日期时间选择插件 yyyy-mm-dd H:i:s
    $(".datetimepicker").datetimepicker({
        language: "zh-CN",
        autoclose: true,//选中之后自动隐藏日期选择框
        clearBtn: true,//清除按钮
        todayBtn: true,//今日按钮
        format: "yyyy-mm-dd hh:ii:ss",
    });
    //选择框效果
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

//	$(".treeClassBody li").hover(
//		function(){
//			$(this).css("background-color","#ccc");
//			$(this).parent().siblings("li").css("background-color","#fff");
//		} ,
//		function(){
//			$(this).css("background-color","#fff");
//		} 
//	) ;

});

//判断所有的checkbox的选中状态 
//@id : checkbox的id 
function checkedStatus(id) {
    var status = document.getElementById(id).checked;
    tag = "sub" + id;
    //获取checkbox，如果子节点没有设置id，就得不到一部分checkbox
    var temp = document.getElementById(tag);
    var inputs = temp.getElementsByTagName("input");
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].checked = status;

    }
    //设置checkbox的下级checkbox的状态
    // setChildCheckBox(temp);

    //设置checkbox的上级checkbox的状态
    // setParentCheckBox(temp);
}


//分页插件
var orderField = '';
var orderDirection = '';
var pageSize = '';
var pageNum = '';
var ajaxSearchFormData = '';

//数据排序操作
$('.07fly-table .sort-filed').click(function () {
    $(this).toggleClass(function () {
        orderField = $(this).attr('orderField');
        if ($(this).hasClass('asc')) {
            $(this).removeClass('asc');
            orderDirection = 'desc';
            turnPage(1);
            return 'desc';
        } else {
            $(this).removeClass('desc');
            orderDirection = 'asc';
            turnPage(1);
            return 'asc';
        }
    })
});

//查询数据，刷新
$('.ajaxSearchForm').click(function () {
    $(this).children("input").prop("checked", true);
    ajaxSearchFormData = $("form").serialize();
    turnPage(1);
});

//设置分页每页条数及跳转页数
$("body").on("blur", ".tfootPageBar", function () {
    pageNum = $(".07fly-table tfoot td input[name='pageNum']").val();
    if (pageNum === undefined) {
        pageNum = '';
    }
    ajaxSearchFormData = $("form").serialize();
    turnPage(pageNum);
});

//获取分页数据
function turnPage(pageNum) {
    //获取查询表单数据
    ajaxSearchFormData = $("form").serialize();
    pageSize = $(".07fly-table tfoot td input[name='pageSize']").val();
    console.log(JSON.stringify(pageSize));
    //alert(pageSize);
    if (pageSize === undefined) {
        pageSize = '';
    }
    //alert(ajaxSearchFormData);
    //ajax 请求数据
    ajaxPostJsonData = ajaxSearchFormData + "&pageNum=" + pageNum + "&pageSize=" + pageSize + "&orderField=" + orderField + "&orderDirection=" + orderDirection;
    $.ajax({
        type: 'POST',
        url: ajaxUrl,     //这里是请求的后台地址，自己定义
        //data: {'pageNum':page,'orderField':orderField,'orderDirection':orderDirection,'textData':textData},
        data: ajaxPostJsonData,
        dataType: 'json',
        beforeSend: function () {
            layer.msg('数据加载中',
                {
                    time: 1000,
                    icon: 16,
                    shade: 0.01
                }
            );
        },
        success: function (returnJsonData) {
            $(".07fly-table tbody").empty();//移除原来的分页数据
            totalCount = returnJsonData.totalCount;
            pageSize = returnJsonData.pageSize;
            pageNum = returnJsonData.pageNum;

            //使用
            var tpl = baidu.template;
            var html = tpl('tableListTpl', returnJsonData);
            $(".07fly-table tbody").html(html);

        },
        complete: function () {    //添加分页按钮栏
            getPageBar(pageNum, pageSize, totalCount);
        },
        error: function () {
            layer.msg('数据加载失败', {
                icon: 5,
                shade: 0.01
            });
        }
    });
}

//获取分页条（分页按钮栏的规则和样式根据自己的需要来设置）
function getPageBar(pageNum, pageSize, totalCount) {
    var pageNum = parseInt(pageNum);
    var pageSize = parseInt(pageSize);
    var totalPage = Math.ceil(totalCount / pageSize);
    if (pageNum > totalPage) {
        pageNum = totalPage;
    }
    if (pageNum < 1) {
        pageNum = 1;
    }
    var pageBar;
    pageBar = "<div class='page-list'>";
    pageBar += "<div class=\"btn-group\"> <span class='btn btn-white'> 共 " + totalCount + "条 </span>";
    pageBar += "<span class='btn btn-white'> 每页 <input type='text' name='pageSize' class='tfootPageBar pageSize' style='width:50px;height:20px;border:solid #ccc 1px;' value='" + pageSize + "'> 条 </span>";
    //如果不是第一页
    pageBar += "<span class='btn btn-white'><a href='javascript:turnPage(1);'>首页</a></span>";
    pageBar += "<span type=\"button\" class=\"btn btn-white\" onclick='javascript:turnPage(" + (pageNum - 1) + ")'><a href='javascript:turnPage(" + (pageNum - 1) + ")'><< </a> </span>";

    //显示的页码按钮(5个)
    var start = 1,
        end = 0;
    if (totalPage <= 5) {
        start = 1;
        end = totalPage;
    } else {
        if (pageNum - 2 <= 0) {
            start = 1;
            end = 5;
        } else {
            if (totalPage - pageNum < 2) {
                start = totalPage - 4;
                end = totalPage;
            } else {
                start = pageNum - 2;
                end = pageNum + 2;
            }
        }
    }

    for (var i = start; i <= end; i++) {
        if (i == pageNum) {
            pageBar += "<span class='btn btn-white active' onclick='javascript:turnPage(" + i + ")'><a href='javascript:turnPage(" + i + ")'>" + i + "</a></span>";
        } else {
            pageBar += "<span class='btn btn-white' onclick='javascript:turnPage(" + i + ")'><a href='javascript:turnPage(" + i + ")'>" + i + "</a></span>";
        }
    }

    //如果不是最后页
    /*if (pageNum != totalPage) {
        pageBar += "<span class='btn btn-white' onlick='javascript:turnPage(" + (parseInt(pageNum) + 1) + ")'>>></span>";
        pageBar += "<span class='btn btn-white' onlick='javascript:turnPage(" + totalPage + ")'>尾页</span>";
    }*/
    pageBar += "<span class='btn btn-white' onclick='javascript:turnPage(" + (parseInt(pageNum) + 1) + ")'><a href='javascript:turnPage(" + (parseInt(pageNum) + 1) + ")'>>></a></span>";
    pageBar += "<span class='btn btn-white' ><a href='javascript:turnPage(" + totalPage + ")'>尾页</a></span>";
    pageBar += "<span class='btn btn-white'> 跳 <input type='text' name='pageNum' class='tfootPageBar pageNum' style='width:50px;height:20px;border:solid #ccc 1px;'> 页 <a>GO</a></span>";
    pageBar += "</div></div>";
    $(".07fly-table tfoot td").html(pageBar);
}

//根据下拉选择客户，返回客户的联系人
function findLinkmanChosenSelect(uptClass, uptUrl, change_val) {
    $.ajax({
        type: "POST",
        url: uptUrl,
        data: {"customer_id": change_val},
        dataType: "json",
        beforeSend: function () {
            $("." + uptClass).empty();
        },
        success: function (jsondata) {
            var html = '';
            $.each(jsondata, function (idx, obj) {
                html += '<option value="' + obj.linkman_id + '" hassubinfo="true">' + obj.name + '</option>';
            });
            console.log(html);
            $("." + uptClass).append(html);
            $("." + uptClass).trigger('chosen:updated');
        }
    });

}

//根据下拉选择客户，返回客户的销售机会
function findChanceChosenSelect(uptClass, uptUrl, change_val) {
    $.ajax({
        type: "POST",
        url: uptUrl,
        data: {"customer_id": change_val},
        dataType: "json",
        beforeSend: function () {
            $("." + uptClass).empty();
        },
        success: function (jsondata) {
            var html = '';
            $.each(jsondata, function (idx, obj) {
                html += '<option value="' + obj.chance_id + '" hassubinfo="true">' + obj.name + '</option>';
            });
            console.log(html);
            $("." + uptClass).append(html);
            $("." + uptClass).trigger('chosen:updated');
        }
    });
}

//根据下拉选择客户，返回客户的销售合同
function findSalContractChosenSelect(uptClass, uptUrl, change_val) {
    $.ajax({
        type: "POST",
        url: uptUrl,
        data: {"customer_id": change_val},
        dataType: "json",
        async: false,
        beforeSend: function () {
            $("." + uptClass).empty();
        },
        success: function (jsondata) {
            var html = '';
            $.each(jsondata, function (idx, obj) {
                if (idx == 0) var chk = "selected";
                html += '<option value="' + obj.contract_id + '" hassubinfo="true" ' + chk + '>' + obj.title + '</option>';
            });
            console.log(html);
            $("." + uptClass).append(html);
            $("." + uptClass).trigger('chosen:updated');
        }
    });

}

//根据下拉选择供应商，返回客户的采购合同
function findPosContractChosenSelect(uptClass, uptUrl, change_val) {
    $.ajax({
        type: "POST",
        url: uptUrl,
        data: {"supplier_id": change_val},
        dataType: "json",
        async: false,
        beforeSend: function () {
            $("." + uptClass).empty();
        },
        success: function (jsondata) {
            var html = '';
            $.each(jsondata, function (idx, obj) {
                if (idx == 0) var chk = "selected";
                html += '<option value="' + obj.contract_id + '" hassubinfo="true" ' + chk + '>' + obj.title + '</option>';
            });
            console.log(html);
            $("." + uptClass).append(html);
            $("." + uptClass).trigger('chosen:updated');
        }
    });

}

/**
  * 将form里面的内容序列化成json
  * 相同的checkbox用分号拼接起来
  * @param {dom} 指定的选择器
  * @param {obj} 需要拼接在后面的json对象
  * @method serializeJson
  * */
$.fn.serializeJson = function (otherString) {
    var serializeObj = {},
        array = this.serializeArray();
    $(array).each(function () {
        if (serializeObj[this.name]) {
            serializeObj[this.name] += ';' + this.value;
        } else {
            serializeObj[this.name] = this.value;
        }
    });

    if (otherString != undefined) {
        var otherArray = otherString.split(';');
        $(otherArray).each(function () {
            var otherSplitArray = this.split(':');
            serializeObj[otherSplitArray[0]] = otherSplitArray[1];
        });
    }
    return serializeObj;
};

/**
 * 将josn对象赋值给form
 * @param {dom} 指定的选择器
 * @param {obj} 需要给form赋值的json对象
 * @method serializeJson
 * */
$.fn.setForm = function (jsonValue) {
    var obj = this;
    $.each(jsonValue, function (name, ival) {
        var $oinput = obj.find("input[name=" + name + "]");
        if ($oinput.attr("type") == "checkbox") {
            if (ival !== null) {
                var checkboxObj = $("[name=" + name + "]");
                var checkArray = ival.split(";");
                for (var i = 0; i < checkboxObj.length; i++) {
                    for (var j = 0; j < checkArray.length; j++) {
                        if (checkboxObj[i].value == checkArray[j]) {
                            checkboxObj[i].click();
                        }
                    }
                }
            }
        } else if ($oinput.attr("type") == "radio") {
            $oinput.each(function () {
                var radioObj = $("[name=" + name + "]");
                for (var i = 0; i < radioObj.length; i++) {
                    if (radioObj[i].value == ival) {
                        radioObj[i].click();
                    }
                }
            });
        } else if ($oinput.attr("type") == "textarea") {
            obj.find("[name=" + name + "]").html(ival);
        } else {
            obj.find("[name=" + name + "]").val(ival);
        }
    })
}

/**
 * null => ''
 * @param {*} data 要处理的数据
 */
function null2zero(data) {
    for (let x in data) {
        if (data[x] === null) { // 如果是null 把直接内容转为 ''
            data[x] = '0';
        } else {
            if (Array.isArray(data[x])) { // 是数组遍历数组 递归继续处理
                data[x] = data[x].map(z => {
                    return null2str(z);
                });
            }
            if (typeof (data[x]) === 'object') { // 是json 递归继续处理
                data[x] = null2zero(data[x])
            }
        }
    }
    return data;
}

/**
 * null => ''
 * @param {*} data 要处理的数据
 */
function null2str(data) {
    for (let x in data) {
        if (data[x] === null) { // 如果是null 把直接内容转为 ''
            data[x] = '';
        } else {
            if (Array.isArray(data[x])) { // 是数组遍历数组 递归继续处理
                data[x] = data[x].map(z => {
                    return null2str(z);
                });
            }
            if (typeof (data[x]) === 'object') { // 是json 递归继续处理
                data[x] = null2str(data[x])
            }
        }
    }
    return data;
}

var _hmt = _hmt || [];
(function () {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?6cbae336ef2e6fc07bbcab9a0872e082";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();
