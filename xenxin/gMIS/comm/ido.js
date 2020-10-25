//- 
//- init from Mon Jul 28 15:38:37 CST 2014
//- Fri Aug  1 13:58:12 CST 2014
//-- Thu Sep 11 09:30:12 CST 2014
//-- Thu Sep 25 16:07:58 CST 2014
//-- Mon Sep 29 16:01:21 CST 2014
//-- Mon Oct 27 15:58:46 CST 2014
//-- 09:48 Wednesday, July 01, 2015
//- with ido_proj.js , 10:37 Sunday, January 10, 2016
//- wrap iId as string, 09:03 09 October 2016
//- imprvs on searchbytime, Thu, 2 Mar 2017 16:32:06 +0800
//- bugfix on firefox with event, 23:34 02 August 2017
//- bugfix for async, 19:14 Thursday, 15 March, 2018
//- imprvs on pivot with addgroupbyseg, 17 August, 2018
//- imprvs on pickup, Fri Sep 21 21:09:34 CST 2018
//- imprvs on todo/filedir, Sat Oct 20 CST 2018
//- imprvs with userinfo.$lang, Tue Nov  5 03:39:27 UTC 2019
//- bugfix for getUrlByTime and PickUp, 13:33 Saturday, March 28, 2020

var currenttbl = currenttbl ? currenttbl : '';
var currentdb =  currentdb ? currentdb : '';
var currentpath = currentpath ?  currentpath : '';
var currentlistid = currentlistid ? currentlistid : {}; //-- associative array
var userinfo =  userinfo ? userinfo : {};

//-
userinfo.$lang = userinfo.$lang ? userinfo.$lang : {};
userinfo.pickUpFromTag = '&frompickup=1';

if(!window.console){
	console = { log:function(){}};
}

//-- script used in /manage/items/index.jsp, 20090303, wadelau
function pnAction(url){
    if(url.indexOf("act=") == -1 && url.indexOf("?") > -1){
        url += "&act=list";
    }
	doAction(url);
}

//-
function doAction(strUrl){
	var rtn = true;
	var sActive = "actarea";
	var myAjax = new GTAjax();
	myAjax.set('targetarea',sActive);
	myAjax.set('forceframe',true);
	myAjax.set('nobacktag','nobacktag');
	var tmps = myAjax.get( appendTab(strUrl) );
	if(typeof strUrl == 'string'){
	if(strUrl.indexOf('needautopickup=no') < 0){ //- see extra/linktbl
        if(strUrl.indexOf('&act=list') > -1 && strUrl.indexOf(userinfo.pickUpFromTag) == -1){
            var pickUpUrl = strUrl.replace('&act=list', '&act=pickup');
            var doActionPickUpTimer=window.setTimeout(function(){ doActionEx(pickUpUrl, 'contentarea'); }, 2*1000);
            //console.log('doAction: found list refresh, trigger pickup reload.... timer:'+doActionPickUpTimer+' purl:'+pickUpUrl);
			if(typeof userinfo.PickUpList == 'undefined'){ userinfo.PickUpList = {}; }
            userinfo.PickUpList.latestUrl = pickUpUrl;
        }
    }
    }
	if(typeof userinfo.time2Quit != 'undefined'){
        userinfo.time2Quit = (new Date()).getTime() + parseInt(userinfo.time4Renew);
    }
	return rtn;
}

function doActionEx(strUrl,sActive){  

	if(sActive=='addareaextra'){
		//document.getElementById('addareaextradiv').style.display='block';
        switchArea('addareaextradiv', 'on');
	}
    if(sActive == 'contentarea'){
        /*
        document.getElementById('contentarea_outer').style.display='block';
        document.getElementById('contentarea').style.display='block';
        */
        switchArea('contentarea_outer', 'on');
        switchArea('contentarea', 'on');
    }
	var myAjax = new GTAjax();
	myAjax.set('targetarea',sActive);
	myAjax.set('nobacktag','nobacktag');
	myAjax.set('forceframe',true);
	var tmps = myAjax.get( appendTab(strUrl) );
	if(typeof strUrl == 'string'){
	    if(strUrl.indexOf('needautopickup=no') < 0){ //- see extra/linktbl
        if(sActive == 'actarea' && strUrl.indexOf('&act=list') > -1 && strUrl.indexOf(userinfo.pickUpFromTag) == -1){
            var pickUpUrl = strUrl.replace('&act=list', '&act=pickup');
            var doActionPickUpTimer=window.setTimeout(function(){ doActionEx(pickUpUrl, 'contentarea'); }, 2*1000);
            //console.log('doActionEx: found list refresh, trigger pickup reload.... timer:'+doActionPickUpTimer+' purl:'+pickUpUrl);
			if(typeof userinfo.PickUpList == 'undefined'){ userinfo.PickUpList = {}; }
            userinfo.PickUpList.latestUrl = pickUpUrl;
        }
        }
		if(strUrl.indexOf('_Updt_Clit_Urlp=1') > -1){
            if(typeof userinfo.urlParams == 'undefined'){ userinfo.urlParams={}; };
            var tmpTimerId = window.setTimeout(function(){ 
                    userinfo.urlParams = getUrlParams(strUrl); 
                    console.log("strUrl:["+strUrl+"] try to reload urlparams"); 
                }, 2*1000);  
        }
    }
	if(typeof userinfo.time2Quit != 'undefined'){
        userinfo.time2Quit = (new Date()).getTime() + parseInt(userinfo.time4Renew);
    }
	return tmps;
}

//-
function _g( str ){
	return document.getElementById( str );
}

//
function appendTab(strUrl){
	if(typeof strUrl == 'string'){
	if(strUrl.indexOf(".php")>-1){
		if(strUrl.indexOf("tbl")==-1){
			//window.alert('need to append acctab.');
			if(strUrl.indexOf("?")==-1){
				strUrl+="?tbl="+currenttbl;
			}
			else{
				strUrl+="&tbl="+currenttbl;
			}
		}
		if(strUrl.indexOf("db")==-1){
			//window.alert('need to append acctab.');
			strUrl+="&db="+currentdb;
			//window.alert('need to append acctab.done:['+strUrl+']');
		}
	}
	}
	return appendSid(strUrl);
}

//- display an area or not
function switchArea(sArea, onf){
    if(sArea == null || sArea == ''){
        sArea = 'contentarea';
    }
    oldv = document.getElementById(sArea).style.display;
    newv = '';

    if(onf == null || onf == ''){
        if(oldv == 'block'){
            newv = 'none';
        }else if(oldv == 'none'){
            newv = 'block';
        }
    }else if(onf == 'on'){
        newv = 'block';
    }else if(onf == 'off'){
        newv = 'none';
    }

    document.getElementById(sArea).style.display=newv;

}

//-- search by a field in navigator menu
//-- updt with security enhancement, Spet 08, 2018
function searchBy(url){
    var fieldlist = document.getElementById('fieldlist').value;
    var fieldlisttype = document.getElementById('fieldlisttype').value;
    var fieldarr = fieldlist.split(",");
    var fieldtypearr = fieldlisttype.split(",");
    var typearr = new Array();
    for(var i=0; i<fieldtypearr.length; i++){
       var tmparr = fieldtypearr[i].split(":");
       typearr[tmparr[0]] = tmparr[1];
    }
    var appendquery = '';
	var reg1055 = new RegExp("，", 'gm');
	var reg1608 = new RegExp("\\\\", 'gm');
	var reg1559 = new RegExp("'", 'gm');
    for(var i=0;i<fieldarr.length;i++){
        var fieldv = null;
        eval("var obj = document.getElementById('pnsk_"+fieldarr[i]+"');");
        if(obj != null){
            if(typearr[fieldarr[i]] == 'select'){
                eval("fieldv = document.getElementById('pnsk_"+fieldarr[i]+"').options[document.getElementById('pnsk_"+fieldarr[i]+"').selectedIndex].value;");
                //window.alert('field:'+fieldarr[i]+' select:fieldv:['+fieldv+']');
                console.log('field:'+fieldarr[i]+' select:fieldv:['+fieldv+']');
                if(fieldv == ""){
					var reg = new RegExp("&pnsk"+fieldarr[i]+"=([^&]*)", 'gm');
					url = url.replace(reg, "");
					reg = new RegExp("&oppnsk"+fieldarr[i]+"=([^&]*)", 'gm');
					url = url.replace(reg, "");
                    continue;
                }
            }
			else{
                eval("fieldv = document.getElementById('pnsk_"+fieldarr[i]+"').value;");
            }
            //if(fieldv != fieldarr[i]){
            if(fieldv != '~~~'){
				var fieldopv = '';
                if(document.getElementById('oppnsk_'+fieldarr[i]) != null){
                    eval("fieldopv = document.getElementById('oppnsk_"+fieldarr[i]+"').options[document.getElementById('oppnsk_"+fieldarr[i]+"').selectedIndex].value;");
                	console.log('fieldv:['+fieldv+'] fieldop:'+fieldarr[i]+' select:fieldopv:['+fieldopv+']');
					if(fieldopv != '----'){
						if((fieldopv == 'inrange' || fieldopv == 'inlist') && fieldv == ''){
							//- omit	
						}
						else{
							fieldv = fieldv.replace(reg1055, ",");
							fieldv = fieldv.replace(reg1608, "");
							fieldv = fieldv.replace(reg1559, "\\\'");
                			appendquery += "&pnsk"+fieldarr[i]+"="+fieldv;
                    		appendquery += "&oppnsk"+fieldarr[i]+"="+fieldopv;
						}
					}
					else{
						//var reg = new RegExp("&oppnsk"+fieldarr[i]+"=([^&]*)");
    					//url = url.replace(reg, "");
					}
                }
				else{
					fieldv = fieldv.replace(reg1055, ","); 
					appendquery += "&pnsk"+fieldarr[i]+"="+fieldv;
				}
                var reg = new RegExp("&pnsk"+fieldarr[i]+"=([^&]*)", 'gm');
                url = url.replace(reg, "");
				reg = new RegExp("&oppnsk"+fieldarr[i]+"=([^&]*)", 'gm');
    			url = url.replace(reg, "");
            }
        }
    }
    //window.alert("fieldlist:"+fieldlist+", url:["+url+"]");
    console.log("fieldlist:"+fieldlist+", url:["+url+"]");
	//- @todo 
    var reg = new RegExp("&pntc=([0-9]*)", 'gm');
    url = url.replace(reg, "");
    reg = new RegExp("&pnpn=([0-9]*)", 'gm');
    url = url.replace(reg, "");
    reg = new RegExp("&pnsk[0-9a-zA-Z]+=([^&]*)", 'gm');
    url = url.replace(reg, "");
	reg = new RegExp("&oppnsk"+fieldarr[i]+"=([^&]*)", 'gm');
    url = url.replace(reg, "");
	reg = new RegExp("&id=([^&]*)", 'gm'); //- remove old id query, Mon, 12 Dec 2016 13:41:21 +0800
    url = url.replace(reg, "");
    
    doAction(url+appendquery);
    console.log("fieldlist:"+fieldlist+",last_url:["+url+appendquery+"]");

}
//-- 挂表操作传递参数
function sendLinkInfo(vars, rw, fieldtag){
    if(rw == 'w'){
        if(parent.currentlistid){
            //parent.currentlistid[fieldtag] = vars;
            parent.currentlistid[fieldtag] = decodeURIComponent(vars.replace(/\+/g,' '));
        }else{
            //window.alert('parent.currentlistid is ['+parent.currentlistid+'].');
        }
    }
    //window.alert('sendLinkInfo:'+currentlistid[fieldtag]+', 22-:'+parent.currentlistid[fieldtag]+', 33-:'+currentlistid.fieldtag+', 44-:'+parent.currentlistid.fieldtag+', win.href:['+window.location.href+'],  rw:['+rw+'] fieldtag:['+fieldtag+']');
    if(rw == 'r'){
        tmpid = parent.currentlistid[fieldtag]==undefined?'':parent.currentlistid[fieldtag];
        parent.currentlistid[fieldtag] = '';
        return tmpid;
    }
    //console.log('sendLinkInfo: vars:['+vars+'] rw:['+rw+'] fieldtag:['+fieldtag+']');
    return true;
}

