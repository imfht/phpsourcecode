function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr;
  for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++){
	  x.src=x.oSrc;
  }
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function swapLayer(sById1, sById2){
	var div1	= document.getElementById(sById1);
	var div2	= document.getElementById(sById2);
	if(div1 == null || div2 == null) return;
	if(div1.style.display == 'none'){
		div1.style.display = "block";
		div2.style.display = "none";
	}else{
		div1.style.display = "none";
		div2.style.display = "block";
	}
}

/* ================================================
<img onMouseOver=xwzImgRollOver(this, true, '/path/src/sample.gif') onMouseOut=xwzImgRollOver(this)
================================================ */
function xwzImgRollOver(oImg, bType, sSrc){
	if(bType == true){
		if( oImg.getAttribute('xwzAlreadySrc') == null) oImg.setAttribute('xwzAlreadySrc', oImg.src);
		oImg.src = sSrc;
	}else{
		if( oImg.getAttribute('xwzAlreadySrc') != null) oImg.src = oImg.getAttribute('xwzAlreadySrc');
	}
}

function xwzRollingImageTrans(imageName, thumbnailName, eventName, winName){
	this.Index = 0;
	this.ListItem = new Array(0);
	this.Name = imageName;
	this.Thumbnail = thumbnailName;
	this.tmRotate = null;
	this.nInterval = 4500;
	this.eventName = eventName;
	this.winTarget = winName;
	
	if(window.xwzRollObject == null) window.xwzRollObject = new Array(0);
	window.xwzRollObject[this.Name] = this;

	this.install = function(){
		window.document.images[this.Name].onclick=this.goLink;
		if(this.ListItem.length == 0) return;
		this.tmRotate = setTimeout("window.xwzRollObject['" + this.Name + "'].rotateTrans()" , this.nInterval);
		var icons = document.getElementsByName(this.Thumbnail);
		for(var i = 0; i < icons.length; i++){
			if(this.eventName == 'over') icons[i].onmouseover=new Function("window.xwzRollObject['"+this.Name+"'].alterImage(" + i + ")");
			else icons[i].onclick=new Function("window.xwzRollObject['"+this.Name+"'].alterImage(" + i + ")");
		}
	}

	this.addItem = function(Link, ImgSrc, Icon1, Icon2 ){
		var itmX = {Link : "", ImgSrc : "", DefIcon : "", OvrIcon : ""};
		itmX.Link = Link;
		itmX.ImgSrc = ImgSrc;
		itmX.DefIcon = Icon1;
		itmX.OvrIcon = Icon2;
		this.ListItem[this.ListItem.length] = itmX;
	}

	this.alterImage = function(index){
		var icons = document.getElementsByName(this.Thumbnail);
		if(this.Index == index) return;
		if(this.ListItem[this.Index].DefIcon !="") icons[this.Index].src = this.ListItem[this.Index].DefIcon;

		this.Index = index;
		this.imgTrans();
		clearTimeout(this.tmRotate);
		this.tmRotate = null;
		this.tmRotate = setTimeout("window.xwzRollObject['" + this.Name + "'].rotateTrans()" , this.nInterval);

	}


	this.goLink = function(){
		var name = this.getAttribute('name');
		var xwzRoll = window.xwzRollObject[name];
		clearTimeout(xwzRoll.tmRotate);
		xwzRoll.tmRotate = null;
        xwzRoll.winTarget="_blank"; //定义是否弹出新页面
		if(xwzRoll.winTarget == '' || xwzRoll.winTarget == null){
			window.location.href=xwzRoll.ListItem[xwzRoll.Index].Link;
		}else{
			window.open(xwzRoll.ListItem[xwzRoll.Index].Link, xwzRoll.winTarget);
		}
	}


	this.rotateTrans = function(){
		var icons = document.getElementsByName(this.Thumbnail);
		var itmX = this.ListItem[this.Index];
		if(itmX.DefIcon !="") icons[this.Index].src = itmX.DefIcon;

		this.Index +=1;
		if(this.Index >= this.ListItem.length) this.Index = 0;

		this.imgTrans();
		
		clearTimeout(this.tmRotate);
		this.tmRotate = null;
		this.tmRotate = setTimeout("window.xwzRollObject['" + this.Name + "'].rotateTrans()" , this.nInterval);
	}


	this.imgTrans = function(){
		var icons = document.getElementsByName(this.Thumbnail);
		var itmX = this.ListItem[this.Index];
		if(itmX.OvrIcon !=null  && itmX.OvrIcon !="") icons[this.Index].src = itmX.OvrIcon;
		try{
			document.images[this.Name].filters[0].apply();
			document.images[this.Name].src = itmX.ImgSrc;
			document.images[this.Name].filters[0].play();
		}catch(e){
			document.images[this.Name].src = itmX.ImgSrc;
		}
	}
}


