/*----------------------------------------------------------------------------|
|  Subject:       JavaScript validation API,Form auto Validation API
|  Version:       1.0
|  Author:        Sunarrow
|  Created:       2007-11-8
|  LastModified:  2008-1-20
|  Download:      http://code.google.com/p/cwin/downloads/list
|  License:       Apache License 2.0
|-----------------------------------------------------------------------------|
|  Email:sunkeei@yahoo.com
|-----------------------------------------------------------------------------|
|功能1.表单字段验证
|      方法签名：validElement(element).
|      功能：如果验证失败，返回false。显示在页面或跳出错误信息。
|      参数说明：element:HTML元素，在页面上调用此方法时请使用 'this'调用。
|                limit:验证条件串：如：'type:float;required:true;decLen:2'
|                      limit的属性可以有: type,required,len,between,decLen,
|                                         equals,general
|                      属性说明:
|						 type:string,int,digit,float,email,ip,url,date,datetime,time
|                             tel,fax,mobileCn,idCard,signName,name,postcodeCn
|                             default: string.
|                        required:可选值有true和false.default:false.
|                        len:字符串长度,值为 "6-20",表示长度在6和20之间。也可
|                             以为"-20"，表示不超过20。
|						 between:数值有效,在两个值之间。可为 "10-100"，表示大
|                                小在10与00之间，也可为"-100",表示小于100。
|                        decLen:浮点型精度长度.若不符合精度将会自动纠正.
|                        equals:是否要求与其它元素相等。用于密码确认等场合.
|                        general:是否是一般字符。不包括特殊字符.Default:true.
						 than:与指定对象比较日期值，如果大于return true.
|                msgArea:显示错误信息的SPAN或DIV的ID。如果传入为空，则会查找
|                        global_error_msg_area 的SPAN或DIV，如果也为空，则会
|                        alert 这些错误信息。
|                msg:错误信息，如果这个参数为""，则会打出默认的错误信息.
|功能2.表单自动验证:
|      方法签名：checkForm(form,isCheckAll)
|      isCheckAll:是否检查所有的元素：如为False,验证会在第一个错误出现时退出，否则，会验证所有的元素
|      功能：验证表单中所有需要验证的字段.失败返回false.显示或跳出错误信息.
|      使用条件：需要验证的元素需要定义至少 limit 属性，程序会自动搜索这个表单中所有需要验证的元素。
|提示:如果您需要把错误信息显示在某一个单独的地方，可以定义一个ID为 global_error_msg_area 的DIV或是SPAN
|   　表单的验证将默认限制特殊字符，可以加入 general:false 来取消限制
\*---------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------*\
|  Subject:       JavaScript validation API
|验证API
|验证函数列表：
|
|judgeDigit(arguments...) :判断是否数字
|三种调用方式:
|1.一个参数,简单判断是否为数字,但长度不超过10位
|2.三个参数,第二个参数为 '>'(大于) 或 '<'(小于),第3个参数为要比较的数字
|3.三个参数，第二个参数与第三个参数均为整数，判断传入的第一个参数值是否在他们中间.(含边界)
|
|judgeFloat(arguments...):浮点数
|如果是一个参数，那么判断是否为浮点数
|如果有两个参数，那么将第二个参数作为精度限定参数
|如果有三个参数，那么第二个参数为最小值，第三个参数是将作为数值上限
|
|isDigit(str):是否数字
|
|isSignName(arguments...)
|两种调用方式：
|一个参数：(默认为2--32位)，判断标识符或是登录名
|判断标识符或是登录名，以字母开头,可带数字、"_"、"." 的字串
|三个参数：
|限定最小长度(第二个参数)与最大长度(第三个参数)
|
|isRealName(str) :判断是否是真实姓名
|isTel(str) :电话号码:除数字外，可含有"-".校验普通电话，除数字外，可用"-"或空格分开
|isMobileCN(s) :中国大陆地区手机号码,以13或15开头，使用时请根据变化修改,
|isPostalCodeCN(s):中国地区邮编
|isEmail(s) :E-mail
|isURL(s) :URL
|isIP(s):IP-32
|isHtmlTag(s):HTML Tag
|isIDNumber15(s):身份证号15位
|isIDNumber18(s):身份证号18位
|isChineseString(s):中文字符
|isDoubleByteString(s):双字节
|hasHESpace(s):是否包含首尾空格，如果包含，返回TRUE
|isQQ(s):QQ
|isFloat(s):是否是浮点数
|isLeapYear(y):是否闰年
|isDateYMD(s):日期：yyyy-mm-dd 或 yyyy/mm/dd,支持1600年以后(包含闰年验证)
|isDateDMY(s):日期：dd-mm-yyyy 或 dd/mm/yyyy,支持1600年以后(包含闰年验证)
|isDateMDY(s):日期：mm/dd/yyyy 或 mm-dd-yyyy,支持1600年以后(包含闰年验证)
|isDateTimeYMD(s):日期：yyyy/mm/dd hh:mm:ss 或 yyyy-mm-dd hh:mm:ss,支持1600年以后(包含闰年验证)
|
|containsSpecialChar()
|是否包含非特殊字符(正常字符包括字母数字,下划线,和点号,空格,@#$% 和双字节)若包含,返回true
|
|以下方法遵守这样的调用法则:
|1.一个参数，不限制长度
|2.三个参数，第二个参数表示允许的最小长度，第三个参数表示允许的最大长度
|
|isDigitString():数字
|isLetter():字母
|isUpperLetter():大写字母
|isLowerLetter():小写字母
|isLetterNumString():字母与数字
|isLNUString() :数字，字母，下划线字符串
|
|兼容性:在IE6.0与Firefox2.0下测试通过。
|License:Apache license2.0.请在使用此代码时包含license与作者信息.
\*-----------------------------------------------------------------------------------------*/
var global_formjs_valid_flag = false;		//全局的是否错误的变量
var error_msg_span = null; //错误显示的SPAN或是DIV

