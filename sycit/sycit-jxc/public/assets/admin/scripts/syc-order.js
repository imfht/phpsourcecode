/**
 * 三叶草IT QQ-316262448
 * www.sycit.cn, hyzwd@outlook.com
 * Created by Peter on 2017/8/29.
 */
$(function() {
    "use strict"; // 严格模式

    //计算面积以求单价
    $("#orderList").on('click keyup paste', 'input', function(e){
        var $id = $(e.target).attr('id');
        //var reg = /^[A-Za-z]+$/; //reg.test(value);
        var _i = $id.split("_")[1].replace(/[^0-9]/ig, ""); //取得数字
        var _n = $id.split("_")[0]; //取得名称
        if (_n!=='Chanph'&&_n!=='Breadth'&&_n!=='Heiget'&&_n!=='Thick'&&_n!=='Diaojiao'&&_n!=='Remark') {
            return false;
        }
        //限制键盘输入
        //$(this).keydowns();
        //宽、高输入框变动
        if (_n=='Breadth' || _n=='Heiget') {
            var $m=window.AreaMinNum;
            var Heiget  =$('#Heiget_'+_i);
            var Breadth =$('#Breadth_'+_i);
            Heiget.change(function () {
                var n1= Heiget.val();
                var n2= Breadth.val();
                var sum=Area(n1,n2,$m);
                $('#Mianji_'+_i).val(sum);
                Areaprice(_i);
            });
            Breadth.change(function () {
                var n1= Heiget.val();
                var n2= Breadth.val();
                var sum=Area(n1,n2,$m);
                $('#Mianji_'+_i).val(sum);
                Areaprice(_i);
            });
        }

        //厚度输入框变动
        if (_n == 'Thick') {
            $('#Thick_'+_i).change(function() {
                Areaprice(_i);
            })
        }
    });

    //下拉选项事件
    $("#orderList").on('change', 'select', function(e) {
        var $id = $(e.target).attr('id');
        var _i = $id.split("_")[1].replace(/[^0-9]/ig, ""); //取得数字
        var _n = $id.split("_")[0]; //取得名称
        //取得选择值
        var option = $(this).find("option:selected").data('price');
        //console.log(option);
        if (_n=='Yanse' || _n == 'Suoxiang') {
            return false;
        }
        if (option == undefined) {
            $('#'+_n+'_price_'+_i).text("0");
            Areaprice(_i);//更新单价
            return false;
        }
        //产品选项
        if (_n=='Products') {
            $('#Products_price_'+_i).text(option);
            if ($(this).val() == '') {
                $("#Baobian_"+_i).empty().append('<option></option>')
            } else {
                selectBaobian(_i,$(this).val());
            }
        }
        //包边选项
        if (_n=='Baobian') {
            $('#Baobian_price_'+_i).text(option);
            var qhjc = $(this).find("option:selected").data('qhjc') || 0;
            var qhdz = $(this).find("option:selected").data('qhdz') || 0;
            var qhdzamo = $(this).find("option:selected").data('qhdzamo') || 0;
            $("#Thick_qhjc_"+_i).text(qhjc);
            $("#Thick_qhdz_"+_i).text(qhdz);
            $("#Thick_qhdzamo_"+_i).text(qhdzamo);
        }
        //锁具选项
        if (_n=='Fittings') {
            $('#Fittings_price_'+_i).text(option);
        }
        Areaprice(_i);//更新单价
        //console.log(option);
    });
});

