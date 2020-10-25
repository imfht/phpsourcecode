/***********************
* XMLParser
* == Licensed Under the MIT License : /LICENSING
* Copyright (c) 2012 Jim Chen ( CQZ, Jabbany )
* Partially modified by 2015 Chouney Zhang
************************/
function CommentLoader(url,xcm,callback){
	if(callback == null)
		callback = function(){return;};
	var xmlhttp = null;
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	else{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("GET",url,true);
	xmlhttp.send();
	var cm = xcm;
	xmlhttp.onreadystatechange = function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			if(navigator.appName == 'Microsoft Internet Explorer'){
				var f = new ActiveXObject("Microsoft.XMLDOM");
				f.async = false;
				f.loadXML(xmlhttp.responseText);
				cm.load(BilibiliParser(f)); //bilibiliParseBUG：不能屏蔽恶意html代码
				callback(); 
			}else{
				cm.load(BilibiliParser(xmlhttp.responseXML));
				callback();
			}
		}
	}
}
function CommentSender(vpath,xcm,video,content,durl){
	var xmlhttp = null;
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
		if(xmlhttp.overrideMimeType){
			xmlhttp.overrideMimeType("text/xml");
		}
	}
	else{
		xmlhttp=new ActiveXObject("<Micr></Micr>osoft.XMLHTTP");
	}
	xmlhttp.open("POST",durl,true);//php后台处理弹幕程序最好也用绝对定位
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.send("cont="+content+"&vpath="+vpath+"&playTime="+video.currentTime); //这里向后台传值暂时只传弹幕出现的实现，其余如用户ID,字幕模式，颜色等稍后做扩展
	xmlhttp.onreadystatechange = function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			//alert(xmlhttp.responseText);
			if(xmlhttp.responseText==0){
				alert("您尚未登录或账户已被冻结，无法发送弹幕");
				return ;
			}
			var danmaku = {
				"mode":1,
				"text":content,
				"stime":video.currentTime,
				"size":25,
				"color":0xff0000
			};
			xcm.send(danmaku);
		}
	}
		
}
function createCORSRequest(method, url){
    var xhr = new XMLHttpRequest();
    if ("withCredentials" in xhr){
        xhr.open(method, url, true);
    } else if (typeof XDomainRequest != "undefined"){
        xhr = new XDomainRequest();
        xhr.open(method, url);
    } else {
        xhr = null;
    }
    return xhr;
}
function interval(n)
{
	while(n) n--;
}