/***
* 检查表单所有元素
* @param form 表单
* @param checkAll 是否检查所有元素(如为false则会在第一个错误出现就会退出)否则将检查所有元素
* **/
function checkForm(form,checkAll){
	error_msg_span = document.getElementById("global_error_msg_area");
	//clear err msg
	if (error_msg_span != undefined && error_msg_span != null) {
		error_msg_span.innerHTML = "";
	}
    
	var eles = form.elements;
 	var hasError = false;
	//keke_valid(eles[3]);
	//return false;
    //遍历所有表元素
 	for(var i=0;i<eles.length;i++){
 		if(typeof(eles[i]).type != 'undefined'){
	 		var eleType = eles[i].type.toLowerCase();
	 		if(eleType!='button'&&eleType!='sbumit' &&eleType!='application/x-shockwave-flash' && typeof eleType != 'undefined'){
	        //取出元素declare
	 		var ignore= eles[i].getAttribute("ignore");
		 		if(ignore==null||ignore!='true'){
					var limit = eles[i].getAttribute("limit");
					if(limit != null && limit != ""){
					var ajax  = eles[i].getAttribute("ajax");
					var valid = 0;
			 			valid = parseInt(eles[i].getAttribute("valid"));
			 			valid==1?valid=1:(valid==2?valid=2:'');
			 			ajax==null?valid=2:'';
						if(checkAll){
							validElement(eles[i]);
							if(!global_formjs_valid_flag){
								hasError = true;
							}
						}else{
							if(!validElement(eles[i])||valid==1){
								//eles[i].type!='checkbox'?eles[i].focus():'';
								return false;
							}
						}
					}
		 		}
	 		}
 		}
	}
	
	return !hasError;
}
function test_hidden(ele){
	var test_a = 0;
	var test = $(ele).parents();
	$(test).each(function(){
		
		if(this.style.display=='none'){
			test_a = 1; 
		} 
	})
	return test_a;
}