//-- auto calculate numbers, designed by Wadelau@ufqi.com, BGN
//-- 16/02/2012 23:11:28
/* example:
 * 	onclick="x_calcu_onf(this);"
 * 	onchange="x_calcu(this, 'zongzhichu', {'*':'peitongfangshu','+':'otherid'});"
 * means: zongzhichu = this.value * peitongfangshu + otherid
 */
var x_currentCalcu = {};
function x_calcu_onf(thisfield){
    //window.alert('oldv: ['+thisfield.value+']');
    var fieldid = thisfield.id;
    x_currentCalcu.fieldid = thisfield.value==''?0:thisfield.value;
}
function x_calcu(thisfield, targetx, otherlist){
    var fieldid = thisfield.id;
    if(x_currentCalcu.fieldid == null || x_currentCalcu.fieldid == undefined || x_currentCalcu.fieldid == 'null'){
        var tmpobj = document.getElementById(fieldid);
        x_currentCalcu.fieldid = tmpobj.value == ''?0:tmpobj.value;
    }
    var thisfieldv = thisfield.value==''?0:thisfield.value;
    var bala = thisfieldv - x_currentCalcu.fieldid;
    var tgt = document.getElementById(targetx);
    var formulax  = '';
    if(tgt != null && isNumber(x_currentCalcu.fieldid) && isNumber(thisfieldv)){
            var oldv = tgt.value==''?0:tgt.value;
            oldv = parseInt(oldv);
            for(var k in otherlist){
                //window.alert('k:['+k+'] val:['+otherlist[k]+']');
                var tmpobj = document.getElementById(otherlist[k]);
                var tmpval = 1;
                if(tmpobj != null){
                    tmpval = tmpobj.value==''?0:tmpobj.value;
                }else if(k == '+' || k == '-'){
                    tmpval = 0;
                }
                formulax += ' ' + k + ' ' + tmpval;
            }
            //window.alert('formulax:['+formulax+']');
            var balax = eval(bala+formulax);
            var newv = oldv + parseInt(balax);
            tgt.value = parseInt(newv);
            //window.alert('oldv:['+x_currentCalcu.fieldid+'] new-field: ['+thisfield.value+'] bala:['+bala+'] formula:['+formulax+'] balax:['+balax+'] newv:['+newv+']');
            //x_currentCalcu.fieldid = null;
    }else{
        window.alert('Javascript:x_calcu: Error! targetx:['+targetx+'] is null or x_currentCalcu.'+fieldid+':['+x_currentCalcu.fieldid+'] is not numeric. \n\tClick an input field firstly.');
        thisfield.focus();
    }
}
function isNumber(n){
	return !isNaN(parseFloat(n)) && isFinite(n);
}
//- added Wed Apr  4 19:57:23 CST 2012
function x_calcuTbl(theform, targetx, f){
    var id = theform.id;
    if(typeof id != 'string'){
        id = theform.name;
    }
    //window.alert('this.id:['+id+'] name:['+theform.name+'] method:['+theform.method+'] formula:['+f+']');
    var fArr = f.split(" "); //-- use space to separate each element in the formula
    var symHm = {'=':'','+':'','-':'','*':'','/':'','(':'',')':''};
    for(var i=0; i<fArr.length; i++){
        //window.alert('i:['+i+'] f:['+fArr[i]+']');
        if(fArr[i] != null && fArr[i] != ''){
        if(fArr[i] in symHm){
            //-
        }else{
            if(isNumber(fArr[i])){
                //-
            }else{
                var field = document.getElementById(fArr[i]);
                var fVal = null;
                if(field != null){
                    fVal = field.value==''?0:field.value;
                    fVal = parseInt(fVal);
                    fVal = fVal == NaN?0:fVal;
                    f = f.replace(new RegExp(' '+fArr[i],"gm"), ' '+fVal);
                    f = f.replace(new RegExp(fArr[i]+' ',"gm"), fVal+' ');
                }else{
                    window.alert('x_calcuTbl: Unknown field:['+fArr[i]+']');
                }
                //window.alert('field:['+fArr[i]+'] val:['+fVal+'] new formula:['+f+']');
            }
        }
        }
    }
    //window.alert('new formula:['+f+']');
    var targetx = document.getElementById(targetx);
    if(targetx != null){
        targetx.value = eval(f);
    }
}
//-- auto calculate numbers, designed by Wadelau@ufqi.com, END

//- dynammic select, bgn, Sun Mar 11 11:36:44 CST 2012
function fillSubSelect(parentId, childId, logicId, myUrl){
    var fieldv = document.getElementById(parentId).options[document.getElementById(parentId).selectedIndex].value;
    var fieldorig = _g(childId+'_select_orig').value;
    fieldorig = fieldorig == null?'':fieldorig;
    console.log("currentVal:["+fieldv+"]");
    console.log("fieldv:["+fieldv+"] logicId:["+logicId+"] orig_value:["+fieldorig+"]"); 
    if(fieldv != ''){

    if(logicId == 'xiane'){
        var gta = new GTAjax();
        gta.set('targetarea', 'addareaextradiv');
        gta.set("callback", function(){
                    //window.alert("getresult:["+this+"]");
                    var s = this;
                    //console.log("getresult:["+s+"]");
                    var sArr = s.split("\n");
                    for(var i=0;i<sArr.length;i++){
                        //console.log('i:['+i+'] line:['+sArr[i]+']');
                        var tmpArr = sArr[i].split(':::');
                        console.log('key:['+tmpArr[0]+'] val:['+tmpArr[1]+']');
                        if(tmpArr[0] != '' && tmpArr[0] != 'id' && tmpArr[1] != undefined){
                            var issel = false;
                            if(fieldorig.indexOf(tmpArr[0]) > -1){
                                issel = true;
                            }
                            document.getElementById(childId).options[i] = new Option(tmpArr[1]+'('+tmpArr[0]+')',tmpArr[0], true,issel);
                        }
                    }
                });
        gta.get(appendSid(myUrl+'?objectid='+fieldv+'&isoput=0&logicid='+logicId));

    }else if(logicId == 'mingcheng'){
        var gta = new GTAjax();
        gta.set('targetarea', 'addareaextradiv');
        gta.set("callback", function(){
                    //window.alert("getresult:["+this+"]");
                    var s = this;
                    console.log("getresult:["+s+"]");
                    var sArr = s.split("\n");
                    for(var i=0;i<sArr.length;i++){
                        //console.log('i:['+i+'] line:['+sArr[i]+']');
                        var tmpArr = sArr[i].split(':::');
                        console.log('key:['+tmpArr[0]+'] val:['+tmpArr[1]+']');
                        if(tmpArr[0] != '' && tmpArr[0] != 'id' && tmpArr[1] != undefined){
                            document.getElementById(childId).options[i] = new Option(tmpArr[1]+'('+tmpArr[0]+')',tmpArr[0], true,false);
                        }
                    }
                });
        gta.get(appendSid(myUrl+'?objectid='+fieldv+'&isoput=0&logicid='+logicId)); 

    }else if(logicId == 'leibie'){
                    console.log("getinto leibie");
        var gta = new GTAjax();
        gta.set('targetarea', 'addareaextradiv');
        gta.set("callback", function(){
                    //window.alert("getresult:["+this+"]");
                    var s = this;
                    console.log("getresult:["+s+"]");
                    var sArr = s.split("\n");
                    for(var i=0;i<sArr.length;i++){
                        //console.log('i:['+i+'] line:['+sArr[i]+']');
                        var tmpArr = sArr[i].split(':::');
                        console.log('key:['+tmpArr[0]+'] val:['+tmpArr[1]+']');
                        if(tmpArr[0] != '' && tmpArr[0] != 'id' && tmpArr[1] != undefined){
                            var issel = false;
                            if(fieldorig.indexOf(tmpArr[0]) > -1){
                                issel = true;
                            }
                            document.getElementById(childId).options[i+1] = new Option(tmpArr[1]+'('+tmpArr[0]+')',tmpArr[0], true,issel);
                        }
                    }
                });
        gta.get(appendSid(myUrl+'?tbl=categorytbl&objectid='+fieldv+'&isoput=0&logicid='+logicId+'&parentid='
        		+parentId+'&childid='+childId));
    
    }else if(logicId == 'area'){
                    console.log("getinto area");
        var gta = new GTAjax();
        gta.set('targetarea', 'addareaextradiv');
        gta.set("callback", function(){
                    //window.alert("getresult:["+this+"]");
                    var s = this;
                    console.log("getresult:["+s+"]");
                    var sArr = s.split("\n");
                    for(var i=0;i<sArr.length;i++){
                        //console.log('i:['+i+'] line:['+sArr[i]+']');
                        var tmpArr = sArr[i].split(':::');
                        console.log('key:['+tmpArr[0]+'] val:['+tmpArr[1]+']');
                        if(tmpArr[0] != '' && tmpArr[0] != 'id' && tmpArr[1] != undefined){
                            var issel = false;
                            if(fieldorig.indexOf(tmpArr[0]) > -1){
                                issel = true;
                            }
                            document.getElementById(childId).options[i] = new Option(tmpArr[1]+'('+tmpArr[0]+')',tmpArr[0], true,issel);
                        }
                    }
                });
        gta.get(appendSid(myUrl+'?tbl=areatbl&objectid='+fieldv+'&isoput=0&logicid='+logicId));
    }

    }
    else{
        console.log("ido.js::fillSubSelect::fieldv:["+fieldv+"] is empty.");
    }
}
//- dynammic select, end, Sun Mar 11 11:36:44 CST 2012

