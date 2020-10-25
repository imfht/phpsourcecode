(function ($) {
    $.fn.jqDrag = function (h, opt) {
        return i(this, h, 'd', opt);
    };
    $.fn.jqResize = function (h, opt) {
        return i(this, h, 'r', opt);
    };
    $.jqDnR = {
        dnr: {},
        e: 0,
        opt: null,
        drag: function (v) {
                if (M.k == 'd') {
                    var left = M.X + v.pageX - M.pX,
                        top = M.Y + v.pageY - M.pY;
                    if (OPT != null) {
                        if (OPT.lock == 'x') {
                            left = M.X;
                        } else if (OPT.lock == 'y') {
                            top = M.Y;
                        }

                        if (OPT.containment) {
                            var c = $(OPT.containment);

                            var offset = c.offset();
                            var _left = parseInt(offset.left),
                                _top = parseInt(offset.top);

                            if (E.offset().left < _left) {
                                left = _left;
                            }
                            if (E.offset().top < _top) {
                                top = offset.top;
                            }
                            if (left + M.W > _left + c.width()) {
                                left = _left + c.width() - M.W;
                            }
                            if (top + M.H > _top + c.height()) {
                                top = _top + c.height() - M.H;
                            }
                        }

                        if (OPT.dragging) OPT.dragging(v, E);
                    }
                    E.css({
                        left: left,
                        top: top
                    });

                } else {
                    var width = Math.max(v.pageX - M.pX + M.W, 0),
                        height = Math.max(v.pageY - M.pY + M.H, 0);

                    if (OPT != null) {
                        if (OPT.containment) {
                            var c = $(OPT.containment);

                            if (M.X + width > c.offset().left + c.width()) {
                                width = c.offset().left + c.width() - M.X;
                            }
                            if (M.Y + height > c.offset().top + c.height()) {
                                height = c.offset().top + c.height() - M.Y;
                            }
                        }

                        if (OPT.resizing) OPT.resizing(v, E);
                    }
                    E.css({
                        width: width,
                        height: height
                    });
                }
                return false;
            },
            stop: function (v) { /*E.css('opacity',M.o);*/
                if (OPT != null && OPT.stop) OPT.stop(v, E);
                $(document).unbind('mousemove', J.drag).unbind('mouseup', J.stop);
            }
    };

    var J = $.jqDnR,
        M = J.dnr,
        E = J.e,
        OPT = J.opt,
        i = function (e, h, k, opt) {
            return e.each(function () {
                h = (h) ? $(h, e) : e;

                h.bind('mousedown', {
                    e: e,
                    k: k
                }, function (v) {
                    var d = v.data,
                        p = {};
                    E = d.e;
                    OPT = opt ? opt : null;
                    if (OPT != null && OPT.drag) OPT.drag(v, E);
                    // attempt utilization of dimensions plugin to fix IE issues
                    if (E.css('position') != 'relative') {
                        try {
                            E.position(p);
                            if (d.k == 'd') E.css({
                                left: E.offset().left + 'px',
                                top: E.offset().top + 'px'
                            });
                        } catch (e) {}
                    }
                    M = {
                        X: p.left || f('left') || 0,
                        Y: p.top || f('top') || 0,
                        W: f('width') || E[0].scrollWidth || 0,
                        H: f('height') || E[0].scrollHeight || 0,
                        pX: v.pageX,
                        pY: v.pageY,
                        k: d.k /*,o:E.css('opacity');*/
                    };
                    /*E.css({opacity:0.8});*/
                    $(document).mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
                    return false;
                });

            });
        },

        f = function (k) {
            return parseInt(E.css(k)) || false;
        };
})(jQuery);



//var admin_url='$admin_url',fromurl='/',label_iframe_width='$label_iframe_width',label_iframe_height='$label_iframe_height';
$(document).ready(function () {
	setTimeout(function(){
	    $('.p8label').each(function () {
			$(this).hover(function () {
				$(this).css({
					'opacity': '0.8',
					'filter': 'alpha(opacity=70)'
				});
			}, function () {
				$(this).css({
					'opacity': '0.4',
					'filter': 'alpha(opacity=50)'
				});
			}).jqResize($('div', this));
		});
	},3000);
	/*
	if(typeof(labelSet)!='undefined'){
		var weburl = window.location.href;
		if(weburl.indexOf('?')>-1){
			weburl += '&';
		}else{
			weburl += '?';
		}
		if(labelSet===true){			
            $('body').dblclick(function(){
				if(confirm('你确认要退出标签管理吗?')){
					window.location.href = weburl+'label_set=quit';
				}
			});
		}else if(labelSet===false){
			$('body').dblclick(function(){
				if(confirm('你确认要进入标签管理吗?')){
					window.location.href = weburl+'label_set=set';
				}
			});
		}
	}
	*/
});



var jumpto=true;
function showlabel_(obj,mouse,type,name,url){	
	if(mouse=='over'){
		obj.style.filter='Alpha(Opacity=80)';
		obj.style.cursor='hand';
		obj.title='点击修改';
	}else if(mouse=='out'){
		obj.style.filter='Alpha(Opacity=50)';
	}else if(mouse=='click'&&jumpto==true){
		var width = parseInt( $(obj).parent().width() );
		if(width<100){
			width = 100;
		}
		width = type=='labelmodel' ?  width  : (parseInt(obj.style.width)==100 ? width : parseInt(obj.style.width));
		height = parseInt(obj.style.height)<10 ? 30 : parseInt(obj.style.height);
		layer_label_iframe( (url!=undefined&&url!=""?url:admin_url)+'?type='+type+'&name='+name+'&div_width='+width+'&div_height='+height);
	}
}
//layer_label_iframe
function layer_label_iframe(url){
	layer.open({
	  type: 2,
	  title: '标签设置',
	  shadeClose: true,
	  shade: false,
	  maxmin: true, //开启最大化最小化按钮
	  offset: label_iframe_width=='100%'?'b':'center', //右下角弹出
	  shade: 0.4,  //遮罩透明度
	  area: [label_iframe_width, label_iframe_height],
	  content: url,
	  end: function(){
		  	window.location.reload();
		}
	});
}
function ckjump_(type){
	if(type==1){
		jumpto=true;
	}else{
		jumpto=false;
	}
}
var layobj=null;
var potype=null;
var ifchange=null;
function change_po_(type,t,tag){
	ifchange=t;
	layobj=document.getElementById(tag);
	potype=type;
	change_ls_();
}

function change_ls_(){
 
	var obj=layobj;
	var type=potype;
	if(type=='up'){
		num=(parseInt(obj.style.height)-1);
		obj.style.height=num+'px';
	}else if(type=='left'){
		num=(parseInt(obj.style.width)-1);
		obj.style.width=num+'px';
	}else if(type=='down'){
		num=(parseInt(obj.style.height)+1);
		obj.style.height=num+'px';
	}else if(type=='right'){
		num=(parseInt(obj.style.width)+1);
		obj.style.width=num+'px';
	}

	if(ifchange==1){
		window.setTimeout('change_ls_()',40);
	}
}