/*
* 表格模板
* */
function AddHtml(i,Yanse,Products,Baobian,Fittings,Bancai) {
    var html = '<tr id="row_'+i+'">\n'+
        '<td id="Inputid_'+i+'">'+i+'<input type="hidden" name="Inputid['+i+']" value="'+i+'"></td>\n'+
        '<td><select name="Yanse['+i+']" id="Yanse_'+i+'" class="form-control">'+Yanse+'</select></td>\n'+
        '<td class="w50"><span style="display:none;" id="Products_price_'+i+'">0</span><select name="Products['+i+']" id="Products_'+i+'" class="form-control">'+Products+'</select></td>\n'+
        '<td class="w50"><input type="text" class="form-control" name="Chanph['+i+']" id="Chanph_'+i+'"></td>\n'+
        '<td><input type="text" class="form-control" name="Breadth['+i+']" id="Breadth_'+i+'"></td>\n'+
        '<td><input type="text" class="form-control" name="Heiget['+i+']" id="Heiget_'+i+'">' +
        '<input type="hidden" name="Mianji['+i+']" id="Mianji_'+i+'"></td>\n'+
        '<td><span style="display:none;" id="Thick_qhjc_'+i+'">0</span><span style="display:none;" id="Thick_qhdz_'+i+'">0</span><span style="display:none;" id="Thick_qhdzamo_'+i+'">0</span> ' +
        '<input type="text" class="form-control" name="Thick['+i+']" id="Thick_'+i+'"></td>\n'+
        '<td><input type="text" class="form-control" name="Diaojiao['+i+']" id="Diaojiao_'+i+'"></td>\n'+
        '<td width="60"><select name="Attribute['+i+']" id="Attribute_'+i+'" class="form-control">'+Baobian+'</select></td>\n'+
        '<td width="80"><span style="display:none;" id="Baobian_price_'+i+'">0</span><select name="Baobian['+i+']" id="Baobian_'+i+'" class="form-control"><option value="-">-</option></select></td>\n'+
        '<td><select name="Suoxiang['+i+']" id="Suoxiang_'+i+'" class="form-control">' +
        '    <option value="-">-</option>\n' +
        '    <option value="左锁内开">左锁内开</option>\n' +
        '    <option value="左锁外开">左锁外开</option>\n' +
        '    <option value="右锁内开">右锁内开</option>\n' +
        '    <option value="右锁外开">右锁外开</option></select></td>\n'+
        '<td><span style="display:none;" id="Fittings_price_'+i+'">0</span><select name="Fittings['+i+']" id="Fittings_'+i+'" class="form-control">'+Fittings+'</select></td>\n'+
        '<!--数量--><td class="add_chose input-group">' +
        '<div class="input-group">\n' +
        '    <div class="input-group-addon reduce" onclick=setAmount.reduce("#Quantity_'+i+'")>-</div>\n' +
        '    <input type="text" class="text Quantity" name="Quantity['+i+']" value="0" id="Quantity_'+i+'" readonly>\n' +
        '    <div class="input-group-addon add" onclick=setAmount.add("#Quantity_'+i+'")>+</div>\n' +
        '</div> \n' +
        '</td>\n'+
        '<td><input type="text" class="form-control sum UnitPrice" name="UnitPrice['+i+']" id="UnitPrice_'+i+'" value="0"></td>\n'+
        '<td><input type="text" class="form-control sum Amount" name="Amount['+i+']" id="Amount_'+i+'" value="0" readonly></td>\n'+
        '<td><input type="text" class="form-control" name="Remark['+i+']" id="Remark_'+i+'"></td>\n'+
        '</tr>';
    return html;
}

//添加行
function addRow() {
    var _Y=$("#AddSelectYanSe").html();
    var _P=$("#AddSelectProducts").html();
    var _B=$("#AddSelectBaobian").html();
    var _F=$("#AddSelectFittings").html();
    var x =setLines.add('#orderList');
    if ($("#AddSelectYanSe").children().size() !== 0) {
        if (x) {
            $('#orderList').append(AddHtml(x,_Y,_P,_B,_F));
        }
    }
}
//删除行
function delRow() {
    var x =setLines.del('#orderList');
    if (x) {
        $('#orderList').find('#row_'+x).remove();
        calcProdSubTotal(true); //计算总价和折扣
        calcTotalPallets() //计算数量
    }
};

//计算面积
function Area(arg1,arg2,p){
    var a1,a2,m,s;
    try{
        a1 = arg1.toString().split(".")[1].length
    }catch(e){
        a1 = 0;
    }
    try{
        a2 = arg2.toString().split(".")[1].length
    }catch(e){
        a2 = 0;
    }
    m = Math.pow(10, Math.max(a1,a2));
    s = ((arg1 * m) * (arg2 * m)) / 1000000;
    //console.log(p);
    //console.log(s);
    if (s <= p) {
        return p;
    } else {
        return s;
    }
}