/**
*出现错误返回FALSE
*/
function validElement(ele){
	
	
    //隐藏标签不验收，直接返回通过
	var a = test_hidden(ele);

	if(a==1){
	
		 return true;
	}
	
	error_msg_span = document.getElementById("global_error_msg_area");
	//属性检查
	var limit = ele.getAttribute("limit");
	if(limit == null || trim(limit) == "") return;
	limit = trim(limit);
	var msgSpan = ele.getAttribute("msgArea");
	if(msgSpan != null) msgSpan = trim(msgSpan);
	var errMsg = ele.getAttribute("msg");
	if(errMsg != null) errMsg = trim(errMsg);
	//全局变量
	global_formjs_valid_flag = false;
	//preparing----
	var form = ele.form;
	var formName = form.name;
	//alert(ele.form.name);
	if(msgSpan != null || msgSpan != ""){
		msgSpan = document.getElementById(msgSpan);
		if(msgSpan == null){
			msgSpan = error_msg_span;
		}
	}else{
		msgSpan = error_msg_span;
	}

	/*if(msgSpan != undefined && msgSpan != null){
		msgSpan.innerHTML = "";
	}*/
	//设置错误信息函数
	var setErrMessage = function(ele,error_msg){
		errMsg = (errMsg == null || errMsg == "") ? ele.name+" input error:" + error_msg : errMsg;
		if(msgSpan !=undefined  && msgSpan != null){
			msgSpan.innerHTML = "";
			//msgSpan.setAttribute('class','msg msg_error');
			msgSpan.className ="msg msg_error";
			msgSpan.innerHTML = '<i></i><span>'+errMsg+'</span>';
		}else{
			//showDialog(errMsg,'alert','tips');
			alert(errMsg);
		}
		 
		return false;
	};
	// prepared....
	//错误信息处理完毕
	//拆分limit信息，提取最重要信息之 -- 是否必须与类型
	var vtype = "string";			//值类型
	var required = false;	//是否必须
	var general = false;		//是否是一般字符串(不允许包含特殊字符)
	var lims = limit.split(";");		//限制列表
	var ii;
	for(ii = 0;ii<lims.length;ii++){
		if(lims[ii].indexOf(":")>0){
			var alim = lims[ii].split(":");
			if(alim[0] == 'type'){
				vtype = alim[1];
			}else if(alim[0] == 'required'){
				required = alim[1] == "true";
			}else if(alim[0] == 'general' && alim[1] == 'true'){
				general = true;
			}
		}else{
			alert("Element config error!")
			return false;
		}
	}
	//类型
	if(required&&ele.type=='checkbox'&&ele.checked==false){
		return setErrMessage(ele," must be choose.");
	}
	
	//值
	var valu = $.trim(ele.value);
			ele.value = valu;
	//是否为空
	var isNull = (valu == undefined) || (valu == "");
	//空判断 -- 如果不允许为空而实际是空，则返回
	if(required && isNull){
		return setErrMessage(ele," can't be null.");
	}else if(!isNull){
		//=============================类型检验========================//
		//=============================类型检验========================//
		//检验类型
		switch(vtype){
            //整数
			case "int":
				if(!isDigit(valu)){
					return setErrMessage(ele," must be int.");
				}
				break;
			case "digit":
				if(!isDigitString(valu)){
					return setErrMessage(ele," must be digit.");
				}
				break;
			case "float":
				if(!isFloat(valu)){
					return setErrMessage(ele," must be float.");
				}
				break;
			case "date":
				if(!isDateYMD(valu)){
					return setErrMessage(ele," must be date.");
				}
				break;
			case "datetime":
				if(!isDateTimeYMD(valu)){
					return setErrMessage(ele," must be datetime.");
				}
				break;
			case "time":
				if(!isTime(valu)){
					return setErrMessage(ele," must be time.");
				}
				break;
			case "tel":
			case "fax":
				if(!isTel(valu)){
					return setErrMessage(ele," must be tel or fax number.");
				}
				break;
			case "mobileCn":
				if(!isMobileCN(valu)){
					return setErrMessage(ele," must be Chinese");
				}
				break;
			case "ip":
				if(!isIP(valu)){
					return setErrMessage(ele," must be IP.");
				}
				break;
			case "url":
				if(!isURL(valu)){
					return setErrMessage(ele," must be URL.");
				}
				break;
			case "idCard":
				if(!(isIDNumber15(valu) || isIDNumber18(valu))){
					return setErrMessage(ele," must be Chinese IDCard number.");
				}
				break;
			case "email":
				if(containsSpecialChar(valu)||!isEmail(valu)){
					return setErrMessage(ele," must be Email address.");
				}
				break;
			case "signName":
				if(!isSignName(valu)){
					return setErrMessage(ele," must be sign name:character,number,underline,point.The first char must be character.");
				}
                break;
            case "name":
				if(!isRealName(valu)){
					return setErrMessage(ele," must be real name:Double byte character or single byte character. or space,point.");
				}
				break;
			case "postcodeCn":
				if(!isPostalCodeCN(valu)){
					return setErrMessage(ele," must be valid postcode.");
				}
				break;
			case "string":
				break;
			default:
				alert(L.ele + ele.name + L.error_config_val + vtype);
				return false;
		}
		//=============================类型检验结束========================//
		//============================其它限制检验=======================//
		if(lims != null){
			var i;
			for(i = 0;i<lims.length;i++){
				var lim = lims[i].split(":");
				if(lim.length != 2){
					alert("attrribute limit config error.");
					return false;
				}
				if(lim[0] == "len"){	//长度检查，不管是什么类型，配置了长度就检查
					var lenDesc = lim[1];
					//alert(lim[1]);
					if(lenDesc.indexOf("-") > -1){
						var als = lenDesc.split("-");
						if(als.length == 2){
							if(als[0] == ""){
								if(valu.length > parseInt(als[1])){
									return setErrMessage(ele," can't more than " + als[1]);
								}
							}else if(als[1] == ""){
								if(valu.length < parseInt(als[0])){
									return setErrMessage(ele," can't less than " + als[0]);
								}
							}else if(valu.length < parseInt(als[0]) || valu.length > parseInt(als[1])){
								return setErrMessage(ele," must between " + als[0] + " and " + als[1]);
							}
						}else{
							alert("Element" + ele.name + " config error.");
							return false;
						}
					}else{
						if(valu.length != parseInt(lenDesc)){
							return setErrMessage(ele," the length must be " + lenDesc);
						}
					}
				//限定值区间,仅用于int型与float型
				}else if(lim[0] == "between" && lim[1].indexOf("-") > -1 && (vtype=="float" || vtype=="int")){
					var ls = lim[1].split("-");
					var fv = parseFloat(valu);
					//如果没有下限
					if(ls[0] == ""){
						if(fv > parseFloat(ls[1])){
							return setErrMessage(ele," can't more than " + ls[1]);
						}
					}else if(ls[1] == ""){	//如果没有上限
						if(fv < parseFloat(ls[0])){
							return setErrMessage(ele," can't less than " + ls[0]);
						}
					}else{
						if(fv < parseFloat(ls[0]) || fv > parseFloat(ls[1])){
							return setErrMessage(ele," must between " + ls[0] + " and " + ls[1]);
						}
					}
				}else if(lim[0] == "decLen" && vtype=="float"){	//浮点数精度
					if((valu.length - valu.indexOf(".")) > parseInt(lim[1])){
						//转换精度
						var precision = Math.pow(10, parseInt(lim[1]) || 0);
						ele.value = Math.round(parseFloat(valu) * precision) / precision;
					}
				}else if(lim[0] == "equals"){
					//是否要求检测其它相等的元素值
					var oevalue = eval("document." + formName + "." + lim[1] + ".value");
					if(oevalue != valu){
						return setErrMessage(ele," not match element " + lim[1] + "'s value.");
					}
				}else if(lim[0]=='bigger'){
					//要求检测当前的值比指定元素值大
					var oevalue = eval("document." + formName + "." + lim[1] + ".value");
					od = parseInt(oevalue);
					cd =  parseInt(valu);
					if(cd<od||isNaN(od)||isNaN(cd)){
						return setErrMessage(ele," "+lim[0]+"  value  must bigger than " + lim[1] );
					}
				}
				else if(lim[0]=='smaller'){
					//要求检测当前的值比指定元素值小
					var oevalue = eval("document." + formName + "." + lim[1] + ".value");
					od = parseInt(oevalue);
					cd =  parseInt(Math.ceil(valu));
					if(cd>od||isNaN(od)||isNaN(cd)){
						return setErrMessage(ele," "+lim[0]+"  value  must small than " + lim[1] );
					}
				}				
				else if(lim[0]=='than'){
					//要求检测当前的时间值比前一个时间值大
					var oevalue = eval("document." + formName + "." + lim[1] + ".value");
					od = toDate(oevalue);
					cd =  toDate(valu);
					if(cd<od){
						return setErrMessage(ele," "+lim[0]+"  date  must than " + lim[1] );
					}
					
				}else if(lim[0]=='less'){
					//要求检测当前的时间值比指定时间值小
					var levalue = eval("document." + formName + "." + lim[1] + ".value");
					ld = toDate(levalue);
					cd =  toDate(valu);
					//alert(ld);
					if(ld&&ld<cd){
						return setErrMessage(ele," "+lim[0]+"  date  must less " + lim[1] );
					}
				}
			}
		}
		if(general && vtype=='string'){// alert(containsSpecialChar(valu));
			if(containsSpecialChar(valu)){
				
				return setErrMessage(ele," can't allow contains special character.");
			}
		}
		//ajax判断验证错误
		if(ele.getAttribute("valid")=='0'){
			return setErrMessage(ele,"ajax valid failed");
		}
	}
	//============================限制检验完毕=======================//
	global_formjs_valid_flag = true;
	return true;
}
/**
 * 清空信息域
 * @param divid
 */