//-- setSelectIndex, bgn, Tue May  8 20:59:42 CST 2012
//-- 其中一个 select 变化时，其余 select 的 selectedIndex 也跟着动
function setSelectIndex(mySelect, myValue){
    var objsel = document.getElementById(mySelect);
    if(objsel != null){
        for(var i = 0; i < objsel.options.length; i++){
            if(objsel.options[i].value == myValue){
                if(objsel.selectedIndex != i){
                    objsel.selectedIndex = i;
                }
                break;
            }
        }
    }
}
//-- setSelectIndex, bgn, Tue May  8 20:59:42 CST 2012

userinfo.input2Select = {};
//- switchEditable, bgn, Thu Mar 15 20:14:02 CST 2012
//-- 增加对 select 点击即编辑的支持
function switchEditable(targetObj,fieldName,fieldType,fieldValue,myUrl,readOnly){
    if(readOnly != ''){
    	console.log("field:["+fieldName+"] is not configed to edit in this view. e.g. multiple select, textarea.");
        return true;
    }
	var theobj = targetObj;
    targetObj = document.getElementById(theobj);
    targetObj.contentEditable = true;
    targetObj.style.background = "#ffffff";
    var newSelId = theobj+'_new1425';
    var newsel = null;
	var newseldiv = null;
    if(fieldType == 'select'){
       //window.alert('is Select:['+fieldValue+']!'); 
       newsel = document.createElement('select');
       newsel.setAttribute('id', newSelId);
       var searsel = document.getElementById('pnsk_'+fieldName);
       if(searsel != null){
            for(var i=0; i < searsel.length; i++){ //- 复制搜索栏里的select选项到当前
                var isselected = false;
                if(searsel.options[i].value == fieldValue){
                    isselected = true;
                }
                //window.alert('is Select:['+fieldValue+'] currentVal:['+searsel.options[i].value+'] isselected:['+isselected+']!'); 
                newsel.add(new Option(searsel.options[i].text, searsel.options[i].value, isselected, isselected));       
            }
       }
       targetObj.innerHTML = '';
       targetObj.appendChild(newsel);
    }
	else if(fieldType == 'input2select'){
		newseldiv = document.getElementById(newSelId);
		if(!newseldiv){
       		newseldiv = document.createElement('div');
       		newseldiv.setAttribute('id', newSelId);
       		newseldiv.style.cssText = 'display:none;position:absolute;background:#fff;border:#777 solid 1px;margin:-1px 0 0;padding: 5px;font-size:12px; overflow:auto;z-index:49;';
	   		//targetObj.appendChild(newseldiv);
	   		targetObj.parentNode.insertBefore(newseldiv, targetObj.nextSibling);
		}
	   targetObj.onkeyup = function(evt){ evt = evt || window.event; input2Search(targetObj,fieldName, newSelId, 'NEED_OPTION_VALUE'); }
	   //targetObj.onclick = function(evt){ evt = evt || window.event; userinfo.input2Select.makeSelect = 0; }

		if(!userinfo.input2Select){ userinfo.input2Select = {}; }
		userinfo.input2Select.makeSelect = 0;
	}
	else{
        //- bugfix for disp shortening with non-select in list view
        var tmpv = targetObj.innerHTML;
        var fieldtmpv = Base62x.decode(fieldValue);
        if(tmpv.length < fieldtmpv.length){
            targetObj.innerHTML = fieldtmpv;
            console.log("found disp shortenning and remedy...");
        }
	}
	if(fieldType != 'input2select'){
		if(!userinfo.input2Select){ userinfo.input2Select = {}; }
		userinfo.input2Select.makeSelect = 1;
	}
    var oldv = targetObj.innerHTML;
    var blurObj = targetObj;
    if(fieldType == 'select'){ 
        oldv = fieldValue;
        blurObj = newsel;
    }
	userinfo.input2Select.originalValue = oldv;

    blurObj.onblur = function(newVal){
        var newv = targetObj.innerHTML;
        var newvStr = '';
        if(fieldType == 'select'){
            newv = newsel.options[newsel.selectedIndex].value;
            newvStr = newsel.options[newsel.selectedIndex].text;
        } 
        console.log('oldv:['+oldv+'] newv:['+newv+'] newvStr:['+newvStr+'] makeselect:['+userinfo.input2Select.makeSelect+']');
        if(newv != oldv && userinfo.input2Select.makeSelect == 1){
            //window.alert('newcont:['+newv+'] \noldv:['+oldv+']');
            var gta = new GTAjax();
            gta.set('targetarea', 'addareaextradiv');
            gta.set("callback", function(){
                        var s = this;
                        var noticeMsg = '';
						if(s.indexOf('--SUCC--') > -1){
							noticeMsg = userinfo.$lang['notice_success'];
                            sendNotice(true, noticeMsg);
                        }
						else{
							noticeMsg = userinfo.$lang['notice_failure'];
                        	sendNotice(false, '');
                        }
                    });
            gta.get(appendSid(myUrl+'&'+fieldName+'='+encodeURIComponent(newv)));
        }
		else{
            //window.alert('same content. unchanged.');
        }

        targetObj.style.background = "#E8EEF7";
        if(fieldType == 'select'){
            targetObj.innerHTML = newvStr; 
        }else{
            targetObj.innerHTML = newv;
        }
		if(fieldType == 'input2select'){
			userinfo.input2Select.makeSelect = 0;
		}
		userinfo.input2Select.originalValue = newv;

    }

}
//- switchEditable, end, Thu Mar 15 20:14:02 CST 2012

//-- notice bgn, Mon Mar 19 21:41:02 CST 2012
function sendNotice(isSucc, sMsg){
    var obj = document.getElementById('top_notice_div');
    if(obj != undefined && obj != null){
        if(isSucc){
            obj.innerHTML = '<span style="background-color:yellow; color:green">&nbsp; <b> '+sMsg+' </b> &nbsp;</span>';
        }
		else{
            obj.innerHTML = '<span style="background-color:yellow; color:red">&nbsp; <b> '+sMsg+' </b> &nbsp; </span>';
        }
        window.setTimeout(function(){ obj.innerHTML = ''; }, 8*1000);
    }
	else{
        window.alert(sMsg);
    }
}
//-- notice end, Mon Mar 19 21:41:02 CST 2012

//-- register an action to be run in a few seconds later, bgn
//-- see xml/x_useraccesstbl.xml
function registerAct(tObj){
    if(tObj.status == 'onload'){
        //window.addEventListener('load', function(){
        if(true){
            //window.alert('delaytime:['+tObj.delaytime+']');
            /*
            var actx = unescape(tObj.action);
            actx = actx.replace('+', ' '); //- need to be replaced with -Base62x, 09:14 24 September 2016
            actx = actx.replace('\+', ' '); //- need to be replaced with -Base62x, 09:14 24 September 2016
            actx = actx.replace('%20', ' '); //- need to be replaced with -Base62x, 09:14 24 September 2016
            */
            var actx = Base62x.decode(tObj.action); //- imprv, July 26, 2018
            var actxId = 0;
            if(!userinfo.registerAct){
                userinfo.registerAct = {};
            }
            actxId = userinfo.registerAct[tObj.action];
            if(actxId){
                window.clearTimeout(actxId);
            }
            actxId = window.setTimeout(actx, tObj.delaytime*1000);
            userinfo.registerAct[tObj.action] = actxId;
        };//, false);
        //console.log('register.action:['+unescape(tObj.action)+'] actx:['+actx+'] axtxId:['+actxId+']');
    }
    else{
        console.log((new Date())+" comm/ido: unsupported registerAct:"+tobj.status);
    }
}
//-- register an action to be run in a few seconds, end

