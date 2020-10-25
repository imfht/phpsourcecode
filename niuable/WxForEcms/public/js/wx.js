//用于wx.reply.temp.php 标签调用、隐藏功能
function start_menu(){
	//var Tab=document.getElementById('tabs');
	var m=document.getElementsByName('menu');
	//var k=document.getElementsByName('rtable');
	for(i=0;i<m.length;i++){
		m[i].index=i;
		m[i].onclick=function(){
			for(i=0;i<k.length;i++){
				k[i].style.display="none";
				m[i].style.backgroundColor='#ffffff';
			}
			this.style.backgroundColor="#9BD3EC";
			k[this.index].style.display="";
		}
	}
}

var value="";
var value2= new Array();
var i=2;
var j;
var x="";
var y="";

//弃用
function MM_jumpMenu(x,type){ //v3.0
	var y=type+"reply_"+document.getElementById(x).value;
	var z;
	for(var k=1; k<7; k++){
		z=type+"reply_" + k;
		document.getElementById(z).style.display="none";
	}
	document.getElementById(y).style.display="";
}

function viewReply(x,type){
	var y=type+'_'+x;
	//alert(y);
	document.getElementById(type+'_text').style.display="none";
	document.getElementById(type+'_img').style.display="none";
	document.getElementById(type+'_voice').style.display="none";
	document.getElementById(type+'_video').style.display="none";
	document.getElementById(type+'_music').style.display="none";
	document.getElementById(type+'_news').style.display="none";
	document.getElementById(y).style.display="";
}

function ClearTxt(s){
  var tag= document.getElementById(s);
  value=tag.value;
	if(value!==value2[s]){
		tag.value="";		
	}
}

function unClearTxt(s){
	var tag2= document.getElementById(s);
	if(tag2.value==""){
		tag2.value=value;		
	}else{
	  value2[s]=tag2.value;
	}
}

//确认更新wx.base.temp.php
function setSite(site){
	document.getElementById('site').value=site;
}

function checkall(form, prefix, checkall){
	var checkall = checkall ? checkall : 'all';
	for(var i = 0; i < form.elements.length; i++){
		var e = form.elements[i];
		if(e.name && e.name != checkall && (!prefix || (prefix && e.name.match(prefix)))){
			e.checked = form.elements[checkall].checked;
		}
	}
}

function newdelete(newid){
	document.getElementById('site').value=newid;
	return confirm('你确定要残忍删除吗？');
}

var a;
var b=1;
//图片大小调整
function change(v,obj){
	if(b==1){
		a=$(obj).width();
		var v=v || 250;
		$(obj).width(a+v);
		b++;
	}
}
//图片大小调整2
function changeBack(obj){
	b--;
	$(obj).width(a);;
}
//wx.news.editor
function editorTime(id,t,i){
	var t=t?' ':'T';
	var now=new Date();
	//alert(nowtime);
	var year=now.getFullYear();
	var month=pad(now.getMonth()+1,2);
	var date=pad(now.getDate(),2);
	var hour=pad(now.getHours(),2);
	var minute=pad(now.getMinutes(),2);
	if(i){
		var seconds=pad(now.getSeconds(),2);
		i= ':'+seconds;
	}else{
		i='';
	}
	newtime=year + '-' + month + '-' + date + t + hour + ':' +minute + i;
	document.getElementById(id).value=newtime;
}

function pad(num, n) {//补0
    var len = num.toString().length;  
    while(len < n) {  
        num = "0" + num;  
        len++;  
    }  
    return num;  
}

function neweditor(id){
	document.getElementById('newsid').value=id;	
}

function somedelete(){
	return confirm('您确定要批量删除吗？');
}

//wx.news.temp.php  
function img_delete_for_news(imgid){
	var id_view = imgid + '_view';
	var id_td = imgid + '_border';
	document.getElementById(id_view).src = '';
	document.getElementById(id_td).style.display = "none"; 
	document.getElementById(imgid).value='';
}

function maxText(obj,wid){
	obj=document.getElementById(obj);
	var val=obj.value;
	var len=strlen(val);
	if(len>obj.maxLength){
		while (strlen(val) > obj.maxLength){
		  val = val.substring(0, len - 2);
		  len--;
		}
		 obj.value=val;
		len=strlen(obj.value);
	}
	len=len/2;
	document.getElementById(wid).innerHTML = Math.floor(len) + "/" + obj.maxLength/2; 
}
function strlen(str){
    var len = 0;
    for (var i=0; i<str.length; i++) { 
     var c = str.charCodeAt(i); 
    //单字节加1 
     if ((c >= 0x0001 && c <= 0x007e) || (0xff60<=c && c<=0xff9f)) { 
       len++; 
     } 
     else { 
      len+=2; 
     } 
    } 
    return len;
}

//wx.reply.content.temp
function delete_content(imgid){
	var id_view = imgid + '_view';
	var id_td = imgid + '_cover';
	document.getElementById(id_view).src = '';
	document.getElementById(id_td).style.display = "none"; 
	document.getElementById(imgid).value='';
}

//wx.reply.php
function reeditor(newid){
	document.getElementById('replyid').value=newid;
}

function openinput(inputid){
	document.getElementsByName(inputid)[0].removeAttribute("disabled");
}

