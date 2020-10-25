// JavaScript Document
var isIe=(document.all)?true:false;
//设置select的可见状态
function setSelectState(state)
{
var objl=document.getElementsByTagName('select');
for(var i=0;i<objl.length;i++)
{
objl[i].style.visibility=state;
}
}
function mousePosition(ev)
{
if(ev.pageX || ev.pageY)
{
return {x:ev.pageX, y:ev.pageY};
}
return {
x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,y:ev.clientY + document.body.scrollTop - document.body.clientTop
};
}
/********************
 * 取窗口可视范围的高度   
 *******************/

  

//弹出方法
function showMessageBox(wTitle,content,pos,wWidth)
{
closeWindow();
var bWidth=parseInt(document.documentElement.scrollWidth);
var bHeight=parseInt(document.documentElement.scrollHeight);
if(isIe){
setSelectState('hidden');}
var back=document.createElement("div");
back.id="back";
var styleStr="top:0px;left:0px;position:absolute;z-index:9999;background:#666;width:"+bWidth+"px;height:"+bHeight+"px;";
styleStr+=(isIe)?"filter:alpha(opacity=0);":"opacity:0;";
back.style.cssText=styleStr;
document.body.appendChild(back);
showBackground(back,50);
var mesW=document.createElement("div");
mesW.id="mesWindow";
mesW.className="mesWindow";
mesW.innerHTML="<div class='mesWindowTop'><span>"+wTitle+"</span><input type='button' onclick='closeWindow();' class='close' value='' /></div><div class='mesWindowContent' id='mesWindowContent'>"+content+"</div><div class='mesWindowBottom'></div>";
document.body.appendChild(mesW);
var clHeight;
if(document.body.clientHeight&&document.documentElement.clientHeight)
{
var clHeight = (document.body.clientHeight<document.documentElement.clientHeight)?document.body.clientHeight:document.documentElement.clientHeight;   
}
else
{
var clHeight = (document.body.clientHeight>document.documentElement.clientHeight)?document.body.clientHeight:document.documentElement.clientHeight;   
}
v_top = (clHeight/2-mesW.clientHeight/2);
styleStr="left:"+(document.body.clientWidth/2-mesW.clientWidth/2)+"px;top:"+v_top+"px;position:absolute;z-index:9999;";
mesW.style.cssText=styleStr;



var nswtips;var toTop = v_top;var old = toTop;
var initTips=function(){
    nswtips = document.getElementById('mesWindow');
    noveTips();
}
var noveTips=function(){
    var tt=50;
    if (window.innerHeight){
    pos = window.pageYOffset
    }else if (document.documentElement && document.documentElement.scrollTop) {
    pos = document.documentElement.scrollTop
    }else if (document.body) {
    pos = document.body.scrollTop;
    }
    pos=pos-nswtips.offsetTop+toTop;
    pos=nswtips.offsetTop+pos/10;
    if (pos < toTop){
     pos = toTop;
    }
    if (pos != old) {
     nswtips.style.top = pos+"px";
     tt=10;
    }
    old = pos;
    setTimeout(noveTips,tt);
}
initTips();
if(typeof(HTMLElement)!="undefined"){
    HTMLElement.prototype.contains=function (obj){
    while(obj!=null&&typeof(obj.tagName)!="undefind"){
    if(obj==this) return true;
    obj=obj.parentNode;
    }
    return false;
    }
}
}
//让背景渐渐变暗
function showBackground(obj,endInt)
{
if(isIe)
{
obj.filters.alpha.opacity+=5;
if(obj.filters.alpha.opacity<endInt)
{
setTimeout(function(){showBackground(obj,endInt)},5);
}
}else{
var al=parseFloat(obj.style.opacity);al+=0.05;
obj.style.opacity=al;
if(al<(endInt/100))
{setTimeout(function(){showBackground(obj,endInt)},5);}
}
}
//关闭窗口
function closeWindow()
{
if(document.getElementById('back')!=null)
{
document.getElementById('back').parentNode.removeChild(document.getElementById('back'));
}
if(document.getElementById('mesWindow')!=null)
{
document.getElementById('mesWindow').parentNode.removeChild(document.getElementById('mesWindow'));
}
if(isIe){
setSelectState('');}
}

