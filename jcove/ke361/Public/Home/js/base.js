/**
 * Created with JetBrains WebStorm.
 * User: zhuleilei
 * Date: 12-9-4
 * Time: 下午3:22
 *
 */
var __PUBLIC__ =  '/Public/static_new';//前端静态文件路径
var __STATIC__ ='/Public';//前端静态文件路径
var __COMMON__ = '/Public/common';//公用前端静态文件路径
var __UPLOAD__ = 'http://s1.juancdn.com'; //资源文件路径
var __URL_UPLOAD__ = 'http://s1.juancdn.com/upload.php?t=3';//上传地址
var __URL_JUANPI__ = 'http://www.juanpi.com';
var __URL_JKY__ = 'http://www.jiukuaiyou.com';
var __URL_FANLI__ = 'http://fanli.juanpi.com';
var __URL_BRAND__ =  'http://brand.juanpi.com';
var __URL_TAO__  =  'http://tao.juanpi.com';
var __URL_CLICK__ =  'http://click.juanpi.com';
var __URL_ZHE__  =  'http://zhe.juanpi.com';
var __URL_USER__ =  'http://www.juanpi.com/user';
var __URL_MEMBER__   = 'http://user.juanpi.com';//用户中心配置
var __URL_SHARE__ =  'http://www.juanpi.com/share';
var __URL_HEZUO__ = 'http://hezuo.juanpi.com';
var __URL_SELLER__ = 'http://seller.juanpi.com';
var __URL_JU__ = 'http://ju.jiukuaiyou.com';
var __URL_M_JUANPI__ = 'http://m.juanpi.com';//卷皮手机WAP
var __URL_M_JIUKUAIYOU__ = 'http://m.jiukuaiyou.com';//九块邮手机WAP
var __URL_SHOP__ = 'http://shop.juanpi.com';
var __URL_CART__ = 'http://cart.juanpi.com'; //购物车

var u = location.href.toString();
//encodeURIComponent();
var u_encode = encodeURIComponent(u);
var t_str = u.substr(7);
var t_a = new Array();
t_a = t_str.split("/");
t_a = t_a[0].split(":");
var port = (null == t_a[1])?"":":"+t_a[1];
t_a = t_a[0].split(".");
var temp_jky;
if (t_a[1] == 'jiukuaiyou') {
    temp_jky = 'jiukuaiyou';
}else{
    temp_jky = 'jiukuaiyoutest';
}
if(t_a[1]=="xiudang"||t_a[1]=="juanpi"||t_a[1]==temp_jky){
    var host = t_a[1];
    var domain = t_a[0];
    t_a.shift();
    t_a.shift();
    var topd = t_a.join(".");
}else{
    var domain = null;
    var host = t_a[0];
    t_a.shift();
    var topd = t_a.join(".");
}
//alert(topd);
var __U_HOST__ = domain;
domain  = (null == domain)?"":domain+".";
var __C_CODE__ = '@A#,^O';
var __C_NAME__ = '&:h#{D';
var __C_DOMAIN__ = host+"."+topd;
var __U_WEB__ = "http://"+domain+host+"."+topd+port;
var __U_MAIN__ = "http://www."+host+"."+topd+port;
var __U_TAO__ = "http://taobao."+host+"."+topd+port;
var __U_FANLI__ = "http://fanli."+host+"."+topd+port;
var __U_CLICK__ = "http://click."+host+"."+topd+port;
var __U_JIE__ = "http://jie."+host+"."+topd+port;
var __U_ZHE__ = "http://zhe."+host+"."+topd+port;
var __U_APP__ = "http://app."+host+"."+topd+port;
var __U_HEZUO__ = "http://hezuo."+host+"."+topd+port;
var __U_JKY__ = "http://www."+temp_jky+"."+topd+port;
var __U_JUANPI__ = "http://www.juanpi."+topd+port;
var __U_XIUDANG__ = "http://www.xiudang."+topd+port;
var __XD_USER__ = {uid:'',pic:'',nick:'',sign:''};
var JP={timer:null,ob:null,delay:null,router:{}};
$(".mouseenter").each(function(){
    JP.ob = $(this);
    JP.act = JP.ob.attr("act");
    JP.delay = JP.ob.attr('delay');
    switch(JP.delay){
        case 'fast':JP.delay=100;break;
        case 'mid':JP.delay=500;break;
        case 'slow':JP.delay=1000;break;
        default:parseInt(JP.delay)>0?parseInt(JP.delay):0;
    }
    funcRouter();
});
function funcRouter(){
    var obj = "JP.router."+JP.act.toLowerCase();
    if(eval(obj).length>0){
        $.hoverDelay(eval(obj));
    }
}
function extend(destination,source){
    for(var property in source){
        destination[property] = source[property];
    }
}
switch(host){
    case "juanpi":var shost = "juancdn.";break;
    case "xiudang":var shost = "xdimg.";break;
    default:var shost = "juancdn.";break;
}
var __U_STATIC__ = "http://s."+shost+"com"+port;
var __U_UPLOAD__ = "http://s1."+shost+"com"+port;

