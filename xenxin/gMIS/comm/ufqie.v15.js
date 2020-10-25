/*
 * UFQIE, ufqie.js, UFQI Editor
 * @author: wadelau@ufqi.com,wadelau@hotmail.com
 * @since: 20080709 
 * @update: 20080821
 * @update: 20110526
 * @update: 14:02 Wednesday, July 24, 2013
 * @code: 15
 */
 
 var UFQIE={
 	
 	//-- config settings
 	currpos:0,//-- current focus-in position
	lastpos:0,//-- last point postion
	xdiv:'diagdiv0706',//-- ufe's pop up diaglog div' id
	area_w:503,//--px, preview panel
	area_h:220,
	area_h_p:1.2,//-- edit panel's height percent of preview div
	ta_x:0, //-- textarea position of x
	ta_y:0, //-- ... y
	isInit:0, //-- tag whether the editor is init or not
	isDebug:0, //-- set whether in debug mode or not
	pvwcss:'position:relative;z-index:9;border:1px solid black;overflow:auto;width:UFE_AREA_Wpx;height:UFE_AREA_Hpx;font-size:12px;line-height:150%;word-spacing:2px;letter-spacing:2px',
	tacss:'font-size:12px;color:#6E6E6E;width:UFE_AREA_Wpx;height:UFE_AREA_Hpx;wrap:soft;line-height:150%;',
	popcss_part:'z-index:11;position:absolute;background-color:#f5f5f5;visibility:visible;border:1px solid black;font-size:10px',
	tab_css:'<style>ufetab{border:2px solid black}</style>',
	pvwid:'',//-- preview div area
	taid:'',//-- edit textarea 
	tlid:'',//-- toolbar div area
	last_sel:'',//-- hold the selected text
	fileup:'./recv.jsp',//-- program to receive file upload
	
	tl_ahref:"<input type=\"button\" value=\" A \" title=\"link url\" onclick=\"javascript:UFQIE.show(UFQIE.linkformstr, '');\" />",
	tl_bold:"<input type=\"button\" value=\" B \" title=\"bold text\" style=\"font-weight:bold\" onclick=\"javascript:UFQIE.insert('b','example text','');\" />",
	tl_ita:"<input type=\"button\" value=\" I \" title=\"italic text\" style=\"font:italic\" onclick=\"javascript:UFQIE.insert('i','example text','');\" />",
	tl_img:"<input type=\"button\" value=\"Img\" title=\"insert an image\""
			+"onclick=\"javascript:UFQIE.show(UFQIE.fileformstr,'');\" />",
	tl_att: function(sImg){ return (sImg.replace('"Img"','"Att"')).replace('an image','an attchment'); },
	tl_tab:"<input type=\"button\" value=\"Tab\" title=\"insert an table\""
			+"onclick=\"javascript:UFQIE.show(UFQIE.tabformstr,'');\" />",
	tl_font:"<input type=\"button\" value=\"Fon\" title=\"font style\""
			+"onclick=\"javascript:UFQIE.show(UFQIE.fontstyle,'');\" />",
	tl_symb:"<input type=\"button\" value=\"Sym\" title=\"insert a symbol\""
			+"onclick=\"javascript:UFQIE.show(UFQIE.symbolstr,'');\" />",			
	tl_pre:"<input type=\"button\" value=\"Cod\" title=\"set pre format\" onclick=\"javascript:UFQIE.insert('pre','example text','');\" />",
	tl_hlp:"<input type=\"button\" value=\" ? \" style=\"font-weight:bold\" title=\"get UFQI Editor Help\" onclick=\"javascript:UFQIE.show(UFQIE.hlpstr,'');\" />",
	
	cancel:'<input type="button" id="filecancel" name="filecancel" onclick="javascript:document.body.removeChild(document.getElementById(UFQIE.xdiv));" value="Cancel"/>',
	linkformstr:function(sCcl){ return '<form id="ufelinkform" name="ufelinkform"><table id="ufepoplink"><tr><td colspan="2"></td></tr><tr><td>Disp Text:</td><td><input name="ufe_link_text" id="ufe_link_text" /></td></tr><tr><td>Link URI:</td><td><input name="ufe_link_uri" id="ufe_link_uri" /></td></tr><tr><td colspan="2"><input type="button" id="ufefontsubmit" name="ufefontsubmit" value="Submit" onclick="javascript:UFQIE.sublink(this.form.name);"/>&nbsp;&nbsp;'+sCcl+'</td></tr></table></form>'; }, 
	fileformstr: function(sFup,sCcl){ return '<form name="ufefileform" id="ufefileform" method="post" action="'+sFup+'" enctype="multipart/form-data"><table id=\"ufepoptab\"><tr><td colspan="4">Insert An Image/File </td></tr><tr><td>External Url:</td><td><input type="text" name="myfileurl" id="myfileurl" size="30"/> </td></tr><tr><td colspan="4">Or</td></tr><tr><td>Upload An Image/File:</td><td><input type="file" name="myufefile" id="myufefile" size="30" /><br/><input type="submit" id="filesubmit" name="filesubmit" value="Upload" onclick="javascript:UFQIE.upfile(this.form.name,\'ufefileta\',\'ufefilenewpath\',0);"><div id="ufefilenewpath" style="font-size:10px"><div id="ufefileta" style="visibility:hidden"></div></td></tr><tr><td>Force as attachment:</td><td><input type="radio" id="forceasatt" name="forceasatt" value="1">Yes <input type="radio" id="forceasatt" name="forceasatt" value="0" checked>No</td></tr><tr><td><input type="button" id="filesubbtn" name="filesubbtn" value="Submit" onclick="javascript:UFQIE.subfile(this.form.name,\'ufefilenewpath\');"/></td><td>'+sCcl+'</td></tr></table></form>'; },
	tabformstr: function(sCcl){ return '<form name="ufetabform" id="ufetabform" method="get" action="./"><table id=\"ufepoptab\"><tr><td colspan="6">Insert An Table</td></tr><tr><td>tabhead: use "|" to sperate each column<br/><input type="text" name="ufetabhead" id="ufetabhead" size="50" value="column1|column2|column3|column4"/></td></tr><tr><td>rows:<input type="text" name="uferows" id="uferows" size="5" value="3"/></td></tr><tr><td><input type="submit" id="ufetabsubmit" name="ufetabsubmit" value="Submit" onclick="javascript:UFQIE.subtbl(this.form.name);"/>&nbsp;&nbsp;'+sCcl+'</td></tr></table></form>'; },
	hlpstr: function(sCcl){ return '<br/><b>UFQIE</b> Help, please go to: <br/><br/>for questions and asks to:<br/><a href=\"http://www.ufqi.com/qa/ufqie.html\" target=\"_blank\">http://www.ufqi.com/qa/ufqie.html</a><br/><br/>for manual & reference to:<br/><a href=\"http://www.wadelau.net/ufqie/\" target=\"_blank\">http://www.wadelau.net/ufqie/</a><br/><br/>'+sCcl+'<br/>'; },
	fontstyle: function(sCcl){ return '<form id="ufefontstyle" name="ufefontstyle"><table id="ufepoptab"><tr><td colspan="2"></td></tr>UFEFT_SEL<tr><td>Font Color:</td><td><div id="ufe_font_color" style="width:55px;height:20px;background-color:#000000;border:1px solid black;" onclick="javascript:UFQIE.colorsel(this.id,0,\'\');">#000000</div></td></tr>UFEFS_SEL<tr><td colspan="2"><input type="button" id="ufefontsubmit" name="ufefontsubmit" value="Submit" onclick="javascript:UFQIE.subfont(this.form.name);"/>&nbsp;&nbsp;'+sCcl+'</td></tr></table></form>'; },
	symbollist:"pound:&#163;,yen:&#165;,euro:&#8364;,section:&#167;,copyright:&#169;,register:&#174;,trademark:&#8482;,degree:&#176;,plus-minus:&#177;,middle-dot:&#183;,multiple:&#215;,divide:&#247;,leftwards:&#8592;,upwards:&#8593;,rightwards:&#8594;,downwards:&#8595;,n-ary:&#8721;,square-root:&#8730;,angle:&#8736;,therefore:&#8756;,notequal:&#8800;,circled:&#8855;,uptack:&#8869;,celsius:&#8451;,farenheit:&#8457;,numero:&#8470;,ohm:&#8486;,sun:&#9728;,moon:&#9789;,moon:&#9790;,cloud:&#9729;,rain:&#9730;,snow:&#9731;,comet:&#9732;,start-solid:&#9733;,star-outline:&#9734;,circle:&#9737;,phone:&#9742;,phone:&#9743;,checkmark:&#9745;,checkmarknot:&#9746;,left-point:&#9754;,right-point:&#9755;,hammer-sickle:&#9773;,yin-yang:&#9775;,sad:&#9785;,smile:&#9786;,venus-female:&#9792;,mars-male:&#9794;,queen:&#9813;,queen:&#9819;,knight:&#9816;,knight:&#9822;,king:&#9818;,black-spade:&#9824;,red-spade:&#9828;,black-heart:&#9829;,red-heart:&#9825;,black-diamond:&#9830;,red-diamond:&#9826;,black-club:&#9827;,red-club:&#9831;,musical:&#9836;,cut-here:&#9986;,plane:&#9992;,mail:&#9993;,victory:&#9996;,signature:&#9997;,pencil:&#10000;,:",
	
	dtua: { isie:(function(testUA){ return navigator.userAgent.toLowerCase().indexOf(testUA.toLowerCase()) > -1 ? true : false;})('msie '), isfirefox:(function(testUA){ return navigator.userAgent.toLowerCase().indexOf(testUA.toLowerCase()) > -1 ? true : false;})('firefox') },
	
	ufesel: function(sTa){ return (new UFQIESELE(document.getElementById(sTa))); },
	
	//-- prepare symbol
	symbolstr:function(sSym,sCcl)
	{ 
		var symarr=sSym.split(',');
		var subarr;
		var tmpstr='<tr height="25px">';
		for(i=0;i<symarr.length;i++)
		{
				subarr=symarr[i].split(':');
				tmpstr+='<td style="border:1px solid #000000;width:25px;text-align:center;font-size:18px" onclick="javascript:UFQIE.insert(\'\',\''+subarr[1]+'\',\'\');" title="'+subarr[0]+'">'+subarr[1]+'</td>';
				if((i+1)%10==0)
				{
					tmpstr+='</tr>';	
				}
		}
		return '<form id="ufesymbol" name="ufesymbol"><table id="ufepoptab" style="border-collapse:collapse;font-size:18px">'+tmpstr+'<tr><td colspan="12" align="center">&nbsp;'+sCcl+'</td></tr></table></form>'; 
	},

	//-- init 
 	init: function(taID,needConfirm)
	{	
		var u=UFQIE;
		var realinit=1;
		if(u.isInit==0)
		{
			var iscfrm=false;
			if(needConfirm){
				if(window.confirm('Use Multifunctional Editor UFQIE ?')){
					iscfrm=true;
				}
			}
			else
			{
					iscfrm=true;
			}
			if(iscfrm){
				if(u.dtcobj(taID)){
					u.taid=taID;
					var ta=document.getElementById(taID);
					u.area_w=ta.offsetWidth;
					u.area_h=ta.offsetHeight;
					u.pvwcss=u.pvwcss.replace('UFE_AREA_W',u.area_w);
					u.pvwcss=u.pvwcss.replace('UFE_AREA_H',u.area_h*u.area_h_p);
					u.tacss=u.tacss.replace('UFE_AREA_W',u.area_w);
					u.tacss=u.tacss.replace('UFE_AREA_H',u.area_h/u.area_h_p);
					
					u.tlid=u.taid+'_tlb';
					if(!u.dtcobj(u.tlid)){
						u.pvwid=u.taid+'_pvw';
						var mydiv=document.createElement('div');
						mydiv.setAttribute('id',this.pvwid);
						mydiv.setAttribute('style',u.pvwcss);
						mydiv.setAttribute('contentEditable', true);
						mydiv.style.cssText=u.pvwcss;
						//pObj.parentNode.insertBefore(mydiv,pObj);
						u.insertAfter(mydiv,ta);
						
						mydiv=document.createElement('div');
						mydiv.setAttribute('id',u.tlid);
						ta.parentNode.insertBefore(mydiv,ta);
						//u.insertAfter(mydiv,ta);
						//var pObj=document.getElementById(u.tlid);
						mydiv=null;	
						
						ta.style.cssText=u.tacss;
						u.addevt(ta,'keyup',UFQIE.prevw);
						u.addevt(ta,'dblclick',UFQIE.prevw);
						u.addevt(ta,'click',function(){return false;}); //-- after init.ufe, override the onclick event
						u.ta_x=ta.offsetLeft;
						u.ta_y=ta.offsetTop;	
						console.log('UFQIE.init: ta_x:['+u.ta_x+'] ta_y:['+u.ta_y+']');
						
						if(typeof u.tl_att != 'string'){
							u.tl_att=u.tl_att(u.tl_img);
						}
						else
						{
							realinit=0;
						}
						
						if(u.dtcobj(u.tlid)){
							var tlobj=document.getElementById(u.tlid);
							tlobj.innerHTML=u.tl_ahref+u.tl_bold+u.tl_ita
									+u.tl_font+u.tl_img+u.tl_tab+u.tl_att+u.tl_symb
									+u.tl_pre
									+u.tl_hlp
									+u.tab_css;	
							//if(u.ta_x<10)
							if(true){
								//getDivPos(tlobj);
								var tmpp=u.getDivPos(tlobj);
								u.ta_x=tmpp.left;
								u.ta_y=tmpp.top;
								console.log('UFQIE.init: tlobj: ta_x:['+u.ta_x+'] ta_y:['+u.ta_y+']');

							}
							u.isInit=1;
						}
					}
					ta.focus();
				}
				if(realinit==1)
				{
					u.fileformstr=u.fileformstr(u.fileup,u.cancel);
					u.tabformstr=u.tabformstr(u.cancel);
					u.hlpstr=u.hlpstr(u.cancel);
					u.fontstyle=u.fontstyle(u.cancel);
					u.linkformstr=u.linkformstr(u.cancel);
					u.symbolstr=u.symbolstr(u.symbollist,u.cancel);
				
					u.ufesel=u.ufesel(u.taid); //--- init UFQIESELE
					u.ufesel.init();
					console.log('realinit:['+realinit+']');
				}
				else
				{
					console.log('u.taid:['+u.taid+'] ufesel:['+u.ufesel+']');
					u.ufesel=new UFQIESELE(document.getElementById(u.taid));
					u.ufesel.init();
				}
			}
			else
			{
				//-- disable manual
				window.alert('UFQIE has been disabled. \nIf you want re-init again, you may need reload/refresh the page.');
				u.isInit=1;	
			}
			
		}
		else
		{
			//-- do nothing	
		}
	},
 	
 	//--- real time preview generator
	prevw: function(evt)
	{
			if(document.getElementById(UFQIE.pvwid) && document.getElementById(UFQIE.taid)){
				var keyc=evt.keyCode;
				if(keyc==13 || evt.type=='dblclick'){
					var pre=document.getElementById(UFQIE.pvwid);
					var ta=document.getElementById(UFQIE.taid);
					var cont=ta.value;
					var reg = new RegExp("[\\n]", "g");
					cont=cont.replace(reg, "<br/>");
					//reg = new RegExp("^[<br\/>\\n]", "g");
					//cont=cont.replace(reg, "<br/>&nbsp;");
					if(cont.indexOf('<tr>')!=-1){
						cont=cont.replace(/<br\/>(<tr>)/g,"$1"); //-- \n in table keep original	
					}
					//window.alert('ta:['+ta.value+'] cont:['+cont+'] 1024');
					pre.innerHTML = UFQIE.encode(cont,1);
					//console.log('ta:['+ta.value+'] cont:['+cont+'] 1024');
					UFQIE.currpos=UFQIE.ufesel.getCaret().start;
					UFQIE.scrlto(UFQIE.pvwid,UFQIE.taid,UFQIE.currpos);
					
				}
				return 0;
			}
			else
			{
				window.alert('something is missing: preID:['+UFQIE.pvwid+'] taID:['+UFQIE.taid+']');	
				return 1;
			}
	},
 	
 	//-- handle any insert action
	insert: function(tagBgn,tagVal,tagEnd)
	{
		var u=UFQIE;
		var taObj=document.getElementById(u.taid);
		var needgetsel=1;
		//-- hidden pop div
		if(tagBgn.indexOf('img')!=-1 || tagBgn.indexOf('font')!=-1 
				|| tagBgn.indexOf('file:')!=-1 || tagBgn.indexOf('table')!=-1
				|| (tagBgn=='' && tagVal!='') //-- spec char
				)
		{
			if(document.getElementById(u.xdiv))
			{
				document.body.removeChild(document.getElementById(u.xdiv));
			}
			needgetsel=0;
		}
		var tmpsel=u.last_sel; //-- chk what has been selected before pop-div
		if(tagBgn!=''){
			if(tmpsel==''){
				if(needgetsel){
					tmpsel=u.getsel();
				}
			}else{
				u.last_sel='';	
			}
			if(tmpsel!=''){
				tagVal=tmpsel;	
			}
		}
		u.ufesel.setCaret(u.currpos,u.currpos+tmpsel.length);

		var tmpstr='';
		if(tagBgn!=''){
			tmpstr+='<'+tagBgn;
			if(tagEnd=='zero'){
				tmpstr+=tagVal+' />';
			}
			else if(tagEnd==''){
				tmpstr+='>'+tagVal+'</'+tagBgn+'>';
			}else{
				tmpstr+='>'+tagVal+'</'+tagEnd+'>';
			}
		}else if(tagVal!=''){
			tmpstr=tagVal;	
		}
		// IE support
		if (document.selection){
			taObj.focus();
			var sel = document.selection.createRange();
			sel.text = tmpstr;
			if(tagVal!=''){
				sel.findText(tagVal, -1);
				sel.select();
			}
		}
		// MOZILLA/NETSCAPE support
		else if(taObj.selectionStart || taObj.selectionStart=='0')
		{
			var startPos = taObj.selectionStart;
			var endPos = taObj.selectionEnd;
			taObj.value = taObj.value.substring(0, startPos) + tmpstr
				+ taObj.value.substring(endPos, taObj.value.length);
			u.setsel(taObj, startPos + tagBgn.length + 2, endPos
				+ tagVal.length + 1 );
				
		}else{
			taObj.value += tmpstr;
			var tmplen = taObj.value.length + tagBgn.length + 2;
			u.setsel(taObj, tmplen, tmplen + tagVal.length + 1 );
			
		}	
	},
	
	//-- show pop diaglog div
	show:function(sCont,divuniq)
	{
		var u=UFQIE;
		if(divuniq=='' && u.last_sel==''){
			u.last_sel=u.getsel();
		}
		if(divuniq=='') //-- very fist layer pop-div
		{
			u.currpos=u.ufesel.getCaret().start;
		}
		var tmpdivid=u.xdiv;
		if(divuniq!='') //-- secondary layer pop-div
		{
			tmpdivid=u.xdiv+'_'+divuniq;	
		}
		//window.alert('left:['+u.ta_x+'],top:['+u.ta_y+']');
		var popcss='left:'+u.ta_x+'px;top:'+u.ta_y+'px;'+u.popcss_part;
		if(!document.getElementById(tmpdivid)){
			var mydiv=document.createElement('div');
			mydiv.setAttribute('id',tmpdivid);
			mydiv.setAttribute('style',popcss);
			mydiv.style.cssText=popcss;
			document.body.appendChild(mydiv);
		}
		else
		{
			document.getElementById(tmpdivid).style.cssText=popcss;
		}
		if(u.dtcobj('ufepoptab')){
			document.getElementById('ufepoptab').style.cssText='size:10px';	
		}
		if(sCont.indexOf('UFEFT_SEL')!=-1){
			var ftlist='Arial,Courier New,Tahoma,Verdana,Times New Roman,新宋体,黑体,华文楷体,华文仿宋,华文隶书';
			var ftarr=ftlist.split(',');
			var tmps='<tr><td>Font Type:</td><td><select name="ufe_font_ft" id="ufe_font_ft">';
			for(i=0;i<ftarr.length;i++)
			{
					tmps+='<option value="'+ftarr[i]+'" style="font-family:'+ftarr[i]+'">'+ftarr[i]+'</option>';
			}
			tmps+='</select><td></tr>';
			sCont=sCont.replace('UFEFT_SEL',tmps);	
		}
		if(sCont.indexOf('UFEFS_SEL')!=-1){
			var fslist='13,15,18,22,27,33,42,55';
			var fsarr=fslist.split(',');
			var tmps='<tr><td>Font Size:</td><td><select id="ufe_font_fs" name="ufe_font_fs">';
			for(i=0;i<fsarr.length;i++)
			{
					tmps+='<option value="'+fsarr[i]+'" style="font-size:'+fsarr[i]+'px">'+fsarr[i]+'px</option>';
			}
			tmps+='</select><td></tr>';
			sCont=sCont.replace('UFEFS_SEL',tmps);
			u.fontstyle=sCont;
		}
		document.getElementById(tmpdivid).innerHTML=sCont;	
	},
	
	//-- show off the pop diaglog div
	showoff: function()
	{
		var u = UFQIE;
		if(u.dtcobj(u.xdiv)){
			document.body.removeChild(document.getElementById(u.xdiv));
		}
		return true;
	},
	
	//-- handle set link uri, 20130724
	sublink: function(sFormID)
	{
		var u=UFQIE;
		var linktext='';
		var linkuri='';
		if(u.dtcobj(sFormID))
		{
			linktextid = 'ufe_link_text';
			linkuriid = 'ufe_link_uri';
			var f=document.getElementById(sFormID);
			linktext = f.ufe_link_text.value;
			linkuri = f.ufe_link_uri.value;
			
			u.insert('a href="'+linkuri+'" target="_blank"', linktext,'a');
			u.showoff();
		}
	},
	
	//-- handle set font style, 20080719
	subfont: function(fontFormID)
	{
		var u=UFQIE;
		var st='';
		var font_color='ufe_font_color';
		if(u.dtcobj(fontFormID))
		{
			var f=document.getElementById(fontFormID);
			var ft=f.ufe_font_ft.value;
			var fc=document.getElementById(font_color).innerText;
			var fs=f.ufe_font_fs.value;
			if(ft!=''){
				st+='font-family:'+ft+';';
			}
			var re=new RegExp("[ ]", "g");
			if(fc){
				fc=fc.replace(re, "");
			}
			if(fc!='' && typeof fc !='undefined'){
				st+='color:'+fc+';';
			}
			if(fs!='')
			{
				st+='size:'+fs+'px;';
			}
			u.insert('span style="'+st+'"','example text','span');
			u.showoff();
		}
	},
	
	//-- handle insert an image/file
	subfile: function(fileformID,sFilePath)
	{
		var u=UFQIE;
		var fileform=document.getElementById(fileformID);
		fileform.onsubmit=function(){ return false;};
		var fileurl = fileform.myfileurl.value;
		var ftitle='';
		var fsize=0;
		if(fileurl==''){
			var tmpstr=document.getElementById(sFilePath).innerText;
			var linearr=tmpstr.split('\n');
			var line='';
			for(i=0;i<linearr.length;i++){
					line=linearr[i];
					//window.alert('curline:['+line+']');
					if(line.indexOf('[file_title]')!=-1){
						//var reg=/\[file_title\](.*)\[\/file_title\]/g;
						var reg=/.*\[file_title\](.*?)\[\/file_title\].*/g;
						ftitle=line.replace(reg,"$1");
						ftitle=u.trim(ftitle);	
					}
					if(line.indexOf('[file_url]')!=-1){
						//var reg=/\[file_url\](.*)\[\/file_url\]/i;
						var reg=/.*\[file_url\](.*?)\[\/file_url\].*/i;
						fileurl=line.replace(reg,"$1");
						fileurl=u.trim(fileurl);	
					}
					if(line.indexOf('[file_size]')!=-1){
						//var reg=/\[file_size\](.*)\[\/file_size\]/g;
						var reg=/.*\[file_size\](.*?)\[\/file_size\].*/g;
						fsize=line.replace(reg,"$1");	
						fsize=u.trim(fsize);	
					}
			}
		}
		if(fileurl!=''){
			fsize=parseInt(fsize/1024);
			var isimage=0;
			var fileext=fileurl.substr(fileurl.length-4,fileurl.length);
			var imageextlist='jpeg,.jpg,.gif,.bmp,.png,.ico,.icl,.tif,.cr2,.crw,.cur,.ani';
			fileext=fileext.toLowerCase();
			if(imageextlist.indexOf(fileext)!=-1){
				isimage=1;
			}
			if(fileform.forceasatt[1].checked && isimage==1){
				//u.insert('img src="'+fileurl+'" alt="'+fsize+'K"', '', 'zero');
				//u.insert('img src="'+fileurl+'" alt="'+ftitle+'" width="90%"', '', 'zero');
				u.insert('img src="'+fileurl+'" alt="'+ftitle+'" width="450px"', '', 'zero');
			}
			else
			{
				fsize=fsize/1024;
				fsize=fsize.toFixed(2);
				if(fsize=='0.00'){
					fsize='0.01';
				}
				if(ftitle!=''){
					u.insert('a href="'+fileurl+'" alt="file:'+ftitle+'" target="_blank"', ftitle+'('+fsize+'M)', 'a');
				}
				else
				{
					u.insert('a href="'+fileurl+'" alt="file:'+fsize+'M" target="_blank"', fileurl, 'a');	
				}
				u.showoff();
			}
		}
		else
		{
			window.alert('UFQIE: file is invalid.');
		}
	},
	 
	//-- handle insert an table
	subtbl: function(tabformID)
	{
		var tabform=document.getElementById(tabformID);
		tabform.onsubmit=function()
			{
				return false;
			};
		var tabhead=tabform.ufetabhead.value;
		var rows=tabform.uferows.value;
		var tab='\n';
		var sep='|';
		if(tabhead.indexOf('｜')>-1)
		{
			sep='｜';	
		}
		var headarr=tabhead.split(sep);
		rows=parseInt(rows)+1;
		for(i=1;i<=rows;i++)
		{
			tab+='<tr>';
			for(j=0;j<headarr.length;j++)
			{
				if(i==1)
				{
					tab+='<td>'+headarr[j]+'</td>';
				}
				else
				{
					tab+='<td>c'+(i-1)+''+(j+1)+'</td>';
				}	
			}
			tab+='</tr>\n';	
		}	
		if(tab!='')
		{
			//tab=tab.substring(0,tab.length-1); //-- remove the last \n
			UFQIE.insert('table border=1px cellspacing=0px cellpadding=0px', tab, 'table');
		}
		else
		{
			window.alert('UFQIE: tabhead is invalid.');
		}
	}, 
	 	
 	//--scroll div to the conresponse position with textarea
	scrlto: function(preID,taID,ipos)
	{
		if(document.getElementById(taID))
		{
			var objDiv=document.getElementById(preID);
			ipos=UFQIE.getscrlc(taID,ipos);
			if(ipos>0)
			{
				ipos=ipos-5;
				objDiv.scrollTop=0;
				for(i=0;i<ipos;i+=2.3)
				{
					objDiv.doScroll('scrollbarDown');	
				}
			}
			else
			{
				var test1 = objDiv.scrollHeight;
				var test2 = objDiv.offsetHeight;
				if (test1 > 0) // all but Explorer Mac
				{
					objDiv.scrollTop = objDiv.scrollHeight;
				}
				else // Explorer Mac;
				    //would also work in Explorer 6 Strict, Mozilla and Safari
				{
					objDiv.scrollTop = objDiv.offsetHeight;
				}
			}
			return 0;
		}
		else
		{
			window.alert('target div is missing:tID:['+tID+'].');	
			return 1;
		}
	},
	
	//-- count how many lines should be scrolled
	getscrlc:function(taID,ipos)
	{
		var linecount=0;
		if(document.getElementById(taID))
		{
			var ta=document.getElementById(taID);
			var tawidth=parseInt(ta.style.width);
			var taheight=parseInt(ta.style.height);
			var fontwidth=parseInt(ta.style.fontSize);
			var fontheight=fontwidth*3/2;
			var linelen=tawidth/fontwidth;
			var cont=ta.value;
			var contlen=cont.length;
			if(ipos>contlen-(linelen*(taheight/fontheight)))
			{
				return linecount;
			}
			else
			{
				cont=cont.substring(0,ipos);
				var contarr=cont.split('\n');
				var arrl=contarr.length;
				for(i=0;i<arrl;i++)
				{
					var tmpline=contarr[i];
					if(tmpline.length<linelen)
					{
						linecount++;
					}	
					else
					{
						linecount+=parseInt(tmpline.length/linelen);	
					}
				}
				return linecount;
			}
		}
		else
		{
			window.alert('target div is missing:tID:['+tID+'].');
			return linecount=-1;	
		}	
	},
	
	//-- get what has been selected, ref:http://www.webmasterworld.com/forum91/5005.htm
	getsel: function()
	{
		var u=UFQIE;
		if(u.dtcobj(u.taid))
		{
			var taObj=document.getElementById(u.taid);
			var sel='';
			var ti = 0; // trace id
			if('selectionStart' in taObj){
				if(taObj.selectionStart != taObj.selectionEnd){
					sel = taObj.value.substring(taObj.selectionStart, taObj.selectionEnd);
				}
				ti = 1;
			}else if(document.selection){ // msie 9
				var tRange = document.selection.createRange();
				var rangeParent = tRange.parentElement();
				if(rangeParent == tRange){
					sel = tRange.text;
				}
				ti = 2;
			}
			if( true || sel!=''){
				u.currpos=u.ufesel.getCaret().start;
			}
			console.log('getsel: sel:['+sel+'] tracei:['+ti+'] dtua.isie:['+u.dtua.isie+']');
			return sel;		
		}
	},
	
	//-- select text
	setsel: function(inputEl,selStart,selEnd)
	{
		if (inputEl.createTextRange) //-- IE
		{
			inputEl.focus();
			var range = inputEl.createTextRange();
			range.collapse(true);
			//range.moveEnd('character', selEnd+10);
			range.moveEnd('character', selEnd);
			range.moveStart('character', selStart);
			range.select();
		}
		else if(inputEl.setSelectionRange) //-- FireFox
		{
			inputEl.focus();
			inputEl.setSelectionRange(selStart, selEnd);
		}
		console.log('setsel: start:['+selStart+'], end:['+selEnd+']');
	},

 	//-- detect whether an object exists by id 
	dtcobj: function(sID)
	{
		if(document.getElementById(sID)){
			return true;	
		}else{
			if(this.isDebug){
				window.alert('target is missing. sID:['+sID+']');
			}
			return false;	
		}	
	},
	
	//-- add event by id
	addevt: function(obj,type,fn)
	{
	    if(obj.addEventListener){
		    obj.addEventListener(type,fn,false);
	    }
	    else if(obj.attachEvent){
	    	/*
	        obj["e"+type+fn]=fn;
	        obj[type+fn]=function()
	        {
	        	obj["e"+type+fn](window.event);
	        }
	        */
	        obj.attachEvent("on"+type,fn);
	    }
	},
	
	//-- safe encode str
	encode:function(sS,needspace)
	{
			if(sS!=null && sS!='')
			{
				if(sS.indexOf('<')!=-1)
				{
					//window.alert('old:['+sS+']');
					//sS=sS.replace(/<([^(a|b|i|img|font|table|tr|td|pre|\/)]+)/gi,"&lt;$1");
					sS=sS.replace(/<([^(a|b|i|f|t|p|s|\/)]+)/gi,"&lt;$1");
					sS=sS.replace(/<(b[^r|>]+)/gi,"&lt;$1");
					sS=sS.replace(/<(i[^>|m]+)/gi,"&lt;$1");
					sS=sS.replace(/<(s[^p|^>]+)/gi,"&lt;$1"); // span
					sS=sS.replace(/<(t[^a|r|d>]+)/gi,"&lt;$1");
					sS=sS.replace(/<(\/[^a|b|i|f|t|p|s|>]+)/gi,"&lt;$1");
					sS=sS.replace(/<(\/s[^p]+)/gi,"&lt;$1"); //span
					sS=sS.replace(/<(\/b[^>]+)/gi,"&lt;$1");
					
					if(needspace==1){
						sS=sS.replace(/  /gi,"&nbsp;&nbsp;");
					}
					//window.alert('i-37-new:['+sS+'] 1006');

				}
				return sS;	
			}
			return sS;
	},
 		
	//-- color selector
	colorsel:function(sID,iStep,sColor)
	{
		var u=UFQIE;
		var divuniq='ufecolortab';
		if(iStep==0)
		{
			var tab='<table border="1" cellspacing="0" cellpadding="0" align="center" style="border-collapse:collapse;" bordercolor="#CCCCCC" width="300px"><tr height="18">';
			var colorlist='190707,2a0a0a,3b0b0b,610b0b,8a0808,b40404,df0101,ff0000,fe2e2e,fa5858,f78181,f5a9a9,f6cece,f8e0e0,fbefef,191007,2a1b0a,3b240b,61380b,8a4b08,b45f04,df7401,ff8000,fe9a2e,faac58,f7be81,f5d0a9,f6e3ce,f8ece0,fbf5ef,181907,292a0a,393b0b,5e610b,868a08,aeb404,d7df01,ffff00,f7fe2e,f4fa58,f3f781,f2f5a9,f5f6ce,f7f8e0,fbfbef,101907,1b2a0a,243b0b,38610b,4b8a08,5fb404,74df00,80ff00,9afe2e,acfa58,bef781,d0f5a9,e3f6ce,ecf8e0,f5fbef,071907,0a2a0a,0b3b0b,0b610b,088a08,04b404,01df01,00ff00,2efe2e,58fa58,81f781,a9f5a9,cef6ce,e0f8e0,effbef,071910,0a2a1b,0b3b24,0b6138,088a4b,04b45f,01df74,00ff80,2efe9a,58faac,81f7be,a9f5d0,cef6e3,e0f8ec,effbf5,071918,0a2a29,0b3b39,0b615e,088a85,04b4ae,01dfd7,00ffff,2efef7,58faf4,81f7f3,a9f5f2,cef6f5,e0f8f7,effbfb,071019,0a1b2a,0b243b,0b3861,084b8a,045fb4,0174df,0080ff,2e9afe,58acfa,81bef7,a9d0f5,cee3f6,e0ecf8,eff5fb,070719,0a0a2a,0b0b3b,0b0b61,08088a,0404b4,0101df,0000ff,2e2efe,5858fa,8181f7,a9a9f5,cecef6,e0e0f8,efeffb,100719,1b0a2a,240b3b,380b61,4b088a,5f04b4,7401df,8000ff,9a2efe,ac58fa,be81f7,d0a9f5,e3cef6,ece0f8,f5effb,190718,2a0a29,3b0b39,610b5e,8a0886,b404ae,df01d7,ff00ff,fe2ef7,fa58f4,f781f3,f5a9f2,f6cef5,f8e0f7,fbeffb,190710,2a0a1b,3b0b24,610b38,8a084b,b4045f,df0174,ff0080,fe2e9a,fa58ac,f781be,f5a9d0,f6cee3,f8e0ec,fbeff5,000000,0b0b0b,151515,1c1c1c,2e2e2e,424242,585858,6e6e6e,848484,a4a4a4,bdbdbd,d8d8d8,e6e6e6,f2f2f2,ffffff';
			var colorarr=colorlist.split(',');
			for(i=0;i<colorarr.length;i++)
			{
			tab+='<td bgcolor="'+colorarr[i]+'" onclick="javascript:UFQIE.colorsel(\''+sID+'\',1,\''+colorarr[i]+'\');"></td>';
				if((i+1)%15==0)
				{
					tab+='</tr><tr height="18">';	
				}	
			}
			tab+='</table>';
			u.show(tab,divuniq);
		}
		else if(iStep==1)
		{
				document.body.removeChild(document.getElementById(u.xdiv+'_'+divuniq));
				document.getElementById(sID).style.background='#'+sColor;
				document.getElementById(sID).innerText='#'+sColor;
		}
	},
	 
	//-- upfile
	upfile: function(sForm,fileTa,pathTa,iStep)
	{
		var intval=2*1000;
		if(iStep==0){
			if(window.GTAjax){
				var gtajax=new GTAjax();
				gtajax.set('forceframe',true);
				gtajax.set('targetarea',fileTa);
				gtajax.set('backlink',false);
				gtajax.set('processbar',true);
				//gtajax.set('isdebug',true);
				gtajax.get(sForm);
				window.setTimeout('UFQIE.upfile(\''+sForm+'\',\''+fileTa+'\',\''+pathTa+'\',1)',intval);
				document.getElementById('filesubbtn').disabled=true;
			}else{
				window.alert('upfile: GTAjax is missing...');	
			}
		}else{
			if(UFQIE.dtcobj(fileTa))
			{
				var tmptxt='';
				//var fileuprtntag='<ufqie_file_desc>';
				var fileuprtntag='[ufqie_file_desc]'; //-- remedy on 20080812, emulate an html
				if(document.getElementById(fileTa).innerText){
					//window.alert('00--innerText: tmptxt:['+tmptxt+']');
					tmptxt=document.getElementById(fileTa).innerText;
					//window.alert('innerText: tmptxt:['+tmptxt+']');
				}
				else if(document.getElementById(fileTa).innerHTML)
				{
					//window.alert('222--innerHTML: tmptxt:['+tmptxt+']');
					tmptxt=document.getElementById(fileTa).innerHTML;	
					//window.alert('innerHTML: tmptxt:['+tmptxt+']');
				}
				//window.alert('tmptxt:['+tmptxt+']');
				console.log('upfile: tmptxt:['+tmptxt+']');
				if(tmptxt!=''){
					if(tmptxt.indexOf(fileuprtntag) == -1){
						window.setTimeout('UFQIE.upfile(\''+sForm+'\',\''+fileTa+'\',\''+pathTa+'\',1)',intval);	
					}
					else
					{
						if(UFQIE.dtcobj(pathTa)){
							document.getElementById(pathTa).innerText=tmptxt;	
							//document.getElementById(pathTa).innerHTML=tmptxt;	
						}
						window.clearTimeout();
						window.alert('SUCC! File has been uploaded as:\n'+tmptxt+'\nplease continue...');
						document.getElementById('filesubbtn').disabled=false;
					}
				}
				else
				{
					window.setTimeout('UFQIE.upfile(\''+sForm+'\',\''+fileTa+'\',1)',intval);	
				}
			}	
		}	
	},
	 	
 	//-- set something
 	set: function(sTag,sVal)
 	{
 		if(sTag=='fileupload')
 		{
 			sTag='fileup';	
 		}
 		eval('UFQIE.'+sTag+'=\''+sVal+'\'');
 	},
 	
 	//-- get something
 	get: function(sTag)
 	{
 		if(sTag=='fileupload')
 		{
 			sTag='fileup';	
 		}
 		return eval('UFQIE.'+sTag);	
 	},
 	
 	//-- trim string
 	trim: function(str)
 	{
		var	str=str.replace(/^\s\s*/, '');
		var ws=/\s/;
		var i=str.length;
		while(ws.test(str.charAt(--i)));
		return str.slice(0, i+1);
	},

	//-- insertafter
	insertAfter: function(newElement, targetElement)
	{
		var parent=targetElement.parentNode;
		if(parent.lastChild==targetElement)
		{
			parent.appendChild(newElement);
		}
		else
		{
			parent.insertBefore(newElement, targetElement.nextSibling);
		}
	},
	
	//-- get the position of an div, added on 20080810 by wadelau
	getDivPos: function(tObj)
	{
	     var osL = 0;
	     var osT = 0;
	     var msg = '';
	     var i = 0;
	     if ((tObj.offsetLeft && tObj.offsetTop) ||
			(document.body.offsetLeft && document.body.offsetTop))
	     {
		     while (tObj.parentNode)
		     {
			     ++i;
			     /*
			     msg += '\n' + i + ': '
			     + tObj.parentNode
			     + ' offsetLeft: '
			     + tObj.offsetLeft
			     + ' offsetTop: '
			     + tObj.offsetTop;
			     */
			     osL += +tObj.offsetLeft;
			     osT += +tObj.offsetTop;
			     tObj = tObj.parentNode;
		     }
		     //window.alert('Totals: offsetLeft = ' + osL
		     //+ ' & offsetTop = ' + osT
		     //+ '\n' + msg);
	     }
	     else
	     {
		     window.alert('Ooops, offsetLeft && offsetTop not supported with '
			     + tObj.nodeName + ' in '
			     + navigator.appName + ' : '
			     + navigator.appVersion);
	     }
	     return {left:osL, top:osT};
     	}

 };
 