//-- act list, 执行动作, bgn, Sat Jun  2 19:19:12 CST 2012
function doActSelect(sSel, sUrl, iId, fieldVal){
    var fieldv = fieldVal;
    if((fieldv == null || fieldv =='') && fieldv != 'ActOption'){
    	fieldv = document.getElementById(sSel).options[document.getElementById(sSel).selectedIndex].value;
	}
    console.log("doActSelect: fieldv:["+fieldv+"]");
    var targetUrl = sUrl;
    if(fieldv != 'ActOption'){
        targetUrl += "&act="+fieldv;
    }
	var actListDiv = document.getElementById('divActList_'+iId); var hideActListDiv = 1;

    if(fieldv != ''){
        if(fieldv == 'list-dodelete'){
           var deleteDelay = 10; // seconds
			if(!actListDiv){
				console.log('actListDiv was lost.....')
			}
			var deleteTimerId = window.setTimeout(function(){
				window.console.log('delay delete started.......'+(new Date()));
				var gta = new GTAjax();
				gta.set('targetarea', 'addareaextradiv');
				gta.set("callback", function(){
						var resp = this;
						console.log('delete_resp:['+resp+']'); // copy from iframe document ?
						if(resp.indexOf('<pre') > -1){
							var resp_1 = /<pre[^>]*>([^<]*)<\/pre>/g;
							var resp_2 = resp_1.exec(resp);
							//console.log('delete_resp_after:['+resp_2[1]+'] id:['+iId+']');
							resp = resp_2[1];
						}
						var json_resp = JSON.parse(resp);
						if(typeof json_resp.resultobj == 'undefined'){ json_resp.resultobj = {}; }
						var iId = json_resp.resultobj.targetid; //- anonymous func embeded in another anonymos func, cannot share variables in runtime.
						//console.log('delete_resp_after-2:['+resp+'] id:['+iId+']');
						if(json_resp.resultobj.resultcode == 0){
							sendNotice(true, userinfo.$lang['notice_success']+' TargetId:['+iId+']');
							var its_list_tr = document.getElementById('list_tr_'+iId);
							if(its_list_tr){
								its_list_tr.style.backgroundColor = '#404040';
							} 
							var actListDiv = document.getElementById('divActList_'+iId); //- 
							if(actListDiv){
    							actListDiv.style.display = 'none';
							}
                            var parentDataTbl = parent._g('gmisjdomaintbl');
                            if(typeof parentDataTbl != 'undefined'){
                                parentDataTbl.deleteRow((parseInt(iId)-1)+3); //- first 3 rows are funcs
                                console.log("found main tbl:["+parentDataTbl+"] and delete tr-id:["+iId+"]");
                            }
                            else{
                                console.log("not found main tbl:["+parentDataTbl+"] with tr-id:["+iId+"]");
                            }
						}
						else{
							iId = json_resp.resultobj.resulttrace;
							sendNotice(false, userinfo.$lang['notice_failure']+' ErrCode:'+iId);
						}
					});
				gta.get(appendSid(targetUrl+'&async=1&fmt=json&targetLineId='+iId));
	
				}, deleteDelay * 1000);
            
			actListDiv.innerHTML = '<span style="color:red"> &nbsp; ' + iId + userinfo.$lang['notice_delete_soon']
				+deleteDelay+' seconds, [<a href="javascript:window.clearTimeout('+deleteTimerId+');switchArea(\'divActList_'
				+iId+'\',\'off\');console.log(\'delete is canceled.\'+(new Date())); ">'+userinfo.$lang['func_cancel']+'</a>]...</span>'; 
			hideActListDiv = 0;	
			//if(isconfirm){
            //}
        }
		else if(fieldv == 'print'){
			window.open(sUrl+'&act=print&isoput=1&isheader=0','PrintWindow','scrollbars,toolbar,location=0');
		}
		else{
            doActionEx(targetUrl, 'contentarea');
        }
    }
    else{
    	//--
    }
	
    if(actListDiv && hideActListDiv == 1){
    	actListDiv.style.display = 'none';
	}

}
//-- act list, end, Sat Jun  2 19:19:12 CST 2012

//-- getUrlByTime, Sat Jun 23 11:15:09 CST 2012
function getUrlByTime(baseUrl, timepara, timeop, timeTag){
    var url = baseUrl;
    var myDate = new Date();
    var today = myDate.getDay();
    var now = myDate.getTime();
    var fromd = 0;
    var tod = 0;
    var fromD = new Date(fromd);
    var toD = new Date(tod);
    var fromDStr = '';
    var toDStr = '';
	if(timeTag == 'TODAY'){
        today = myDate.getDate(); 
        fromd = now; // + (-today)*86400*1000;
        tod = now; // + (+today)*86400*1000;
     }
     else if(timeTag == 'YESTERDAY'){
        //today = myDate.getDate(); 
        //now = now - 86400*1000*30;
        fromd = now + (-1)*86400*1000;
        tod = now + (-1)*86400*1000;
     }
     else if(timeTag == 'THIS_WEEK'){
       fromd = now + (-today+1)*86400*1000;
       tod = now + (7-today)*86400*1000;
     }
     else if(timeTag == 'LAST_WEEK'){
       now = now - 86400*1000*7;
       fromd = now + (-today+1)*86400*1000;
       tod = now + (7-today)*86400*1000;
    }
    fromD = new Date(fromd);
    toD = new Date(tod);
    fromDStr = fromD.getFullYear()+'-'+(fromD.getMonth()+1)+'-'+fromD.getDate();
    toDStr = toD.getFullYear()+'-'+(toD.getMonth()+1)+'-'+toD.getDate();
	if(timepara.indexOf('time') > -1 || timepara.indexOf('hour') > -1){
    	fromDStr += ' 00:00:00';
    	toDStr += ' 23:59:59';
    }
    if(timeop == 'inrange'){
        url += '&pnsk'+timepara+'='+fromDStr+','+toDStr+'&oppnsk'+timepara+'='+timeop+'&pnsm=1';
    }
    else{
    	console.log('unknown timeop:['+timeop+'].1703021921.');
    }
    //window.alert('now:['+now+'] fromd:['+fromd+'] tod:['+tod+'] url:['+url+']');
    doAction(url);
}
//-- getUrlByTime, Sat Jun 23 11:15:09 CST 2012


//-- old functions
function updateTag(tagtype,tagid,str){
	try{
		if(tagtype=='div' || tagtype=='span'){
			document.getElementById(tagid).innerHTML=str;
		}
		else{
			document.getElementById(tagid).value=str;
		}
	}
	catch(err){
		//--
		window.alert('update err.');
	}

}

//-
function checkAll(){
	var boxValue="";
	for(var i=0;i<document.all.checkboxid.length;i++){
	    document.all.checkboxid[i].checked   =   true;
		boxValue= boxValue +document.all.checkboxid[i].value+",";
	}
	window.clipboardData.setData('text',boxValue);
	window.alert("Something wrong. 03061743.");
}

//-
function uncheckAll(){
	var box1="";
	for(var i=0;i<document.all.checkboxid.length;i++){
		if(document.all.checkboxid[i].checked == false)
		{
			 document.all.checkboxid[i].checked   =   true;
		   box1 = box1+document.all.checkboxid[i].value+",";
		}
		else
		{
			 document.all.checkboxid[i].checked = false;
		}
	}
  window.clipboardData.setData('text',box1);
  window.alert("Something wrong. 03061744.");
}

//-
function batchDelete(url,checkboxid){
	var box="";
	for(var i=0;i<document.all.checkboxid.length;i++){
		if(document.all.checkboxid[i].checked == true){
	    	box = box+document.all.checkboxid[i].value+",";
		}
	}
	var url1 = url+"&checkboxid="+box;
	if(box==""){
		if(document.all.copyid.value=="??"){
		    alert("Something wrong. 03061745.");
		}
		else{
		    alert("Something wrong. 03061746.");
		}
	}
	else{
		if(document.all.copyid.value=="??"){
			if(confirm("Are you sure:"+box)){
			    doAction(url1);
			}
		}
		else if(document.all.copyid.value=="??"){
			if(confirm("Are you sure:"+box)){
				doAction(url1);
			}
		}
	}
}

//-
function WdatePicker(){
    var evt;
    if(navigator.userAgent.toLowerCase().indexOf('firefox/') > -1){ // firefox
            var evtarg = arguments[0];
            if(!evtarg){
                console.log('firefox has no global event, please invoke as \"WdatePicker(event);\" .201708022320.'); 
            }
            evt = window.event ? window.event : evtarg;
    }
    else{
        evt = window.event ? window.event : event;
    }
    //var obj = getElementByEvent(event);
	var obj = getElementByEvent(evt);
    obj = document.getElementById(obj); 
    //window.alert('obj.id:['+obj.id+'] this.name:['+obj.name+']');
    if(obj && obj.id != null){
        var newId = (obj.id).replace(new RegExp('-','gm'), '_');
        var myDatePicker = new DatePicker('_tmp'+newId,{
            inputId: obj.id,
            separator: '-',
            className: 'date-picker-wp'
        });
    }
	else{
        sendNotice(userinfo.$lang['notice_datap_invalid']+' Object:['+obj+']');
    }
}

