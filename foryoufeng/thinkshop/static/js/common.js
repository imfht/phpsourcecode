//String.format 同时匹配[](){}内容方式
if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        var tag = '';
        return this.replace(/(\{|\(|\[)(\d+)(\}|\)|\])/g, function (match, m0,m1,m2) {
        	tag = m0+m2;
        	if(tag=='()' || tag == '{}' || tag == '[]') return typeof args[m1] != 'undefined'? args[m1]: (m0+m1+m2);
        });
    };
}
function url(str){
    if(str==undefined) str = '';
    str = str.replace(/(^\/*)|(\/*$)/g,"");
    var option = str.split('/');
    var url = server_url.replace('__con__',option[0]);
    url = url.replace('__act__',option[1]==undefined?'':option[1]);
    var param = '';
    if(option.length>2){
        if(server_url.indexOf('?')!=-1){
            for(i=3;i<option.length;i=i+2)
            {
                param += '&'+option[i-1]+'='+option[i];
            }
        }
        else{
            for(i=3;i<option.length;i=i+2)
            {
                param += '/'+option[i-1]+'/'+option[i];
            }
        }
    }
    return url+param;
}
//tab插件
function tabs_init(){
	$(".tab").each(function(j){
		var that = $(this);
		that.attr("index",0);
		$(">.tab-head >*",that).each(function(i){
			var index = i;

			if(i!=0)$(this).removeClass('current');
			else $(this).addClass('current');
			$(">.tab-body > *",that).css({display:'none'});
			$(">.tab-body > *:eq(0)",that).css({display:'block'});
			$(this).on("click",function(){
				$(">.tab-head >*",that).removeClass('current');
				$(this).addClass("current");
				$(">.tab-body > *",that).css({display:'none'});
				$(">.tab-body > *:eq("+index+")",that).css({display:'block'})
				that.attr("index",i);
				tab_page_nav();
			})
		});
	});
	var hash = window.location.hash;
	var re = /#tab(-(\d+))+$/i;
	if(re.test(hash)){
		var num = hash.match(/\d+/g);
		var len = num.length;
		for(var i=0;i<len;i++) tabs_select(i,num[i]);
		tab_page_nav();
	}
}

function tab_page_nav(){
	var hash = '#tab';
	$(".tab").each(function(){
		hash += '-'+$(this).attr("index");
	});
	$(".page-nav a").each(function(){
		if($(this).attr('href')!='javascript:;' && $(this).attr('href')!='#'){
			var href = $(this).attr("href");
			href = href.replace(/#(.+)$/i,'')+hash;
			$(this).attr("href",href);
		}else{
			var onclick = $(this).attr("onclick");
			if(onclick!=undefined){
				onclick = onclick.replace(/(\+*\"#(.+)\")?;$/i,'+')+'\"'+hash+'\";';
				$(this).attr("onclick",onclick);
			}
		}
	})
}
//tabs插件选择
function tabs_select(num,index){
		var that = $(".tab:eq("+num+")");
		that.attr("index",index);
		$(">.tab-head >*",that).each(function(i){
			if(i!=index)$(this).removeClass('current');
			else $(this).addClass('current');
			$(">.tab-body > *",that).css({display:'none'});
			$(">.tab-body > *:eq("+index+")",that).css({display:'block'});
		});
}
$(document).ready(function(){
　　tabs_init();
});
//表单验证回调处理tab定位
function check_tab_location(e){
  var index = $('.tab-body > *').has(e).index();
  if(index!=-1){
    tabs_select(0,index);
    return false;
  }
  else {
    return true;
  }
}
//tools
function tools_select(name,obj){
	//var obj=document.elementFromPoint(event.clientX,event.clientY);
	$('input[name=\''+name+'\']').attr('checked',$(obj).hasClass('icon-checkbox-unchecked'));
	$(obj).toggleClass('icon-checkbox-unchecked');
	return false;
}
//工具栏提交
function tools_submit(obj){
	var form = $('form:first');
	var confirm_flag = false;

	if(obj!=undefined){
		if(obj['form']!=undefined) form = $(obj['form']);
		if(obj['action']!=undefined)form.attr('action',obj['action']);
		if(obj['method']!=undefined){
			if(obj['method']=='get' || obj['method']=='post'){
				form.attr('method',obj['method']);
			}
			else form.attr('method','post');
		}
		else form.attr('method','post');
		if(form.attr('method')=='get'){
			var pattern = /(\w+)=(\w+)/ig;
			var parames = {};
			obj['action'].replace(pattern, function(a, b, c){parames[b] = c;});
			for(par in parames)form.append("<input type='hidden' name='"+par+"' value='"+parames[par]+"'>");
		}
		if(obj['msg']!=undefined) confirm_flag = true;
	}
	if(confirm_flag){
		var select_id = "id";
		if(obj['select_id']!=undefined) select_id = obj['select_id'];
		art.dialog.confirm('你确认删除操作？', function(){
			if($("input[name='"+select_id+"[]']:checked").size()>0)form.submit();
			else art.dialog.tips("<p class='warning'>没有选择任何项目，无法删除</p>");
		});
	}else{
		form.submit();
	}
	return false;
}
//刷新
function tools_reload(){
	location.reload();
}
//提交前咨询
function confirm_action(url,msg){
	if(msg==undefined) msg = '你确认删除操作吗？删除后无法恢复！';
	art.dialog.confirm(msg, function(){
		window.location.href = url;
	});
}
//数组的笛卡尔积
    function descartes(args){
	//var args = Array.prototype.slice.apply(arguments);
	if(args==undefined) return null;
	var len = args.length;
	if(len == 1) return args[0];
	else{
		var tem = new Array();
		tem = group(args[0],args[1]);
		for(var i=2;i<len;i++){
			tem = group(tem,args[i]);
		}
		var result = new Array();
		var tem_len = tem.length;
		num = 0;
		for(var i=0;i<tem_len;i++){
			result[num++] = tem[i].split('*#*');
		}
		return result;
	}
	function group (m,n){
	    var tem = new Array();
	    var num = 0;
	    for(var i=0;i<m.length;i++){
	        for(var j=0;j<n.length;j++){
	            tem[num++] =m[i]+'*#*'+n[j];
	        }
	    }
	    return tem;
	}
}

// 无限级连动插件
(function($){
	$.fn.Linkage = function(o){

		o = $.extend({url:'',selects:['#province','#city','#county'],initRunCallBack:false,selected:['0','0','0']}, o || {});
		var url = o.url;
		var arrNodeChild = new Array();
		var arrSelect = o.selected;
		var options = new Array();
		$.each(arrSelect,function(i){
			options[i] = '';
		});
		var len = o.selects.length;
		for(var i=0;i<len;i++) arrNodeChild[i] = new Array();
		//请求格式化后的JSON数据
		$.post(o.url,function(data){
			$.each(data, function(i, n){
				var c_id = i.substr(2);
				var selected = (c_id == arrSelect[0]) ? 'selected="selected"' : '';
				options[0] += '<option value="' + c_id + '" ' + selected + '>' + n.t + '</option>';

				n.id = c_id;
				if(n.c !== null){
					arrNodeChild[0][i] = n.c;
					parse(n,0);
				}
			});

			$.each(o.selects,function(i,em){
				$(em).append(options[i]);
			});
			if(o.initRunCallBack)callback();
		},"json");
		//解析每一层元素
		function parse(data,num){
			if(data.c !==undefined && data.c !== null) {
				$.each(data.c, function(i, n) {
					var c_id = i.substr(2);
					if(data.id == arrSelect[num]) {
						var selected = (c_id == arrSelect[num+1]) ? 'selected="selected"' : '';
						options[num+1] += '<option value="' + c_id + '" ' + selected + '>' + n.t + '</option>';
					}
					n.id = c_id;
					arrNodeChild[num+1][i] = n.c;
					if(n.c !== null) parse(n,num+1);
				});
			}
		}
		//回调处理
		function callback()
		{
			if(typeof(o.callback) == 'function'){
				var selected =new Array(); value =new Array(); text = new Array();
				$.each(o.selects,function(i,em){
					value[i] = $(em).val();
					text[i] = $('option:selected',$(em)).text();
				});
				selected[0] = value;
				selected[1] = text;
				o.callback(selected);
			}
		}
		//逐级绑定连动事件
		var len = o.selects.length;
		$.each(o.selects,function(i,em){
			$(em).change(function(){
				var val = 'o_'+$(this).val();
				if(arrNodeChild[i][val] !== null && i<len-1) {

					for(var j=i+1 ; j<len ;j++){
						var option = $(o.selects[j]).children().first();
						if(option.val()==0)$(o.selects[j]).empty().append(option);
						else $(o.selects[j]).empty().append("<option value='0'>请选择</option>");
					}
					if(val != 0){
						var select = '';
						if(arrNodeChild[i][val]!==undefined){
							$.each(arrNodeChild[i][val], function(k, n) {
		                	var c_id = k.substr(2);

		                    select += '<option value="' + c_id + '">' + n.t + '</option>';
		                });
		                $(o.selects[i+1]).append(select);
						}

					}
				}
				callback();
			});
		});
	};
})(jQuery);

// 高级筛选插件
// 此插件必需与artdialog联合使用
// author webning
(function($){
	$.fn.Condition = function(o){
		o = $.extend({data:[],okVal:'保存'}, o || {});
		var fields_select = "";
		if(o.data!=undefined){
			var data = o.data;
			for(i in data){
				fields_select += "<option value='"+i+"'>"+data[i]['name']+"</option>";
			}
		}
		if(fields_select!='')fields_select = "<select><option value='noselect'>添加筛选字段</option>"+fields_select+"</select>";
		art.dialog({id:'conditionDialog',title:'高级查询',padding:"6px",fixed: true,resize: false,content:'<div id="condition_dialog" style="width:600px; height:336px; " ><div class="tools_bar clearfix"> <span class="icon-plus fl"> '+fields_select+'</span> </div><div style="overflow: auto;width:600px; height:250px;"><table id="condition_table" class="default form2"><tr><th width="80">关系</th><th>字段名称</th><th width="100">比较关系</th><th>比较值</th><th width="60">操作</th></tr></table></div><div class="tc mt10"><a href="javascript:;" class="button" id="condition_create">'+o.okVal+'</a></div></div>'});
		//检测表达式
		function check_condition(condition)
	    {
	    	var reg = /^(\S+--\S+--\S+--[\S ]+__)*(\S+--\S+--\S+--[\S ]+)$/i;
	        return reg.test(condition);
	    }
		if(o.input!=undefined){
			var ini_data = $(o.input).val();
			if(ini_data!='' && check_condition(ini_data)){
				ini_data = ini_data.split('__');
				for(var i in ini_data){
					var value = '';
					var item = ini_data[i].split('--');
					if(o.data[item[1]]!=undefined)
					{
						if(o.data[item[1]]['values']!=undefined){
							var values = o.data[item[1]]['values'];
							for(var k in values){
								if(k==item[3])
									value += "<option value='"+k+"' selected='selected'>"+values[k]+"</option>";
								else
									value += "<option value='"+k+"' >"+values[k]+"</option>";
							}
							if(value!='') value = "<select>"+value+"</select>";
						}
						if(value=='') value = "<input value='"+item[3]+"'/>";
					var str = '<tr><td><select><option value="and">并且</option><option value="or">或者</option></select></td><td><input type="hidden" value="'+item[1]+'">'+o.data[item[1]]['name']+'</td><td><select> <option value="eq"> = </option> <option value="ne"> != </option> <option value="lt"> < </option> <option value="le"> <= </option> <option value="gt"> > </option> <option value="ge"> >= </option> <option value="ct">包含</option> <option value="nct">不包含</option> </select></td><td>'+value+'</td><td><a class="icon-close" href="javascript:;"> 删除</a></td></tr>';

					var reg = new  RegExp('(value=\\"'+item[0]+'\\")',"i");
					str = str.replace(reg,'$1 selected="selected"');
					var reg = new  RegExp('(value=\\"'+item[2]+'\\")',"i");
					str = str.replace(reg,'$1 selected="selected"');
					$("#condition_table").append(str);

					}

				}
				bindEvent();
			}

		}

		//添加事件的绑定
		$("#condition_dialog .icon-plus select").on("change",function(){
			var current = $(this).find("option:selected");
			var value = '';//'<input value=""/>';43
			if(o.data[current.val()]['values']!=undefined){
				var values = o.data[current.val()]['values'];
				for(var i in values){
					value += "<option value='"+i+"'>"+values[i]+"</option>";
				}
				if(value!='') value = "<select>"+value+"</select>";
			}
			if(value=='') value = "<input value=''/>";
			if(current.val()!='noselect'){
				$("#condition_table").append('<tr><td><select><option value="and">并且</option><option value="or">或者</option></select></td><td><input type="hidden" value="'+current.val()+'">'+current.text()+'</td><td><select> <option value="eq"> = </option> <option value="ne"> != </option> <option value="lt"> < </option> <option value="le"> <= </option> <option value="gt"> > </option> <option value="ge"> >= </option> <option value="ct">包含</option> <option value="nct">不包含</option> </select></td><td>'+value+'</td><td><a class="icon-close" href="javascript:;"> 删除</a></td></tr>');
					bindEvent();
			}
			$(this).val('');//重置select
		})

		$("#condition_create").on("click",function(){
			var con = '';
			$("#condition_table tr:has(td)").each(function(i){
				var item = $("select,input",$(this));
				var str = item.eq(0).val()+'--'+item.eq(1).val()+'--'+item.eq(2).val()+'--'+item.eq(3).val()+'__';
				if(item.eq(3).val()!='' && con.indexOf(str)==-1)con += str;
			});
			con = con.replace(/__$/g,'');
			if(o.input!=undefined){
				if(con!=''){
					$(o.input).val(con);
					art.dialog({id:'conditionDialog'}).close();
					art.dialog.tips("<p class='success'>筛选条件保存成功！('关系值'为空的选项自动忽略)</p>");
					if(o.callback != undefined) o.callback(con);
				}
				else{
					art.dialog.tips("<p class='warning'>所有【关系值】为空，筛选无效！</p>");
				}
			}
		})
			//操作按钮事件绑定
			function bindEvent(){
			  $("#condition_dialog .icon-close").off();
			    $("#condition_dialog table .icon-close").on("click",function(){
			      $(this).parent().parent().remove();
			    });
			}
			bindEvent();
	};
})(jQuery);

//滑过菜单
//author webning
//version 1.0
function initOperat(){
	$(".operat").each(function(){
	    var operat = $(this);
	    operat.find(".action").on("mouseenter DOMNodeInserted",function(){

	        operat.removeClass("hidden").addClass("show_munu");
	        var offset = operat.offset();
	        var height = operat.find(".action").height();
	        var action_width = operat.find(".action").outerWidth();
	        var menu_select_width = operat.find(".menu_select").outerWidth();

	        if(offset.top+operat.find(".menu_select").height()+height < $(window).height()){
	        	if(offset.left+menu_select_width< $(window).outerWidth()) operat.find(".menu_select").offset({left:offset.left,top:(Math.floor(offset.top)+1+height)});
	        	else operat.find(".menu_select").offset({left:(offset.left+action_width-menu_select_width),top:(Math.floor(offset.top)+1+height)});
	            operat.find(".action").removeClass("top").addClass("bottom");
	        }else{
	            if(offset.left+menu_select_width< $(window).outerWidth()) operat.find(".menu_select").offset({left:offset.left,top:Math.ceil(offset.top)-1-operat.find(".menu_select").height()});
	            else operat.find(".menu_select").offset({left:(offset.left+action_width-menu_select_width),top:Math.ceil(offset.top)-1-operat.find(".menu_select").height()});
	            operat.find(".action").removeClass("bottom").addClass("top");
	        }

	    });
	    operat.on("mouseleave",function(){
	        operat.removeClass("show_munu").addClass("hidden");
	    })
	})
}

(function($){
    $.fn.scrollToTop = function(method){
        var defaults = {
            duration : 300,						// scroll duration
            is_fixed : true,					// if false, all params below will be ignored
            top_blank : 100,				// max height from top
            is_scrollstop : true,			// use scroll stop event or not
            scrollstop_latency : 300,	// latency for scroll stop event
            btm_position : 100				// for absolute position, valid for IE6-
        }
        var settings = {}

        /* publick methods */
        var methods = {
            init : function(options){
                settings = $.extend({}, defaults, options);

                /* special event 'scrollstop' */
                if(settings.is_fixed && settings.is_scrollstop){
                    var uid = 'uid' + (+new Date());
                    $.event.special.scrollstop = {
                        setup: function() {
                            var timer;
                            var handler = function(evt) {
                                var _self = this;
                                var _args = arguments;
                                if (timer) {
                                    clearTimeout(timer);
                                }
                                timer = setTimeout( function(){
                                        timer = null;
                                        evt.type = 'scrollstop';
                                        $.event.handle.apply(_self, _args);
                                }, settings.scrollstop_latency);
                            };
                            $(this).bind('scroll', handler).data(uid, handler);
                        },
                        teardown: function() {
                            $(this).unbind('scroll', $(this).data(uid) );
                        }
                    };
                }

                return this.each(function(){
                    var	$element = $(this),
                        element = this;
                    if(settings.is_fixed){
                        var scrollEvent = settings.is_scrollstop ? 'scrollstop' : 'scroll';
                        $(window).bind(scrollEvent,{elm : $element}, helpers.scrollToTop);
                        $(window).trigger(scrollEvent, [$element]);
                    }
                    $element.click(function(){
                        $('body,html').animate({scrollTop:0},settings.duration);
                        return false;
                    });
                });
            }
        }

        /* private methods */
        var helpers = {
            scrollToTop : function(event, param1){
                var $elm = param1 ? param1 : event.data.elm;
                var winTop = $(window).scrollTop();
                if(winTop > settings.top_blank){
                    //IE6 通过 hack来解决
                    $elm.fadeIn("400");
                }else{
                    $elm.hide();
                }
            }
        }

        if(methods[method]){
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if(typeof method === 'object' || !method){
            return methods.init.apply(this, arguments);
        } else{
            $.error( '[scrollToTop] : Method "' +  method + '" does not exist');
        }
    }
})(jQuery);

//此插件必需结合Tiny系统的框架定义的Ping JQuery插件
(function($){
	//默认参数

	$.fn.Paging = function(options){
		var defaults = {url:null, params:{}, content:'',callback:function(){}};
		var o = {};
		var self = null;
		self = $(this);
		//对最原始模板的处理
		var id = self.attr("id");
		var content = $("#"+id).data("page-content-template");
		if(content){
			defaults.content = content;
		}else{
			defaults.content = self.find(".page-content").html();
			$("#"+id).data("page-content-template",defaults.content);
		}
		o = $.extend(defaults,options);
		getPage(1);

		//内部私有取得第几页
	function getPage(page){
		o.params = $.extend(o.params,{page:page});
		var data = $.data(self, "page_"+page);
		if(data){
			handle(data);
			if(typeof(o.callback)=="function") o.callback();
		}else{
			$.post(o.url,o.params,function(data){
				$.data(self, "page_"+page, data);
				handle(data);
				if(typeof(o.callback)=="function") o.callback();
			},"json");
		}

	}
	//处理数据
	function handle(data){
		if(data['status']=='success'){
				self.find(".page-content").html(rander(data['data']));
				self.find(".page-nav").html(data['html']);
				self.find(".page-nav a").each(function(){
					$(this).on("click",function(){
						var index = parseInt($(this).attr("page-index"));
						getPage(index);
					})
				})
			}
	}
	//数据渲染
	function rander (data){
		var str = '';
		if(typeof data=="object"){
			var num = 1;
			for(var i in data){
				data[i]['odd-even'] = "odd";
				if(num++%2==0)data[i]['odd-even'] = "even";
				str += o.content.replace(/{\s*([^}]+)\s*}/ig,function(s0,s1){
					var tem = s1.split("|");
					if(tem.length==2){
						return data[i][tem[0]] || tem[1];
					}
					else if(tem.length>2){
						if(data[i][tem[0]]) return tem[1];
						else return tem[2];
					}
					return data[i][s1]  || '';
				});
			}
		}
		return str;
	}
	}

})(jQuery);