var UFQIESELE=function(Input)
{
	this.input=Input; //document.getElementById(Input);
	this.isTA=this.input.type.toLowerCase()=="textarea";
};
with({o: UFQIESELE.prototype})
{
	o.setCaret=function(start, end)
	{
		var o=this.input;
		if(UFQIESELE.isStandard)
		{
			o.setSelectionRange(start, end);
		}
		else if(UFQIESELE.isSupported)
		{
			var t=this.input.createTextRange();
			end-=start+o.value.slice(start+1,end).split("\n").length-1;
			start-=o.value.slice(0,start).split("\n").length-1;
			t.move("character", start), t.moveEnd("character", end), t.select();
		}
	};
	o.getCaret=function()
	{
		var o=this.input, d=document;
		if(UFQIESELE.isStandard)
		{
			//window.alert('aaaa2222');
			return {start: o.selectionStart, end: o.selectionEnd};
		}
		else if(UFQIESELE.isSupported)
		{
			var s=(this.input.focus(), d.selection.createRange()), r, start, end, value;
			if(s.parentElement() != o)
			{
				return {start: 0, end: 0};
			}
			if(this.isTA ? (r = s.duplicate()).moveToElementText(o) : r = o.createTextRange(), !this.isTA)
			{
				return r.setEndPoint("EndToStart", s), {start: r.text.length, end: r.text.length + s.text.length};
			}
			for(var $ = "[###]"; (value = o.value).indexOf($) + 1; $ += $);
			r.setEndPoint("StartToEnd", s), r.text = $ + r.text, end = o.value.indexOf($);
			s.text = $, start = o.value.indexOf($);
			if(d.execCommand && d.queryCommandSupported("Undo"))
			{
				for(r = 3; --r; d.execCommand("Undo"));
			}
			//window.alert('aaaa');
			return o.value = value, this.setCaret(start, end), {start: start, end: end};
		}
		else
		{
			window.alert('UFQIESELE invalod.iso:['+UFQIESELE.isSupported+']');
		}
		return {start: 0, end: 0};
	};
	o.getText=function() //--- unused
	{
		var o=this.getCaret();
		return this.input.value.slice(o.start, o.end);
	};
	o.setText = function(text) //-- unused
	{
		var o=this.getCaret(), i=this.input, s=i.value;
		i.value = s.slice(0, o.start) + text + s.slice(o.end);
		this.setCaret(o.start += text.length, o.start);
	};
	o.init=function()
	{
		var d=document, o=d.createElement("input"), s=UFQIESELE;
		s.isStandard = "selectionStart" in o;
		s.isSupported = s.isStandard || (o=d.selection) && !!o.createRange();
		//window.alert(' new func iss:['+s.isStandard+'] isso:['+s.isSupported+']');
	};

};

window.UFQIE = UFQIE;