function clearMsgArea(divid){
    var msgSpan = document.getElementById(divid);
    if(msgSpan != undefined && msgSpan != null){
		msgSpan.innerHTML = "";
	}
}

/*去除空格*/
function trim(str){
	return str.replace(/^\s+|\s+$/g, '');
}

/**
*   判断是否数字
*   三种调用方式:
*	1.一个参数,简单判断是否为数字,但长度不超过10位
*	2.三个参数,第二个参数为 '>'(大于) 或 '<'(小于),第3个参数为要比较的数字
*	3.三个参数，第二个参数与第三个参数均为整数，判断传入的第一个参数值是否在他们中间。（含边界）
*/
function judgeDigit(){
	var s = arguments[0];
	if(arguments.length == 1){
		return isDigit(s);
	}else if(arguments.length == 3){
		//通过验证
		var patrn=/^-?[0-9]{1,10}$/;
		if(patrn.test(s)){
			var p1 = arguments[1];
			var sint = parseInt(s);
			if(isDigit(arguments[2])){
				var pint = parseInt(arguments[2]);
				if(p1 == '>' || p1 == '<'){
					if(p1 == '>'){
						return sint > pint;
					}else if(p1 == '<'){
						return sint < pint;
					}
				}else if(isDigit(p1)){
					var pmin = parseInt(p1);
					return (sint >= pmin) && (sint <= pint);
				}else{
					alert('arguments error,the 2nd argument is not a number and not an operation:greater|less|equals.');
				}
			}else{
				alert('arguments error,the 3rd argument is not a number.');
			}
		}
	}
	return false;
}
/**
*是否数字
*/
function isDigit(s){
	var patrn=/^[0-9]{1,10}$/;
	return patrn.test(s);
}