//计算单价
function Areaprice(i) {
    if ($('#Products_price_'+i).text() == '0') {
        qingkongtonghangdata(i);
        calcTotalPallets();
        calcProdSubTotal(true);
        return false;
    }
    $('#UnitPrice_'+i).calc(
        '(p*m)+(td+f+b)',
        {
            p: $('#Products_price_'+i), //产品单价
            m: $('#Mianji_'+i), //面积
            td: HouduPrice(i),//厚度单价
            f: $('#Fittings_price_'+i), //锁具单价
            b: $('#Baobian_price_'+i) //包边单价
        },
        function (s) {
            return "￥" + s.toFixed(2); //默认
        },
        function ($this) {
            var sum = $this.sum();
            $(this).val("￥" + sum.toFixed(2)); //当前行改变价格
        }
    );
};

// 计算厚度单价
function HouduPrice(i) {
    var sum='0';
    var qhjc = $("#Thick_qhjc_"+i).text() || 0;
    var qhdz = $("#Thick_qhdz_"+i).text() || 0;
    var qhdzamo = $("#Thick_qhdzamo_"+i).text() || 0;
    var code = $('#Thick_'+i).val() || '';
    if (code=='' || code<=0 || qhdzamo<=0 || code <= qhjc) {
        return sum;
    } else {
        //toFixed(2) parseInt
        var x = Number((code * 1) - (qhjc * 1));
        var z = (x / Number(qhdz)).toFixed(2);
        var s0 = parseInt(z.split('.')[0]);
        var s1 = parseInt(z.split('.')[1]);
        if (z <= 1) {
            sum = Number(qhdzamo); //小于一倍
        } else {
            if (s1 == 0) {
                sum = (Number(qhdzamo) * s0); //小数点后为0
            } else {
                sum = (Number(qhdzamo) * (s0+Number(1)));
            }
        }
    }
    return sum;
}
function HouduPrice_ko(i) {
    var sum='0';
    var code = $('#Thick_'+i).val(),result,array=new Array();
    if (code=='') return sum;
    for (var e in pArr) {
        if (code==e) {
            return pArr[e];
        } else {
            array.push(e);
        }
    }
    if (array.length==0) return false;
    var codeMin = Math.min.apply(null,array);
    if (code <= codeMin) {
        result = codeMin;
    } else {
        array.push(code);
        array.sort();
        var idx;
        for(var i=0;i<array.length;i++){
            if(array[i] == code){
                idx = i;
            }
        }
        //idx = Math.max(0,idx - 1);
        result = array[Math.max(0,idx - 1)];
    }
    for (var k in pArr) {
        if (result==k) {
            return pArr[k];
        }
    }
}

// 增减数量
/* reduce_add */
var setAmount = {
    min:1,
    max:99,
    reg:function(x) {
        return new RegExp("^[0-9]\\d*$").test(x);
    },
    amount:function(obj, mode) {
        var x = $(obj).val();
        if (this.reg(x)) {
            if (mode) {
                x++;
            } else {
                x--;
            }
        } else {
            toastr.warning('请输入正确数字！');
            $(obj).val(this.min);
            $(obj).focus();
        }
        return x;
    },
    reduce:function(obj) {
        var x = this.amount(obj, false);
        if (x >= this.min) {
            $(obj).val(x);
            recalc(obj);
            calcTotalPallets(); // 计算数量
        } else {
            toastr.warning("数量最少为" + this.min);
            $(obj).val(this.min);
            $(obj).focus();
        }
    },
    add:function(obj) {
        var x = this.amount(obj, true);
        if (x <= this.max) {
            $(obj).val(x);
            recalc(obj);
            calcTotalPallets(); // 计算数量
        } else {
            //toastr.warning("商品数量最多为" + this.max);
            $(obj).val(this.max);
            $(obj).focus();
        }
    },
    modify:function(obj) {
        var x = $(obj).val();
        if (x < this.min || x > this.max || !this.reg(x)) {
            toastr.warning('请输入正确！');
            $(obj).val(this.min);
            $(obj).focus();
        }
    }
};

