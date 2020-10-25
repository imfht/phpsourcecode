//页面连接处理
$$$(function(){
	var alllink = document.links;		//获取页面所有连接
	for(var i=0;i<alllink.length;i++){
		if({isatel}==0){
			if(alllink[i].href.indexOf("tel:")>-1){
				alllink[i].target = "_blank";		//设置电话连接为新窗口打开
				if(!is_mobile_request() && {dhurltel}==0){	//手机端与PC端显示不同连接
					alllink[i].href = "{tel}";		//替换电话连接为系统设置的连接
				}else{
					alllink[i].href = "{dhurl}";		//替换电话连接为系统设置的电话页面
				}
			}
		}
		if({isaonclick}==0){
			var httpdir="{swtdir}";
			httpdir=httpdir.replace("{httpdir}","");
			if(alllink[i].getAttribute('href',2)=="{swtdir}" || alllink[i].getAttribute('href',2)=="{swtdir}"+"/" || alllink[i].getAttribute('href',2)==httpdir || alllink[i].getAttribute('href',2)==httpdir+"/"){
				alllink[i].target = "_blank";		//设置商务通连接为新窗口打开
				//给无参数连接加上gotoswt(event,this)，需JQ支持
				if(!$(alllink[i]).attr("onclick")){
					$(alllink[i]).attr("onclick","gotoswt(event,this);");
				}
			}
		}
	}
	//CNZZ事件处理
	if(info["cnzzid"]!=""){
		var cnzzsj=$("[cnzzsj]");	//获取页面有cnzzsj属性的元素
		for(var i=0;i<cnzzsj.length;i++){
				if(!$(cnzzsj[i]).attr("onclick")){	
					$(cnzzsj[i]).attr("onclick","cnzzsj(this);");
				}else if(!$(cnzzsj[i]).is('a')){	//选取非A标签
					$(cnzzsj[i]).attr("onclick","cnzzsj(this);"+$(cnzzsj[i]).attr("onclick"));
				}
		}
	}
	//自动弹出QQ聊天窗口
	if({isopenqqchat}!=0){
		setTimeout(function (){popwin=window.location.href="tencent://message/?Menu=yes&uin={qq}&Service=58"},1000 * {isopenqqchat});
	}
})
//设置第一次来源网址
if(getCookie("laiyuanurl")==""){
	setCookie('laiyuanurl',document.referrer,0) ;
}