/**
* 判断标识符或是登录名，以字母开头,可带数字、"_"、"." 的字串
* 限定最小长度(第二个参数)与最大长度(第三个参数)(默认为2--32位)
* @param string
* @param min length
* @param max length
*/
function isSignName(){
	var s = arguments[0];
	if(arguments.length == 1){
		var patrn=/^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){1,31}$/;
		return patrn.test(s);
	}else if(arguments.length == 3){
		if(isDigit(arguments[1]) && isDigit(arguments[2])){
			eval("var patrn=/^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){" + (parseInt(arguments[1]) - 1) + "," + (parseInt(arguments[2]) - 1) + "}$/;");
			return eval("patrn.test(s);");
		}else{
			alert('Error:the 2nd argument and the 3rd argument must be number.');
			return false;
		}
	}else{
		alert('method invoke error.error arguments number.');
		return false;
	}
}
/**
*判断是否是真实姓名
*/
function isRealName(s){
	var patrn = /^([a-zA-Z0-9]|[._ ]){2,64}$/;		//英文名
	var p2 = /^([^\x00-\xff]|[\s]){2,32}$/;		//双字节名
	return patrn.test(s) || p2.test(s);
}

/**
* 电话号码
* 必须以数字开头，除数字外，可含有"-"
**/
function isTel(s){
	//var patrn=/^[+]{0,1}(\d){1,3}[ ]?([-]?((\d)|[ ]){1,12})+$/;
	var patrn = /^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
	var patrn2 = /^1[3|5|8]{1}[0-9]{1}[-| ]?\d{8}$/;
	var patrn3 = /^(400)[6|7|8|1|0]{1}[-| ]?\d{3}[-| ]?\d{3}$/;
	var patrn4 = /^(800)[-| ]?\d{3}[-| ]?\d{4}$/;
	var patrn5 = /^(00852)?[-| ]?[6|9]{1}\d{7}$/;
	return patrn.test(s) || patrn2.test(s)||patrn3.test(s)||patrn4.test(s)||patrn5.test(s);
}

/**
* 中国大陆地区手机号码
* 以13或15开头，使用时请根据变化修改
* 校验普通电话，除数字外，可用"-"或空格分开
**/
function isMobileCN(s){
	var patrn = /^1[0-9]{1}[0-9]{1}[-| ]?\d{8}$/;
	var patrn2 = /^(00852)?[-| ]?[6|9]{1}\d{7}$/;

	return patrn.test(s)||patrn2.test(s);
}

/**
* 中国地区邮编
***/
function isPostalCodeCN(s){
	var patrn=/^[1-9]\d{5}$/;
	return patrn.test(s);
}
/**Emai*/
function isEmail(s){
	var patrn = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
	return patrn.test(s);
}