//wx.reply.editor.temp.php
function sure_replymodify(){
	return confirm('你确定要修改吗？');
}

//wx.reply.selectnews.php
function chooseOneNews(s){
	var title=document.getElementById("title_"+s).value;
	var titlepic=document.getElementById("titlepic_"+s).value;
	var smalltext=document.getElementById("smalltext_"+s).value;
	var newsid=document.getElementById("id_"+s).value;
	var seat=document.getElementById('replyid').value;
	window.parent.document.getElementById("title_"+seat).innerHTML=title;
	window.parent.document.getElementById("titlepic_"+seat).src=titlepic;
	window.parent.document.getElementById("smalltext_"+seat).innerHTML=smalltext;
	window.parent.document.getElementById("news_"+seat).value=newsid;
	window.parent.document.getElementById('select_cover').style.display='none';
}


//wx.reply.selectnews.php
function go_back_reply(){
	//window.parent.document.getElementById('news_select').style.display='none';
	window.parent.document.getElementById('select_cover').style.display='none';
	window.parent.document.getElementById('select').style.display='none';
}

//wx.news.oneToSome.temp.php
function deleteNewsForReply(seat){
	document.getElementById('smalltext_'+seat).innerHTML='';
	document.getElementById('title_'+seat).innerHTML='';
	document.getElementById('news_'+seat).value='';
	document.getElementById('titlepic_'+seat).src='';
}

//wx.reply.temp.php
function checkdataofReply(word){
	var word = word || "添加";
	var a=document.getElementById('keyword').value;
	if((!a || a==='') && a!==0){
		alert('您还没有填写关键词');
		return false;
	}else{	
		return confirm('您确定要'+word+'关键词：'+a+' 吗?');
	}
}

//wx.mass.temp.php
function selectMass(obj){
	var a='content_';
	$('#massContent li').attr('class','');
	$(obj).parent().attr('class','active');
}

function selectCont(type){
	var a='iframe_';
	var iframe=a+type;
	document.getElementById('select_cover').style.display='';
	document.getElementById(iframe).style.display='';
}

//wx.mass.select.news.php
function DialogNewsSelectionForMass(seat) { 
	var a=document.getElementById('select_cover');
	a.style.display='';
		
	var goal=document.getElementById('iframe_news');
	goal.style.display='';
	goal.contentWindow.document.getElementById('replyid').value=seat;
}
function add_news(){
	var i=1;
	var id;
	for(i;i<10;i++){
		id='news_'+i;
		if(document.getElementById(id).style.display=='none'){
			document.getElementById(id).style.display='';
			break;
		}
	}
}
function newsHide(x){
	var id="news_"+x;
	document.getElementById(id).style.display="none";
}

//wx.mass.temp.php
function deleteForMass(image,border){
	document.getElementById(image).value='';
	var cover=image+'_cover';
	document.getElementById(cover).style.display='none';
	document.getElementById(border).style.display='';
}

//wx.file.select.temp.php
function chooseOnefile(s,t){
	var path=document.getElementById("path_"+s).value;
	var seat;
	var border;
	switch(t){
		case '图片':
			seat='image';
			border=2;
		break;
		case '音频':
			seat='voice';
			border=3;
		break;
		
		case '视频':
			seat='video';
			border=4;
		break;
		default:
	}
	window.parent.document.getElementById(seat).value=path;
	window.parent.document.getElementById('select').style.display='none';
	window.parent.document.getElementById('border_'+border).style.display='none';
	window.parent.document.getElementById(seat+'_cover').style.display='';
	window.parent.document.getElementById(seat+'_view').src=path;
}


//构造通用函数

//删除单个内容
function deleteOnlyOne(val,id){
	if(val && id)document.getElementById(id).value=val;
	return confirm('您确定要残忍删除吗？');	
}

//弹出“确定”对话框
function makeSure(words){
	return confirm(words);
}

function editorById(val,id){
	document.getElementById(id).innerHTML=val;
}

function editorModal(body,title,id){
	title=title || '确认操作';
	id=id || 'myModal';
	$('#'+id+' '+'.modal-title')[0].innerHTML=title;
	$('#'+id+' '+'.modal-body')[0].innerHTML=body;
}

function editorModal2(body,id){
	id=id || 'myModal2';
	$('#'+id+' '+'.modal-content')[0].innerHTML=body;
}
//修改辅助input参数
function editorInput(val,id){
	document.getElementById(id).value=val;
}

//删除图文组合页面的图文选择
function deleteOneNews(seat){
	$('#abstract_'+seat).html('');
	$('#title_'+seat).html('');
	$('#news_id_'+seat).val('');
	//document.getElementById().value='';
	$('#title_img_'+seat).attr('src','');
}

//JS操作cookies方法!
//写cookies
function setCookie(name,value){
	var Days = 30;
	var exp = new Date();
	exp.setTime(exp.getTime() + Days*24*60*60*1000);
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}

//读取cookies
function getCookie(name){
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg))
	return unescape(arr[2]);
	else
	return null;
}

//删除cookies
function delCookie(name){
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval=getCookie(name);
	if(cval!=null)
	document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}

function sure(){
	var Form='form';
	var a;
	a=document.getElementById('operation_form');
	if(a){
		var id=a.value;
		document.getElementById(id).submit();
	}else{
		document.getElementsByTagName('form')[1].submit();

	}
}