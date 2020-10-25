//<!--
/*
 * GTAjax.js 
 * @abstract: General-Targeted Ajax 
 * @author: wadelau@hotmail.com,wadelau@gmail.com
 * @since: 2006-2-17 14:04
 * @code: 5.7 // a.bc , funcs added b+, errs updated c+ 
 * @NOTICE: DO NOT USE THIS COMMERICALLY WITHOUT AUTHOR'S PAPER AUTHORIZATION
 * @update: Tue Feb  1 20:47:03 GMT 2011
 * Tue Jan 25 12:55:01 GMT 2011
 * Wed Jan 26 17:31:33 GMT 2011
 * Wed Jul 20 08:08:09 BST 2011
 * Fri Mar 16 16:36:52 CST 2012
 * 12:15 Friday, February 20, 2015
 * Sun Jan 24 12:56:43 CST 2016
 * Fri May 25 08:51:23 CST 2018, code format refine and cA.sort bugfix
 * Wed Oct 31 21:57:26 CST 2018, +form Name validate
 * 11:06 Friday, August 16, 2019, imprvs with form validate
 * 11:55 2020-07-04, bugfix for submit fail with forceFrame.
 */ 
//---- DO NOT CHANGE ANY PART OF THE CODE UNDER THIS LINE ---
var GTAj = null ; 
var GTAjVar = {helpurl:'https://ufqi.com/dev/gtajax/', version:5.7} ;
var GTAjStatus = {hdlcp:0, nowopen:0, lastopen:0, gti:0, tlpid:0, bki:0,maxbk:9, ierdy:0};
var GTAjBK = {} ;

if(!window){ window = {}; } //- why this?
if(typeof window.console == "undefined"){
    window.console = {log: function(errMsg){ window.alert(errMsg); }};
}