/**URL*/
function isURL(s){
	var patrn = /^http:\/\/([\w-]+(\.[\w-]+)+(\/[\w-.\/\?%&=\u4e00-\u9fa5]*)?)?$/;
	return patrn.test(s);
}
/**
* IP
**/
function isIP(s) {
	var patrn=/^((1?\d?\d|(2([0-4]\d|5[0-5])))\.){3}(1?\d?\d|(2([0-4]\d|5[0-5])))$/;
	return patrn.test(s);
}
/**
*是否是完整的正则表达式
*只有开始标记与结束标记相匹配才为TRUE
*HTML Tag
*/
function isHtmlTag(s){
	var patrn = /^<(.*)>.*<\/\1>|<(.*) \/>$/;
	return patrn.test(s);
}
/**
*身份证号
*这里的省与地区码还没有判断
*15位
*/
function isIDNumber15(s){
	var patrn=/^[\d]{6}((\d{2}((0[13578]|1[02])(0[1-9]|[12]\d|3[01])|(0[13456789]|1[012])(0[1-9]|[12]\d|30)|02(0[1-9]|1\d|2[0-8])))|([02468][048]|[13579][26])0229)[\d]{3}$/;
	return patrn.test(s);
}
/**
*身份证号
*这里的省与地区码还没有判断
*18位
*/
function isIDNumber18(s){
	var patrn = /^[\d]{6}[0-9]{4}(((0[13578]|(10|12))(0[1-9]|[1-2][0-9]|3[0-1]))|(02(0[1-9]|[1-2][0-9]))|((0[469]|11)(0[1-9]|[1-2][0-9]|30)))[\d]{3}[\d|x|X]$/;
	return patrn.test(s);
}