/*增减tr行*/
var setLines = {
    min:1,
    max:99,
    amount: function (e) {
        var x = $(e).find('tr').size(); // 遍历tr数量
        return x;
    },
    add: function (obj) {
        var x = this.amount(obj);

        if (x < this.max) {
            x++;
        } else {
            toastr.warning('不能再增加了，已经有那么多了！');
            x = false;
        }
        return x
    },
    del: function (obj) {
        var x = this.amount(obj);
        if (x > this.min) {
            x;
        } else {
            toastr.warning('不能再删了，表格还得留一行啊！');
            x = false;
        }
        return x;
    }
};

/** total_item **/
//数量增加
function recalc(e) {
    var $id = e.replace(/[^0-9]/ig, "") || 0; //取得数字
    //产品价格统计
    $("#Amount_"+$id).calc(
        "qty * price",
        {
            qty: $("#Quantity_"+$id),
            price: $("#UnitPrice_"+$id)
        },
        function (s) {
            return "￥" + s.toFixed(2);
        },
        function ($this) {
            var sum = $this.sum();
            $(this).val("￥" + sum.toFixed(2)); //当前行改变价格
        }
    );
    calcProdSubTotal(true);
};

// 统计总额
function calcProdSubTotal(model) {
    // t数量，m总额，p折扣，model 是否计算折扣
    var t=0 , m=0, p=0;
    // 判断是否需要计算折扣 true为计算
    if (model) {
        p = Number($("input[name='Preferential']").val()); // 有折扣计算折扣后
    };
    $(".Amount").each(function(){
        var val = $(this).val() || 0;
        t += Number(filterMoney(val));
    });
    if (p<=99 && p >= 1) {
        m = viewModel(t,p);
    } else {
        m = t;
    };
    $("input[name='AmountSmall']").val(formatMoney(m)); // 小写金额
    $("input[name='AmountBig']").val(smalltoBIG(m)); // 大写金额
};

// 计算总数量
function calcTotalPallets() {
    var totalPallets = 0;
    $("input[id^=Quantity_]").each(function() {
        var thisValue = $(this).val();
        totalPallets += parseInt(thisValue);
    });
    $("input[name='OrderQuantity']").val(totalPallets);
};

// 百分比计算
function viewModel(a,b) {
    var m = Number(a);
    var d = Number(b);
    var x = '';
    if(m && d){
        if(d <= 100 && d >= 0){
            //$("#result").html("￥"+(m*d/100))
            x = m*d/100;
        }
    }
    return x;
};

// 过滤金额的非数字并取得小数点
function filterMoney(s) {
    var a = s.split(".")[0].replace(/[^0-9]/ig, "") || 0; // 取得小数点前数字并过滤非数字
    var b = s.toString().match(/\.\d+$/gi) || ''; //取小数点后面数字
    return a + b;
};

//清空同行的已输入数据
function qingkongtonghangdata(i) {
    $("#Yanse_"+i).removeSelected(); //颜色清空
    $("#Baobian_"+i).removeSelected(); //包边清空
    $("#Suoxiang_"+i).removeSelected(); //锁向清空
    $("#Fittings_"+i).removeSelected(); //锁具清空

    $("#Chanph_"+i).val(''); //编号清空
    $("#Breadth_"+i).val(''); //宽清空
    $("#Heiget_"+i).val(''); //高清空
    $("#Thick_"+i).val(''); //厚清空
    $("#Mianji_"+i).val(''); //面积清空
    $("#Diaojiao_"+i).val(''); //吊脚清空
    $("#Quantity_"+i).val('0'); //单价清0
    $("#Amount_"+i).val('0'); //金额清0
    $("#UnitPrice_"+i).val('0'); //单价清0
    $("#Remark_"+i).val(''); //备注清空

    $("#Baobian_price_"+i).text('0'); //包边单价
    $("#Fittings_price_"+i).text('0'); //锁具清空
}