//- main object, global with window
function GTAjax(){
	//--- initiate
	GTAjStatus.gti++;
	GTAj = this ;
	GTAj.sRe = '' ; //-- sReturn
	GTAj.iTm = 0 ; //-- iTimer
	GTAj.sTd = 0 ; //-- sToid
	GTAj.sTd2 = 0 ; //--- process id
	GTAj.sPtWi= 'myPOstWIn';  //-- sPstWin
	GTAj.sUrl = '?' ;
	GTAj.sMtd = 'GET' ; //-- sMethod
	GTAj.iItl = 1000 ; // iInterval, one second 
	GTAj.isWtRp = false ;  //-- isWaitResponse
	GTAj.sForm = '' ; 
	GTAj.sFld = '' ; //-- sField 
	GTAj.sBkCt = '' ; //-- sBackCont
	GTAj.nEmt = null ; //-- newElmt
	GTAj.wFl = false ; //-- withFile
	GTAj.tErr = new Error() ; //-- tmpErr
	GTAj.nEmt2 = null ;
	GTAj.gtFDiv = 'icld200607232107'; //-- gtfloatdiv
	GTAj.tBro = (navigator.userAgent).toLowerCase(); //-- tmpBro
	GTAj.tLgu = navigator.language?navigator.language:navigator.userLanguage ; //--- user charset
	GTAj.myBdTt = null ; //-- myBodyTarget
	GTAj.pswd = 'Processing';
	GTAj.rtns = null; //--- return status
	GTAj.noticed = 0; //-- form submitting during process will be noticed, added on 20110126
	GTAj.ti = 0; //-- runing track id, positioning by line no, added on 20160124 
	GTAj.cA = new Array();
	GTAj.vA = new Array()
		GTAj.vA['sbv'] = 'Submit' ;
		GTAj.vA['mul'] = 3*60*1000 ;
		GTAj.vA['ta'] = '' ;
		GTAj.vA['rn'] = ' Return ' ;
		GTAj.vA['ib'] = false ;
		GTAj.vA['rdo'] = false ;
		GTAj.vA['ic'] = true ;
		GTAj.vA['cf'] = '' ;
		GTAj.vA['ff'] = true; // false ;
		GTAj.vA['pb'] = true ;
		GTAj.vA['bl'] = true ;
		GTAj.vA['fft'] = 'fftag';
		GTAj.vA['req'] = 'undefined';
		GTAj.vA['nbl'] = '' ;
		GTAj.vA['ncp'] = false ;
		GTAj.vA['fbl'] = false ;
		GTAj.vA['chkv'] = 1 ;
		GTAj.vA['cfs'] = '::' ; //- chkformsep, reform since v5.2, 201107
	GTAj.vArr = new Array()
		GTAj.vArr['subbtnval']='sbv';
		GTAj.vArr['maxuploadfiletime']='mul';
		GTAj.vArr['targetarea']='ta';
		GTAj.vArr['returnname']='rn';
		GTAj.vArr['isdebug']='ib';
		GTAj.vArr['returndataonly']='rdo';
		GTAj.vArr['iscache']='ic';
		GTAj.vArr['chkform']='cf';
		GTAj.vArr['forceframe']='ff';
		GTAj.vArr['processbar']='pb';
		GTAj.vArr['backlink']='bl';
		GTAj.vArr['forceframetag']='fft';
		GTAj.vArr['request'] = 'req';
		GTAj.vArr['nobacktag'] = 'nbl';
		GTAj.vArr['nocopy'] = 'ncp';
		GTAj.vArr['forcebacktag'] = 'fbl';
		GTAj.vArr['callback'] = 'cb'; //- callback function, added on Sun Mar 11 12:56:52 CST 2012
	
	GTAj.xmlhttp = null ;
	
	//- set runtime variables
	this.set = function(sName,sVal){
		try{ 
			if(sName=='chkform'){
				if(sVal!=null && sVal!=''){
					var sValA = sVal.split(GTAj.vA['cfs'],3); //- remedy 20110711
					// formfield:chktype:errmsg,  validate bgn
					if(sValA[0]!=null && sValA[0]!=''){
						if(sValA[1]==null || sValA[1]==''){
							sValA[1] = 'notvalue=' ;	
						}
						if(sValA[2]==null || sValA[2]==''){
							sValA[2] = ' has not expected value.' ;	
						}
						GTAj.cA[sValA[0]] = sValA[1]+GTAj.vA['cfs']+sValA[2];
					}
					if(GTAj.cA.length==0){
						GTAj.cA[100] = '1' ;	
					}
					//-- validate end
				}
			}
			else{
				 GTAj.vA[ GTAj.vArr[sName]] = sVal ; 
			}
			if( GTAj.vA['ff'] && ( GTAj.vA['rdo'] ) ){
				GTAj.vA['chkv'] = 0 ;
				GTAj.tErr.message = 'returndataonly cannot be with forceframe!';
			}
			else if( GTAj.vA['fbl'] && GTAj.vA['nbl']){
				GTAj.vA['chkv'] = 0 ;
				GTAj.tErr.message = 'forcebacktag cannot be with nobacktag!';
			}	
		}
		catch(e6){ 
			return this._RGT('this.set',e6);
		}	
	}
		
	//- main method, prepare, encapsulate, validate, submit a request 
	//- and read response for further handling 
	this.get = function( sForm ){
		var iswaiting = false ;
		var isnum = false ;
		var tmpgti = GTAjStatus.gti ;
			
        if(sForm == null || sForm == ''){
            GTAj.tErr.message = 'invalid form/url:['+sForm+'] found, stopped.';
            return this._RGT('invalidForm/URL',GTAj.tErr);
        }
        else if( typeof sForm == 'number' ){
			//--- numeric, the waiting-req come again..
			tmpgti = sForm ;
			eval('GTAj=GTAjStatus.pid'+tmpgti+';');		
			if( tmpgti == GTAjStatus.nowopen 
                    || ( GTAjStatus.nowopen == 0 && tmpgti == GTAjStatus.lastopen+1 ) ){
				//--- will go on with nowopen or next one in queue, added on 201102 
			}			
			else{
				//--- another instance is runing, keep waiting again
				iswaiting = true ;
			}
			this._DBG( GTAj.vA['ib'],'process-num','sForm:['+tmpgti+'] gti:'+GTAjStatus.gti
					+',req:['+GTAj.reg.req+'] ta:['+GTAj.vA['ta']+'] waiting:['+iswaiting
					+'] nowopen:['+GTAjStatus.nowopen+'] lastopen:['+GTAjStatus.lastopen+']');
			isnum = true ;
		}
		else{
			//- validate sForm as string, Oct 31, 2018	
            if(typeof sForm != 'string'){ //- form has an input field named as 'name'
                var hasFoundF = false; var sFormStr = ''; 
                for(var tmpi=0; tmpi<document.forms.length; tmpi++){
                    var tmpform = document.forms[tmpi];
                    for(var tmpk in tmpform){
                        if(tmpform.hasOwnProperty(tmpk)){
                            var tmpobj = tmpform[tmpk];
                            //console.log(' >>'+tmpk+' -> '+tmpform[tmpk]+'/name:'+tmpobj.name+'/'+tmpobj.value);
                            if(tmpobj.name != undefined && tmpobj.name != ''){
                                if(tmpobj.name == 'name'){
                                    sForm = tmpform;
                                    this._DBG( GTAj.vA['ib'],'sForm/validate', 'sForm:'+sForm+'/obj:['+JSON.stringify(sForm)
                                        +'],found illegal field named as "name", will retrieve with form ['
                                        +sForm+'] "title" or "data-formid" attributes.'); 
                                    formTitle = sForm.title;
                                    hasFoundF = true;
                                }
                            }
                        }
                        if(hasFoundF){ break; }
                    }
                    if(hasFoundF){ 
                        this._DBG(GTAj.vA['ib'], 'sForm/validate', "real form name:["+sForm[GTAj.formName]+"] id:"+sForm.id
                            +' tit:'+sForm.title+' data-formid:'+sForm.dataset['formid']);
                        if(typeof sForm.title == 'string'){
                            sFormStr = sForm.title;
                        }
                        else{
                            sFormStr = sForm.dataset['formid'];;
                        }
                        break;
                    }
                }
                sForm = sFormStr; //- found real form name
                this._DBG( GTAj.vA['ib'],'sForm', 'sForm:'+sForm+'/obj:['+JSON.stringify(sForm)+'], remd succ.'); 
            }
			//--- new instanceof, save settings in an GTAjStatus with a newly pid
			GTAj.vA['req'] = sForm ;
			GTAj.reg = {req:sForm};
			if( GTAjStatus.nowopen!=0 ){
				//--- another instance is runing , queue and keep waiting...
				iswaiting = true ;	
			}
			this._DBG( GTAj.vA['ib'],'process-not-num', 'sForm:['+sForm+'] typeof:['+typeof sForm
					+'] gti:['+GTAjStatus.gti+'],req:['+GTAj.reg.req+'] ta2:['+GTAj.vA['ta']
					+'] waiting:['+iswaiting+']') ;
		}
		if( GTAj.tBro.indexOf('explorer')>-1 && document.readyState!='complete' ){
			iswaiting = true ;
		}
		if( iswaiting ){
			//--- waiting...
			if( !isnum ){
				eval('GTAjStatus.pid'+GTAjStatus.gti+' = GTAj;');
			}
			var waitingi = GTAjStatus.gti ;
			if( isnum ){
				waitingi = tmpgti ; 
			}
			GTAjStatus.tlpid = window.setTimeout('GTAj.get('+waitingi+');',GTAj.iItl);
			this._DBG( GTAj.vA['ib'],'process-waiting', 'gti:['+GTAjStatus.gti
					+'] nowopen:['+GTAjStatus.nowopen+'] req:['+GTAj.reg.req+'] ta:['+GTAj.vA['ta']
					+'] waitingi:['+waitingi+'] tlpid:['+GTAjStatus.tlpid+']'); 
            if(typeof GTAj.vA['req'] == 'string'){
                try{
                    if( GTAj.noticed==0){
                        GTAj.noticed=1;
				        document.getElementById( GTAj.vA['req']).onsubmit= this._DFM_F ;
						this._SAY( GTAj.sFld,'Attention: The request has added in processing....', false);
                   }
                }
                catch(e1650){
                    this._DBG( GTAj.vA['ib'],'disable-possible-onsubmit', 'sForm:['+GTAj.vA['req']+']');
                }
            }
		}
		else { 
			if( GTAjStatus.nowopen==0 ){
				GTAjStatus.nowopen = tmpgti ;
			}
			if(  GTAjStatus.hdlcp==0 || GTAj.vA['ncp'] != GTAjStatus.hdlcp ){
				this._DISCP( GTAj.vA['ncp'] ); 	//--- do no copy handle
				GTAjStatus.hdlcp = GTAj.vA['ncp'] ;	
			}	
			
			//- init a new request
			if( !GTAj.isWtRp ){
                if( GTAj.sTd2 == 0){
				    this._PSD( GTAj.iTm ); 
                    //-- update to dynamic again 2007-10-4 21:19, take static process div instead of dynamic, updated 20060810, 201102
                }
                else{
                    this._DBG( GTAj.vA['ib'],'process-GTAj.sTd2', 'gti:['+GTAjStatus.nowopen
                    		+'] sTd2:['+GTAj.sTd2+'] ta:['+GTAj.vA['ta']+']') ;
                }
				//------------- reinit a new 
				GTAj.sRe = '' ;
				GTAj.rtns = '';
				sForm = GTAj.vA['req'] ;
				var sPara = '';
				GTAj.isDN = false ; //--- isdone, is complete request
				this._DBG( GTAj.vA['ib'],'sForm',sForm);
				try{
					if(typeof sForm == 'string'){
					    sForm = document.forms[sForm];
                    }
                    else{
                        this._RPT('sFormError.', {'name':'sForm name error.', 'description':'Cannot find a form with this name.'+sForm}); 
                    }
                    if(typeof sForm == 'object'){
                        if(sForm.id == null || sForm.id == ''){
                            sForm.id = GTAj.vA['req']; //-- for form.id is missing, 20110711
                        }
                    }
				}
				catch (eFNm){
					console.log(eFNm);
				}
				this._DBG( GTAj.vA['ib'],'sForm/aft',sForm);
				if( sForm!=null && typeof sForm !='undefined' ){
					//- real html form
					this._DBG( GTAj.vA['ib'],'ta-1', GTAj.vA['ta']);		
					document.getElementById(GTAj.vA['req']).onsubmit= this._DFM_F ;
					if( GTAj.vA['chkv']==0 ){
						return this._RGT('this.set.chkvar',GTAj.tErr);
					}
					try{
						if( GTAj.vA['ta']=='' && GTAj.vA['rdo'] == false ){ 
							//--- if rdo==true,omited this block,updated 2006-7-23 11:42
							var Emsg = 'targetarea is empty.';
							if(typeof sForm != 'undefined'){
								 GTAj.vA['ta'] = sForm.parentNode.getAttribute('id') ; 	
							}
							else{
								GTAj.tErr.message = Emsg ;
								return this._RGT( 'formElement',GTAj.tErr );
							}
							if( typeof GTAj.vA['ta']=='undefined' 
									|| GTAj.vA['ta']=='' || GTAj.vA['ta']==null ){
								GTAj.tErr.message = Emsg;
								return this._RGT( 'formElement1',GTAj.tErr );
							}
							else{
								this._DBG( GTAj.vA['ib'],'ta', GTAj.vA['ta']);	
							}
						}
					}
					catch(e8){
						return this._RGT( 'parentArea', e8 ); // when err reinit the environment...
					}
					try{
						GTAj.sUrl = sForm.action ;
						GTAj.sMtd = sForm.method ;
						for (var i = sForm.elements.length-1 ; i>=0; i--){
							var el = sForm.elements[i];
							var myCFM = this._CFM ;
							var typeOfElement = (typeof GTAj.cA[el.name]);
							if(typeOfElement == 'function'){ //- May 26, 2018 
								this._DBG( GTAj.vA['ib'],'formValidate-201805250855', 'typeof '+el.name+' is '
										+ typeOfElement
										+ ', conflict with JavaScript native code, please rename it to xxx'
										+ el.name+' or '+el.name+'xxx.') ;
								continue;
							}
							if (el.tagName.toLowerCase() == 'select') {
								for (var j = 0; j < el.options.length; j++){
									var op = el.options[j];
									if (op.selected){ 
                                        if(!this._chkAccept(sForm, el, myCFM)){ return false; }
										if(typeOfElement != 'undefined'){
											if(!myCFM(sForm,el.name,op.value,GTAj.cA[el.name])){
												return false;	
											}
										}
										sPara += '&' + encodeURIComponent(el.name) + '=' 
											+ encodeURIComponent(el.value);
									}
								}
							} 
							else if (el.tagName.toLowerCase() == 'textarea'){
                                if(!this._chkAccept(sForm, el, myCFM)){ return false; }
								if(typeOfElement != 'undefined'){
									if(!myCFM(sForm,el.name,el.value,GTAj.cA[el.name])){
										return false;	
									}
								}
								sPara += '&' + encodeURIComponent(el.name) + '=' 
									+ encodeURIComponent(el.value);
							} 
							else if (el.tagName.toLowerCase() == 'input'){
								if (el.type.toLowerCase() == 'checkbox' 
									|| el.type.toLowerCase() == 'radio'){
									if (el.checked){ 
										sPara += '&' + encodeURIComponent(el.name) + '=' 
											+ encodeURIComponent(el.value); 
									}
                                    if(!this._chkAccept(sForm, el, myCFM)){ return false; }
									if(typeOfElement != 'undefined'){
										if(!myCFM(sForm,el.name,el.value,GTAj.cA[el.name])){
											return false;	
										}
									}		
								}
								else if( el.type.toLowerCase() == 'file'){
                                    if(!this._chkAccept(sForm, el, myCFM)){ return false; }
									if(typeOfElement != 'undefined'){
										if(!myCFM(sForm,el.name,el.value,GTAj.cA[el.name])){
											return false;	
										}
									}
									if(el.value!='' && el.value!=null){
										GTAj.wFl = true ;
										this._DBG( GTAj.vA['ib'],'fileUpload2',GTAj.wFl);
										if(eval(GTAj.cA.length)==0){
											break ;
										}
									}
								} 
								else if(el.type.toLowerCase() != 'button' 
									&& el.type.toLowerCase() != 'submit'){
                                    if(!this._chkAccept(sForm, el, myCFM)){ return false; }
                                    if(typeOfElement != 'undefined'){
										if(!myCFM(sForm,el.name,el.value,GTAj.cA[el.name])){
											return false;	
										}
									}
									sPara += '&' + encodeURIComponent(el.name) + '=' 
										+ encodeURIComponent(el.value);
								}
								else{
									if(el.type.toLowerCase()=='submit'){ 
										if( el.name ){
											GTAj.sFld = el.name ;
										}
										else{
											GTAj.sFld = el.id ;
										}
                                        if(el.value != ''){
										    GTAj.vA['sbv'] = el.value;
                                        }
										if( GTAj.sFld!=null && typeof GTAj.sFld !='undefined' 
                                                && GTAj.sFld!='' && document.getElementById(GTAj.sFld)){
											//--- added on 2007-1-29 23:22
											document.getElementById(GTAj.sFld).disabled =true ;
										}
										else if( GTAj.tBro.indexOf("netscape")==-1 && GTAj.sFld!=null 
                                                && typeof GTAj.sFld !='undefined' && GTAj.sFld!='' 
                                                && document.getElementsByName(GTAj.sFld)){
											document.getElementsByName(GTAj.sFld).disabled =true ;
										}
										else{
											GTAj.tErr.message = 'submit field in form:['+sForm
												+'] not NAMEed or IDed.';
											return this._RGT('invalidsubmit',GTAj.tErr);
										}
										// for firefox 
										this._SAY(GTAj.sFld,'Data Collecting',false);
									}
								}
							}
						}
					}
					catch(e2){
						console.log(e2);
					    return this._RGT( 'formElement3',e2 ); // when err reinit the environment...
					}
			        //---- end of real form action
				}
				else{
					//--- handle url
					sForm = GTAj.vA['req'];
					this._DBG( GTAj.vA['ib'],'handleurl', sForm);
					try{
						if( GTAj.vA['ta']=='' && GTAj.vA['rdo']==false ){
							//--- if rdo==true, omited this block
							var sLinks = document.links;  
							var sLinkHref = '' ;
							for(var i=0;i<sLinks.length;i++){
								sLinkHref = sLinks[i].href ;
								if(sLinkHref.indexOf(sForm)>-1){
									sForm = sLinks[i];
									GTAj.sUrl = sForm ;
									break;
								}
							}
							var Emsg = 'targetarea is empty.';
							if(typeof sForm != 'undefined'){
								 GTAj.vA['ta'] = sForm.parentNode.getAttribute('id') ; 	
							}
							else{
								GTAj.tErr.message = Emsg ;
								return this._RGT('formElement2',GTAj.tErr) ;
							}
							if(typeof GTAj.vA['ta']=='undefined' 
								|| GTAj.vA['ta']=='' || GTAj.vA['ta']==null){
								GTAj.tErr.message = Emsg ;
								return this._RGT('formElement4',GTAj.tErr) ;
							}
							else{
								this._DBG( GTAj.vA['ib'],'ta', GTAj.vA['ta']);	
							}
							sLinks = null ;
							sForm = null ;
							
							GTAjStatus.bki++;
							if( GTAjStatus.bki>GTAjStatus.maxbk ){
								GTAjStatus.bki = 0 ;	
							}
							eval('GTAjBK.bk'+GTAjStatus.bki+'=document.getElementById('+GTAj.vA['ta']
								+').innerHTML');
							this._DBG(GTAj.vA['ib'], 'GTAjBK.bk'+GTAjStatus.bki,'val');
							GTAj.sBkCt = '1' ;
						}
						else if( GTAj.vA['ta']!=''){
							if(!document.getElementById( GTAj.vA['ta'])){
								GTAj.tErr.message = 'cant find target:['+ GTAj.vA['ta']+'].';
								return this._RGT('invalidta',GTAj.tErr);
							}	
						}
						if(GTAj.sUrl=='?'){ GTAj.sUrl = sForm ; }
						if(GTAj.sUrl.indexOf(GTAj.vA['fft'])>-1){
							GTAj.vA['ff']=true;
						}
					}
					catch(e9){
						return this._RGT('parentElement',e9 ); 
						// when err reinit the environment...
					}
				}
				//- create a request object
				if( GTAj.wFl || GTAj.vA['ff'] ){	
					//--- use form submit with forceFrame
					try{
						this._SAY(GTAj.sFld,'Loading data...',false);
						var randframei = (new Date).getMilliseconds(); // for firefox continual fileuploads...
						GTAj.sPtWi= GTAj.sPtWi+ randframei ;
						var postFrame = '<iframe name="'+GTAj.sPtWi+'" id="'+GTAj.sPtWi+'"  '
										+ 'style="border:0px;width:0px;height:0px"><\/iframe>'
										+ '';
						var myBdTt2 = null ;
				        if( document.getElementById( GTAj.gtFDiv )){
							myBdTt2 = document.getElementById( GTAj.gtFDiv ) ;	
						}
						else{
							this._DBG( GTAj.vA['ib'],'iframefail',
									document.getElementById( GTAj.gtFDiv ).innerHTML);
						}
						GTAj.nEmt = document.createElement('div');
						GTAj.nEmt.setAttribute('id', 'icld'+GTAj.sPtWi);
						myBdTt2.appendChild(GTAj.nEmt);
						document.getElementById('icld'+GTAj.sPtWi).innerHTML = postFrame ;
						
						var dgt = document;
						var fgt = dgt.frames ? dgt.frames[GTAj.sPtWi] : dgt.getElementById(GTAj.sPtWi);
						fgt.src = 'about:blank'; // for opera compatible
						
						this._SUB(sForm,GTAj.sUrl,GTAj.sPtWi, GTAj.vA['ib'], GTAj.vA['ff']); 
						GTAj.isWtRp = true ;
						eval('GTAjStatus.pid'+GTAjStatus.nowopen+'=GTAj;');
						GTAj.sTd = window.setTimeout( 'GTAj._LOP()', GTAj.iItl);
						postFrame= null ;
						randframei = null ;
						this._DBG( GTAj.vA['ib'],'iframeListen','starting');
					}
					catch (e4){
						return this._RGT('fileUpload or forceFrame',e4 ) ;					
					}
			    }
			    else{
					// using xmlhttp for url request
					var isAsync = false ;
					this._SAY(GTAj.sFld,'Initiating',false);
					try{
						if (window.XMLHttpRequest){
							GTAj.xmlhttp = new XMLHttpRequest();
							this._DBG( GTAj.vA['ib'],'xmlhttp1',GTAj.xmlhttp
									+',type:['+(typeof GTAj.xmlhttp)+']');
						} 
						else if (window.ActiveXObject){
							try{
								GTAj.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
								this._DBG( GTAj.vA['ib'],'GTAj.xmlhttp2',GTAj.xmlhttp+',type:['
										+(typeof GTAj.xmlhttp)+']');
							} 
							catch(e){
								try {
									GTAj.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
									this._DBG( GTAj.vA['ib'],'GTAj.xmlhttp3',GTAj.xmlhttp+',type:['
											+(typeof GTAj.xmlhttp)+']');
								} 
								catch(e1){
									//--- xmlhttp.open fail, set ff==true and try again..
									this._SAY(GTAj.sFld, GTAj.vA['sbv'],true);
									GTAj.vA['ff'] = true ;
									eval('GTAjStatus.pid'+GTAjStatus.nowopen+'=GTAj;');
									GTAj.sTd = window.setTimeout('GTAj._LOP()',GTAj.iItl);					
								}
							}
						}
					}
					catch(e39){
						console.log(e39);
						//--- xmlhttp.open fail, set ff==true and try again..
						this._SAY(GTAj.sFld, GTAj.vA['sbv'],true);
						GTAj.vA['ff'] = true ;
						eval('GTAjStatus.pid'+GTAjStatus.nowopen+'=GTAj;');
						GTAj.sTd = window.setTimeout('GTAj._LOP()',GTAj.iItl);
					}
					//- submitting request
					this._SAY(GTAj.sFld,'Communicating',false);
					GTAj.sMtd = GTAj.sMtd.toUpperCase();
					if( GTAj.xmlhttp!=null && typeof GTAj.xmlhttp!='undefined' ){	
						this._SAY( GTAj.sFld,'Transacting',false );      	
						try {
							if ( GTAj.sMtd == 'GET' ){
								if(!GTAj.vA['ic'] ){
									if( sPara == null || typeof sPara =='undefined' ){
										sPara = '' ;
									}
									if( true ){
										var ici = Math.random() ;
										var lastc = sPara.substr(sPara.length-1,1) ; // 20080410
										if(  lastc != '&' && lastc != '' ){
											sPara += '&' ;
										}
										sPara += 'ici='+ici ;
										lastc = null ;
										ici = null ;
									}
								}
								var lastc = GTAj.sUrl.substr(GTAj.sUrl.length-1,1);
								if( GTAj.sUrl.indexOf('?') > 0 ){
									if( lastc != '?' ){
										GTAj.sUrl += '&' ;
									}
								}
								else{
									GTAj.sUrl += '?' ;
								}
								lastc = null ;
								GTAj.sUrl += sPara ;
								this._DBG( GTAj.vA['ib'],'GTAj.xmlhttp-open',('mth:['+GTAj.sMtd
										+'] GTAj.sUrl:['
										+GTAj.sUrl+'] isAsync:['+isAsync+']'));
								try{
									GTAj.xmlhttp.open(GTAj.sMtd,GTAj.sUrl,isAsync);
								}
								catch(e1116){
										return this._RGT('GTAj.xmlhttp-open',e1116 ) ;	
								}
								this._DBG( GTAj.vA['ib'],'GTAj.xmlhttp-try0',(new Date()));
								if(! GTAj.vA['ic']){
									GTAj.xmlhttp.setRequestHeader(
											'If-Modified-Since', 'Sat, 1 Jan 2000 00:00:01 GMT'); 	
								}
								sPara = null ;
							}
							else{
								GTAj.xmlhttp.open(GTAj.sMtd, GTAj.sUrl, isAsync);
								GTAj.xmlhttp.setRequestHeader('Content-Type',
										'application/x-www-form-urlencoded');
								GTAj.xmlhttp.setRequestHeader("Accept", "text/*");
								try{
									GTAj.xmlhttp.setRequestHeader("Accept-Language", GTAj.tLgu);
								}
								catch(e1101){
								}
							}
							this._DBG( GTAj.vA['ib'],'GTAj.sMtd',GTAj.sMtd+',GTAj.sUrl: '+GTAj.sUrl
									+', sPara:'+sPara);
							
							if( sPara ){
								sPara = sPara.substring(1); 
								//--- added on 2007-7-6 17:40, delete one at the very beginning, '&'
							}
							GTAj.xmlhttp.send(sPara);
							try{
								this._DBG( GTAj.vA['ib'],'GTAj.xmlhttp-try',(new Date()));
								GTAj.xmlhttp.onreadystatechange = this._RCV;
							}
							catch(e1140){
									return this._RGT('GTAj.xmlhttp-status',e1140 ) ;	
							}
							
							if(GTAj.sRe==''){ // for firefox confirm  
                                GTAj.sRe = GTAj.xmlhttp.responseText ; 
								if( GTAj.sRe!='' ){
									GTAj.rtns = 'complete' ;
								}
							}
							this._DBG( GTAj.vA['ib'],'GTAj.xmlhttp-sRe',GTAj.sRe 
									+ ', GTAj.rtns:['+GTAj.rtns	+'] ');
						}
						catch(e3){
							console.log(e3);
							//--- xmlhttp.open fail, set ff==true and try again..
							this._DBG(GTAj.vA["ib"],"GTAj.xmlhttp.fail","xmlhttp:["
									+GTAj.xmlhttp+"],failed.");
        					if( !document.forms[GTAj.vA['req']] 
        						|| (document.forms[GTAj.vA['req']] 
        								&& (GTAj.tBro.indexOf('gecko')!=-1 
        										|| GTAj.tBro.indexOf('firefox')!=-1 ) ) ){
        						GTAj.vA['ff'] = true ;
								eval('GTAjStatus.pid'+GTAjStatus.nowopen+'=GTAj;');
								GTAj.sTd = window.setTimeout( 'GTAj._LOP()',GTAj.iItl );
							}
							else{
								e3.message += "\nmaybe you can gtajax.set('forceframe',true) to try again.";
								this._RGT('formXmlHttpFail',e3 );	
							}		
						}	
						GTAj.xmlhttp = null ; 
					}
				}
				if(GTAj.isDN){
					this._SAY( GTAj.sFld,'Loaded Successfully',false );	
				}
				//-------------  end of sending data .		
			}
			else if( GTAj.isWtRp ){ //- waiting in a defined _LOP
				try{
					if( !dgt ){
						var dgt = document;
						var fgt = dgt.frames ? dgt.frames[GTAj.sPtWi] : dgt.getElementById(GTAj.sPtWi);
					}
					var pgt = fgt.document || fgt.contentWindow.document;
                    if( typeof pgt == 'undefined' || pgt == null){
                        //window.alert('isWtRp-error! dgt:['+dgt+'] fgt:['+fgt+'] pgt:['+pgt+']');
                        GTAj.tErr.name = 'GTAj.readResp';
                        GTAj.tErr.message = 'dgt:['+dgt+'] fgt:['+fgt+'] pgt:['+pgt+']';
					    this._RGT('GTAj.isWtRp',GTAj.tErr);
                        return false;
                    }
					var sRe2 =  pgt.body!=null ? pgt.body.innerHTML : '';
					GTAj.rtns = pgt.readyState ;
					if( GTAj.rtns=='complete' && sRe2==''){
						GTAj.rtns = ''; //-- remedy on 20101111 for chrome
					}
					if( GTAj.rtns!=null && typeof GTAj.rtns!='undefined' ){
						GTAj.sRe = sRe2;
					}
					else {
						if( ( GTAj.sRe!=null && sRe2!=null &&  GTAj.sRe!='' && sRe2!='' ) 
								&& GTAj.sRe.length==sRe2.length ){
							GTAj.rtns = 'complete';
						}
						GTAj.sRe = sRe2;
						GTAj.iItl = 2000;	
					}
					if( GTAj.tBro.indexOf('opera')>-1 ){
						GTAj.sRe = GTAj.sRe.replace(/\n{3,}/,''); //--- for opera compatible
					}
					this._DBG( GTAj.vA['ib'],'GTAj.rtns', GTAj.rtns + ',GTAj.sRe:['+GTAj.sRe+']');
					if( GTAj.rtns==null || typeof GTAj.rtns=='undefined' ){
						GTAj.rtns = '';	
					}
					if( GTAj.rtns.toLowerCase()!='complete' ){
						if(GTAj.iTm < GTAj.vA['mul']){
							eval('GTAjStatus.pid'+GTAjStatus.nowopen+'=GTAj;');
							GTAj.sTd = window.setTimeout( 'GTAj._LOP()',GTAj.iItl );
							this._DBG( GTAj.vA['ib'],'!=complelte-1534','sTd:['+GTAj.sTd
									+'] iTm:['+GTAj.iTm+']');	
						}
						else{
							this._DBG( GTAj.vA['ib'],'connTimeOut','connection timeout');	
						}
					}
					else{
						window.clearTimeout(GTAj.sTd);
						window.clearTimeout(GTAj.sTd2);
					}
					sRe2 = null ;
				}
				catch( e7 ){
					console.log(e7);
					this._DBG( GTAj.vA['ib'],'accessRe',e7.message );
					//--- tell script to waiting again,Modified on 2006-7-7 11:36 
					eval('GTAjStatus.pid'+GTAjStatus.nowopen+'=GTAj;');
					GTAj.sTd = window.setTimeout('GTAj._LOP()',GTAj.iItl);
				}
				//--- end of get content from iframe
			}
			
			//- read response
			if( GTAj.rtns!=null && typeof GTAj.rtns!='undefined' 
				&& GTAj.rtns.toLowerCase()=='complete' ){
				this._SAY(GTAj.sFld, GTAj.vA['sbv'],true);
				GTAj.ti = 1241259; //-- mmddHHii
				try{
					if(GTAj.nEmt2){
						if(GTAj.myBdTt!=null && typeof GTAj.myBdTt !='undefined'){ 
							try{
								GTAj.myBdTt.removeChild(GTAj.nEmt2);
							}
							catch(erm){
							}  
						}
						else{
							document.body.removeChild(GTAj.nEmt2); //--- refer to this._PSD()
						}
						GTAj.nEmt2=null;
                        //window.alert('nEmt2 is cleared!');
					}
                    else{
                       this._DBG(GTAj.vA['ib'],'return','nEmt2 is nothing!');
                    }
					GTAj.ti = 1241300; 
					if( ( GTAj.sFld!=''|| GTAj.vA['fbl'] ) && !GTAj.vA['rdo'] ){ 
						if( GTAj.vA['bl'] || GTAj.vA['fbl'] ){
							//--- need bl or not, updated 2006-9-22 11:55 
							if( GTAj.sBkCt=='' ){
								GTAjStatus.bki++;
								if( GTAjStatus.bki > GTAjStatus.maxbk ){
									GTAjStatus.bki = 0 ;	
								}
								eval('GTAjBK.bk'+GTAjStatus.bki+'=document.getElementById(\''
										+GTAj.vA['ta']+'\').innerHTML');
								this._DBG( GTAj.vA['ib'],'GTAjBK.bk'+GTAjStatus.bki,'val');
								GTAj.sBkCt = '1' ;
							}
							GTAj.ti = 1241301; 
							var sbackstr = '&nbsp;<br/>&nbsp;<br/><a href="javascript:void(0);" '
								+'onclick="javascript:GTAj.backGTAjax(\''
								+ GTAj.vA['ta']+'\',\''+GTAjStatus.bki+'\');">&laquo;'
								+ GTAj.vA['rn']+'</a><br/>&nbsp;' ;
							if( GTAj.vA['nbl']!='' ){
								if( GTAj.sRe.indexOf( GTAj.vA['nbl'] ) == -1 ){
									GTAj.sRe += sbackstr ;
								}
							}
							else{
								GTAj.sRe += sbackstr ;
							}
						}
					}
					GTAj.ti = 1241301; 
                    //-- callback function, added on Sun Mar 11 12:56:18 CST 2012
                    if(GTAj.vA['cb'] != undefined && GTAj.vA['cb'] != ''){
                        //console.log("callback:["+GTAj.vA['cb']+"]");
                        GTAj.vA['cb'].call(GTAj.sRe);
						GTAj.ti = 1241302; 
                    }
                    else if(GTAj.wFl || ( GTAj.vA['ff'] && !GTAj.vA['rdo'] ) 
                    		|| (!GTAj.wFl && !GTAj.vA['rdo']) ){ // GTAj.wFl==true,always write directly
						GTAj.ti = 1241303; 
						if(  typeof  GTAj.vA['ta'] !='undefined' &&  GTAj.vA['ta']!='' ){
							document.getElementById( GTAj.vA['ta']).innerHTML = GTAj.sRe ;
						}
					}
					if( sRe2 ){
						sRe2 = null ;	
					}
					//-
					eval('GTAjStatus.pid'+GTAjStatus.nowopen+'=null;') ;
					GTAjStatus.lastopen = GTAjStatus.nowopen ;
					GTAjStatus.nowopen = 0 ;
					if( GTAj.sTd != 0 ){
						window.clearTimeout( GTAj.sTd ) ;
					}
					GTAj.ti = 1241304; 
					if( true || GTAj.sTd2 != 0  ){
						window.clearTimeout( GTAj.sTd2 ) ;
					    this._DBG( GTAj.vA['ib'],'sTd2-accessRe-2', GTAj.sTd2 );
					}
					this._RGT('done',GTAj.tErr);
					
					return GTAj.sRe ;
					
				}
				catch(e5){
					console.log("e5:["+JSON.stringify(e5)+"] ti:["+GTAj.ti+"]");
					return this._RGT('finishJob',e5);	
				}
			}
			else if(!GTAj.isWtRp){
				this._DBG( GTAj.vA['ib'],'serverResponse',' emptyValue');
			}
			else{
				this._DBG( GTAj.vA['ib'],'sRe-waiting','sRe:['+GTAj.sRe+'],state:['+GTAj.rtns+']');
			}
		}	
	}
	
	//- output debug info when isdebug=1
	this._DBG = function(ib,sName,sVal){
		if(typeof sVal == 'undefined'){ sVal = ''; }
		//console.log((new Date()).getTime()+" debug: sName:["+sName+"] sVal:["+sVal+"]");
		if( ib ){ 
			try{
				var debugid = 'gtajax-debugid';
				var isieready = GTAjStatus.ierdy ;
                if( isieready == 0) {
					if( GTAj.tBro.indexOf('explorer')>-1 ){
						if( document.readyState!='complete' ){
						}
	                    else {
	                        isieready = 1;
	                        GTAjStatus.ierdy = isieready;
	                    }
					}
	                else{
                        isieready = 1;
                        GTAjStatus.ierdy = isieready;
	                }
                }
				if( isieready == 0 ){
					window.alert('the '+sName+' is:['+sVal+']'); 
				}
				else{
					if( !document.getElementById( debugid ) ){
						var debugdiv = document.createElement('div');
						debugdiv.setAttribute('id', debugid );
						document.body.appendChild( debugdiv );
					}
					document.getElementById( debugid ).innerHTML += "<br/><br/>"+(new Date()).getTime()
						+":<br/>";
                    if(GTAj.tBro.indexOf('firefox') > -1){ 
                        if(sVal.replace){
                            sVal = sVal.replace((new RegExp('<','gm')),'&lt;');
                        }
					    document.getElementById( debugid ).innerHTML += sName+': ['+sVal+']';
                    }
                    else{
					    document.getElementById( debugid ).innerText += sName+': ['+sVal+']';
                    }
				}
			}
			catch( edebug ){
				this._RPT('edebug',edebug);	
			}
		} 
	}
	
	//- intimate a waiting loop on curret request for resonse, with a globle process counter
	this._LOP = function(){  
		try{
			if( GTAj){
				GTAj.get( GTAjStatus.nowopen ); 
                GTAj.iTm += GTAj.iItl; 	//-- remedy on Wed Jan 26 15:38:16 GMT 2011
                GTAj._PSD( GTAj.iTm );
                GTAj._DBG(GTAj.vA['ib'],'_LOP', 'req:['+GTAj.reg.req+']');
			}
		}
		catch(e1609){
			console.log(e1609);
            if( GTAj){
			    return GTAj._RGT('GTAj._LOP()',e1609)
            }
            else{
			    return this._RGT('this._LOP()',e1609)
            }
		}
	}
	
	//- report message while _SAY failed...
	this._RPT = function(sTag,sObj){ 
		try{
			var Errmsg = 'err@GTAjax: '+sTag ;
			if( sObj.name ){
				Errmsg += '\n name:'+sObj.name
						+'\n message:'+sObj.message;
				if(true || GTAj.tBro.indexOf('explorer')!= -1 ){
					Errmsg += '\n location:'+sObj.location;	
				}
				Errmsg += '\n description:'+sObj.description + '. ['+JSON.stringify(sObj)
					+'] more at '+GTAjVar.helpurl;
			}
			else{
				Errmsg = sObj ;	
			}
			window.alert( Errmsg );
		}
		catch(rpte){
			window.alert('this._RPT: '+rpte+', sObj:'+(typeof sObj)+', sTag: '+sTag);
			return false;
		}
	}
	
	//- display processing message
	this._SAY = function(sFld,sProcess,isAble){
		if( sFld!=null && typeof sFld !='undefined' 
			&& sFld!='' && document.getElementById(sFld)){	
			var inPro = '....';
			if(isAble){ 
				document.getElementById(sFld).disabled = false ;
				document.getElementsByName(sFld).disabled = false ; // for firefox
			}
			if(sProcess!= GTAj.vA['sbv']){ sProcess += inPro; }
			document.getElementById(sFld).value = sProcess ;
			document.getElementsByName(sFld).value = sProcess ;
		}
		else if(sFld=='' && ! GTAj.vA['rdo']){ 
			//---disabled process text when prcsDiv is on ...
            if( sProcess.indexOf("Attention:") == 0 ){
                window.alert( sProcess );
            }
		}
	}
	
	//- submit request via either a request object or an iframe
	this._SUB = function(sForm,sUrlx,sPtWi,ib,fft){
		var postForm = null ;
		if( document.getElementById( GTAj.reg.req ) ){
			postForm = document.getElementById( GTAj.reg.req ) ;
		}
		else{
			//--- when ff==true, need a virtual form to skip on,udpate on 20060918, 
			//--- always create form in a top-level of a html body at the very first time Initiating...
			var myforceform = 'myff200607251525';
			var myff = myforceform+'_f';
			try{
				if(!document.getElementById(myforceform)){
					GTAj.nEmt = document.createElement('div');
					GTAj.nEmt.setAttribute('id', myff); 
					document.body.appendChild(GTAj.nEmt);	
					document.getElementById(myff).innerHTML = '<form id="'+myforceform
						+'" name="'+myforceform+'"></form>';
				}
				postForm = document.getElementById(myforceform) ;	
			}
			catch(efrmdiv){
				this._RGT('postFormInit',efrmdiv);	
			}
		}
		postForm.lang = GTAj.tLgu;
		if(postForm.encoding == null || typeof postForm.encoding == 'undefined'){
			if(GTAj.wFl){
				postForm.encoding = "multipart/form-data";
			}
			else{
				postForm.encoding = "application/x-www-form-urlencoded";	
			}
		}
		postForm.target = sPtWi;
		var iQmark = sUrlx.indexOf('?');
		var sUrlPart;
		if(iQmark>0){
			sUrlPart = sUrlx.substring(0,iQmark);
		}
		else{
			sUrlPart = sUrlx ;	
		}
		if(fft){
			if(sUrlx.indexOf(GTAj.vA['fft'])==-1){
				if( iQmark > -1 ){
					sUrlx += '&';	
				}
				else{
					sUrlx += '?';	
				}
				sUrlx += GTAj.vA['fft']+'=1';	
			}
		}
		if(true){
			if(sUrlPart.indexOf('.htm')>0 || sUrlPart.indexOf('.txt')>0 ){
				//--- only if main action url is .htm or .txt file, use this, update on 2006-10-18 9:24
				postForm.method = 'GET';
			}
			else{
				postForm.method = 'POST';                  
			}
		}
		postForm.action = sUrlx;
		//postForm.submit(); // maybe fail due to this._DFM_F . 10:42 2020-07-04
		if(postForm.onsubmit == null){
			postForm.submit();
			GTAj._DBG(GTAj.vA['ib'], 'this._SUB', 'Sync form:['+postForm.name+'] is being submitted.');
		}
		else{
			GTAj.currentPostFormId = postForm.name!='' ? postForm.name : postForm.id;
			postForm.onsubmit = (function(){
				var myTimerId = window.setTimeout(function(formId){
					document.getElementById(GTAj.currentPostFormId).submit();
					GTAj._DBG(GTAj.vA['ib'], 'this._SUB', 'Async form:['+GTAj.currentPostFormId+'] is being submitted.');
				}, 10); //- 0.01 sec
				return true;
			})();
		}
		this._DBG( GTAj.vA['ib'],'this._SUB',sForm+':'+postForm.method+':'+sUrlx+':'+sPtWi
				+ ',ff:['+fft+'] postform:['+postForm.name+'] enctype:['+postForm.encoding+']');
		postForm = null;
	} 
	
	//- reset all into inital status after a complete request
	this._RGT = function( eTag, tErr ){
        //--- reset();
        if( typeof GTAj == 'undefined' || GTAj==null){
            window.alert('GTAj is null? ');
            document.getElementById( this.gtFDiv ).innerHTMl = '';
            document.getElementById( this.gtFDiv+'_x' ).innerHTMl = '';
            //return true;
        }
        else if(GTAj.reg != null){
		    this._DBG( GTAj.vA['ib'],'need to reset-GTAj', 'req:['+GTAj.reg.req+'] eTag:['+eTag+']' );
        }
		if( eTag!='' && eTag!='done' && eTag != 'formValidate-0' && eTag != 'formValidate-1' ){
			this._RPT(eTag,tErr);
		}
		if( eTag !='done' ){
            if( GTAj!=null && GTAj.sFld!=null){
			    this._SAY(GTAj.sFld, GTAj.vA['sbv'], true);
            }
		}
		if( GTAj.nEmt2 ){
			if(GTAj.myBdTt!=null && typeof GTAj.myBdTt !='undefined'){ 
				try{
					GTAj.myBdTt.removeChild(GTAj.nEmt2);
				}
				catch(erm){
				}  
			}
			else{
				GTAj.ti = 1241310;
				document.body.removeChild(GTAj.nEmt2); //--- refer to this._PSD()
			}
		}
        else{
		    this._DBG( GTAj.vA['ib'],'reset-GTAj-nEmt2-is-null', 'req:['+GTAj.reg.req+']' );
        }

		if( GTAj.sTd != 0 ){
			window.clearTimeout( GTAj.sTd ) ;
		}
		if( true || GTAj.sTd2 != 0 ){
			window.clearTimeout( GTAj.sTd2 ) ;
		    this._DBG( GTAj.vA['ib'],'sTd2-RGT', GTAj.sTd2 );
		}
        if( GTAjStatus.nowopen != 0 ){
		    GTAjStatus.lastopen = GTAjStatus.nowopen ;
            GTAjStatus.nowopen = 0 ;
        }
        else{
            //-- why nowopen==0?
        }
		
        //window.alert('i am clicked! 44');
        GTAj.iTm = 0 ;
		GTAj.sTd = 0 ; 
		GTAj.sUrl = '?' ;
		GTAj.sMtd = 'GET' ;
		GTAj.iItl = 1000 ;
		GTAj.isWtRp = false ;  
		GTAj.sForm = '' ; 
		GTAj.sFld = '' ;
		GTAj.sBkCt = '' ;
		GTAj.nEmt = null ;
		GTAj.wFl = false ;
		GTAj.tErr = new Error() ;
		GTAj.nEmt2 = null ;
		GTAj.myBdTt = null ;
		GTAj.rtns = null ;
        GTAj.noticed = 0;
        GTAj.ti = 0;
	
		GTAj.cA = new Array();
		GTAj.vA = new Array()
			GTAj.vA['sbv'] = 'Submit' ;
			GTAj.vA['mul'] = 3*60*1000 ;
			GTAj.vA['ta'] = '' ;
			GTAj.vA['rn'] = ' Return ' ;
			//GTAj.vA['ib'] = false ;
			GTAj.vA['rdo'] = false ;
			GTAj.vA['ic'] = true ;
			GTAj.vA['cf'] = '' ;
			GTAj.vA['ff'] = true; //false ;
			GTAj.vA['pb'] = true ;
			GTAj.vA['bl'] = true ;
			GTAj.vA['fft'] = 'fftag';
			GTAj.vA['nbl'] = '' ;	
			GTAj.vA['fbl'] = false ;
			GTAj.vA['chkv'] = 1 ;
			
		return false ;
		
	}
	
	//- retrieve data via a request object
	this._RCV = function(){
		if (GTAj.xmlhttp.readyState == 4 && !GTAj.isDN){
			this._DBG( GTAj.vA['ib'],'RCV:steady', GTAj.xmlhttp.readyState
					+', rtn:'+GTAj.xmlhttp.responseText);
			try{
				GTAj.isDN = true ;
				GTAj.sRe = GTAj.xmlhttp.responseText ;
				GTAj.rtns = 'complete';
			}
			catch(e1148){
				return this._RGT('this._RCV', e1148 ) ;	
			}
		}
		else{
			this._SAY( GTAj.sFld,'Loading',false);
			this._DBG( GTAj.vA['ib'],'GTAj.sRe','waiting content....');
		}
  	}
	
	//-
	this._DFM_F = function(){ return false ; }
	this._DFM_T = function(){ return true ; }

	this.backGTAjax = function(sBkTarget,iBK){ 
		eval( 'document.getElementById(\''+sBkTarget+'\').innerHTML=GTAjBK.bk'+iBK ) ; 
	}
	
	//- show processing progress with a timer
	this._PSD = function( tmpiTm ){
		try{
			if( GTAjStatus.nowopen!=0 ){
                tmpiTm = tmpiTm/1000 ;
				if( !document.getElementById( GTAj.gtFDiv ) ){
					var divstyle2 = 'position:absolute;top:0px;left:0px;z-index:11';
					if( GTAj.vA['ta']!=''){
						GTAj.myBdTt = document.getElementById( GTAj.vA['ta'] );
					} 
					GTAj.nEmt2 = document.createElement('div');
					if(GTAj.nEmt2){
						GTAj.nEmt2.setAttribute('id', GTAj.gtFDiv);
						GTAj.nEmt2.style.cssText = divstyle2 ; 
					}
					else{
						console.log("GTAj.nEmt2 is null.....");	
					}
					if(GTAj.myBdTt!=null){
						GTAj.myBdTt.appendChild(GTAj.nEmt2);
					}
					else{
						document.body.appendChild(GTAj.nEmt2);	
					}
                    //window.alert('nEmt2 is created! ['+GTAj.nEmt2+'] req:['+GTAj.reg.req+']');
				}
				if( GTAj.vA['pb']){
					//--- need display pb or not, updated 2006-9-22 11:57
					var ns = ( GTAj.tBro.indexOf("netscape") != -1 || GTAj.tBro.indexOf("gecko")!=-1 );
					var pX, pY;
					pY = ns ? pageYOffset : document.documentElement && document.documentElement.scrollTop 
							? document.documentElement.scrollTop : document.body.scrollTop;
					
					var scont = '&nbsp;'+GTAj.pswd+'...'+tmpiTm
					+' &nbsp;[ <b><a href="javascript:void(0);" '
						+'onclick="javascript:GTAj._RGT(\'\',\'\');" '
						+'title="Cancel" style="color:#ff0000">X</a></b> ]&nbsp;';
					var stmpcont = '<div id="'+GTAj.gtFDiv
						+'_x" style="position:absolute;left:0;top:'+pY
						+'px;height:20px;background-color:#FFFF99;color:#ff0000;font-size:12pt;'
						+'font-weight:700;z-index:11;white-space:nowrap">'+scont+'</div>';
					var ostr = document.getElementById( GTAj.gtFDiv ).innerHTML ;
					if( typeof ostr == 'undefined' || ostr == '' ){
						document.getElementById( GTAj.gtFDiv ).innerHTML = stmpcont ;
					}
					else{
						if(document.getElementById(GTAj.gtFDiv+'_x')){
							document.getElementById( GTAj.gtFDiv+'_x' ).innerHTML=scont ;
						}
					}
				}
			}
		}
		catch(eDiv){
			console.log(eDiv);
			return this._RGT('prcsDiv',eDiv);
		}
	}

	//- check form field with validating rules
	this._CFM = function(strForm,strField,strVal,strChk){
		//--- _chkFrm();
		try{
			if( strChk.indexOf(GTAj.vA['cfs']) > -1 ){
				var chkTmpA = strChk.split(GTAj.vA['cfs'],2) ;
				var chktype = chkTmpA[0] ;
				var strError = strField+': Unacceptable:['+strVal+']. '+chkTmpA[1] ;
				var prefixval = 0 ;
				var chkok = true ;
				if(chktype.indexOf('+')>-1){
					chktype = chktype.replace('+','');
					if(eval(strVal.length) == prefixval){
						chkok = false ;
					}//if 
				}
				if(chkok){
					if(chktype.indexOf('=')>-1){
						var subChkTmpA = chktype.split('=');
						chktype = subChkTmpA[0];
						if(subChkTmpA[0]==null || subChkTmpA==''){
							subChkTmpA[1] = 0 ;	
						}
						prefixval = subChkTmpA[1] ;
					}
					if(chktype=='req' || chktype=='required' || chktype=='notvalue'){
						if(strVal.length==prefixval){
							chkok = false;	
						}	
					}
					else if(chktype=='maxlength' || chktype=='maxlen'){
						if(strVal.length > prefixval){
							chkok=false;	
						}		
					}
					else if(chktype=='minlength' || chktype=='minlen'){
						if(strVal.length < prefixval){
							chkok=false;	
						}		
					}
					else if(chktype=='alnum' || chktype=='alphanumeric')
					{
						var charpos = strVal.search(/[^A-Za-z0-9]/); 
						if(strVal.length > 0 &&  charpos >= 0){
							chkok = false ;
						} 
					}
					else if(chktype=='num' || chktype=='numeric'){
						var charpos = strVal.search(/[^0-9]/); 
						if(strVal.length > 0 &&  charpos >= 0){
							chkok = false ;
						}//if 
					}
					else if(chktype=='alphabetic' || chktype=='alpha'){
						var charpos = strVal.search(/[^A-Za-z]/); 
						if(strVal.length > 0 &&  charpos >= 0){
							chkok = false ;
						}//if 
					}
					else if(chktype=='alnumhyphen'){
						var charpos = strVal.search(/[^A-Za-z0-9\-_]/); 
						if(strVal.length > 0 &&  charpos >= 0){
							chkok = false ;
						}//if 
					}
					else if(chktype=='email'){
						if(strVal=='' 
							|| strVal.match(/^[\w-\.]+\@[\w\.-]+\.[a-z]{2,4}$/)==null){
							chkok = false ; 
						}
					}
					else if(chktype=='lt' || chktype=='lessthan'){
						if( isNaN(strVal) || strVal.trim()=='' || (eval(strVal) >= prefixval)){ 
							chkok = false ;                 
						}//if    	
					}
					else if(chktype=='gt' || chktype=='greaterthan'){
						if( isNaN(strVal) || strVal.trim()=='' ||(eval(strVal) <= prefixval)){ 
							chkok = false ;                
						}//if    
					}
					else if(chktype=='regexp'){
						if(strVal.length > 0){
							if(!strVal.match(prefixval)) { 
								chkok = false ;                  
							}//if 
						}
					}
					else if(chktype=='checked'){
                        eval('var btn_type = strForm.'+strField+'[0];');
                        if(typeof btn_type == 'undefined'){
						    eval('var ischecked = strForm.'+strField+'.checked');
                            //eval('ischk_1=document.'+GTAj.reg.req+'.'+strField+'.checked');
                            //window.alert('ischk:['+ischecked+']');
                            if(!ischecked){
                                chkok=false;
                            }
                        }
                        else{ //- added on Mon Aug  1 23:33:46 BST 2011
                            var cnt = -1;
                            eval('var btn = strForm.'+strField+';');
                            for (var i=btn.length-1; i > -1; i--){
                                if (btn[i].checked) {cnt = i; i = -1;}
                            }
                            if(cnt == -1){
                                chkok = false;
                            }
                        }
					}
                    else if(chktype == 'unique' || chktype == 'uniq'){
                        //- todo    
                    }
					else{
						strError = 'Unpredefined form validate type:['+chktype+']' ;
						chkok = false ;	
					}
				}
				if(!chkok){
					window.alert('Attention:\n'+strError+ ' .');
					eval('document.'+GTAj.reg.req+'.'+strField+'.style.background=\'#FFFF99\'');
					eval('document.'+GTAj.reg.req+'.'+strField+'.focus()');
					return GTAj._RGT('formValidate-1',GTAj.tErr);  
				}
			}
			else{
				//--- invalid chkform str, omitted, 20080222
				GTAj._DBG( GTAj.vA['ib'], 'formValidate','invalid chkstr:['+strChk+'] omitted.');
			}	
			return chkok; 
		}
		catch(echk){
			console.log(echk);
			return GTAj._RGT('formValidate-0',echk);
		}
	}
	
	//- support do disable copy & paste on page
	this._DISCP = function( isnocp ){
		//--- disable select & copy
		if( true ){
			try{
				if( document.body ){
					if( GTAj.tBro.indexOf('msie') > -1 || GTAj.tBro.indexOf('explorer') > -1 ){
						if( isnocp ){
							document.body.ondragstart = this._DFM_F ;
							document.body.onselectstart = this._DFM_F ;
						}
						else if( !isnocp ){
								document.body.ondragstart = this._DFM_T;
								document.body.onselectstart = this._DFM_T; 
						}
					}
					else{
						if( isnocp ){
							document.body.onmousedown = function(e) { 
									if (typeof e.preventDefault != 'undefined') { 
										return e.preventDefault();
									}
								};	
						}
						else if( !isnocp ){
							//--- todo: cancel prevent if exists in firefox	
							document.body.onmousedown=this._DFM_T; 
						}
					}	
				}
				else{
					window.alert('err when document.body: '+document.body);	
				}		
			}
			catch( e1827 ){
				window.alert('err when disable copy&paste: ' + e1827 );	
			}
		}
	}
	
	//- support <input accept="minlen=5::Minmal length requires at least 5.">
    this._chkAccept = function(sForm, el, myCFM){ //- added 20110711
        var rtn = true;
        var acpt = el.accept;
        if(acpt != null && acpt != '' && acpt != 'undefined'){
            var acptarr = acpt.split(',');//- e.g. 'image/*,minlen=3::el.name need at least 3 chars.'            
            var count = acptarr.length;
            for(var i=0;i<count;i++){
                if(!rtn){ break; }
                if(acptarr[i].indexOf(GTAj.vA['cfs']) > -1){
                    if(!myCFM(sForm,el.name,el.value,acptarr[i])){
                        rtn = false;
                    }
                }
            }
        }
        return rtn;
    }

	this.setcp = this._DISCP ;
	
}

//- register with global window
window.GTAjax = GTAjax;

//---- DO NOT CHANGE ANY PART OF THE CODE ABOVE THIS LINE ---
/*

var gtaj = new GTAjax();
gtaj.set('nobacktag','<!--gtajaxsucc-->'); //--- server response with this tag, no append back link
gtaj.set('nocopy',true); //--- forbid copy content from current page
gtaj.set('isdebug', true); //--- output verbosely
var resp = gtaj.get(sUrl);

*/
//-- http://www.thescripts.com/forum/thread508775.html
// compress tool : http://dojotoolkit.org/docs/compressor_system.html
// java -jar custom_rhino.jar -c infile.js > outfile.js 2>&1
// java -jar custom_rhino.jar -c GTAjax-20070515-2.01.js > GTAjax_.js 2>&1

//-->