function xwzRollingMarqTrans(Name, eventName){
	this.Name = Name;
	this.Index = 0;
	this.ListItem = new Array(0);

	this.tmRotate = null;
	this.nInterval = 4500;
	this.eventName = eventName;

	if(window.xwzMarqObject == null) window.xwzMarqObject = new Array(0);
	window.xwzMarqObject[this.Name] = this;


	this.install = function(){
		if(this.ListItem.length == 0) return;
		this.tmRotate = setTimeout("window.xwzMarqObject['" + this.Name + "'].rotateTrans()" , this.nInterval);
		for(var i = 0; i < this.ListItem.length; i++){
			if(this.eventName == 'over') this.ListItem[i].Img.onmouseover=new Function("window.xwzMarqObject['"+this.Name+"'].alterTrans(" + i + ")");
			else this.ListItem[i].Img.onclick=new Function("window.xwzMarqObject['"+this.Name+"'].alterTrans(" + i + ")");
		}
	}

	this.addItem = function(targetObj, targetImg, Icon1, Icon2 ){
		var itmX = {Objects : null, Imgs : null, DefaultSrc : "",OverSrc : ""};
		itmX.Object = targetObj;
		itmX.Img = targetImg;
		itmX.DefaultSrc = Icon1;
		itmX.OverSrc = Icon2;
		this.ListItem[this.ListItem.length] = itmX;
	}


	this.alterTrans = function(index){
		if(this.Index == index) return;
		var itmX = this.ListItem[this.Index];
		if(itmX.DefaultSrc !="") itmX.Img.src = itmX.DefaultSrc;
		this.Index = index;
		this.objTrans();
		clearTimeout(this.tmRotate);
		this.tmRotate = null;
		this.tmRotate = setTimeout("window.xwzMarqObject['" + this.Name + "'].rotateTrans()" , this.nInterval);
	}

	this.rotateTrans = function(){
		var itmX = this.ListItem[this.Index];
		if(itmX.DefaultSrc !="") itmX.Img.src = itmX.DefaultSrc;

		this.Index +=1;
		if(this.Index >= this.ListItem.length) this.Index = 0;

		this.objTrans();
		
		clearTimeout(this.tmRotate);
		this.tmRotate = null;
		this.tmRotate = setTimeout("window.xwzMarqObject['" + this.Name + "'].rotateTrans()" , this.nInterval);
	}


	this.objTrans = function(){
		var itmX = this.ListItem[this.Index];
		if(itmX.Img !=null  && itmX.OverSrc !="") itmX.Img.src = itmX.OverSrc;
		for(var i =0; i <  this.ListItem.length; i++) this.ListItem[i].Object.style.display = 'none';
		try{
			itmX.Object.filters[0].apply();
			itmX.Object.style.display='';
			itmX.Object.filters[0].play();
		}catch(e){
			itmX.Object.style.display='';
		}
	}
}

function verticalWheel(id, height, nSec ){
	if(window.xwzWheelMarq == null) window.xwzWheelMarq = new Array(0);
	xwzWheelMarq[id]  = {
		install : function(id, height, nSec){
			this.id = id;
			this.div = document.getElementById('ID_DIV_KEYWORD');
			this.table = document.getElementById('ID_TABLE_KEYWORD');
			if(this.div == null) return;
			//this.div.style.cssText = "height:"+height+";position:relative;cursor:pointer;";
			this.div.style.cssText = "height:"+height+";overflow:hidden;position:relative;cursor:pointer;clip:rect(0 auto "+this.height+" 0);left:0;top:0";
			this.div.parentNode.style.position='relative'
			this.div.parentNode.onmouseover=function(){xwzWheelMarq[id].table.style.visibility ='visible';	xwzWheelMarq[id].nPause=true;}
			this.div.parentNode.onmouseout= function(){xwzWheelMarq[id].table.style.visibility ='hidden';xwzWheelMarq[id].nPause=false;}	

			this.index =0;
			this.height = height;
			this.items = new Array(0);
			this.tmID = null;
			this.nPause = false;
			this.nSec = nSec;
			var rows =this.table.tBodies[0].rows;
			for(var i = 0; i < rows.length; i++){
				this.items[i] = document.createElement("DIV");
				this.items[i].innerHTML=rows[i].innerHTML;
				this.items[i].style.padding="3";
				this.items[i].style.width="100%";
				this.items[i].style.height=this.height ;
				this.items[i].style.position="absolute";
				this.items[i].style.top = this.height*i;
				this.div.appendChild(this.items[i]);

				rows[i].cells[0].style.cssText="padding-left:5px;border-bottom:#CACACA 1px dotted;";
				rows[i].onmouseover=function(){this.style.backgroundColor="#FDF1F0";}
				rows[i].onmouseout=function(){this.style.backgroundColor="";}
				if(i >= rows.length -1){rows[i].cells[0].style.border="";}
			}
		},
		doWheel : function(){
			var itmN = this.items[this.index];
			var nSleep= 50;
			var nIndex= this.index+1 >=  this.items.length ? 0 :  this.index+1;
			clearTimeout(this.tmID); this.tmID = null;
			if(this.nPause != true){
				for(var i = 0; i < this.items.length; i++){
					this.items[i].style.top = parseInt(this.items[i].style.top)-1;
				}
				if(parseInt(itmN.style.top) <= this.height*-1){
					itmN.style.top = this.height*(this.items.length-1);
					this.index = this.index+1 >=  this.items.length ? 0 :  this.index+1;
					nSleep= this.nSec;
				}
			}else{
				if(parseInt(itmN.style.top) < (this.height/2)*-1){
					itmN.style.top = this.height*(this.items.length-1);
					this.index = this.index+1 >=  this.items.length ? 0 :  this.index+1;
				}

				for(var i = 0; i < this.items.length; i++){
					this.items[i].style.top =  this.height * (  (this.items.length -  this.index + i ) % this.items.length );
				}
				nSleep = 10;
			}

			
			this.tmID = setTimeout("xwzWheelMarq['" + this.id + "'].doWheel()", nSleep);
		}
	}


	xwzWheelMarq[id].install(id, height, nSec);
	xwzWheelMarq[id].tmID = setTimeout("xwzWheelMarq['" +id + "'].doWheel()", nSec);

	
}