//-
var DatePicker = function () {
    var $ = function (i) {return document.getElementById(i)},
        addEvent = function (o, e, f) {o.addEventListener ? o.addEventListener(e, f, false) : o.attachEvent('on'+e, function(){f.call(o)})},
        getPos = function (el) {
            for (var pos = {x:0, y:0}; el; el = el.offsetParent) {
                pos.x += el.offsetLeft;
                pos.y += el.offsetTop;
            }
            return pos;
        }

    var init = function (n, config) {
        window[n] = this;
        Date.prototype._fd = function () {var d = new Date(this); d.setDate(1); return d.getDay()};
        Date.prototype._fc = function () {var d1 = new Date(this), d2 = new Date(this); d1.setDate(1); d2.setDate(1); d2.setMonth(d2.getMonth()+1); return (d2-d1)/86400000;};
        this.n = n;
        this.config = config;
        this.D = new Date;
        this.el = $(config.inputId);
        this.el.title = this.n+'DatePicker';
        this.update();
        this.bind();
    }

    init.prototype = {
        update : function (y, m) {
             var con = [], week = ['Su','Mo','Tu','We','Th','Fr','Sa'], D = this.D, _this = this;
             fn = function (a, b) {return '<td title="'+_this.n+'DatePicker" class="noborder hand" onclick="'+_this.n+'.update('+a+')">'+b+'</td>'},
                _html = '<table cellpadding=0 cellspacing=2>';
             y && D.setYear(D.getFullYear() + y);
             m && D.setMonth(D.getMonth() + m);
             var year = D.getFullYear(), month = D.getMonth() + 1, date = D.getDate();
             for (var i=0; i<week.length; i++) con.push('<td title="'+this.n+'DatePicker" class="noborder">'+week[i]+'</td>');
             for (var i=0; i<D._fd(); i++ ) con.push('<td title="'+this.n+'DatePicker" class="noborder">?</td>');
             for (var i=0; i<D._fc(); i++ ) con.push('<td class="hand" onclick="'+this.n+'.fillInput('+year+', '+month+', '+(i+1)+')">'+(i+1)+'</td>');
             var toend = con.length%7;
             if (toend != 0) for (var i=0; i<7-toend; i++) con.push('<td class="noborder">?</td>');
             _html += '<tr>'+fn("-1, null", "<<")+fn("null, -1", "<")+'<td title="'+this.n+'DatePicker" colspan=3 class="strong">'+year+'/'+month+'/'+date+'</td>'+fn("null, 1", ">")+fn("1, null", ">>")+'</tr>';
             for (var i=0; i<con.length; i++) _html += (i==0 ? '<tr>' : i%7==0 ? '</tr><tr>' : '') + con[i] + (i == con.length-1 ? '</tr>' : '');
             !!this.box ? this.box.innerHTML = _html : this.createBox(_html);
         },
        
        fillInput : function (y, m, d) {
                var s = this.config.separator || '/';
                this.el.value = y + s + m + s + d;
                this.box.style.display = 'none';
            },
        
        show : function () {
           var s = this.box.style, is = this.mask.style;
           s['left'] = is['left'] = getPos(this.el).x + 'px';
           s['top'] = is['top'] = getPos(this.el).y + this.el.offsetHeight + 'px';
           s['display'] = is['display'] = 'block';
           is['width'] = this.box.offsetWidth - 2 + 'px';
           is['height'] = this.box.offsetHeight - 2 + 'px';
        },

        hide : function () {
           this.box.style.display = 'none';
           this.mask.style.display = 'none';
        },

        bind : function () {
           var _this = this;
           addEvent(document, 'click', function (e) {
                   e = e || window.event;
                   var t = e.target || e.srcElement;
                   if (t.title != _this.n+'DatePicker') {_this.hide()} else {_this.show()}
                   })
        },

        createBox : function (html) {
                var box = this.box = document.createElement('div'), mask = this.mask = document.createElement('iframe');
                box.className = this.config.className || 'datepicker';
                mask.src = 'javascript:false';
                mask.frameBorder = 0;
                box.style.cssText = 'position:absolute;display:none;z-index:9999';
                mask.style.cssText = 'position:absolute;display:none;z-index:9998';
                box.title = this.n+'DatePicker';
                box.innerHTML = html;
                document.body.appendChild(box);
                document.body.appendChild(mask);
                return box;
            }
        }

    return init;
}();

//-  getElementByEvent works well with MS Edge && Google Chrome, but uncertain with Mozilla Firefox
//- remedy by Xenxin@Ufqi on 23:42 02 August 2017
function getElementByEvent(e){
    var targ;
	var evt = e;
    if (!evt){ evt = window.event; }
    if (evt && evt.target){ targ = evt.target;}
    else if (evt && evt.srcElement){ targ = evt.srcElement };
    
    if (targ && targ.nodeType == 3){ // defeat Safari bug
        targ = targ.parentNode
    }
    //window.alert('targ:['+targ+']');
    var tId;
	if(targ){
		tId=targ.id;
		if(tId == null || tId == '' || tId == undefined){
			tId = targ.name;
		}
	}
	else{
		console.log('getElementByEvent failed. ev:['+ev+'] e:['+e+'] targ:['+targ+']');
	}
    //window.alert('targ:['+targ+'] id:['+tId+']');
    return tId;
}

//- added on Thu Jul 25 09:13:23 CST 2013
//- by wadelau@ufqi.com
//- for html editor
function getCont(sId){
    var obj = document.getElementById(sId);
    var objtype = '';
    var cont = '';
    if(obj){
        objtype = obj.tagName.toLowerCase();
        if(objtype == 'div'){
            cont = obj.innerHTML; 
        }else{
            cont = obj.value;
        }
    }
    console.log('./comm/ido.js: getCont: sId:['+sId+'] cont:['+cont+']');
    return cont;
}

//-
function setCont(sId, sCont){
    var obj = document.getElementById(sId);
    var objtype = '';
    if(obj){
        objtype = obj.tagName.toLowerCase();
        if(objtype == 'div'){
            obj.innerHTML = sCont;
        }else{
            obj.value = sCont;
        }
    }
	console.log("setCont: ["+sCont+"]");
	//window.alert("setCont once");
    return 0;
}

//-
function openEditor(sUrl, sField){
    document.getElementById(sField+"_myeditordiv").innerHTML = "<iframe name=\'myeditoriframe\' id=\'myeditoriframe\' src=\'"+sUrl+"\' width=\'680px\' height=\'450px\' border=\'0px\' frameborder=\'0px\'></iframe>"; 
} 

//-- select to input & search, Sun Jul 27 21:25:39 CST 2014
function changeBGC(obj, onoff){
	if(onoff==1){
		obj.style.background='silver';
	}
	else{ 
		obj.style.background='#fff';
	}
}

//- 10:37 2020-07-26
function switchBgc(obj, newBgc){
	var currBgc = obj.style.background;
	if(currBgc != ''){
		obj.style.background='';
	}
	else{ 
		obj.style.background=newBgc;
	}
}

//-
function makeSelect(sId, sCont, sDiv, sSele, iStop){
	//-- this would be called after targetObj.onblur
	setCont(sId, sCont);
	if(!iStop){
		var hidesele = document.getElementById(sSele);
		if(hidesele != null){
			for(var i=0; i < hidesele.length; i++){ //- 复制搜索栏里的select选项到当前
				var seleText = hidesele.options[i].text;
				if(seleText == sCont){
					hidesele.selectedIndex = i;	
					break;
				}
			}
			console.log('makeSelect: i:'+i);
		}
	}
	if(!iStop || iStop == '2' || iStop == '4'){
		var objDiv = document.getElementById(sDiv)
		objDiv.style.display='none';
		objDiv.innerHTML = '';
	}
	if(iStop == '1' || iStop == '4'){
		userinfo.input2Select.makeSelect = 1;
	}
	if(iStop == '2' || iStop == '3'){
		userinfo.input2Select.makeSelect = 0;
	}
}

