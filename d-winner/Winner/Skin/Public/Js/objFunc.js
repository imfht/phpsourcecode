// JavaScript Document
//日期差
function getDateDiff(date1,date2){
     var arr1=date1.split('-');
	 var arr2=date2.split('-');
	 var d1=new Date(arr1[0],arr1[1],arr1[2]);
	 var d2=new Date(arr2[0],arr2[1],arr2[2]);
	 return (d2.getTime()-d1.getTime())/(1000*3600*24);
}

//判断变量
function isset(variable){
    return typeof(variable)=='undefined' ? false : true;
}

//键盘对应值
function keyCode(w){
	var k = {65:'a',66:'b',67:'c',68:'d',69:'e',70:'f',71:'g',72:'h',73:'i',74:'j',75:'k',76:'l',77:'m',78:'n',79:'o',80:'p',81:'q',82:'r',83:'s',84:'t',85:'u',86:'v',87:'w',88:'x',89:'y',90:'z'};
	return k[w].toUpperCase();
}

var ns = 0;
var vs = '';
function keyValue(w,t){
	var kw = keyCode(w);
	var d = new Date();
	d = d.getTime();
	d = Math.floor(d);
	if(ns==0 || d-ns>t){
		vs = '';
	}
	ns = d;
	vs += kw;
	return vs;
}

function showLocale(objD){  
    var str,colorhead,colorfoot;  
    var yy = objD.getYear();  
    if(yy<1900) yy = yy+1900;  
    var MM = objD.getMonth()+1;  
    if(MM<10) MM = '0' + MM;  
    var dd = objD.getDate();  
    if(dd<10) dd = '0' + dd;  
    var hh = objD.getHours();  
    if(hh<10) hh = '0' + hh;  
    var mm = objD.getMinutes();  
    if(mm<10) mm = '0' + mm;  
    var ss = objD.getSeconds();  
    if(ss<10) ss = '0' + ss;  
    var ww = objD.getDay();  
    if  ( ww==0 )  colorhead="<font class=\"up-font-over\">";  
    if  ( ww > 0 && ww < 6 )  colorhead="<font class=\"up-fonts\">";  
    if  ( ww==6 )  colorhead="<font class=\"up-font-over\">";  
    if  (ww==0)  ww="星期日";  
    if  (ww==1)  ww="星期一";  
    if  (ww==2)  ww="星期二";  
    if  (ww==3)  ww="星期三";  
    if  (ww==4)  ww="星期四";  
    if  (ww==5)  ww="星期五";  
    if  (ww==6)  ww="星期六";  
    colorfoot="</font>"  
    str = colorhead + yy + "年" + MM + "月" + dd + "日  " + ww + colorfoot;  
    return(str);  
}

function dump_obj(obj) {  
	var s = "";  
	for (var property in obj) {  
		s = s + "\r\n" + property +": " + obj[property];  
	}  
 	alert(s);
}  
function pad(num, n) {  
  return Array(n>num?(n-(''+num).length+1):0).join(0)+num;  
} 

function queryComboTree(q, comboid, mode) {
	var combotreeid = "#" + comboid;
	var trees = $(combotreeid).combotree('tree');					//得到根节点
	var roots = trees.tree('getRoots');								//得到根节点数组
	var m = trees[0].innerHTML;
	var step = 1;
	var arr=m.match(/<span class="tree-title">.*?<\/span>/g);   
	var aq = q.split(',');
	var qp = aq[0].toUpperCase();
	var children;
	if (q == "") {						//如果文本框的值为空，或者将文本框的值删除了，重新reload数据
	    $(combotreeid).combotree('reload');
		$(combotreeid).combotree('clear');
		$(combotreeid).combotree('setText', q);
		trees.tree('scrollTo',roots[0].target);
	    return;
	}
	//循环数组，找到与输入值相似的，加到前面定义的数组中，
	for (i = roots.length-1; i >= 0; i--) {
		var v = arr[i];
		v = v.replace(/<\/?.+?>/g,"");
		var vu = v.toUpperCase();
		var vp = String(getPinYin(v));
		if(aq[0]){
			if(mode==1){
				if(vp.indexOf(qp)>=0 || vu.indexOf(aq) >= 0){
					trees.tree('scrollTo',roots[i].target);
					trees.tree('select',roots[i].target);
				}
			}
			//找子节点（递归）
			childrensTree(combotreeid, roots[i].target, aq[1]?aq[1]:aq[0] ,aq ,step++);
		}
	}
}

function childrensTree(combotreeid, rootstarget, qs ,aq ,step) {
	var childrenlist = [];
	var qp = qs.toUpperCase();
	trees = $(combotreeid).combotree('tree');
	var ischlid = trees.tree('isLeaf',rootstarget);
	if(!ischlid){
		child = trees.tree('getChildren',rootstarget);
		for(z=0; z<child.length; z++){
			childrenlist[z] = child[z].text;
		}
		for (j = child.length-1; j >= 0; j--) {
			var a = childrenlist[j];
			var au = a.toUpperCase();
			var ap = String(getPinYin(a));
			if(qs){
				if(ap.indexOf(qp)>=0 || au.indexOf(qp) >= 0){
					trees.tree('scrollTo',child[j].target);
					trees.tree('select',child[j].target);
				}
				//找子节点（递归）
				childrensTree(combotreeid, child[j].target, aq[step]?aq[step]:qs ,aq ,step++);
			}
		}
	}
}