var isIE = /msie/i.test(navigator.userAgent);

function gID(id){return document.getElementById(id);}

//漂浮
//参数：层ID，记录上次滚动位置（默认可以为空，递归使用）
function ScrollDiv(id,pScrollY){ 
	//var ScrollY = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
	var ScrollY = document.documentElement.scrollTop || document.body.scrollTop; //兼容处理
	if (pScrollY ==null) { pScrollY=0; }

	var moveTop = .1 * (ScrollY - pScrollY); //可调整移动速度
	moveTop = (moveTop > 0) ? Math.ceil(moveTop) : Math.floor(moveTop);
	gID(id).style.top = parseInt(gID(id).style.top) + moveTop + "px";

	pScrollY = pScrollY + moveTop; 
	setTimeout("ScrollDiv('"+id+"',"+pScrollY+");",50); //可调整滚动后的反映速度
}


//增加事件动作，不冲突原来的事件
//参数，对象，事件名称（带on），事件定义（如果要带参数，则要function(){eventFunc("")}这样传递参数）
function addObjEvent(obj,eventName,eventFunc){
	if (obj.attachEvent){ //IE
		obj.attachEvent(eventName,eventFunc);
	}else if (obj.addEventListener){ //FF Gecko / W3C
		var eventName2 = eventName.toString().replace(/on(.*)/i,'$1'); //正则过滤第1个on
		obj.addEventListener(eventName2,eventFunc, false); //fslse为倒序执行事件
	}else{
		obj[eventName] = eventFunc;
	}
}


//移除事件动作
//参数，对象，事件名称（带on），事件定义（如果要带参数，则要function(){eventFunc("")}这样传递参数）
function delObjEvent(obj,eventName,eventFunc){
	if (obj.detachEvent) { // IE
		obj.detachEvent(eventName,eventFunc);
	}else if (obj.removeEventListener){ //FF Gecko / W3C
		var eventName2 = eventName.toString().replace(/on(.*)/i,'$1'); //正则过滤第1个on
		obj.removeEventListener(eventName2,eventFunc, false); //fslse为倒序执行事件
	}else{
		obj[eventName] = null;
	}
}


//可以任意拖动的层（支持Firefox,IE)
//参数，移动的层对象和event对象，方法 onmousedown="MoveDiv(this,event)"
function MoveDiv(obj,e){
	e = e||window.event;

	var ie6=isIE;
	if (/msie 9/i.test(navigator.userAgent)) {ie6=false;} //把IE9设置为非IE浏览器
	//只允许通过鼠标左键进行拖拽,IE68鼠标左键为1 FireFox ie9其他为0
	if (ie6 && e.button == 1 || !ie6 && e.button == 0) {}else{return false;}

	obj.style.position='absolute'; //设置浮动模式
	obj.ondragstart =function(){return false;} //禁止对象的拖动事件，不然图片在火狐下会无法拖动

	var x = e.screenX - obj.offsetLeft;
	var y = e.screenY - obj.offsetTop;
	addObjEvent(document,'onmousemove',moving); //鼠标移动时，增加移动事件
	addObjEvent(document,'onmouseup',endMov); //鼠标放开时，增加停止事件
	e.cancelBubble = true; //禁止事件冒泡,使触发在子对象上的事件不传递给父对象
	
	//IE去除选中背景文字
	if (isIE) {
		obj.setCapture(); //设置捕获范围 releaseCapture() 释放
	} else {
		window.captureEvents(Event.mousemove); //window.releaseEvents(Event.eventType) 释放
	}

	//if (!isIE){e.stopPropagation();} //W3C 禁止冒泡
	//FireFox 去除容器内拖拽图片问题，火狐防止选中背景文字
	if (e.preventDefault) {
		e.preventDefault(); //取消事件的默认动作
		e.stopPropagation(); //事件不再被分派到其他节点
	}
	e.returnValue = false; //指事件的返回值是false 。return false;是指函数的返回值为false
	return false;

	//移动
	function moving(e){
		obj.style.left = (e.screenX - x) + 'px';
		obj.style.top = (e.screenY - y) + 'px';
		return false; //图片移动时会出现拖动图片的动作，增加这个return可以不执行这个动作
	}
		
	//停止
	function endMov(e){
		delObjEvent(document,'onmousemove',moving); //删除鼠标移动事件
		delObjEvent(document,'onmouseup',arguments.callee); //删除鼠标放开事件,arguments.callee为函数本身
		if (isIE) {
			obj.releaseCapture(); //释放捕获
		} else {
			window.releaseEvents(Event.mousemove); //释放
		}
	}
}