//-
function input2Search(inputx, obj, div3rd, valueoption){
	var lastSearchTime = userinfo.lastInput2SearchTime;
	var lastSearchItem = userinfo.lastInput2SearchItem;
	var nowTime = (new Date()).getTime();
	var balaTime = nowTime - lastSearchTime;
	var inputVal = inputx.value==null?inputx.innerText:inputx.value;
	var destobj = 'input2sele_'+obj; //-- where selected content would be copied to
	var selelistdiv = 'pnsk_'+obj+'_sele_div'; //-- where items would be listed on
	var obj1737 = '';
	if(userinfo.input2Select != null && userinfo.input2Select.obj1737 != null){
		obj1737 = userinfo.input2Select.obj1737;
	}
	else{
		obj1737 = document.getElementById(selelistdiv); 
		if(userinfo.input2Select == null){ userinfo.input2Select = {}; }
		userinfo.input2Select.obj1737 = obj1737;
	}
	if(div3rd != null){
		selelistdiv = div3rd;
		obj1737 = document.getElementById(selelistdiv);
		destobj = div3rd.replace(new RegExp('_new1425', 'gm'), ''); 
		//-- there are two modes to invoke input2Search, one is on search bar, the other is on each record line. 'div3rd' is the latter tag.
	}
	var origdestvalue = ''; if(!userinfo.input2Select){ userinfo.input2Select = {}; }
	origdestvalue = userinfo.input2Select.originalValue; //-- the value which display as the page loaded.
	obj1737.style.display = 'block';
	if(inputVal.length < 2 || balaTime < 8 || inputVal == lastSearchItem){
		// || balaTime < 100 
		//console.log('input-length:'+inputVal.length+', balaTime:'+balaTime+', lastItem:'+lastSearchItem+',  thisItem:'+inputVal+', bypass');
		//obj1737.innerHTML = '';
		return 0;
	}
	else{
		console.log('input-length:'+inputVal.length+', balaTime:'+balaTime+', lastItem:'+lastSearchItem+', thisItem:'+inputVal);
		var iInputX = inputVal.toLowerCase();
		var hidesele = '';
		if(userinfo.input2Select.hideSele != null){
			hidesele = userinfo.input2Select.hideSele;	
		}
		else{
			hidesele = document.getElementById('pnsk_'+obj+'');
			userinfo.input2Select.hideSele = hidesele;
		}
		var odata = ""; selectLength = 0; 
		var dataarr = []; var j = 0;
		if(userinfo.input2Select.selectLength != null){
			selectLength = userinfo.input2Select.selectLength;	
		}
		else{
			selectLength = hidesele.length;
			userinfo.input2Select.selectLength = selectLength;
		}
		//-- cacheOptionList, added by wadelau, 	Tue Oct 13 08:22:55 CST 2015
		var cacheOptionList = document.getElementById('pnsk_'+obj+'_optionlist'); //- see lazyLoad and class/gtbl.class
		//console.log(cacheOptionList.value);
		if(cacheOptionList != null && cacheOptionList != ''){
			console.log("use high-speed cacheOptionList....");
			var col = JSON.parse(cacheOptionList.value);
			for(var opti in col){
				var seleText = col[opti];
				var seleVal = opti;
				//console.log("text:["+seleText+"] val:["+seleVal+"]");	
				if(seleText.toLowerCase().indexOf(iInputX) > -1){
					//--
					if(valueoption == null || valueoption == ''){
						dataarr[j++] = '<span onmouseover=parent.changeBGC(this,1); onmouseout=parent.changeBGC(this,0);'+
							+' onclick=parent.makeSelect(\''+destobj+'\',this.innerText,\''+selelistdiv+'\',\'pnsk_'
							+obj+'\');>'+seleText+'-('+seleVal+')</span>';
					}
					else if(valueoption == 'NEED_OPTION_VALUE'){ //-- div3rd mode
						dataarr[j++] = '<span onmouseover=parent.changeBGC(this,1);parent.makeSelect(\''+destobj+'\',\''
						+seleVal+'\',\''+selelistdiv+'\',\'pnsk_'+obj+'\',1); onmouseout=parent.changeBGC(this,0);'+
						+'userinfo.input2Select.makeSelect=0; onclick=parent.makeSelect(\''+destobj+'\',this.innerText,\''
						+selelistdiv+'\',\'pnsk_'+obj+'\',4);>'+seleText+'-('+seleVal+')</span>';
					}
					if(j>30){
						dataarr[j++] = userinfo.$lang['more']+'....';
						break;	
					}
				}
			}
		}
		else if(hidesele != null){
			for(var i=0; i < selectLength; i++){ //- 复制搜索栏里的select选项到当前
				var seleText = hidesele.options[i].text;
				var seleVal = hidesele.options[i].value;
				if(seleText.toLowerCase().indexOf(iInputX) > -1){
					//--
					if(valueoption == null || valueoption == ''){
						dataarr[j++] = '<span onmouseover=parent.changeBGC(this,1); onmouseout=parent.changeBGC(this,0); onclick=parent.makeSelect(\''+destobj+'\',this.innerText,\''+selelistdiv+'\',\'pnsk_'+obj+'\');>'+seleText+'</span>';
					}
					else if(valueoption == 'NEED_OPTION_VALUE'){ //-- div3rd mode
						dataarr[j++] = '<span onmouseover=parent.changeBGC(this,1);parent.makeSelect(\''+destobj+'\',\''+seleVal+'\',\''+selelistdiv+'\',\'pnsk_'+obj+'\',1); onmouseout=parent.changeBGC(this,0);userinfo.input2Select.makeSelect=0; onclick=parent.makeSelect(\''+destobj+'\',this.innerText,\''+selelistdiv+'\',\'pnsk_'+obj+'\',4);>'+seleText+'</span>';
					}
					if(j>30){
						dataarr[j++] = userinfo.$lang['more']+'....';
						break;	
					}
				}
			}
		}
		if(dataarr.length == 0){
			j=0;
			//dataarr[j] = "......Not Found....";	
			dataarr[j] = "......Searching....";	
		}
		if(true){
			//-- close action
			j++;
			dataarr[j] = '<span onmouseover="parent.changeBGC(this,1);parent.makeSelect(\''+destobj+'\',\''+origdestvalue+'\',\''+selelistdiv+'\',\'pnsk_'+obj+'\',3);" onmouseout="parent.changeBGC(this,0);" onclick="javascript:userinfo.input2Select.makeSelect=0;parent.makeSelect(\''+destobj+'\',\''+origdestvalue+'\',\''+selelistdiv+'\',\'pnsk_'+obj+'\',2);">'+userinfo.$lang['func_cancel']+'</span>';
		}
		odata = dataarr.join('<br/>');
		//console.log(odata);	
		obj1737.innerHTML = odata;
		userinfo.lastInput2SearchTime = (new Date()).getTime();
		userinfo.lastInput2SearchItem = inputVal;
		userinfo.input2Select.makeSelect = 0; //-- clear makeSelect
	}
}

//-
function showActList(nId, isOn, sUrl, dataId){
	var divId = 'divActList_'+nId;		
	//console.log((new Date())+": divId:["+divId+"]");
	var divObj = document.getElementById(divId);
	var dispVal = divObj.style.display;
	if(isOn == 1){ dispVal = 'block'; }else{ dispVal = 'none';}
	divObj.style.display = dispVal;
	divObj.onmouseover = function(){ this.style.display='block'; };
	divObj.onmouseout = function(){ this.style.display='none'; };

	var sCont = '<p>'; var targetAreaId = '#contentarea_outer';
	sCont += '&nbsp; &nbsp;&nbsp;<a href="'+targetAreaId+'" onclick="javascript:doActSelect(\'\', \''+sUrl+'\', \''
		+nId+'\', \'view\');"> - '+userinfo.$lang['func_view']+'</a>&nbsp; &nbsp;&nbsp;';
	sCont += '<br/>&nbsp; &nbsp;&nbsp;<a href="'+targetAreaId+'" onclick="javascript:doActSelect(\'\', \''+sUrl+'\', \''
		+nId+'\', \'modify\');"> - '+userinfo.$lang['func_edit']+'</a>&nbsp; &nbsp;&nbsp;';
	sCont += '<br/>&nbsp; &nbsp;&nbsp;<a href="'+targetAreaId+'" onclick="javascript:doActSelect(\'\', \''+sUrl+'\', \''
		+nId+'\', \'print\');"> - '+userinfo.$lang['func_print']+'</a>&nbsp; &nbsp;&nbsp;';
	sCont += '<br/>&nbsp; &nbsp;&nbsp;<a href="'+targetAreaId+'" onclick="javascript:doActSelect(\'\', \''+sUrl+'\', \''
		+nId+'\', \'list-dodelete\');"> - '+userinfo.$lang['func_delete']+'</a>&nbsp; &nbsp;&nbsp;';
	sCont += '<br/>&nbsp; &nbsp;&nbsp;<a href="'+targetAreaId+'" onclick="javascript:doActSelect(\'\', \''+sUrl+'\', \''
        +nId+'\', \'addbycopy\');"> - '+userinfo.$lang['func_copy']+'</a>&nbsp; &nbsp;&nbsp;';
   
    //- add more options on popup menu, Fri Apr 26 10:58:24 HKT 2019
    if(typeof userinfo.actListOption != 'undefined'){
        var actArr = userinfo.actListOption; var tmpName, tmpUrl;
        var tmpRecord = {};
        //- userinfo.dataList init in ido.php and load data in jdo.php
        for(var ri=0; ri<userinfo.dataList.length; ri++){
            if(userinfo.dataList[ri].id==dataId){
                tmpRecord = userinfo.dataList[ri];
                //console.log("dataList:"+tmpRecord+" id:"+tmpRecord['id']);
                break;
            }
        }
        for(var ai=0; ai<actArr.length; ai++){
            tmpName = actArr[ai].actName;
            tmpUrl = actArr[ai].actUrl;
            if(tmpUrl != null && tmpUrl != ''){
                //console.log("comm/ido: tmpUrl:"+tmpUrl);
                var tmpk = ''; var fieldRe = /THIS_([a-zA-Z]+)/gm; var match;
                var tmpUrl2 = tmpUrl;
                while(match = fieldRe.exec(tmpUrl)){
                    //console.log(match);
                    tmpk = match[1]; if(tmpk=='ID'){ tmpk = 'id'; }
                    // due to 'field' 'fieldx' 'fieldxxxx'
                    tmpUrl2 = tmpUrl2.replace((new RegExp(match[0]+"([&|'|$]+)", "gm")), tmpRecord[tmpk]+'$1');
                }
                tmpUrl = tmpUrl2;
            }
            sCont += '<br/>&nbsp; &nbsp;&nbsp;<a href="'+targetAreaId+'" onclick="javascript:doActSelect(\'\', \''+tmpUrl+'\', \''
                +nId+'\', \'ActOption\');"> - '+tmpName+'</a>&nbsp; &nbsp;&nbsp;';
        }
    }
	 
	sCont += '</p>';
	divObj.innerHTML = sCont;

}

//-- lazy load long list, Wed Oct 14 09:08:51 CST 2015
function lazyLoad(myObj, myType, myUrl){
	window.console.log("lazyload is starting.... myurl:["+myUrl+"] myobj:["+myObj+"]");
	if(true){
	//document.onreadystatechange = function(){
	//window.onload = function(){
	window.setTimeout(function(){
		if(document.readyState == 'complete' || document.readyState == 'interactive'){
			sendNotice(true, userinfo.$lang['lazyloading']+'.... myObj:['+myObj+']');
			var gta = new GTAjax();
        	gta.set('targetarea', 'addareaextradiv');
        	gta.set("callback", function(){
            	//window.alert("getresult:["+this+"]");
                var s = this;
				var resultList = JSON.parse(s);
				var fieldName = resultList.thefield;
				var dispField = resultList.dispfield;
				//var mySele = document.getElementById(resultList.thefield);
				console.log("thefield:["+resultList.thefield+"] "+(new Date()));
				var optionList = {};
				for(var i=0;i<resultList.result_list.length;i++){
					var aresult = resultList.result_list[i];
					//mySele.options[i] = new Option(aresult.sitename+'('+aresult.id+')',aresult.id, true,issel=false);
					optionList[aresult.id] = eval('aresult.'+dispField);
				}
				var myOptionList = document.getElementById('pnsk_'+fieldName+'_optionlist');
				myOptionList.value = JSON.stringify(optionList);
				console.log("thefield:["+resultList.thefield+"] completed......"+(new Date()));
				//console.log(JSON.stringify(myOptionList.value));
				sendNotice(true, userinfo.$lang['notice_lazyload_success']+'.... myObj:['+myObj+']');
			});
        	gta.get(appendSid(myUrl));
		}
		else{
			sendNotice(false, userinfo.$lang['notice_lazyloading']+'....');
		}
	}, 3*1000);

	}
}