/*
 * formatMoney(s,type)
 * 功能：金额按千位逗号分割
 * 参数：s，需要格式化的金额数值.
 * 参数：type,判断格式化后的金额是否需要小数位.
 * 返回：返回格式化后的数值字符串.
 */
function formatMoney(s, type) {
    if (/[^0-9\.]/.test(s))
        return "0";
    if (s == null || s == "")
        return "0";
    s = s.toString().replace(/^(\d*)$/, "$1.");
    s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
    s = s.replace(".", ",");
    var re = /(\d)(\d{3},)/;
    while (re.test(s))
        s = s.replace(re, "$1,$2");
    s = s.replace(/,(\d\d)$/, ".$1");
    if (type == 0) {// 不带小数位(默认是有小数位)
        var a = s.split(".");
        if (a[1] == "00") {
            s = a[0];
        }
    }
    return "￥" + s;
};

//日期加上天数得到新的日期
//dateTemp 需要参加计算的日期，days要添加的天数，返回新的日期，日期格式：YYYY-MM-DD
function getNewDay(dateTemp, days) {
    var dateTemp = dateTemp.split("-");
    var nDate = new Date(dateTemp[0] + '-' + dateTemp[1] + '-' + dateTemp[2]); //转换为YYYY-MM-DD格式
    var millSeconds = Math.abs(nDate) + (days * 24 * 60 * 60 * 1000);
    var rDate = new Date(millSeconds);
    var year = rDate.getFullYear();
    var month = rDate.getMonth() + 1;
    if (month < 10) month = "0" + month;
    var date = rDate.getDate();
    if (date < 10) date = "0" + date;
    return (year + "-" + month + "-" + date);
}

/** 数字金额大写转换(可以处理整数,小数,负数) */
function smalltoBIG(n) {
    var fraction = ['角', '分'];
    var digit = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
    var unit = [ ['元', '万', '亿'], ['', '拾', '佰', '仟']  ];
    var head = n < 0 ? '欠' : '';
    n = Math.abs(n);
    var s = '';
    for (var i = 0; i < fraction.length; i++)
    {
        s += (digit[Math.floor(n * 10 * Math.pow(10, i)) % 10] + fraction[i]).replace(/零./, '');
    }
    s = s || '整';
    n = Math.floor(n);

    for (var i = 0; i < unit[0].length && n > 0; i++)
    {
        var p = '';
        for (var j = 0; j < unit[1].length && n > 0; j++)
        {
            p = digit[n % 10] + unit[1][j] + p;
            n = Math.floor(n / 10);
        }
        s = p.replace(/(零.)*零$/, '').replace(/^$/, '零')  + unit[0][i] + s;
    }
    return head + s.replace(/(零.)*零元/, '元').replace(/(零.)+/g, '零').replace(/^整$/, '零元整');
};

//输入框变动
;(function ($, window, document, undefinde) {
    //计算厚度的单价
    $.fn.price = function (callback) {
        
    };
    $.fn.removeSelected = function() {
        this.val("");
    }
})(jQuery, window, document);

