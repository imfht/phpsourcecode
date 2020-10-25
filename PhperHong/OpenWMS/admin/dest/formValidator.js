//====================================================================================================
// [插件名称] jQuery formValidator
//----------------------------------------------------------------------------------------------------
// [描    述] jQuery formValidator表单验证插件，它是基于jQuery类库，实现了js脚本于页面的分离。对一个表
//            单对象，你只需要写一行代码就可以轻松实现20种以上的脚本控制。现支持一个表单元素累加很多种
//            校验方式,采用配置信息的思想，而不是把信息写在表单元素上，能比较完美的实现ajax请求。
//----------------------------------------------------------------------------------------------------
// [作者网名] 猫冬	
// [邮    箱] wzmaodong@126.com
// [作者博客] http://wzmaodong.cnblogs.com
// [QQ群交流] 74106519
// [更新日期] 2011-04-30
// [版 本 号] ver4.0
//====================================================================================================
(function($) {

$.formValidator = 
{
	//各种校验方式支持的控件类型
	sustainType : function(id,setting)
	{
		var elem = $("#"+id).get(0);
		var srcTag = elem.tagName;
		var stype = elem.type;
		switch(setting.validatetype)
		{
			case "InitValidator":
				return true;
			case "InputValidator":
				return (srcTag == "INPUT" || srcTag == "TEXTAREA" || srcTag == "SELECT");
			case "CompareValidator":
				return ((srcTag == "INPUT" || srcTag == "TEXTAREA") ? (stype != "checkbox" && stype != "radio") : false);
			case "AjaxValidator":
				return (stype == "text" || stype == "textarea" || stype == "file" || stype == "password" || stype == "select-one");
			case "RegexValidator":
				return ((srcTag == "INPUT" || srcTag == "TEXTAREA") ? (stype != "checkbox" && stype != "radio") : false);
			case "FunctionValidator":
			    return true;
		}
	},
    
	//全局配置
	initConfig : function(controlOptions)
	{
		var settings = 
		{
			debug:false,
			validatorgroup : "1",
			alertmessage:false,
			validobjectids:[],
			ajaxobjectids:"",
			forcevalid:false,
			onsuccess: function() {return true;},
			onerror: function() {},
			submitonce:false,
			formid:"",
			autotip: false,
			tidymode:false,
			errorfocus:true,
			wideword:true
		};
		controlOptions = controlOptions || {};
		$.extend(settings, controlOptions);
		//如果是精简模式，发生错误的时候，第一个错误的控件就不获得焦点
		if(settings.tidymode){settings.errorfocus=false};
		if(settings.formid!=""){
							
			$("#"+settings.formid).submit(function(){

				return $.formValidator.pageIsValid(settings.validatorgroup);})};
		$('body').data(settings.validatorgroup, settings);
	},
	
	//如果validator对象对应的element对象的validator属性追加要进行的校验。
	appendValid : function(id, setting )
	{
		//如果是各种校验不支持的类型，就不追加到。返回-1表示没有追加成功
		if(!$.formValidator.sustainType(id,setting)) return -1;
		var srcjo = $("#"+id).get(0);   
		//重新初始化
		if (setting.validatetype=="InitValidator" || srcjo.settings == undefined ){srcjo.settings = new Array();}   
		var len = srcjo.settings.push( setting );
		srcjo.settings[len - 1].index = len - 1;
		return len - 1;
	},

	//触发每个控件上的各种校验
	triggerValidate : function(returnObj)
	{
		
		switch(returnObj.setting.validatetype)
		{
			case "InputValidator":
			
				$.formValidator.inputValid(returnObj);
				break;
			case "CompareValidator":
				$.formValidator.compareValid(returnObj);
				break;
			case "AjaxValidator":
			
				$.formValidator.ajaxValid(returnObj);
				break;
			case "RegexValidator":
				$.formValidator.regexValid(returnObj);
				break;
			case "FunctionValidator":
				$.formValidator.functionValid(returnObj);
				break;
		}
	},
	
	//设置显示信息
	setTipState : function(elem,showclass,showmsg)
	{
		var setting0 = elem.settings[0];
		var initConfig = $('body').data(setting0.validatorgroup);
	    var tip = $("#"+setting0.tipid);
		if(showmsg==null || showmsg=="")
		{
			tip.hide();
		}
		else
		{
			if(initConfig.tidymode)
			{
				//显示和保存提示信息
				$("#fv_content").html(showmsg);
				elem.Tooltip = showmsg;
				if(showclass!="onError"){tip.hide();}
			}
			else
			{
				tip.show().removeClass().addClass( showclass ).html( showmsg );
			}
		}
	},
		
	//把提示层重置成原始提示
	resetTipState : function(validatorgroup)
	{
		var initConfig = $('body').data(validatorgroup);
		$.each(initConfig.validobjectids,function(){
			$.formValidator.setTipState(this,"onShow",this.settings[0].onshow);	
		});
	},
	
	//设置错误的显示信息
	setFailState : function(tipid,showmsg)
	{
	    var tip = $("#"+tipid);
	    tip.removeClass().addClass("onError").html(showmsg);
	},

	//根据单个对象,正确:正确提示,错误:错误提示
	showMessage : function(returnObj)
	{
		
	    var id = returnObj.id;
		var elem = $("#"+id).get(0);
		var isvalid = returnObj.isvalid;
		var setting = returnObj.setting;//正确:setting[0],错误:对应的setting[i]
		
		var showmsg = "",showclass = "";
		var settings = $("#"+id).get(0).settings;
		var intiConfig = $('body').data(settings[0].validatorgroup);
		if (!isvalid)
		{		
			showclass = "onError";
			if(setting.validatetype=="AjaxValidator")
			{
				if(setting.lastValid=="")
				{
				    showclass = "onLoad";
				    showmsg = setting.onwait;
				}
				else
				{
				    showmsg = setting.onerror;
				}
			}
			else
			{
				
				showmsg = (returnObj.errormsg==""? setting.onerror : returnObj.errormsg);
				
			}
			if(intiConfig.alertmessage)		
			{
				var elem = $("#"+id).get(0);
				if(elem.validoldvalue!=$(elem).val()){alert(showmsg);}   
			}
			else
			{
				$.formValidator.setTipState(elem,showclass,showmsg);
			}
		}
		else
		{		
			//验证成功后,如果没有设置成功提示信息,则给出默认提示,否则给出自定义提示;允许为空,值为空的提示
			showmsg = $.formValidator.isEmpty(id) ? setting.onempty : setting.oncorrect;
			//alert(setting.oncorrect)
			$.formValidator.setTipState(elem,"onCorrect",showmsg);
		}
		return showmsg;
	},
	//取消验证某个控件
	CancelCheck:function(id,message){
		var returnObj = $.formValidator.oneIsValid(id);
		var setting = returnObj.setting;
		returnObj.isvalid = true;
		setting.oncorrect = message
		$.formValidator.showMessage(returnObj)
	},
	showAjaxMessage : function(returnObj)
	{
		
		var setting = returnObj.setting;
		var elem = $("#"+returnObj.id).get(0);
		
		if(elem.validoldvalue!=$(elem).val())
		{
			$.formValidator.ajaxValid(returnObj);
		}
		else
		{
			$.formValidator.ajaxValid(returnObj);
			//if(setting.isvalid!=undefined && !setting.isvalid){
			//	elem.lastshowclass = "onError"; 
			//	elem.lastshowmsg = setting.onerror;
			//}
			//$.formValidator.setTipState(elem,elem.lastshowclass,elem.lastshowmsg);
		}
	},

	//获取指定字符串的长度
    getLength : function(id)
    {
        var srcjo = $("#"+id);
		var elem = srcjo.get(0);
        sType = elem.type;
        var len = 0;
        switch(sType)
		{
			case "text":
			case "hidden":
			case "password":
			case "textarea":
			case "file":
		        var val = srcjo.val();
				var initConfig = $('body').data(elem.settings[0].validatorgroup);
				if (initConfig.wideword)
				{
					for (var i = 0; i < val.length; i++) 
					{
						len = len + ((val.charCodeAt(i) >= 0x4e00 && val.charCodeAt(i) <= 0x9fa5) ? 2 : 1); 
						/*
						if (val.charCodeAt(i) >= 0x4e00 && val.charCodeAt(i) <= 0x9fa5)
							len = len + 2;
						else
							len = len + 1;
							*/
					}
				}
				else{
					len = val.length;
				}
		        break;
			case "checkbox":
			case "radio": 
				len = $("input[type='"+sType+"'][name='"+srcjo.attr("name")+"']:checked").length;
				break;
		    case "select-one":
		        len = elem.options ? elem.options.selectedIndex : -1;
				break;
			case "select-multiple":
				len = $("select[name="+elem.name+"] option:selected").length;
				break;
	    }
		return len;
    },
    
	//结合empty这个属性，判断仅仅是否为空的校验情况。
    isEmpty : function(id)
    {
        return ($("#"+id).get(0).settings[0].empty && $.formValidator.getLength(id)==0);
    },
    
	//对外调用：判断单个表单元素是否验证通过，不带回调函数
    isOneValid : function(id)
    {
	    return $.formValidator.oneIsValid(id).isvalid;
    },
    
	//验证单个是否验证通过,正确返回settings[0],错误返回对应的settings[i]
	oneIsValid : function (id)
	{
		
		var returnObj = new Object();
		returnObj.id = id;
		returnObj.ajax = -1;
		returnObj.errormsg = "";       //自定义错误信息
		var elem = $("#"+id).get(0);
	    var settings = elem.settings;
	    var settingslen = settings.length;
		//只有一个formValidator的时候不检验
		if (settingslen==1){settings[0].bind=false;}
		if(!settings[0].bind){return null;}
		for ( var i = 0 ; i < settingslen ; i ++ )
		{   
			if(i==0){
				if($.formValidator.isEmpty(id)){
					returnObj.isvalid = true;
					returnObj.setting = settings[0];
					break;
				}
				continue;
			}
			returnObj.setting = settings[i];
			if(settings[i].validatetype!="AjaxValidator") {
				$.formValidator.triggerValidate(returnObj);
			}else{
				returnObj.ajax = i;
			}
			if(!settings[i].isvalid) {
				
				returnObj.isvalid = false;
				returnObj.setting = settings[i];
				break;
			}else{
				returnObj.isvalid = true;
				returnObj.setting = settings[0];
				if(settings[i].validatetype=="AjaxValidator") break;
			}
		}
		return returnObj;
	},

	//验证所有需要验证的对象，并返回是否验证成功。
	pageIsValid : function (validatorgroup)
	{
	    if(validatorgroup == null || validatorgroup == undefined){validatorgroup = "1"};
		var isvalid = true;
		var returnObj;
		var error_tip = "^",thefirstid,name,name_list="^"; 	
		var errorlist=new Array();
		var initConfig = $('body').data(validatorgroup);
		$.each(initConfig.validobjectids,function()
		{
			//只校验绑定的控件
			if(this.settings[0].bind){
				name = this.name;
				//name相同的只校验一次
				if (name_list.indexOf("^"+name+"^") == -1) {
					if(name) name_list = name_list + name + "^";
					returnObj = $.formValidator.oneIsValid(this.id);
					if (returnObj) {
						//校验失败,获取第一个发生错误的信息和ID
						if (!returnObj.isvalid) {
							isvalid = false;
							if (thefirstid == null) thefirstid = returnObj.id;
							errorlist[errorlist.length] = returnObj.errormsg == "" ? returnObj.setting.onerror : returnObj.errormsg;
						}
						//为了解决使用同个TIP提示问题:后面的成功或失败都不覆盖前面的失败
						if (!initConfig.alertmessage) {
							var tipid = this.settings[0].tipid;
							if (error_tip.indexOf("^" + tipid + "^") == -1) {
								if (!returnObj.isvalid) {
									error_tip = error_tip + tipid + "^";
								}
								$.formValidator.showMessage(returnObj);
							}
						}
					}
				}
			}
		});
		
		//成功或失败后，进行回调函数的处理，以及成功后的灰掉提交按钮的功能
		if(isvalid)
		{
            isvalid = initConfig.onsuccess();
			if(initConfig.submitonce){$(":submit,:button").attr("disabled",true);}
		}
		else
		{
			var obj = $("#"+thefirstid).get(0);
			initConfig.onerror(errorlist[0],obj,errorlist);
			if(thefirstid!="" && initConfig.errorfocus){$("#"+thefirstid).focus();}
		}
		return !initConfig.debug && isvalid;
	},

	//ajax校验
	ajaxValid : function(returnObj)
	{
		
		var id = returnObj.id;
	    var srcjo = $("#"+id);
		var elem = srcjo.get(0);
		var settings = elem.settings;
		var setting = settings[returnObj.ajax];
		var ls_url = setting.url;
	    if (srcjo.size() == 0 && settings[0].empty) {
			returnObj.setting = settings[0];
			returnObj.isvalid = true;
			$.formValidator.showMessage(returnObj);
			setting.isvalid = true;
			return;
		}  
		
		//获取要传递的参数
		var initConfig = $('body').data(settings[0].validatorgroup);
		var parm = $.param($(initConfig.ajaxobjectids).serializeArray());
		//parm = "clientid=" + id + (parm.length > 0 ? "&" + parm : "");
		parm = id+"=" + $("#"+id).val() + (parm.length > 0 ? "&" + parm : "");
		ls_url = ls_url + (ls_url.indexOf("?") > -1 ? ("&" + parm) : ("?" + parm));
		//发送ajax请求
		$.ajax(
		{	
			type : setting.type, 
			url : ls_url, 
			cache : setting.cache,
			data : setting.data, 
			async : setting.async, 
			timeout : setting.timeout, 
			dataType : setting.datatype, 
			success : function(data, textStatus, XMLHttpRequest){
				
			    if(setting.success(data, textStatus, XMLHttpRequest))
			    {
			        $.formValidator.setTipState(elem,"onCorrect",settings[0].oncorrect);
			        setting.isvalid = true;
			    }
			    else
			    {
			        $.formValidator.setTipState(elem,"onError",setting.onerror);
			        setting.isvalid = false;
			    }
			},
			complete : function(XMLHttpRequest, textStatus){
				if(setting.buttons && setting.buttons.length > 0){setting.buttons.attr({"disabled":false})};
				setting.complete(XMLHttpRequest, textStatus);
			}, 
			beforeSend : function(XMLHttpRequest){
				//再服务器没有返回数据之前，先回调提交按钮
				if(setting.buttons && setting.buttons.length > 0){setting.buttons.attr({"disabled":true})};
				var isvalid = setting.beforesend(XMLHttpRequest);
				if(isvalid)
				{
					setting.isvalid = false;		//如果前面ajax请求成功了，再次请求之前先当作错误处理
					$.formValidator.setTipState(elem,"onLoad",settings[returnObj.ajax].onwait);
				}
				setting.lastValid = "-1";
				return isvalid;
			}, 
			error : function(XMLHttpRequest, textStatus, errorThrown){
			    $.formValidator.setTipState(elem,"onError",setting.onerror);
			    setting.isvalid = false;
				setting.error(XMLHttpRequest, textStatus, errorThrown);
			},
			processData : setting.processdata 
		});
	},

	//对正则表达式进行校验（目前只针对input和textarea）
	regexValid : function(returnObj)
	{
		var id = returnObj.id;
		var setting = returnObj.setting;
		var srcTag = $("#"+id).get(0).tagName;
		var elem = $("#"+id).get(0);
		var isvalid;
		//如果有输入正则表达式，就进行表达式校验
		if(elem.settings[0].empty && elem.value==""){
			setting.isvalid = true;
		}
		else 
		{
		   
			var regexpresslist;
			setting.isvalid = false;
			if(typeof(setting.regexp)=="string") 
			{regexpresslist = [setting.regexp];}
			else
			{regexpresslist = setting.regexp;}
			
			$.each(regexpresslist, function(i,val) {
			    var r = val;				
			    if(setting.datatype=="enum"){r = eval("regexEnum."+r);}			
			    if(r==undefined || r=="") 
			    {
			        return true;
			    }
				
			    isvalid = $("#"+id).val().match(r);
			    
			    if(setting.comparetype=="||" && isvalid)
			    {
					
			        setting.isvalid = true;
			        return true;
			    }
			    if(setting.comparetype=="&&" && !isvalid) 
			    {
			        return true
			    }
            });
            if(!setting.isvalid) setting.isvalid = isvalid;
		}
	},
	
	//函数校验。返回true/false表示校验是否成功;返回字符串表示错误信息，校验失败;如果没有返回值表示处理函数，校验成功
	functionValid : function(returnObj)
	{
		var id = returnObj.id;
		var setting = returnObj.setting;
	    var srcjo = $("#"+id);
		var lb_ret = setting.fun(srcjo.val(),srcjo.get(0));
		if(lb_ret != undefined) 
		{
			if(typeof(lb_ret) === "string"){
				setting.isvalid = false;
				returnObj.errormsg = lb_ret;
			}else{
				setting.isvalid = lb_ret;
			}
		}
	},
	
	//对input和select类型控件进行校验
	inputValid : function(returnObj)
	{
		var id = returnObj.id;
		var setting = returnObj.setting;
		var srcjo = $("#"+id);
		var elem = srcjo.get(0);
		var val = srcjo.val();
		
		var sType = elem.type;
		var len = $.formValidator.getLength(id);
		var empty = setting.empty,emptyerror = false;
		switch(sType)
		{
			case "text":
			case "hidden":
			case "password":
			case "textarea":
			case "file":
				if (setting.type == "size") {
					empty = setting.empty;
					if(!empty.leftempty){
						emptyerror = (val.replace(/^[ \s]+/, '').length != val.length);
					}
					if(!emptyerror && !empty.rightempty){
						emptyerror = (val.replace(/[ \s]+$/, '').length != val.length);
					}
					if(emptyerror && empty.emptyerror){returnObj.errormsg= empty.emptyerror}
				}
			case "checkbox":
			case "select-one":
			case "select-multiple":
			case "radio":
				
				var lb_go_on = false;
				if(sType=="select-one" || sType=="select-multiple"){setting.type = "size";}
				var type = setting.type;
				if (type == "size") {		//获得输入的字符长度，并进行校验
					if(!emptyerror){lb_go_on = true}
					if(lb_go_on){val = len}
			
				}
				else if (type =="date" || type =="datetime")
				{
					var isok = false;
					if(type=="date"){lb_go_on = isDate(val)};
					if(type=="datetime"){lb_go_on = isDate(val)};
					if(lb_go_on){val = new Date(val);setting.min=new Date(setting.min);setting.max=new Date(setting.max);};
					
				}else{
					stype = (typeof setting.min);
				
					if(stype =="number")
					{
						
						val = (new Number(val)).valueOf();
						if(!isNaN(val)){lb_go_on = true;}
					}
					if(stype =="string"){lb_go_on = true;}
				}
				setting.isvalid = false;
				
				if(lb_go_on)
				{
				
					if(val < setting.min || val > setting.max){
							
						if(val < setting.min && setting.onerrormin){
							returnObj.errormsg= setting.onerrormin;
						}
						if(val > setting.min && setting.onerrormax){
							returnObj.errormsg= setting.onerrormax;
						}
					}
					else{
						setting.isvalid = true;

					}
				}
				break;
		}
	},
	
	//对两个控件进行比较校验
	compareValid : function(returnObj)
	{
		var id = returnObj.id;
		var setting = returnObj.setting;
		var srcjo = $("#"+id);
	    var desjo = $("#"+setting.desid );
		var ls_datatype = setting.datatype;
		
		curvalue = srcjo.val();
		ls_data = desjo.val();
		if(ls_datatype=="number")
        {
            if(!isNaN(curvalue) && !isNaN(ls_data)){
				curvalue = parseFloat(curvalue);
                ls_data = parseFloat(ls_data);
			}
			else{
			    return;
			}
        }
		if(ls_datatype=="date" || ls_datatype=="datetime")
		{
			var isok = false;
			if(ls_datatype=="date"){isok = (isDate(curvalue) && isDate(ls_data))};
			if(ls_datatype=="datetime"){isok = (isDateTime(curvalue) && isDateTime(ls_data))};
			if(isok){
				curvalue = new Date(curvalue);
				ls_data = new Date(ls_data)
			}
			else{
				return;
			}
		}
		
	    switch(setting.operateor)
	    {
	        case "=":
	            setting.isvalid = (curvalue == ls_data);
	            break;
	        case "!=":
	            setting.isvalid = (curvalue != ls_data);
	            break;
	        case ">":
	            setting.isvalid = (curvalue > ls_data);
	            break;
	        case ">=":
	            setting.isvalid = (curvalue >= ls_data);
	            break;
	        case "<": 
	            setting.isvalid = (curvalue < ls_data);
	            break;
	        case "<=":
	            setting.isvalid = (curvalue <= ls_data);
	            break;
			default :
				setting.isvalid = false;
				break; 
	    }
	},
	
	//定位漂浮层
	localTooltip : function(e)
	{
		e = e || window.event;
		var mouseX = e.pageX || (e.clientX ? e.clientX + document.body.scrollLeft : 0);
		var mouseY = e.pageY || (e.clientY ? e.clientY + document.body.scrollTop : 0);
		$("#fvtt").css({"top":(mouseY+2)+"px","left":(mouseX-40)+"px"});
	},
	
	reloadAutoTip : function(validatorgroup)
	{
		if(validatorgroup == undefined) validatorgroup = "1";
		var initConfig = $('body').data(validatorgroup);
		var jqObjs = $();
		$.each(initConfig.validobjectids,function()
		{
			var settings = this.settings;
			if(initConfig.autotip)
			{
				if(!initConfig.tidymode)
				{
					//获取层的ID、相对定位控件的ID和坐标
					var setting = settings[0];
					var afterid = "#"+setting.afterid;
					var offset = $(afterid ).offset();
					var y = offset.top;
					var x = $(afterid ).width() + offset.left;
					$("#"+setting.tipid).parent().css({left: x+"px", top: y+"px"});			
				}
			}
		});
	}
};

//每个校验控件必须初始化的
$.fn.formValidator = function(cs) 
{
	var setting = 
	{
		validatorgroup : "1",
		empty :false,
		automodify : false,
		onshow :"请输入内容",
		onfocus: "请输入内容",
		oncorrect: "输入正确",
		onempty: "输入内容为空",
		defaultvalue : null,
		bind : true,
		ajax : true,
		validatetype : "InitValidator",
		tipcss : 
		{
			"left" : "10px",
			"top" : "1px",
			"height" : "20px",
			"width":"250px"
		},
		triggerevent:"blur",
		forcevalid : false,
		tipid : null,
		afterid : null
	};

	//获取该校验组的全局配置信息
	cs = cs || {};
	if(cs.validatorgroup == undefined){cs.validatorgroup = "1"};
	var initConfig = $('body').data(cs.validatorgroup);

	//如果为精简模式，tipcss要重新设置初始值
	if(initConfig.tidymode){setting.tipcss = {"left" : "2px","width":"22px","height":"22px","display":"none"}};
	
	//先合并整个配置(深度拷贝)
	$.extend(true,setting, cs);

	return this.each(function(e)
	{
		var jqobj = $(this);
		//自动形成TIP
		var setting_temp = {};
		$.extend(true,setting_temp, setting);
		var tip = setting_temp.tipid ? setting_temp.tipid : this.id+"Tip";
		if(initConfig.autotip)
		{
			if(!initConfig.tidymode)
			{				
				//获取层的ID、相对定位控件的ID和坐标
				if($("body [id="+tip+"]").length==0)
				{		
					var afterid = setting_temp.relativeid ? setting_temp.relativeid : this.id;
					var offset = $("#"+afterid ).position();
					var y = offset.top;
					var x = $("#"+afterid ).width() + offset.left;
					$("<div class='formValidateTip'></div>").appendTo($("body")).css({left: x+"px", top: y+"px"}).prepend($('<div id="'+tip+'"></div>').css(setting_temp.tipcss));
					setting.afterid = afterid ;
				}
			}
			else
			{
				jqobj.showTooltips();
			}
		}
		//每个控件都要保存这个配置信息
		setting.tipid = tip;
		$.formValidator.appendValid(this.id,setting);

		//保存控件ID
		if($.inArray(jqobj,initConfig.validobjectids) == -1)
		{
			if (setting_temp.ajax) {
				var ajax = initConfig.ajaxobjectids;
				initConfig.ajaxobjectids = ajax + (ajax != "" ? ",#" : "#") + this.id;
			}
			initConfig.validobjectids.push(this);
		}

		//初始化显示信息
		if(!initConfig.alertmessage){
			$.formValidator.setTipState(this,"onShow",setting.onshow);
		}

		var srcTag = this.tagName.toLowerCase();
		var stype = this.type;
		var defaultval = setting.defaultvalue;
		//处理默认值
		if(defaultval){
			jqobj.val(defaultval);
		}

		if(srcTag == "input" || srcTag=="textarea")
		{
			//注册获得焦点的事件。改变提示对象的文字和样式，保存原值
			jqobj.focus(function()
			{	
				if(!initConfig.alertmessage){
					//保存原来的状态
					var tipjq = $("#"+tip);
					this.lastshowclass = tipjq.attr("class");
					this.lastshowmsg = tipjq.html();
					$.formValidator.setTipState(this,"onFocus",setting.onfocus);
				}
				if (stype == "password" || stype == "text" || stype == "textarea" || stype == "file") {
					this.validoldvalue = jqobj.val();
				}
			});
			//注册失去焦点的事件。进行校验，改变提示对象的文字和样式；出错就提示处理
			jqobj.bind(setting.triggerevent, function(){
				var settings = this.settings;
				var returnObj = $.formValidator.oneIsValid(this.id);
				if(returnObj==null){return;}
				if(returnObj.ajax >= 0) 
				{
					$.formValidator.showAjaxMessage(returnObj);
				}
				else
				{
					var showmsg = $.formValidator.showMessage(returnObj);
					if(!returnObj.isvalid)
					{
						//自动修正错误
						var auto = setting.automodify && (this.type=="text" || this.type=="textarea" || this.type=="file");
						if(auto && !initConfig.alertmessage)
						{
							alert(showmsg);
							$(this).val(this.validoldvalue);
							$.formValidator.setTipState(this,"onShow",setting.onshow);
						}
						else
						{
							if(initConfig.forcevalid || setting.forcevalid){
								alert(showmsg);this.focus();
							}
						}
					}
				}
			});
		} 
		else if (srcTag == "select")
		{
			jqobj.bind({
				//获得焦点
				focus : function(){	
					if (!initConfig.alertmessage) {
						$.formValidator.setTipState(this, "onFocus", setting.onfocus)
					};
				},
				//失去焦点
				blur : function(){jqobj.trigger("change")},
				//选择项目后触发
				change : function(){
					var returnObj = $.formValidator.oneIsValid(this.id);	
					if(returnObj==null){return;}
					if ( returnObj.ajax >= 0){
						$.formValidator.showAjaxMessage(returnObj);
					}else{
						$.formValidator.showMessage(returnObj); 
					}
				}
			});
		}
	});
}; 

$.fn.inputValidator = function(controlOptions)
{
	var settings = 
	{
		isvalid : false,
		min : 0,
		max : 99999999999999,
		type : "size",
		onerror:"输入错误",
		validatetype:"InputValidator",
		empty:{leftempty:true,rightempty:true,leftemptyerror:null,rightemptyerror:null},
		wideword:true
	};
	controlOptions = controlOptions || {};
	$.extend(true, settings, controlOptions);
	return this.each(function(){
		$.formValidator.appendValid(this.id,settings);
	});
};

$.fn.compareValidator = function(controlOptions)
{
	var settings = 
	{
		isvalid : false,
		desid : "",
		operateor :"=",
		onerror:"输入错误",
		validatetype:"CompareValidator"
	};
	controlOptions = controlOptions || {};
	$.extend(true, settings, controlOptions);
	return this.each(function(){
		$.formValidator.appendValid(this.id,settings);
	});
};

$.fn.regexValidator = function(controlOptions)
{
	var settings = 
	{
		isvalid : false,
		regexp : "",
		param : "i",
		datatype : "string",
		comparetype : "||",
		onerror:"输入的格式不正确",
		validatetype:"RegexValidator"
	};
	controlOptions = controlOptions || {};
	$.extend(true, settings, controlOptions);
	return this.each(function(){
		$.formValidator.appendValid(this.id,settings);
	});
};

$.fn.functionValidator = function(controlOptions)
{
	var settings = 
	{
		isvalid : true,
		fun : function(){this.isvalid = true;},
		validatetype:"FunctionValidator",
		onerror:"输入错误"
	};
	controlOptions = controlOptions || {};
	$.extend(true, settings, controlOptions);
	return this.each(function(){
		$.formValidator.appendValid(this.id,settings);
	});
};

$.fn.ajaxValidator = function(controlOptions)
{
	var settings = 
	{
		isvalid : false,
		lastValid : "",
		type : "GET",
		url : "",
		datatype : "html",
		timeout : 999,
		data : "",
		async : false,
		cache : false,
		beforesend : function(){return true;},
		success : function(){return true;},
		complete : function(){},
		processdata : false,
		error : function(){},
		buttons : null,
		onerror:"服务器校验没有通过",
		onwait:"正在等待服务器返回数据",
		validatetype:"AjaxValidator"
	};
	
	controlOptions = controlOptions || {};
	$.extend(true, settings, controlOptions);
	return this.each(function()
	{
		$.formValidator.appendValid(this.id,settings);
	});
};

//指定控件显示通过或不通过样式
$.fn.defaultPassed = function(onshow)
{
	return this.each(function()
	{
		var settings = this.settings;
		for ( var i = 1 ; i < settings.length ; i ++ )
		{   
			settings[i].isvalid = true;
			if(!$('body').data(settings[0].validatorgroup).alertmessage){
				var ls_style = onshow ? "onShow" : "onCorrect";
				$.formValidator.setTipState(this,ls_style,settings[0].oncorrect);
			}
		}
	});
};

//指定控件不参加校验
$.fn.unFormValidator = function(unbind)
{
	return this.each(function()
	{
		this.settings[0].bind = !unbind;
		if(unbind){
			$("#"+this.settings[0].tipid).hide();
		}else{
			$("#"+this.settings[0].tipid).show();
		}
	});
};

//显示漂浮显示层
$.fn.showTooltips = function()
{
	if($("body [id=fvtt]").length==0){
		fvtt = $("<div id='fvtt' style='position:absolute;z-index:56002'></div>");
		$("body").append(fvtt);
		fvtt.before("<iframe src='about:blank' class='fv_iframe' scrolling='no' frameborder='0'></iframe>");
		
	}
	return this.each(function()
	{
		jqobj = $(this);
		s = $("<span class='top' id=fv_content style='display:block'></span>");
		b = $("<b class='bottom' style='display:block' />");
		this.tooltip = $("<span class='fv_tooltip' style='display:block'></span>").append(s).append(b).css({"filter":"alpha(opacity:95)","KHTMLOpacity":"0.95","MozOpacity":"0.95","opacity":"0.95"});
		//注册事件
		jqobj.bind({
			mouseover : function(e){
				$("#fvtt").append(this.tooltip);
				$("#fv_content").html(this.Tooltip);
				$.formValidator.localTooltip(e);
			},
			mouseout : function(){
				$("#fvtt").empty();
			},
			mousemove: function(e){
				$("#fv_content").html(this.Tooltip);
				$.formValidator.localTooltip(e);
			}
		});
	});
}

})(jQuery);