//- copy and return, 
//- wadelau@ufqi.com, Sat Feb 13 10:52:35 CST 2016
function copyAndReturn(theField){ 
	var iId = parent.userinfo.targetId;
	var theAct = parent.userinfo.act;
	console.log('copyAndReturn: iId:['+iId+'] theAct:['+theAct+']');
	//if(iId != ''){ //- ? 
	if( true ){ 
		var linkobj = document.getElementById(theField); 
		if(linkobj != null){ 
			document.getElementById(theField).value = document.getElementById('linktblframe').contentWindow.sendLinkInfo('', 'r', theField);
		}
	}
	//document.getElementById('extrainput_'+theAct+'_'+theField).style.display='none'; 
	//document.getElementById('extendicon_'+iId+'_'+theField).src='./img/plus.gif';
}

//- show pivot list
//- Xenxin@Ufqi, 18:23 05 December 2016
userinfo.showList = {}; //- holder of current active showing div
function showPivotList(nId, isOn, sUrl, sName){
	var divPrefix = 'divPivotList_';
	var divId = divPrefix + nId;
	if(userinfo.showList){
		var oldnId = userinfo.showList.divPrefix;
		if(oldnId && oldnId != nId){
			//console.log("found oldnId:["+oldnId+"], try to switch off.");
			showPivotList(oldnId, 0, sUrl, sName);
		}
	}
	//console.log((new Date())+": divId:["+divId+"]");
	var divObj = document.getElementById(divId);
	var dispVal = divObj.style.display;
	if(isOn == 1){ dispVal = 'block'; }else{ dispVal = 'none';}
	divObj.style.display = dispVal;
	divObj.onmouseover = function(){ this.style.display='block'; };
	divObj.onmouseout = function(){ this.style.display='none'; };

	var sCont = '<p> &nbsp; <b style="color:red;">'+nId+'. '+sName+'</b>: ';
	var groupCol = userinfo.$lang['func_pivot_group_col'];
	var valueCol = userinfo.$lang['func_pivot_value_col'];
	var orderCol = userinfo.$lang['func_pivot_order_col'];
	var opList = {'addgroupby':groupCol,
			'addgroupbyymd':groupCol+'Ymd', 'addgroupbyseg':groupCol+'Seg',
			'addgroupbyother':groupCol+'Other(?)', 
			'__SEPRTa':1,
			'addvaluebysum':valueCol+'Sum', 'addvaluebycount':valueCol+'Count',
			'addvaluebycountdistinct':valueCol+'CountUniq', 'addvaluebyavg':valueCol+'Average',
			'addvaluebymiddle':valueCol+'Median(?)', 'addvaluebymax':valueCol+'Max', 
			'addvaluebymin':valueCol+'Min', 'addvaluebystddev_pop':valueCol+'Stddev_Pop', 
			'addvaluebystddev_samp':valueCol+'Stddev_Samp',
			'addvaluebyother':valueCol+'Other(?)',
			'__SEPRTb':1,
			'addorderby':orderCol};
	var opi = 1;
	for(var op in opList){
		if(op.indexOf('__SEPRT') > -1){
			sCont += "<br/>";
		}
		else{
			sCont += '&nbsp; &nbsp;'+nId+'.'+(opi++)+'&nbsp;<a href="javascript:void(0);" onclick="javascript:doPivotSelect(\''
				+sUrl+'\', \''
				+nId+'\', \''+op+'\', 1, \''+sName+'\');">+'+opList[op]+'</a>&nbsp; &nbsp;&nbsp;';
		}
	}
	sCont += '</p>';

	divObj.innerHTML = sCont;
	if(!userinfo.showList){
		userinfo.showList = {};
	}
	userinfo.showList.divPrefix = nId;
}

//- select/unselect pivot field
//- Xenxin@Ufqi, 18:29 05 December 2016
function doPivotSelect(sField, iId, sOp, isOn, sName){
	var rtn = true;
	var spanObj = _g('span_groupby');
	var fieldObj = _g('groupby');
	var fieldValue = fieldObj.value;
	if(sOp == 'addgroupby'  || sOp == 'addgroupbyymd'
        || sOp == 'addgroupbyother'
        || sOp.indexOf('addgroupbyseg') > -1){
        if(isOn == 1){
            if(sOp == 'addgroupbyseg'){
                var segPoints = window.prompt(userinfo.$lang['input']+sName+'/'+sField
						+ userinfo.$lang['func_pivot_seg_range'], '1-4');
                if(segPoints.indexOf('-') > 0){
                    sOp += segPoints.replace(' ', '');
                }
                console.log("addgroupbyseg: "+sField+" +"+segPoints);
            }
        }
        spanObj = _g('span_groupby');
        fieldObj = _g('groupby');
    }
	else if(sOp == 'addorderby'){
		spanObj = _g('span_orderby');
		fieldObj = _g('orderby');
	}
	else{
		spanObj = _g('span_calculateby');
		fieldObj = _g('calculateby');
	}
	fieldValue = fieldObj.value;
	//console.log("span:["+spanObj.innerHTML+"] field:["+fieldValue+"]");
	var tmps = sName+'('+sField+') '+sOp+'   <a href="javascript:void(0);" onclick="javascript:doPivotSelect(\''
			+sField+'\', \''+iId+'\', \''+sOp+'\', 0, \''+sName+'\');" title="'+userinfo.$lang['func_delete']+'"> X(Rm) </a>'
			+'   <a href="javascript:void(0);" onclick="javascript:doPivotSelect(\''
	+sField+'\', \''+iId+'\', \'addorderby\', 1, \''+sName+'\');" title="'+userinfo.$lang['func_orderby']+'"> ↿⇂(Od) </a><br>';
	if(isOn == 1){
		if(fieldValue.indexOf(sField+sOp) == -1){
			spanObj.innerHTML += tmps;
			fieldObj.value += ','+sField+'::'+sOp;
		}
	}
	else{
		fieldValue = fieldValue.replace(','+sField+'::'+sOp, '');
		fieldObj.value = fieldValue;
		var spanValue = spanObj.innerHTML;
		spanValue = spanValue.replace(tmps, '');
		spanObj.innerHTML = spanValue;
	}
	return rtn;
}

//- filter something of user input and replace with matched
//- work with regexp by Xenxin@Ufqi
//- 19:14 16 December 2016
/* e.g. 
<jsaction>onblur::filterReplace('pnsk_THISNAME', '[^0-9]*([0-9]+)[^0-9]*');|onkeyup::filterReplace('pnsk_THISNAME', '[^0-9]*([0-9]+)[^0-9]*');|onpaste::filterReplace('pnsk_THISNAME', '[^0-9]*([0-9]+)[^0-9]*');|</jsaction>
*/
userinfo.filterReplaceI = {};
function filterReplace(myField, myRegx){
	var rtn = 0;
	var realdo = false;
	var frpk = myField + myRegx;
	if(!userinfo.filterReplaceI){
		userinfo.filterReplaceI = {frpk:1};
		window.setTimeout('filterReplace(\''+myField+'\', \''+myRegx+'\')', 10);
		//console.log("set a delay exec. 1612161417.");
	}
	else{
		var sregi = userinfo.filterReplaceI.frpk;
		if(!sregi){
			userinfo.filterReplaceI.frpk = 1;
			window.setTimeout('filterReplace(\''+myField+'\', \''+myRegx+'\')', 10);
			//console.log("set a delay exec. 1612161407.");
		}
		else if(sregi == 1){
			userinfo.filterReplaceI.frpk = 0;
			realdo = true;
		}
	}
	if(realdo){
		var obj = _g(myField);
		if(obj){
			var isVal = true;
			var val = null;
			if(obj.value){
				val = obj.value;
			}
			else if(obj.innerText){
				val = obj.innerText;
				isVal = false;
			}
			else{
				console.log("obj.value failed. 16121138.");
			}
			var regx = new RegExp(myRegx, 'gm');
			// number: "[^0-9]*([0-9]+)[^0-9]*"
			// string: ?
			var mtch = regx.exec(val);
			if(mtch){
				//console.log("0:"+mtch[0]); // whole matched string
				console.log("1:"+mtch[1]); // first group
				val = mtch[1];
			}
			else{
				console.log("mtch failed.");
				val = '';
			}
			if(isVal){
				obj.value = val;
			}
			else{
				obj.innerText = val;
			}
		}
		else{
			console.log("obj failed. 16121151.");
		}
	}
	return rtn;
}

//- append sid
//- Tue, 7 Mar 2017 21:31:36 +0800
function appendSid(urlx){
	if(typeof urlx != 'string'){
		return urlx;
	}
	else if(urlx.indexOf('.') == -1 && urlx.indexOf('?') == -1){
		//console.log('ido.js: invalid url:['+urlx+']');
		return urlx;
	}
	var sidstr = 'sid='+userinfo.sid;
	if(urlx.indexOf('?sid=') > -1 || urlx.indexOf('&sid=') > -1){
		//- goood
	}
	else{
		if(urlx.indexOf('http') == 0){
			// outside
		}
		else{
			var hasFilled = false;
			var fileArr = ['ido.php', 'jdo.php', './', 'index.php'];
			for(var i=0; i<fileArr.length; i++){
				var f = fileArr[i];
				if(urlx.indexOf(f+'?') > -1){
					urlx = urlx.replace(f+'?', f+'?'+sidstr+'&');
					hasFilled = true;
					break;
				}
				else if(urlx.indexOf(f) > -1){
					urlx = urlx.replace(f, f+'?'+sidstr);
					hasFilled = true;
					break;
				}
			}
			if(!hasFilled){
				if(urlx.indexOf('?') > -1){
					urlx += '&'+sidstr;
				}
				else{
					urlx += '?'+sidstr;
				}
			}
		}
	}
	return urlx;
}

//- user agent detection
//- 19:00 03 August 2017
//- @todo
userinfo.userAgent = {};
(function(container){
	var env = container==null ? window : container;
	var ua = navigator.userAgent.toLowerCase();
	env.isChrome = false; env.isIE = false; env.isEdge = false; 
	env.isFirefox = false; env.isOpera = false;
	if(ua.indexOf('chrome/') > -1 || ua.indexOf('chromium/') > -1){ env.isChrome = true; }
	else if(ua.indexOf('firefox/') > -1){ env.isFirefox = true; }
	else if(ua.indexOf('edge/') > -1){ env.isEdge = true; }
	else if(ua.indexOf('msie ') > -1){ env.isIE = true; }
	else if(ua.indexOf('opr/') > -1 || ua.indexOf('opera/') > -1){ env.isOpera = true; }
	else{
		console.log('Unknown ua:['+ua+']');
	}
	var isLog = false;
	if(isLog){
    	Object.keys(env).forEach(function(k){
    		console.log('ua k:'+k+', v:'+env[k]);
    	});
	}
	return container = env;
})(userinfo.userAgent);

