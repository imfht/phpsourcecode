var offsetxpoint=-60 //Customize x offset of tooltip
var offsetypoint=20 //Customize y offset of tooltip
var ie=document.all
var ns6=document.getElementById && !document.all
var enabletip=false
if (ie||ns6)
var tipobj=document.all? document.all["dhtmltooltip"] : document.getElementById? document.getElementById("dhtmltooltip") : ""


function ietruebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function ddrivetip(thetext, thecolor, thewidth){

if (ns6||ie){
if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px"
if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor
tipobj.innerHTML=thetext
//tipobj.style='border: 1px solid FFDA8C;padding: 4px;background-color: FFFFDD;color: #993300;';
enabletip=true
return false
}
}

function positiontip(e){
if (enabletip){
var curX=(ns6)?e.pageX : event.x+ietruebody().scrollLeft;
var curY=(ns6)?e.pageY : event.y+ietruebody().scrollTop;
//Find out how close the mouse is to the corner of the window
var rightedge=ie&&!window.opera? ietruebody().clientWidth-event.clientX-offsetxpoint : window.innerWidth-e.clientX-offsetxpoint-20
var bottomedge=ie&&!window.opera? ietruebody().clientHeight-event.clientY-offsetypoint : window.innerHeight-e.clientY-offsetypoint-20

var leftedge=(offsetxpoint<0)? offsetxpoint*(-1) : -1000

//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<tipobj.offsetWidth)
//move the horizontal position of the menu to the left by it's width
tipobj.style.left=ie? ietruebody().scrollLeft+event.clientX-tipobj.offsetWidth+"px" : window.pageXOffset+e.clientX-tipobj.offsetWidth+"px"
else if (curX<leftedge)
tipobj.style.left="5px"
else
//position the horizontal position of the menu where the mouse is positioned
tipobj.style.left=curX+offsetxpoint+"px"

//same concept with the vertical position
if (bottomedge<tipobj.offsetHeight)
tipobj.style.top=ie? ietruebody().scrollTop+event.clientY-tipobj.offsetHeight-offsetypoint+"px" : window.pageYOffset+e.clientY-tipobj.offsetHeight-offsetypoint+"px"
else
tipobj.style.top=curY+offsetypoint+"px"
tipobj.style.visibility="visible"
}
}

function hideddrivetip(){
if (ns6||ie){
enabletip=false
tipobj.style.visibility="hidden"
tipobj.style.left="-1000px"
tipobj.style.backgroundColor=''
tipobj.style.width=''
}
}
//Kill IE 6
var ietips='<div id=\"_ietips\" style=\"display:none;background:#000;height:40px;line-height:40px;left:0; opacity:0.80; -moz-opacity:0.80; filter:alpha(opacity=80); position:fixed;bottom:0;width:100%;z-index:999; text-align:center; color:#FFF; font-size:16px;_bottom:auto; _width: 100%; _position: absolute; _top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-(parseInt(this.currentStyle.marginTop,10)||0)-(parseInt(this.currentStyle.marginBottom,10)||0)))\">\u5F53\u524D\u6D4F\u89C8\u5668\u7248\u672C\u592A\u4F4E\uFF0C\u60A8\u5C06\u65E0\u6CD5\u5B8C\u7F8E\u4F53\u9A8C\u6211\u4EEC\u7CFB\u7EDF\uFF01<a href=\"http://www.doccms.com\" target=\"_blank\">\u7A3B\u58F3CMS<\/a>\u5C06\u5168\u9762\u4E0D\u8003\u8651\u517C\u5BB9IE6\u7684\u95EE\u9898\uFF0C\u5982\u4E0D\u80FD\u6EE1\u8DB3\u60A8\u7684\u8981\u6C42\uFF0C\u8BF7<a href=\"http://www.shlcms.com\" target=\"_blank\">\u4E0B\u8F7DSHLCMS4.2<\/a>\u6765\u89E3\u51B3\21</div>';
if($.browser.version=="6.0"){$("body").append(ietips);setTimeout('$("#_ietips").fadeIn(2000);',1000);}
//-->