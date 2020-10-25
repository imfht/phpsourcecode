var url_store = {};
var token_store = {};
	var fromusername_store = {};
	var tousername_store = {};
	var nr_store = {};
	var yy_store = {};
	var tp_store = {};
	var wzx_store = {};
	var wzy_store = {};

	function appendul(classname,storename){ 
		var a = ""
		for(var key in storename){
			a += "<li class='active-result'>"+storename[key]+"</li>";
		}
		$("."+classname).html(a);
	}

	$(function(){
		$("#clearcc").val("清除本地存储("+store.length()+")");
		for(var i=0; i<store.length();i++){
			var skey = store.key(i);
			var svalue = store.get(skey);
			if(skey.indexOf("URL")==0){
				url_store[skey] = svalue;
			}else if(skey.indexOf("Token")==0){
                token_store[skey] = svalue;
            }else if(skey.indexOf("FromUserName")==0){
				fromusername_store[skey] = svalue;
			}else if(skey.indexOf("ToUserName")==0){
				tousername_store[skey] = svalue;
			}else if(skey.indexOf("nr")==0){
				nr_store[skey] = svalue;
			}else if(skey.indexOf("yy")==0){
				yy_store[skey] = svalue;
			}else if(skey.indexOf("tp")==0){
				tp_store[skey] = svalue;
			}else if(skey.indexOf("wzx")==0){
				wzx_store[skey] = svalue;
			}else if(skey.indexOf("wzy")==0){
				wzy_store[skey] = svalue;
			}
		}
		appendul("URL_ul",url_store);
		appendul("Token_ul",token_store);
		appendul("FromUserName_ul",fromusername_store);
		appendul("ToUserName_ul",tousername_store);
		appendul("nr_ul",nr_store);
		appendul("yy_ul",yy_store);
		appendul("tp_ul",tp_store);
		appendul("x_ul",wzx_store);
		appendul("y_ul",wzy_store);
		
		$("#URL").val($(".URL_ul").find("li").last().html());
		$("#FromUserName").val($(".FromUserName_ul").find("li").last().html());
		$("#ToUserName").val($(".ToUserName_ul").find("li").last().html());
		$("#nr").val($(".nr_ul").find("li").last().html());
		$("#yy").val($(".yy_ul").find("li").last().html());
		$("#tp").val($(".tp_ul").find("li").last().html());
		$("#x").val($(".x_ul").find("li").last().html());
		$("#y").val($(".y_ul").find("li").last().html());
		
		var begin = "<xml>\r\n";
		var toUserName = "<ToUserName><![CDATA["+$("#ToUserName").val()+"]]></ToUserName>\r\n";
		var fromUserName = "<FromUserName><![CDATA["+$("#FromUserName").val()+"]]></FromUserName>\r\n";
		var content = "<Content><![CDATA["+$("#nr").val()+"]]></Content>\r\n";
		var msgId = "<MsgId>1234567890123456</MsgId>\r\n";
		var mediaId = "<MediaId><![CDATA[1234567890]]></MediaId>\r\n";
		var format = "<Format><![CDATA["+$("#yy").val()+"]]></Format>\r\n";
		var picUrl = "<PicUrl><![CDATA["+$("#tp").val()+"]]></PicUrl>\r\n";
		var location_X = "<Location_X>"+$("#x").val()+"</Location_X>\r\n";
		var location_Y = "<Location_Y>"+$("#y").val()+"</Location_Y>\r\n";
		var scale = "<Scale>20</Scale>\r\n";
		var label = "<Label><![CDATA[]]></Label>\r\n";
		var end = "</xml>";
		var time = parseInt(new Date().getTime()/1000);
		var br = "\r\n-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------\r\n"
		function msgType(type){
			return "<MsgType><![CDATA["+type+"]]></MsgType>\r\n";
		}
		function event(event){
			return "<Event><![CDATA["+event+"]]></Event>\r\n";
		}
		
		//类型选择
		$('input[type="radio"]').click(function(){
			time = parseInt(new Date().getTime()/1000);
			var createTime = "<CreateTime>"+time+"</CreateTime>\r\n";
			if($(this).val()==0){//文本
				$("#content").val(begin+toUserName+fromUserName+createTime+msgType("text")+content+msgId+end);
				$(".nytz").children().addClass("hidden");
				$(".nr").removeClass("hidden");
			}else if($(this).val()==1){//语音
				$("#content").val(begin+toUserName+fromUserName+createTime+msgType("voice")+mediaId+format+msgId+end);
				$(".nytz").children().addClass("hidden");
				$(".yy").removeClass("hidden");
			}else if($(this).val()==2){//图片
				$("#content").val(begin+toUserName+fromUserName+createTime+msgType("image")+picUrl+mediaId+msgId+end);
				$(".nytz").children().addClass("hidden");
				$(".tp").removeClass("hidden");
			}else if($(this).val()==3){//位置
				$("#content").val(begin+toUserName+fromUserName+createTime+msgType("location")+location_X+location_Y+scale+label+msgId+end);
				$(".nytz").children().addClass("hidden");
				$(".wz").removeClass("hidden");
			}else if($(this).val()==4){//关注
				$("#content").val(begin+toUserName+fromUserName+createTime+msgType("event")+event("subscribe")+end);
				$(".nytz").children().addClass("hidden");
			}else if($(this).val()==5){//取消关注
				$("#content").val(begin+toUserName+fromUserName+createTime+msgType("event")+event("unsubscribe")+end);
				$(".nytz").children().addClass("hidden");
			}
		});
		
		//页面打开默认选中文本
		$("#wenben").click();
		
		//将文本框中的内容同步到参数文本域中
		$("#FromUserName,#ToUserName,#nr,#yy,#tp,#x,#y").bind("keyup keydown propertychange input",function(time){
			fromUserName = "<FromUserName><![CDATA["+$("#FromUserName").val()+"]]></FromUserName>\r\n";
			toUserName = "<ToUserName><![CDATA["+$("#ToUserName").val()+"]]></ToUserName>\r\n";
			content = "<Content><![CDATA["+$("#nr").val()+"]]></Content>\r\n";
			format = "<Format><![CDATA["+$("#yy").val()+"]]></Format>\r\n";
			picUrl = "<PicUrl><![CDATA["+$("#tp").val()+"]]></PicUrl>\r\n";
			location_X = "<Location_X>"+$("#x").val()+"</Location_X>\r\n";
			location_Y = "<Location_Y>"+$("#y").val()+"</Location_Y>\r\n";
			$('input[type="radio"]:checked').click();
		});
		
		//清空结果文本域
		$("#clear").click(function(){
			$("#content_back").val("");
		});
		
		//点击提交按钮
		$("#button").click(function(){ 
			var url = $("#URL").val();
			var con = $("#content_back").val();
			var data123 = $("#content").val();
			$.post("demo.php",{data:data123,url:url,token:$("#token").val()},function(rs){
				if(con==""){
					$("#content_back").val(rs);
				}else{
					$("#content_back").val(con+br+rs);
				}
			});
			//存储
			if($("#URL").val()!=""){isHas("URL",url_store);}
			if($("#token").val()!=""){isHas("token",token_store);}
			if($("#FromUserName").val()!=""){isHas("FromUserName",fromusername_store);}
			if($("#ToUserName").val()!=""){isHas("ToUserName",tousername_store);}
			if($("#nr").val()!=""){isHas("nr",nr_store);}
			if($("#yy").val()!=""){isHas("yy",yy_store);}
			if($("#tp").val()!=""){isHas("tp",tp_store);}
			if($("#x").val()!=""){isHas("x",wzx_store);}
			if($("#y").val()!=""){isHas("y",wzx_store);}
		});
		
		$(".default").click(function(){
			if($(this).parent().parent().hasClass("chosen-with-drop")){
				$(".chosen-container").removeClass("chosen-with-drop");
			}else{//显示	
				$(".chosen-container").removeClass("chosen-with-drop");
				$(this).parent().parent().addClass("chosen-with-drop");
			}
		});
	});
	
	//存储
	function isHas(id,jsonname){
		//遍历 内容否已经存在
		for(var key in jsonname){
			//如果内容相同,存在
			if($("#"+id).val()==jsonname[key]){
				return;
			}
		}
		store.set(id+"_"+getJsonLength(jsonname), $("#"+id).val()+"");
	}
	
	//获取json长度,用于定义store的key
	function getJsonLength(jsonData){
		var jsonLength = 0;
		for(var item in jsonData){
			jsonLength++;
		}
		return jsonLength;
	}
	
	//历史
	$(function(){
		//点击li时将值赋给input和参数文本域
		$(".chosen-results").find("li").click(function(){
			$(this).parent().parent().prev().find("input").val($(this).html());
			$(this).parent().parent().prev().find("input").keyup();
			$(".chosen-container").removeClass("chosen-with-drop");
		});
		
		//li背景变蓝
		$(".chosen-results").find("li").hover(function(){
			$(this).addClass("highlighted");
		},function(){
			$(this).removeClass("highlighted");
		});
		//鼠标移开下拉时消失
		$(".default").mouseover(function(){
			$(".chosen-container").removeClass("chosen-with-drop");
		});
		
		$("#clearcc").click(function(){
			store.clear();
			alert("清除成功！");
			window.location.reload();
		});
		
	});