//-
//- pick up and make a reqt
//- Fri Sep 21 19:59:08 CST 2018
//- see class/pagenavi
//userinfo.PickUpList = {};
if(typeof userinfo.PickUpList == 'undefined'){ userinfo.PickUpList = {}; }
function fillPickUpReqt(myUrl, field, fieldv, opStr, linkObj){
    console.log("url:"+myUrl+", field:"+field+" link-text:"+linkObj.text);
    var linkText = '';
	var base62xTag = 'b62x.';
	//var pickUpFromTag = userinfo.pickUpFromTag;
	var pickUpFromTag = userinfo.pickUpFromTag ? userinfo.pickUpFromTag : '&frompickup=1';
    if(linkObj){
        linkText = linkObj.text;
        if(linkText.substring(0, 1) == '+'){
            linkText = '-' + linkText.substring(1);
            linkObj.style.color = '#ffffff';
            linkObj.style.backgroundColor = '#1730FD';
        }
        else{
            linkText = '+' + linkText.substring(1);
            linkObj.style.color = '';
            linkObj.style.backgroundColor = '';
        }
        linkObj.text = linkText;
    }

    var fieldObj = {};
    if(!userinfo.PickUpList){ userinfo.PickUpList = {}; }
    if(userinfo.PickUpList.field){
        fieldObj = userinfo.PickUpList.field;
    }
    if(fieldObj[fieldv]){
        //- have
        delete fieldObj[fieldv];
    }
    else{
        fieldObj[fieldv] = opStr; //- why?
    }
    userinfo.PickUpList.field = fieldObj;
    
    var latestUrl = myUrl;
    if(userinfo.PickUpList.latestUrl){
        latestUrl = userinfo.PickUpList.latestUrl;
    }
    myUrl = latestUrl;
    console.log("latesturl:"+myUrl+", field:"+field+" fieldobj:"+JSON.stringify(userinfo.PickUpList.field));

    var hasReqK = false; var hasReqKop = false; var hasReqV = false;
    var urlParts = myUrl.split('&');
    if(opStr == 'inlist' || opStr == 'containslist'
        || opStr == 'inrangelist'){

        var urlSize = urlParts.length;
        var paramParts = []; var pk = ''; var pv = '';
        var tmpV = ''; var emptyList = {}; var newPList = [];
        //fieldv = fieldv.toLowerCase(); //- why?
		var isString = false;
        if(opStr == 'containslist'){ isString = true; }
        for(var i=0; i<urlSize; i++){
            tmpV = urlParts[i]; emptyP = false;
            paramParts = urlParts[i].split('=');
            if(paramParts.length > 1){
                pk = paramParts[0];
                pv = paramParts[1]; 
                if(pk == "pnsk"+field){
                    //pv = pv.toLowerCase(); //- why?
					if(true && isString){
                        if(pv.indexOf(',') > -1){
                            var tmpArr = pv.split(',');
                            for(var tmpi=0; tmpi<tmpArr.length; tmpi++){
                                if(tmpArr[tmpi].indexOf(base62xTag) > -1){
                                
                                }
                                else{
                                    tmpArr[tmpi] = base62xTag + Base62x.encode(tmpArr[tmpi]);
                                }
                            }
                            pv = tmpArr.join(',');
                        }
                        else{
                            if(pv.indexOf(base62xTag) > -1){
                                //- okay
                            }
                            else{
                                pv = base62xTag + Base62x.encode(pv);
                            }
                        }
                    }
                    if(pv.indexOf(',') > -1){
                        if(pv.indexOf(','+fieldv) > -1){
                            pv = pv.replace(','+fieldv, ''); 
                            hasReqV = true;
                        }
                        else if(pv.indexOf(fieldv+',') > -1){
                            pv = pv.replace(fieldv+',', ''); 
                            hasReqV = true;
                        }
						else if(pv == fieldv){
                        	pv = ''; hasReqV = true;
                        	emptyList[pk] = true;
						}
						else{
                            pv += ','+fieldv;
                        }
                    }
                    else if(pv == fieldv){
                        pv = ''; hasReqV = true;
                        emptyList[pk] = true;
                    }
                    else{
                        pv += ','+fieldv;
                    }
                    hasReqK = true;
                }
                else if(pk == "oppnsk"+field){
                    if(emptyList['pnsk'+field]){
                        emptyList[pk] = true;
                    }
                    else{
                        pv = opStr;
                    }
                    hasReqKop = true;
                }
            }
            if(!emptyList[pk]){
                tmpV = pk + '=' + pv; 
                urlParts[i] = tmpV;
                newPList[i] = tmpV;
            }
            else{
                //urlParts.splice(i, 1);
                console.log("\ti:"+i+" updt pk:"+pk+" pv:"+pv);
            }
        }
        myUrl = newPList.join('&');
        if(!hasReqK){
            myUrl += '&pnsk'+field+'='+fieldv;
        }
        if(!hasReqKop){
            myUrl += '&oppnsk'+field+'='+opStr;
        }
        console.log("newurl:"+myUrl+' ->'+opStr);

        userinfo.PickUpList.latestUrl = myUrl;

        //-
		myUrl = myUrl.replace('&act=', '&dummyact=');
        doActionEx(myUrl+'&act=list&pnsm=1'+pickUpFromTag, 'actarea');

    }
    else if(opStr == 'moreoption'){
        
        console.log("newurl:"+myUrl+" ->"+opStr);
        doActionEx(myUrl+'&act=pickup&pnsm=1&pickupfieldcount='+fieldv, 'contentarea');

    }
	else if(opStr == 'filterrollback'){
        
        myUrl = myUrl.replace('&pnsk'+field, '&dummy');
        myUrl = myUrl.replace('oppnsk'+field, '&dummy');
        console.log("newurl:"+myUrl+" ->"+opStr);

        userinfo.PickUpList.latestUrl = myUrl;

        doActionEx(myUrl+'&act=pickup&pnsm=1&pickupfieldcount='+fieldv, 'contentarea');

    }
    else{
        console.log("Unknown opstr:["+opStr+"].");
    }
}

//- fill reset value
//- Thu Apr 12 10:36:27 CST 2018, tdid=537
function fillReset(fieldId, iType, myVal){
    var f = document.getElementById(fieldId);
    if(typeof f != 'undefined'){
        var oldv = f.value;
        if(true){
            if(iType == null || iType == ''){
                iType = 'input';
            }
            if(iType == 'input'){
                f.value = myVal;
            }
            else if(iType == 'select'){
                parent.setSelectIndex(fieldId, myVal);
            }
            console.log(' myVal:['+myVal+'] fillReset succ.');
        }
        else{
            console.log('oldv not empty. filReset stopped.');
        }
    }
    else{
        console.log('fieldId:['+fieldId+'] invalid. fillReset failed.');
    }
}

//-
if(typeof userinfo.urlParams == 'undefined'){ userinfo.urlParams = {}; }
function getUrlParams(tmpUrl){
    var vars = {};
	if(typeof tmpUrl == 'undefined' || tmpUrl == '' || tmpUrl == null){ tmpUrl = window.location.href; }
    var parts = tmpUrl.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
        vars[key] = value;
        });
    //console.log('vars:'+JSON.stringify(vars));    
    return vars;
};
userinfo.urlParams = getUrlParams();

//- for triger by registerAct from child page, Tue Jul 16 17:28:40 HKT 2019
function addEvent(sId, sAct, sFunc){
    var timerId0716=0;
    var tmpSelect=document.getElementById(sId);
    if(tmpSelect){
        tmpSelect.addEventListener(sAct, sFunc);
        //console.log('evnt added:'+tmpSelect.sAct);
        window.clearTimeout(timerId0716);
    }
    else{
        //console.log('tmpSelect:['+tmpSelect+'] is empty? try later..'); 
        //timerId0716=window.setTimeout(addEvent(sId, sAct, sFunc), 15*1000);
        //- anti dead lock?
        timerId0716=window.setTimeout(function(){ addEvent(sId, sAct, sFunc); }, 10*1000);
    }
}

//- for select onchange in list view, Tue Jul 16 17:29:15 HKT 2019
function searchBySelect(){
    var url = userinfo.searchBySelectUrl;
    searchBy(url);
}

//- image load async
//- Mon Sep  2 20:55:57 HKT 2019
function imageLoadAsync(imgId, imgRealPath){
    var image = _g(imgId);
    if(image && image.src == imgRealPath){
        console.log((new Date())+" imgid:"+imgId+" path:"+imgRealPath+" img already loaded!");
    }
    else{
		var realImage = new Image();
		//console.log("imgid:"+imgId+" path:"+imgRealPath+" imgobj:"+realImage);
		realImage.onload = function(){
			var baseSize = 118;
			if(image){
				image.src = this.src;
				image.onload = null; //- stop further refer to imageLoadAsync
				if(image.width > baseSize){
					image.style.width = baseSize+'px';
				}
				else if(image.height > baseSize){
					image.style.height = baseSize+'px';
				}
				//console.log((new Date())+"image load async succ...src:"+image.src);
				realImage = null;
			}
			else{
				console.log((new Date())+" image is not ready....");
			}
		};
		//- anti empty src, Thu Mar 19 10:21:21 CST 2020
		if(imgRealPath == ''){
			imgRealPath = 'data:image/jpeg;base64,MA==';
		}
		//window.setTimeout(function(){ realImage.src = imgRealPath; }, 1*1000);
		realImage.src = imgRealPath;
    }
}