// 增减数量插件
;(function ($) {
    var defaults = {reNumbers: /(-|-\$)?(\d+(,\d{3})*(\.\d{1,})?|\.\d{1,})/g, cleanseNumber: function (v) {
        return v.replace(/[^0-9.\-]/g, "");
    }, useFieldPlugin: (!!$.fn.getValue), onParseError: null, onParseClear: null};
    $.Calculation = {version: "0.4.07",setDefaults: function(options) {
        $.extend(defaults, options);
    }};
    $.fn.parseNumber = function(options) {
        var aValues = [];
        options = $.extend(options, defaults);
        this.each(function () {
            var $el = $(this),sMethod = ($el.is(":input") ? (defaults.useFieldPlugin ? "getValue" : "val") : "text"),v = $.trim($el[sMethod]()).match(defaults.reNumbers, "");
            if (v == null) {
                v = 0;
                if (jQuery.isFunction(options.onParseError)) options.onParseError.apply($el, [sMethod]);
                $.data($el[0], "calcParseError", true);
            } else {
                v = options.cleanseNumber.apply(this, [v[0]]);
                if ($.data($el[0], "calcParseError") && jQuery.isFunction(options.onParseClear)) {
                    options.onParseClear.apply($el, [sMethod]);
                    $.data($el[0], "calcParseError", false);
                }
            }
            aValues.push(parseFloat(v, 10));
        });
        return aValues;
    };
    $.fn.calc = function(expr, vars, cbFormat, cbDone) {
        var $this = this, exprValue = "", precision = 0, $el, parsedVars = {}, tmp, sMethod, _, bIsError = false;
        for (var k in vars) {
            expr = expr.replace((new RegExp("(" + k + ")", "g")), "_.$1");
            if (!!vars[k] && !!vars[k].jquery) {
                parsedVars[k] = vars[k].parseNumber();
            } else {
                parsedVars[k] = vars[k];
            }
        }
        this.each(function (i, el) {
            var p, len;
            $el = $(this);
            sMethod = ($el.is(":input") ? (defaults.useFieldPlugin ? "setValue" : "val") : "text");
            _ = {};
            for (var k in parsedVars) {
                if (typeof parsedVars[k] == "number") {
                    _[k] = parsedVars[k];
                } else if (typeof parsedVars[k] == "string") {
                    _[k] = parseFloat(parsedVars[k], 10);
                } else if (!!parsedVars[k] && (parsedVars[k] instanceof Array)) {
                    tmp = (parsedVars[k].length == $this.length) ? i : 0;
                    _[k] = parsedVars[k][tmp];
                }
                if (isNaN(_[k])) _[k] = 0;
                p = _[k].toString().match(/\.\d+$/gi);
                len = (p) ? p[0].length - 1 : 0;
                if (len > precision) precision = len;
            }
            try {
                exprValue = eval(expr);
                if (precision) exprValue = Number(exprValue.toFixed(Math.max(precision, 4)));
                if (jQuery.isFunction(cbFormat)) {
                    var tmp = cbFormat.apply(this, [exprValue]);
                    if (!!tmp) exprValue = tmp;
                }
            } catch(e) {
                exprValue = e;
                bIsError = true;
            }
            $el[sMethod](exprValue.toString());
        });
        if (jQuery.isFunction(cbDone)) cbDone.apply(this, [this]);
        return this;
    };
    $.each(["sum", "avg", "min", "max"], function (i, method) {
        $.fn[method] = function (bind, selector) {
            if (arguments.length == 0)return math[method](this.parseNumber());
            var bSelOpt = selector && (selector.constructor == Object) && !(selector instanceof jQuery);
            var opt = bind && bind.constructor == Object ? bind : {bind: bind || "keyup", selector: (!bSelOpt) ? selector : null, oncalc: null};
            if (bSelOpt) opt = jQuery.extend(opt, selector);
            if (!!opt.selector) opt.selector = $(opt.selector);
            var self = this, sMethod, doCalc = function () {
                var value = math[method](self.parseNumber(opt));
                if (!!opt.selector) {
                    sMethod = (opt.selector.is(":input") ? (defaults.useFieldPlugin ? "setValue" : "val") : "text");
                    opt.selector[sMethod](value.toString());
                }
                if (jQuery.isFunction(opt.oncalc)) opt.oncalc.apply(self, [value, opt]);
            };
            doCalc();
            return self.bind(opt.bind, doCalc);
        }
    });
    var math = {sum: function (a) {
        var total = 0, precision = 0;
        $.each(a, function (i, v) {
            var p = v.toString().match(/\.\d+$/gi), len = (p) ? p[0].length - 1 : 0;
            if (len > precision) precision = len;
            total += v;
        });
        if (precision) total = Number(total.toFixed(precision));
        return total;
    },avg: function (a) {
        return math.sum(a) / a.length;
    },min: function (a) {
        return Math.min.apply(Math, a);
    },max: function (a) {
        return Math.max.apply(Math, a);
    }};
})(jQuery);