/**
*中文
*/
function isChineseString(s){
	var patrn = /^[\u4e00-\u9fa5]+$/
	return patrn.test(s);
}
/**
*双字节
*/
function isDoubleByteString(s){
	var patrn = /^[^x00-xff]+$/;
	return patrn.test(s);
}
/**
*是否包含首尾空格，如果包含，返回TRUE
*/
function hasHESpace(s){
	var patrn = /^\s+|\s+$/;
	return patrn.test(s);
}
/**
*	QQ，最大10位，最小5位
*/
function isQQ(s){
	var patrn=/^[1-9]{1}\d{4,9}$/;
	return patrn.test(s);
}
/**
*浮点数
*	如果是一个参数，那么判断是否为浮点数
*	如果有两个参数，那么将第二个参数作为精度限定参数
*	如果有三个参数，那么第三个参数是将作为数值上限
*/
function judgeFloat(){
	if(arguments.length == 1){
		return isFloat(arguments[0]);
	}else if(arguments.length == 2){
		eval("var patrn = /^-?\\d+.?\\d{0," + arguments[1] + "}$/;");
		return eval("patrn.test(arguments[0]);");
	}else if(arguments.length == 4){
		var a3 = arguments[2];
		if(a3 == '>' || a3 == '<'){
			if(isFloat(arguments[3])){
				eval("var patrn = /^-?\\d+.?\\d{0," + arguments[1] + "}$/;");
				if(eval("patrn.test(arguments[0]);")){
					if(a3 == '<'){
						if(parseFloat(arguments[0]) < parseFloat(arguments[3])) return true;
					}else{
						if(parseFloat(arguments[0]) > parseFloat(arguments[3])) return true;
					}
				}
				return false;
			}
		}else if(isFloat(a3)){
			eval("var patrn = /^-?\\d+.?\\d{0," + arguments[1] + "}$/;");
				if(eval("patrn.test(arguments[0]);")){
					var f0 = parseFloat(arguments[0]);
					var f3 = parseFloat(arguments[2]);
					var f4 = parseFloat(arguments[3]);
					return f0 >= f3 && f0 <= f4;
				}else{
					return false;
				}
		}else{
			alert('the 3rd and the 4th arguments are not number.');
			return false;
		}
	}
	return false;
}
/**
*是否是浮点数
**/
function isFloat(s){
	var patrn = /^-?\d*.?\d+$/;
	return patrn.test(s);
}
/**
*是否闰年
**/
function isLeapYear(y){
	return (y % 4 == 0 && y % 100 != 0) || y % 400 == 0;
}
/**
*日期
*yyyy-mm-dd格式或yyyy/mm/dd格式，年用两位表示亦可
*Regex author:Michael Ash
*支持1600年以后
*/
function isDateYMD(s){
	var patrn = /^(?:(?:(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(\/|-|\.)(?:0?2\1(?:29)))|(?:(?:(?:1[6-9]|[2-9]\d)?\d{2})(\/|-|\.)(?:(?:(?:0?[13578]|1[02])\2(?:31))|(?:(?:0?[1,3-9]|1[0-2])\2(29|30))|(?:(?:0?[1-9])|(?:1[0-2]))\2(?:0?[1-9]|1\d|2[0-8]))))$/;
	return patrn.test(s);
}
/**
*日期
*dd-mm-yyyy格式或dd/mm/yyyy格式，年用两位表示亦可
*Regex author:Marco Storti
*支持1600年以后
*/
function isDateDMY(s){
	var patrn = /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/;
	return patrn.test(s);
}
/**
*日期
*mm-dd-yyyy格式或mm/dd/yyyy格式，年用两位表示亦可
*Regex author:Michael Ash
*支持1600年以后
*/
function isDateMDY(s){
	var patrn =  /^(?:(?:(?:0?[13578]|1[02])(\/|-|\.)31)\1|(?:(?:0?[13-9]|1[0-2])(\/|-|\.)(?:29|30)\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:0?2(\/|-|\.)29\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:(?:0?[1-9])|(?:1[0-2]))(\/|-|\.)(?:0?[1-9]|1\d|2[0-8])\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/;
	return patrn.test(s);
}
/**
*日期时间：M/d/y hh:mm:ss
*Regex author:Michael Ash
*支持1600年以后
*/
function isDateTimeMDY(s){
	var patrn = /^(?=\d)(?:(?:(?:(?:(?:0?[13578]|1[02])(\/|-|\.)31)\1|(?:(?:0?[1,3-9]|1[0-2])(\/|-|\.)(?:29|30)\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})|(?:0?2(\/|-|\.)29\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))|(?:(?:0?[1-9])|(?:1[0-2]))(\/|-|\.)(?:0?[1-9]|1\d|2[0-8])\4(?:(?:1[6-9]|[2-9]\d)?\d{2}))($|\ (?=\d)))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\ [AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
	return patrn.test(s);
}
/**
*日期时间 yyyy/mm/dd hh:mm:ss 或 yyyy-mm-dd hh:mm:ss
*Date Regex author:Michael Ash
*Modified by Shaw Sunkee
*支持1600年以后
*/
function isDateTimeYMD(s){
	var patrn = /^(?:(?:(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(\/|-|\.)(?:0?2\1(?:29)))|(?:(?:(?:1[6-9]|[2-9]\d)?\d{2})(\/|-|\.)(?:(?:(?:0?[13578]|1[02])\2(?:31))|(?:(?:0?[1,3-9]|1[0-2])\2(29|30))|(?:(?:0?[1-9])|(?:1[0-2]))\2(?:0?[1-9]|1\d|2[0-8]))))[ ]([0-1]?[0-9]|[2][0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/;;
	return patrn.test(s);
}
/**
*时间
*hh:mm:ss 24小时制 0 ~ 23 hour
*/
function isTime(s){
	var patrn = /^([0-1]?[0-9]|[2][0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/;
	return patrn.test(s);
}
function toDate(s){
	return s.replace("-","");
	/*var sd=s.split("-");
    return new Date(sd[0],sd[1],sd[2]);
    */
}
/**
*	是否包含非特殊字符(正常字符包括字母数字，下划线，和点号，空格，@#$% 和双字节)
*	若包含，返回 true
*/
var validation_specialChars = new Array('\'','\"','\n','\r','\t',';',':','=','<','>',',','|','\\','<','>','/','^','~','`','$','#',' ');
function containsSpecialChar(str){
	for(var i = 0;i<validation_specialChars.length;i++){
		if(str.indexOf(validation_specialChars[i]) > -1){
			return true;
		}
	}
	return false;
}
/**
*	判断是否为数字串(可在串前加"-"号，如：-123)
*	两种调用方式:
*	1.一个参数，不限制长度
*	2.三个参数，第二个参数表示允许的最小长度，第三个参数表示允许的最大长度
*/
function isDigitString(){
	return judgePattrnAndLen("-?\\d",arguments);
}
/**字母串
*	两种调用方式：
*	一种是一个参数，传入要验证的值
*	二种是带三个参数，第二和第三个参数分别代表最小长度和最大长度
*/
function isLetter(){
	return judgePattrnAndLen("[A-Za-z]",arguments);
}

/**
*	大写字母
*	两种调用方式：
*	一种是一个参数，传入要验证的值
*	二种是带三个参数，第二和第三个参数分别代表最小长度和最大长度
*/
function isUpperLetter(){
	return judgePattrnAndLen("[A-Z]",arguments);
}
/**
*	小写字母
*	两种调用方式：
*	一种是一个参数，传入要验证的值
*	二种是带三个参数，第二和第三个参数分别代表最小长度和最大长度
*/
function isLowerLetter(){
	return judgePattrnAndLen("[a-z]",arguments);
}
/**数字与字符串*/
function isLetterNumString(){
	return judgePattrnAndLen("[A-Za-z0-9]",arguments);
}
/**数字，字母，下划线字符串*/
function isLNUString(s){
	return judgePattrnAndLen("\\w",arguments);
}

/**
 * 传入一个简单的正则式串，要判定的值，传入限定最小长度和最大长度
 * @return
 */
function judgePattrnAndLen(){
	var pat = arguments[0];
	var as = arguments[1];
	if(as == null || as == undefined || as.length == 0){
		alert('no arguments.');
		return false;
	}else if(as.length == 1){
		eval("var patrn= /^" + pat + "+$/;");
		return eval("patrn.test(as[0]);");
	}else if(as.length == 3){
		if(isDigit(as[1]) && isDigit(as[2])){
			eval("patrn =" + "/^" + pat + "{" + as[1] + "," + as[2] + "}$/;");
			return eval("patrn.test(as[0]);");
		}else{
			alert('error arguments:the 2nd argument and the 3rd argument must be number.');
			return false;
		}
	}else{
		alert('error arguments number');
		return false;
	}
}
Array.prototype.in_array = function(e) 
{ 
    for(i=0;i<this.length;i++)
    {
        if(this[i] == e)
        return true;
    }
    return false;
}
String.prototype.Trim     =   function(){return   this.replace(/(^\s*)|(\s*$)/g,   " ");} 
/**
 * obj input_obj
 * isAlert boolen 
 */
function isExtName(obj,isalert,msgType,showTarget){
	var value = obj.value;
	var ext = obj.getAttribute('ext');
	var ext_arr = ext.split(',');
	var s_num = value.lastIndexOf(".");
	var lastname = value.substring(s_num,value.length).toLowerCase();
	if(isalert)
	{
	    if(ext_arr.in_array(lastname))
	    	{return true;}else{
	    		if(msgType){
					tipsAppend(showTarget,lastname+L.file_format_error,'error','red');
	    		}else{
	    			showDialog(lastname+L.file_format_error, 'alert', L.file_format_error,'',0);
	    		}return false;
	    	}
	}else{
		if(ext_arr.in_array(lastname))
    	{return true;}else{return false;}
	}
}

//页面加载时验证
$(function(){
	form_valid();
})
//验证页面所有input标签
function form_valid(){
	var eles = $("input,select"); //document.getElementsByTagName('input');
	
	for(i=0;i<eles.length;i++){
		var limit = eles[i].getAttribute("limit");
		if(limit != null && limit != ""){
			id = eles[i].getAttribute('id');
			if(id !='' && id != null ){
			  ele_valid(id);	
			}
			
		}	
	}
	return true;
}
//通用元素离焦与focus验证方法
function ele_valid(id){
	
	var obj = document.getElementById(id);
	var msgArea = obj.getAttribute("msgArea");
	var msg = obj.getAttribute('msg');
	var tips = obj.getAttribute('title');
	
	if(tips==null) tips='&nbsp;';
	
	$("#"+id).blur(function(){
		var url = obj.getAttribute('ajax');
		var value="";
		var aa = validElement(obj);
		if (!aa) {
			
			$("#" + msgArea).removeClass('msg_tips').removeClass('msg_ok');
			$("#" + msgArea).html("<i></i><span>"+msg+"</span>");
			return false;
		} else {
			//ajax验证
			if(url){
				value = trim($("#"+id).val());
				if(!value.length){
					return false;
				}
				url +=  value;
				$.post(url,function(data){
					if($.trim(data)==true){
						$("#" + msgArea).addClass('msg').addClass('msg_ok').removeClass('msg_tips').removeClass('msg_error');
						$("#" + msgArea).html('<i></i>');
						$("#"+id).attr("valid",2);
						return true;
					}else{
						$("#" + msgArea).addClass('msg_error').removeClass('msg_tips').removeClass('msg_ok');
						$("#" + msgArea).html('<i></i><span>'+data+'</span>');
						$("#"+id).attr("valid",1);
						return false;
					}
				})
			}else{
				//行业选择验证
				if(($("#indus_pid").val() && $("#indus_id").val() == '') || ($("#indus_pid").val() == '' && $("#indus_id").val()) ){
					var span_indus = "#" + msgArea;
					if(span_indus == '#span_indus'){
						$("#span_indus").addClass('msg_error').removeClass('msg_tips');
						$("#span_indus").html("<i></i><span>请选择行业子分类</span>");
						return false;
					}else{
						$("#" + msgArea).addClass('msg_ok').removeClass('msg_tips').removeClass('msg_error');
						$("#" + msgArea).html("<i></i>");
						return true;
					}
					
				}
				else{
				$("#" + msgArea).addClass('msg_ok').removeClass('msg_tips').removeClass('msg_error');
				$("#" + msgArea).html("<i></i>");
				return true;
				}
			}
			
		}
	}).focus(function(){
		$("#" + msgArea).addClass('msg').removeClass('msg_ok').removeClass('msg_tips').removeClass('msg_error');
		$("#" + msgArea).html('');
		return false;
	})
}


 
 