(function (d) {
    d.fn.floatUp = function (a) {
        a = d.extend({},
            a || {});
        return this.each(function () {
            $this = d(this);
            var c = $this.height();
            $this.css({
                height: 0,
                opacity: 0
            });
            $this.show();
            var b = $this.position().top;
            XDTOOL.empty($this.data("top")) ? $this.data("top", b) : (b = $this.data("top"), $this.css("top", b));
            $this.animate({
                    height: c + "px",
                    top: b - c + "px",
                    opacity: "1"
                },
                a.time || 1E3)
        })
    };
    d.fn.floatDown = function (a) {
        a = d.extend({},
            a || {});
        return this.each(function () {
            $this = d(this);
            var c = $this.height(),
                b = $this.position().top;
            XDTOOL.empty($this.data("top")) ?
                $this.data("top", b) : (b = $this.data("top"), $this.css("top", b));
            $this.animate({
                    height: "0px",
                    top: b + c + "px",
                    opacity: "0"
                },
                a.time || 1E3,
                function () {
                    $this.remove()
                })
        })
    };
    XD = {
        FX_Item_Array: {},
        FX_Item_Id_Array: {},
        FX_Item_PIC_Array: {},
        FX_Bao_Array: {},
        FX_Shai_Array: {},
        FX_Zhe_Array: {},
        FX_Images_Array: "",
        FX_Shop_Array: "",
        Globe_Textarea_Auto_Height: function (a) {
            var c = a.height(),
                b = function () {
                    c < 0 && (c = a.height()); (d.browser.mozilla || d.browser.safari) && a.height(c);
                    var b = a[0].scrollHeight,
                        e = b < c ? c : b,
                        e = e < c * 1.5 ? c : b;
                    a.height(e)
                };
            a.bind("keyup", b).bind("input", b).bind("propertychange", b).bind("focus", b)
        },
        Globe_Item_URL_Support: function (a) {
            var c = /tmall.com/i,
                b = /auction\d?.paipai.com/i,
                g = /buy.caomeipai.com\/goods/i,
                d = /www.360buy.com\/product/i,
                f = /product.dangdang.com\/Product.aspx\?product_id=/i,
                h = /book.360buy.com/i,
                i = /www.vancl.com\/StyleDetail/i,
                j = /www.vancl.com\/Product/i,
                k = /vt.vancl.com\/item/i,
                l = /item.vancl.com\/\d+/i,
                m = /mbaobao.com\/pshow/i,
                n = /[www|us].topshop.com\/webapp\/wcs\/stores\/servlet\/ProductDisplay/i,
                o = /quwan.com\/goods/i,
                p = /nala.com.cn\/product/i,
                q = /maymay.cn\/pitem/i;
            return /item(.lp|.beta)?.taobao.com\/(.?)[item.htm|item_num_id|item_detail|itemID|item_id|default_item_id]/i.test(a) ||
                c.test(a) || h.test(a) || d.test(a) || b.test(a) || g.test(a) || f.test(a) || i.test(a) || j.test(a) || k.test(a) || l.test(a) || m.test(a) || n.test(a) || o.test(a) || p.test(a) || q.test(a)
        },
        Globe_Input_Text: function (a, c) {
            c = c || a.val();
            a.focus(function () {
                XDTOOL.trim(a.val()) == c && a.val("");
                a.css("color", "#000")
            });
            a.blur(function () {
                XDTOOL.trim(a.val()) == "" && (a.val(c), a.css("color", "#999"))
            })
        },
        FX_Word_Count: function (a, c) {
            if (d("#" + a)[0]) {
                var b = function () {
                    var b = 0,
                        b = c ? XDTOOL.getMsgLength(d("#" + c).val()) : XDTOOL.getMsgLength(d("#" + a).find(".pub_txt").val()),
                        e = 140 - b;
                    b == 0 ? d("#" + a).find(".word_count").html("140") : (d("#" + a).find(".word_count").html(e), b > 140 ? d("#" + a).find(".word_count").addClass("out") : d("#" + a).find(".word_count").removeClass("out"))
                };
                b();
                c ? d("#" + c).bind("keyup", b).bind("input", b).bind("propertychange", b) : d("#" + a).find(".pub_txt").bind("keyup", b).bind("input", b).bind("propertychange", b)
            }
        },
        Globe_Check_Login: function () {
            if (XDPROFILE.uid == "") return XD.user_handsome_login_init(),
                XD.user_handsome_login(),
                false;
            return true
        },
        Globe_Short_Link_From: function () {
            d(".mg_slink").live("click",
                function () {
                    var a = this,
                        c = a.href,
                        b = d(a).attr("s"),
                        g = d(a).attr("c");
                    if (g == "") g = XDPROFILE.uid;
                    var e = c,
                        f = "",
                        f = c.indexOf("?") == -1 ? "?c=" + g + "&s=" + b : "&c=" + g + "&s=" + b;
                    e += f;
                    a.href = e;
                    setTimeout(function () {
                            a.href = c
                        },
                        500)
                })
        },
        Globe_Bind_Keybord_Submit: function (a, c, b) {
            b = b || "need_focus";
            b == "need_focus" && (a.focus(function () {
                d("body").unbind("keydown");
                d("body").bind("keydown",
                    function (a) {
                        a.ctrlKey && a.keyCode == 13 && c.click()
                    })
            }), a.blur(function () {
                d("body").unbind("keydown")
            }));
            b == "not_need_focus" && d(document).bind("keydown",
                function (a) {
                    a.ctrlKey && a.keyCode == 13 && (c.click(), d("body").unbind("keydown"))
                })
        },
        Globe_Upload:function(callback){
            var a = $;
            if(a("#iframe1").size() == 0){
                var uploadIframe = '<div style="display: none;"><iframe frameborder="0" name="iframe1" id="iframe1" src="about:blank"></iframe></div>';
                a("body").append(uploadIframe);
                var messenger = new Messenger('parent', 'MessengerDemo'),
                    iframe1 = document.getElementById('iframe1');

                // 绑定子页面 iframe
                messenger.addTarget(iframe1.contentWindow, 'iframe1');

                messenger.listen(function (msg) {
                    msg = eval("("+msg+")");
                    callback(msg);
                })
            }
        }
    };
    XDTOOL = {
        isfree_post:function(a){
            var pArea = new Array("西藏自治区","新疆维吾尔自治区","宁夏回族自治区","甘肃省","甘肃","青海省","青海","内蒙古自治区");
            var pArea_100 = new Array("台湾省","香港特别行政区","澳门特别行政区","海外");
            if($.inArray(a,pArea) >= 0) return 5;
            if($.inArray(a,pArea_100) >= 0) return 100;
            return true;
        },
        distance2Bottom: function (a) {
            var c = d(document).scrollTop(),
                b = d(window).height(),
                g = d(document).height();
            return c + b + a >= g ? !0 : !1
        },
        trim: function (a) {
            return a.replace(/(^\s*)|(\s*$)/g, "").replace(/(^\u3000*)|(\u3000*$)/g, "")
        },
        isURl: function (a) {
            return /([\w-]+\.)+[\w-]+.([^a-z])(\/[\w-.\/?%&=]*)?|[a-zA-Z0-9\-\.][\w-]+.([^a-z])(\/[\w-.\/?%&=]*)?/i.test(a) ? !0 : !1
        },
        byteLength: function (a) {
            var c =
                a.match(/[^\x00-\x80]/g);
            return a.length + (!c ? 0 : c.length)
        },
        getMsgLength: function (a) {
            if(typeof a == "undefined"){
                return false;
            }
            var c = a.length;
            if (c > 0) {
                for (var b = a, a = a.match(/http[s]?:\/\/[a-zA-Z0-9-]+(\.[a-zA-Z0-9]+)+([-A-Z0-9a-z_\$\.\+\!\*\(\)\/\/,:;@&=\?\~\#\%]*)/gi) || [], d = 0, e = 0, c = a.length; e < c; e++) {
                    var f = XDTOOL.byteLength(a[e]);
                    /^(http:\/\/xiudang.net)/.test(a[e]) || (d += /^(http:\/\/)+(xiudang.net|xiudang.com)/.test(a[e]) ? f <= 41 ? f : f <= 140 ? 24 : f - 140 + 24 : f <= 140 ? 24 : f - 140 + 24, b = b.replace(a[e], ""));
                }
                return Math.ceil((d + XDTOOL.byteLength(b)) / 2);
            } else return 0
        },
        jsMbSubstr: function (a, c) {
            if (!a || !c) return "";
            for (var b = 0, d = 0, e = "", d = 0; d < a.length; d++) {
                a.charCodeAt(d) > 255 ? b += 2 : b++;
                if (b > c * 2) return e;
                e += a.charAt(d)
            }
            return a
        },
        getAbsoluteLocation: function (a) {
            if (arguments.length != 1 || a == null) return null;
            var c = d(a),
                b = c.offset(),
                g = b.top,
                b = b.left,
                c = c.height(),
                e = d(window).height(),
                f = d(document).scrollTop();
            return {
                absoluteTop: g,
                absoluteLeft: b,
                isInView: g >= f && g <= f + e,
                isLoad: g + c + 200 >= f && g - 400 <= f + e
            }
        },
        objToJson: function (a) {
            var c = "{";
            for (var b in a){
                if (a[b] == null) {
                    continue;
                } else if (typeof a[b] === 'object') {
                    c += '"' + b + '":' + XDTOOL.objToJson(a[b]) + ",";
                } else {
                    c += '"' + b + '":"' + a[b] + '",';
                }
            }
            c += "}";
            return c = c.replace(/,}/g, "}")
        },
        getPicExtension: function (a) {
            return a.toLowerCase().substring(a.lastIndexOf(".") + 1)
        },
        empty: function (a) {
            return void 0 === a || null === a || "" === a
        },
        emptyObj: function (a) {
            for (var c in a) return !1;
            return !0
        },
        filterSpaces: function (s) {
            s = '' + s;
            var sarr = s.split(" ");
            var t;
            for(i = 0; i < sarr.length; i++){
                t += sarr[i];
            }
            return t;
        },
        isParent: function (a, c) {
            for (; a != void 0 && a != null && a.tagName.toUpperCase() != "BODY"; ) {
                if (a == c) return !0;
                a = a.parentNode
            }
            return !1
        },
        setCookie: function (a, c, b) {
            b = b || {};
            if (c === null) c = "",
                b.expires = -1;
            var d = "";
            if (b.expires && (typeof b.expires == "number" || b.expires.toUTCString)){
                typeof b.expires == "number"
                    ? (d = new Date, d.setTime(d.getTime() + b.expires * 864E5))
                    : d = b.expires;
                d = "; expires=" + d.toUTCString();
            }
            var e = b.path ? "; path=" + b.path : "",
                f = b.secure ? "; secure" : "",
                h = "";
            null != b.domain || void 0 != b.domain
                ? h = "; domain=" + b.domain
                : (h = document.domain.toString().split("."), h = "; domain=." + h[1] + "." + h[2]);
            document.cookie = [a, "=", encodeURIComponent(c), d, e, h, f].join("")
        },
        getCookie: function (a) {
            a = document.cookie.match(RegExp("(^| )" + a + "=([^;]*)(;|$)"));
            if (a != null){
                return unescape(a[2]);
            } else {
                return "";
            }
        },        
        getMousePosition: function (a) {
            var c = 0,
                b = 0;
            if (!a){
                a = window.event;
            }
            if (a.pageX || a.pageY){
                c = a.pageX, b = a.pageY;
            } else if (a.clientX || a.clientY){
                c = a.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                b = a.clientY + document.body.scrollTop + document.documentElement.scrollTop;
            }
            return {x: c, y: b}
        },
        getQueryString: function (a) {
            if (RegExp("(^|\\?|&)" + a + "=([^&]*)(\\s|&|$)", "i").test(location.href)) {
                return unescape(RegExp.$2.replace(/\+/g, " "));
            } else {
                return ""
            }
        },
        isIOS: function () {
            return /\((iPhone|iPad|iPod)/i.test(navigator.userAgent)
        },
		getAnchor:function(){
			if (RegExp("(#)([0-9|a-z]{6})($|[,#&].*?)", "i").test(location.href)) {
				//console.log(RegExp.$1+' '+RegExp.$2 );
                return unescape(RegExp.$2.replace(/\+/g, " "));
            } else {
                return ""
            }
		}
    };
    XDLANG = {
        msgTimeout: "网络连接超时",
        msgNologin: "您尚未登录"
    };
    XDTEMPLATE = {
        lightBox: '<div id="{id}" class="alert_bg">'
            + '<div class="alert_box">'
            + '<div class="alert_top">'
            + '<span>{title}</span>'
            + '<a href="javascript:;" class="alert_close"></a>'
            + '</div>'
            + '<div class="alert_content">{body}</div>'
            + '</div></div>',
        lightBoxLoading: '<div class="alert_loading">'
            + '<img src="'+__U_STATIC__+'/img/icon/loading.gif" />'
            + '<span>请稍后......</span>'
            + '<a href="javascript:;" class="alert_close">取消</a>'
            + '</div>',
        addPicUp: '<div class="load">'
            + '<form class="picUploadForm" action="'+__U_MAIN__+'/xiuajax" method="POST" enctype="multipart/form-data">'
            + '<input type="button" class="button" value="上传图片" />'
            + '<input type="hidden" name="action" value="picUpload" />'
            + '<input hidefocus="true" type="file" class="tfile" name="image" style="cursor: pointer;display: none;height: 35px;left: 0;opacity: 0;filter:alpha(opacity=0);position: absolute;top: 62px;width: 65px;" />'
            + '</form>'
            + '</div>'
            + '<p class="choose">选择您要上传的图片（支持GIF/JPG/PNG，最大2M）。</p>',
        picFeed: '<li iid="{id}"><a href="javascript:;" class="close"></a><img src="{src}" /></li>',
        picFeedFashionElement: '<div class="fashion_element" id="fashion_element">'
            + '<input type="text" class="fashion_input r3" '
            + 'def_val="\u6dfb\u52a0\u65f6\u5c1a\u5143\u7d20\uff0c\u6700\u591a\u53ef\u4ee5\u586b\u519910\u4e2a\uff0c\u7528\u9017\u53f7\u9694\u5f00" value="\u6dfb\u52a0\u65f6\u5c1a\u5143\u7d20\uff0c\u6700\u591a\u53ef\u4ee5\u586b\u519910\u4e2a\uff0c\u7528\u9017\u53f7\u9694\u5f00" />'
            + '<div class="fashion_ele">{fashions}</div></div>',
        itemFeed: '<li iid="{id}"><a href="javascript:;" class="close"></a><img src="{src}" /></li>',
        addItemStart: '<div class="goods_box">'
            + '<span>将宝贝网址粘贴到下面框中，跟大家一起分享心水的宝贝吧！<br /><b class="org">小提示：</b>亲，分享优质宝贝将有机会被推荐到卷粉街或值得买首页哦！</span>'
            + '<div class="input">'
            + '<input type="text" class="text" />'
            + '<input type="button" value="确 定" class="button" />'
            + '</div>'
            + '<div class="support">'
            + '已支持以下网站（<a href="mailto:service@juanpi.com" class="in">商家申请加入</a>）：'
            + '<p>'
            + '<a target="_blank" href="http://click.juanpi.com/t?url=aHR0cDovL3M4LnRhb2Jhby5jb20vc2VhcmNoP3BpZD1tbV8yNzAyMzAxMF8wXzAmdGFva2VfdHlwZT0x" class="taobao">淘宝</a>'
            + '<a target="_blank" href="http://click.juanpi.com/t?url=aHR0cDovL3MuY2xpY2sudGFvYmFvLmNvbS90P2U9ekdVMzRDQTdLJTJCUGtxQjA1JTJCbTdyZkdLYXMxUElLcDBVMzdwWnVCb3R3TTNxWTRmN2o0N2dLdFElMkJtZHQxODI2VzlydE5FSVJtTTdYSHdkVmJFQzRpZmVnaWFPcFhyUmtkajk2R2NjSnphb2tWcHkzWCZwaWQ9bW1fMjcwMjMwMTBfMF8w" class="juhuasuan">聚划算</a>'
            + '<a target="_blank" href="http://click.juanpi.com/t?url=aHR0cDovL3d3dy50bWFsbC5jb20v" class="tianmao">天猫</a>'
            + '<a target="_blank" class="jingdong" href="http://www.360buy.com">京东商城</a>'
            + '<a target="_blank" class="oneshop" href="http://www.yihaodian.com">一号店</a>'
            + '<a target="_blank" class="fanke" href="http://www.vancl.com">凡客诚品</a>'
            + '<a target="_blank" href = "http://fanli.juanpi.com">更多...</a>'
            + '</p>'
            + '</div></div>',
        addItemResult: '<div class="goods_box">'
            + '<div class="pic">'
            + '<img src="{src}" />'
            + '</div>'
            + '<div class="queren">'
            + '<span class="goods_n">{title}</span>'
            + '<span class="goods_p">{price}</span>'
            + '<input type="button" value="确　定（3）" />'
            + '</div></div>',
        addZheStart: '<div class="goods_box">'
            + '<span>将折扣商品网址粘贴到下面框中，实惠的东东要分享哦！<br /><b class="org">小提示：</b>亲，分享超值折扣宝贝就有机会被推荐到聚折扣首页哦！</span>'
            + '<div class="input">'
            + '<input type="text" class="text" />'
            + '<input type="button" value="确 定" class="button" />'
            + '</div>'
            + '<div class="support">'
            + '已支持以下网站（<a href="mailto:service@xiudang.com" class="in">商家申请加入</a>）：'
            + '<p>'
            + '<a target="_blank" href="http://click.juanpi.com/t?url=aHR0cDovL3M4LnRhb2Jhby5jb20vc2VhcmNoP3BpZD1tbV8yNzAyMzAxMF8wXzAmdGFva2VfdHlwZT0x" class="taobao">淘宝</a>'
            + '<a target="_blank" href="http://click.juanpi.com/t?url=aHR0cDovL3MuY2xpY2sudGFvYmFvLmNvbS90P2U9ekdVMzRDQTdLJTJCUGtxQjA1JTJCbTdyZkdLYXMxUElLcDBVMzdwWnVCb3R3TTNxWTRmN2o0N2dLdFElMkJtZHQxODI2VzlydE5FSVJtTTdYSHdkVmJFQzRpZmVnaWFPcFhyUmtkajk2R2NjSnphb2tWcHkzWCZwaWQ9bW1fMjcwMjMwMTBfMF8w" class="juhuasuan">聚划算</a>'
            + '<a target="_blank" href="http://click.juanpi.com/t?url=aHR0cDovL3d3dy50bWFsbC5jb20v" class="tianmao">天猫</a>'
            + '</p>'
            + '</div></div>',
        addShopUp: '<div class="shop_box">'
            + '<span>\u5c06\u5e97\u94fa\u7f51\u5740\u7c98\u8d34\u5230\u4e0b\u9762\u6846\u4e2d\u5373\u53ef\u3002\uff08\u76ee\u524d\u53ea\u652f\u6301\u6dd8\u5b9d\u5e97\u94fa\uff09</span>'
            + '<div>'
            + '<input class="s_url fl rl3" type="text" />'
            + '<input class="s_s fl rr3" value="\u786e \u5b9a" type="button" />'
            + '</div></div> ',
        addFollow: '<span>\u5df2\u4e92\u76f8\u5173\u6ce8</span><br />'
            + '<a class="unFollow" href="javascript:void(0);">\u53d6\u6d88\u5173\u6ce8</a><br />'
            + '<a href="javascript:void(0);" class="unFollow">\u79fb\u9664\u7c89\u4e1d</a><br />'
            + '<a href="'+__U_MAIN__+'/message/send/toId/{uid}">\u53d1\u79c1\u4fe1</a>',
        uInfoTip: '<div class="tip_info">'
            + '<img class="avatar" src="'+__U_UPLOAD__+'{face}" alt="" />'
            + '<div class="info">'
            + '<p><a href="{url}" class="uname" target="_blank">{name}</a></p>'
            + '<p class="gray_3">{address}</p>'
            + '<p>'
            + '关注：<a href="'+__U_MAIN__+'/u/{uid}/follow" target="_blank"><span>{follow}</span></a>　'
            + '粉丝：<a href="'+__U_MAIN__+'/u/{uid}/fans" target="_blank"><span>{fans}</span></a>'
            + '</p>'
            + '<div>{medal}</div>'
            + '</div>'
            + '<div class="intro">{intro}</div>'
            + '</div>'
            + '<div class="tip_toolbar">{toolbar}</div>'
            + '<div class="tip_arrow"></div>'
    };
    XDFACE = {
        faceTab: '<span class="f1">默认</span>',
        facePage: {
            f1: '<ul class="face" title="默认">'
                + '<li title="还行"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/hx.gif" /></a></li>'
                + '<li title="看棒"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/kb.gif" /></a></li>'
                + '<li title="膜拜"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/mb.gif" /></a></li>'
                + '<li title="工作"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/gz.gif" /></a></li>'
                + '<li title="勾引"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/gy.gif" /></a></li>'
                + '<li title="给力"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/gl.gif" /></a></li>'
                + '<li title="不给力"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/bgl.gif" /></a></li>'
                + '<li title="不高兴"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/bgx.gif" /></a></li>'
                + '<li title="嘻嘻"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/xx.gif" /></a></li>'
                + '<li title="开心"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/kx.gif" /></a></li>'
                + '<li title="伤心"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/sx.gif" /></a></li>'
                + '<li title="泪奔"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/lb.gif" /></a></li>'
                + '<li title="愤愤"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/ff.gif" /></a></li>'
                + '<li title="嘟嘟"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/dd.gif" /></a></li>'
                + '<li title="崩溃"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/bk.gif" /></a></li>'
                + '<li title="犯困"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/fk.gif" /></a></li>'
                + '<li title="狂汗"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/kh.gif" /></a></li>'
                + '<li title="鬼脸"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/gn.gif" /></a></li>'
                + '<li title="生病"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/sb.gif" /></a></li>'
                + '<li title="yy"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/yy.gif" /></a></li>'
                + '<li title="一般般"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/ybb.gif" /></a></li>'
                + '<li title="得瑟"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/ds.gif" /></a></li>'
                + '<li title="鄙视"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/bs.gif" /></a></li>'
                + '<li title="晕眩"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/yx.gif" /></a></li>'
                + '<li title="恶心"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/ex.gif" /></a></li>'
                + '<li title="心动"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/xd.gif" /></a></li>'
                + '<li title="无聊"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/wl.gif" /></a></li>'
                + '<li title="糗"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/qiu.gif" /></a></li>'
                + '<li title="害羞"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/hxiu.gif" /></a></li>'
                + '<li title="坚持"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/jc.gif" /></a></li>'
                + '<li title="惊讶"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/jy.gif" /></a></li>'
                + '<li title="囧"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/jiong.gif" /></a></li>'
                + '<li title="酷狗"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/kg.gif" /></a></li>'
                + '<li title="贼笑"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/zx.gif" /></a></li>'
                + '<li title="倒霉"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/dm.gif" /></a></li>'
                + '<li title="委屈"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/wq.gif" /></a></li>'
                + '<li title="疑问"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/yw.gif" /></a></li>'
                + '<li title="嚎叫"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/hj.gif" /></a></li>'
                + '<li title="拜拜"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/bb.gif" /></a></li>'
                + '<li title="兔星星"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/txx.gif" /></a></li>'
                + '<li title="春运"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/cy.gif" /></a></li>'
                + '<li title="点炮"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/dp.gif" /></a></li>'
                + '<li title="喜得贵子"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/xdgz.gif" /></a></li>'
                + '<li title="红包"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/hb.gif" /></a></li>'
                + '<li title="圣诞"><a href="javascript:;"><img src="'+__U_STATIC__+'/common/img/mood/sd.gif" /></a></li>'
                + '</ul>'
        }
    };
})(jQuery);

__XD_USER__.uid = XDTOOL.empty(XDTOOL.getCookie('s_uid'))?'':XDTOOL.getCookie('s_uid');
__XD_USER__.pic = XDTOOL.empty(XDTOOL.getCookie('s_pic'))?'':decodeURIComponent(XDTOOL.getCookie('s_pic'));
__XD_USER__.nick = XDTOOL.empty(XDTOOL.getCookie('s_name'))?'':decodeURIComponent(XDTOOL.getCookie('s_name'));
__XD_USER__.sign = XDTOOL.empty(XDTOOL.getCookie('s_sign'))?'':decodeURIComponent(XDTOOL.getCookie('s_sign'));
XDPROFILE = {uid:__XD_USER__.uid,face:__U_UPLOAD__+__XD_USER__.pic,username:__XD_USER__.nick,sign:__XD_USER__.sign};

var __S_TIME__ = XDTOOL.getCookie('server_time');

$(function() {
    if ($.browser.msie && $.browser.version == "6.0") {
        try {
            document.execCommand("BackgroundImageCache", false, true)
        } catch(e) {}
    }
    // XD.Globe_Short_Link_From();
    var u = XDTOOL.getAnchor()?XDTOOL.getAnchor():XDTOOL.getQueryString("u");
    XDPROFILE.uid == "" && u != "" && XDTOOL.getCookie("recomU") != u && XDTOOL.setCookie("recomU", u, {
        expires: 2,
        path: "/"
    });
});
(function($) {
    $.fn.hoverDelay = function (options) {
        var defaults = {hoverDuring:300, outDuring:300, hoverEvent:function () {
            $.noop()
        }, outEvent:function () {
            $.noop()
        }};
        var sets = $.extend(defaults, options || {});
        var hoverTimer, outTimer;
        return $(this).each(function () {
            var t = this;
            $(this).hover(function () {
                clearTimeout(outTimer);
                hoverTimer = setTimeout(function () {
                    sets.hoverEvent.apply(t)
                }, sets.hoverDuring)
            }, function () {
                clearTimeout(hoverTimer);
                outTimer = setTimeout(function () {
                    sets.outEvent.apply(t)
                }, sets.outDuring)
            })
        })
    };    
})(jQuery);

(function($) {
    $.fn.JP_hoverDelay = function (options) {
        var defaults = {hoverDuring:300, outDuring:300, hoverEvent:function () {
            $.noop()
        }, outEvent:function () {
            $.noop()
        }};
        var sets = $.extend(defaults, options || {});
        var hoverTimer, outTimer;
        return $(this).each(function () {
            var t = this;
            $(this).hover(function () {
                if (sets.outDuring !== 0) {
                    clearTimeout(outTimer);
                };
                hoverTimer = setTimeout(function () {
                    sets.hoverEvent.apply(t)
                }, sets.hoverDuring)
            }, function () {
                if (sets.hoverDuring !== 0) {
                    clearTimeout(hoverTimer);
                };
                outTimer = setTimeout(function () {
                    sets.outEvent.apply(t)
                }, sets.outDuring)
            })
        })
    };
})(jQuery);