//倒计时
(function($){
    $.fn.countdown = function(options){
    	var self = this;
    	var defaults = {id:'countdown',end_time:"2020-12-31 23:59:59",format:'{d}天 {h}小时{m}分{s}.{mi}秒',callback:function(){}};
		var o = $.extend(defaults, options);

		function runTime(){
			var endtime=new Date(o.end_time);
			var nowtime = new Date();
			var time = (endtime.getTime()-nowtime.getTime());
			var leftsecond=parseInt(time/1000);
			if(leftsecond<0){leftsecond=0;time=0;}
			__d=parseInt(leftsecond/3600/24);
			__h=parseInt((leftsecond/3600)%24);
			__m=parseInt((leftsecond/60)%60);
			__s=parseInt(leftsecond%60);
			__mi=parseInt(time/100%10);
			__d = __d<10?'0'+__d:__d;
			__h = __h<10?'0'+__h:__h;
			__m = __m<10?'0'+__m:__m;
			__s = __s<10?'0'+__s:__s;
			var date = {d:__d,h:__h,m:__m,s:__s,mi:__mi};
			var str = o.format.replace(/{\s*([^}]+)\s*}/ig,function(s0,s1){return date[s1];});
			self.html(str);
			if(leftsecond>0)setTimeout(runTime,100);
			else{o.callback();}
		}
		runTime();
	}
})(jQuery);

//加载完成后处理事件
$(document).ready(function(){
	initOperat();
	setTimeout(function(){
		$("#message-bar").fadeOut();
	},2000);
});