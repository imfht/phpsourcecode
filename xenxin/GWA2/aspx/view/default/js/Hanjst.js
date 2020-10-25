//-
//- Hanjst
//- 汉吉斯特
/* 
 * Han JavaScript Template Engine
 * --- The template semantic, syntax and its engine ---
 * 基于JavaScript通用HTML页面模板语言及其解析引擎
 * @Born with GWA2， General Web Application Architecture
 * @Xenxin@ufqi.com, Wadelau@hotmail.com
 * @Since July 07, 2016, refactor on Oct 10, 2018
 * @More at the page footer.
 * @Ver 1.6
 */

"use strict"; //- we are serious

//- Hanjst configs, modify at your conveniences
if(!window){ window = {}; } //- why this?
if (typeof window.console == "undefined") {
    window.console = {log: function(errMsg){ window.alert(errMsg); }};
}
window.HanjstDefault = {
	"TplVarTag": "$", //- variables from response starting with this, e.g. $pageTitle
	"JsonDataId": "Hanjstjsondata", //- an html element id which holds server response json data
	"LogTag": "Hanjst", //- inner usage
	"ParseTag": "__JSTPL__",  //- inner usage
	"IsDebug": false, //- verbose output in console
};

/*
 * Hanjst runtime 
 * ----------------- !!! Please do not edit any code below this line !!! -----------------
 */
 //- parent's configs will override defaults
if(window.Hanjst){
	for(var $k in window.Hanjst){
		window.HanjstDefault[$k] = window.Hanjst[$k];
	}
}
window.Hanjst = window.HanjstDefault;
//- ----------------- MAGIC START -----------------
(function(window){ //- anonymous Hanjst main func bgn

	//- global objects
	if(!window.Hanjst){
		var errMsg="Hanjst undefined. 201812011128"; 
		console.log(errMsg); return errMsg;
	}
    else{
        var myNav = navigator.userAgent.toLowerCase();
        if(myNav != null && myNav.indexOf("msie") > -1){
		    var errMsg="MSIE is too old. \nPlease update it to one of "
                +"\nMS Edge, Google Chrome or Mozilla Firefox.\n201906161036"; 
            window.alert(errMsg); console.log(errMsg); console.log("UA:"+myNav);
            return errMsg;
        }
    }

	//- constants
	var parseTag = window.Hanjst.ParseTag; var unParseTag = '__NOT' + parseTag;
	var tplVarTag = window.Hanjst.TplVarTag; var jsonDataId = window.Hanjst.JsonDataId; 
	var logTag = window.Hanjst.LogTag+" "; var isDebug = window.Hanjst.IsDebug;
	
	var timeCostBgn = 0; var myDate = new Date();
    if(isDebug){ timeCostBgn = myDate.getTime(); }
	var pageJsonElement = document.getElementById(jsonDataId);
	//- check parent node
	if(pageJsonElement){
		var parentName = pageJsonElement.parentNode.nodeName;
		if(parentName.toLowerCase() != 'body'){
			console.log(logTag+'Error! parentNode:['+parentName+'] should be "BODY". Please consider to put '+logTag+' codes block under <body>. 201906040809.');
		} 
	}
	//- handle server response in json,
	//- parse it into global variables starting with this tplVarTag, ie, $ as the default.
	var tplData = {}; // data holder
	if(pageJsonElement){
		var tplDataStr = pageJsonElement.innerText;
		try{
			tplDataStr = tplDataStr.trim();
			tplData = JSON.parse(tplDataStr);
		}
		catch(e0939){ console.log(e0939); console.log('Error! pageJsonElement in malformat JSON. 201911071307.'); }
		if(!tplData['copyright_year']){ tplData['copyright_year'] = myDate.getFullYear(); }
		if(!tplData['time_stamp']){ tplData['time_stamp'] = timeCostBgn; }
		//- parse json keys as global variables
		//- variables starting with tplVarTag, i.e., $ as default
		if(window){
			//- window ready...
		}
		else{
			window = {}; //- when and why?	
			console.log('window undefined error. 201812011122.'); 
		}
		for(var $k in tplData){
			//console.log("k:"+$k+" v:"+tplData[$k]);
			if($k != null && $k != ''){
				var $v = tplData[$k];
				$k = tplVarTag + $k;
				if(typeof window[$k] != 'undefined'){
						console.log(logTag+': Variables:['+$k+'] conflict error. '+
							+' Please consider rename one of window.'+$k+' or pageJsonData.'+
							+' 201904202305.');
				}
				else{
					window[$k] = $v; //- globally unique
				}
			}
		}
		//- hide raw data
		pageJsonElement.style.visibility = 'hidden'; // hide json data element
		tplDataStr = null;
	}
	else{
        window[tplVarTag+'copyright_year'] = myDate.getFullYear();
		console.log(logTag+'pageJsonElement:['+jsonDataId+'] has error. 201812010927'); 
	}
	tplData = null;
	
	//- main function
	//- parse all tag blocks
	if(isDebug){ console.log(logTag+"aft parse copyright_year:"+$copyright_year); }
	//- inner renderTemplate
	var _renderTemplateRecurse = function(window, document, tplHTML){
		//- collect tpl content
		var match, tplRaw, tplObject, firstContent; // firstContent for contents in first page
		tplObject = document.body || document;
        //console.log(tplObject);
		if(tplHTML == null || tplHTML == ''){
			tplRaw = tplObject.innerHTML;
			//console.log(tplRaw);
			var tmppos = tplRaw.indexOf(' id="'+jsonDataId+'"');
			if(tmppos == -1){
				tmppos = tplRaw.indexOf(' id="'+jsonDataId+'"');
			}
			if(tmppos > -1){
				tplRaw = tplRaw.substring(0, tmppos); // discard json data
				tmppos = tplRaw.lastIndexOf('<'); 
				tplRaw = tplRaw.substring(0, tmppos); // remove json data tag
			}
			else{
				console.log(logTag + "jsonDataId:["+jsonDataId+"] has error.201812011028");
			}
		}
		else{ tplRaw = tplHTML; }
        tplRaw = _remedyMemoLine(tplRaw); firstContent = tplRaw;
		if(tplHTML == null || tplHTML == ''){ 
			Hanjst.tplObject = tplObject; Hanjst.firstContent = firstContent;
		}

		//- prepare-1
		//- parse include parts recursively
		var includeRe = /\{include [file|content]*="([^\}]*?)"\}/gm;
		var tplRawNew, tmpCont, matchStr, exprStr;	
		tplRawNew = tplRaw;
		while(match = includeRe.exec(tplRaw)){
			//console.log(match);
			matchStr = match[0]; exprStr = match[1];
			tmpCont = (new Function("return "+exprStr+";")).apply();
			if(tmpCont != null && tmpCont != ''){
				tmpCont = _renderTemplateRecurse(window, document, tmpCont);
			}
			tplRawNew = tplRawNew.replace(matchStr, tmpCont);
		}
		tplRaw = tplRawNew;
		//console.log(tplRaw);
		return tplRaw;
	}
	
	//- main & entry call
	var renderTemplate = function(window, document, tplHTML){
		var tplSegment = [], tplRawNew, match, matchStr, exprStr; 
		var staticStr, ipos, segi, segStr, lastpos = 0;
		var tplRaw = _renderTemplateRecurse(window, document, tplHTML);
		var firstContent = Hanjst.firstContent;
		
		//- parepare-2
		//- fix innerHTML bug {if="" for tpl embedded in <>
		var embeddedRe = /([^<]*)(if|for|while|switch|else|eq|lt|gt|\d|")[\}]*=""/gm;
		lastpos = 0; tplRawNew = tplRaw;
		while(match = embeddedRe.exec(tplRaw)){
			matchStr = match[0]; exprStr = matchStr;
			exprStr = _parseTagInElement(exprStr, match);
			tplRawNew = tplRawNew.replace(matchStr, exprStr);
			if(isDebug){
				console.log(logTag+"found embedded tpl sentence:["+matchStr
					+"] but compatible partially.");
			}
		}
		tplRaw = tplRawNew; tplRawNew = null;
		//console.log(tplRaw);
		
		//- parepare-3
		//- parse literal scripts, self-defined literal tag? @todo
		var literalRe = /\{literal\}(.*?)\{\/literal\}/gm;
		var tplSegmentPre = []; var hasLiteralScript = false; lastpos = 0;
		if(tplRaw.indexOf('{literal}') > -1){
			while(match = literalRe.exec(tplRaw)){
				//console.log(match);
				ipos = match.index;
				staticStr = tplRaw.substring(lastpos, ipos);
				matchStr = match[0]; exprStr = match[1];
				tplSegmentPre.push(staticStr);
				tplSegmentPre.push(unParseTag + exprStr);
				lastpos = ipos + matchStr.length;
				hasLiteralScript = true;
			}
		}
		if(hasLiteralScript){
			staticStr = tplRaw.substring(lastpos); // remainings
			tplSegmentPre.push(staticStr);
		}
		else{
			if(isDebug){ console.log(logTag + "no literals:"+tplRaw); }
			tplSegmentPre.push(tplRaw);
		}
		//console.log(tplSegmentPre);
		
		//- prepare-4
		//- parse original scripts
		var scriptRe = /<script[^>]*>(.*?)<\/script>/gm;
		var hasScript = false; var isIncludeScript = false; 
		var asyncScriptArr = []; var isAsync = false; var srcPos = -1;
		var regExp1906 = new RegExp("'", 'gm');
		for(var $prei in tplSegmentPre){
			tplRawNew = tplSegmentPre[$prei];
			if(tplRawNew.indexOf(unParseTag) > -1){ // literal scripts
				tplSegment.push(tplRawNew);
			}
			else{
				lastpos = 0; srcPos = -1; isIncludeScript = false;
				while(match = scriptRe.exec(tplRawNew)){
					//console.log(match);
					ipos = match.index;
					staticStr = tplRawNew.substring(lastpos, ipos);
					matchStr = match[0]; exprStr = match[1];
					srcPos = matchStr.indexOf(' src=');
					if(srcPos > 0){ isIncludeScript = firstContent.indexOf(matchStr)>-1 ? false : true; }
					else{ isIncludeScript = firstContent.indexOf(exprStr)>-1 ? false : true; }
					if(matchStr.indexOf(' async') > -1){ isAsync = true; }else{ isAsync = false; }
					if(isAsync || isIncludeScript){
						if(exprStr != null && exprStr != ''){
							if(exprStr.indexOf('document.write') > -1){
								/* should skip */
								if(isDebug){ console.log(logTag+"found 'document.write' and skip..."); }
							}
							else{
								if(isAsync){
									//asyncScriptArr.push(exprStr);
									/* failed for function defined? */
									exprStr = exprStr.replace(regExp1906, "\\'");
									tplSegment.push('var tmpTimer'+ipos+'=window.setTimeout(function(){try{'
										+'Hanjst.appendScript(\''+exprStr+'\', \'\');'
										+'}catch(tmpErr){if('+isDebug+'){console.log("'+logTag
										+' found error with embed scripts:\"+JSON.stringify(tmpErr)+\"");}};}, '
										+ 'parseInt(Math.random()*200+100));'); //- why two seconds?
								}
								else{
									if(isDebug){ 
										console.log(logTag+"includeScript:"+exprStr+" matchStr:"+matchStr);
									}
									appendScript(exprStr, matchStr);
								}
							}
						}
						else if(srcPos > 0){
							if(isAsync){
								matchStr = matchStr.replace(regExp1906, "\\'");
								tplSegment.push('var tmpTimer'+ipos+'=window.setTimeout(function(){try{ Hanjst.appendScript(\'\', \''+matchStr+'\');' 
										+'}catch(tmpErr){if('+isDebug+'){console.log("'+logTag
										+' found error with embed src scripts:\"+JSON.stringify(tmpErr)+\"");}};}, '
										+ 'parseInt(Math.random()*200+100));');
							}
							else{
								appendScript(exprStr, matchStr);
							}
						}
					}
					else{
						if(isDebug){ console.log(logTag+"found scripts:["+matchStr+"] skiped for isIncludeScript:["+isIncludeScript+"] or isAsync:["+isAsync+"]"); }
					}
					tplSegment.push(parseTag + staticStr);
					lastpos = ipos + matchStr.length;
					hasScript = true;
				}
				if(hasScript){
					staticStr = tplRawNew.substring(lastpos); // remainings
					tplSegment.push(parseTag + staticStr);
				}
				else{
					if(isDebug){ console.log(logTag + "no scripts:"+tplRawNew); }
					tplSegment.push(parseTag + tplRawNew);
				}
			}
		}
		Hanjst.asyncScriptArr = asyncScriptArr;
		//console.log(tplSegment);
		
		//- main body of the main function
		//- loop over tplSegment for tags interpret
		segStr = ''; segi = 0; var tpl2codeArr = []; var tpl2code = '';
		tpl2codeArr.push("var tpl2js = []; var blockLoopCount = 0;");
		var blockBeginRe, tmpmatch, needSemiComma, containsDot, containsBracket;
		var tmpArr, containsEqual, tmpIfPos, hasLoopElse, loopElseStr, bracketPos, dotPos;
		//- tpl keywords and patterns
		var tplRe = /\{((for|if|while|else|switch|break|case|\$|\/|var|let)[^}]*)\}/gm;
		for(segi in tplSegment){ //- loop over segments besides originals
			segStr = tplSegment[segi];
			if(segStr.indexOf(unParseTag) > -1){ //- literal scripts
				segStr = segStr.replace(unParseTag, '');
				tpl2codeArr.push("\ttpl2js.push(\""+segStr+"\");");
			}
			else if(segStr.indexOf(parseTag) == -1){ //- original scripts
				tpl2codeArr.push("\n" + segStr);
			}
			else{ //- mixed tpl content, unspecified
				//- parse all tpl tags with match
				segStr = segStr.replace(parseTag, ''); lastpos = 0; 
				hasLoopElse = false; loopElseStr = '';
				while(match = tplRe.exec(segStr)){
					ipos = match.index;
					staticStr = segStr.substring(lastpos, ipos);
					staticStr = staticStr.replace(/"/g, '\\"');
					if(staticStr != ''){
						if(hasLoopElse){
							loopElseStr += staticStr; // empty after every loop at end
						}
						else{
							tpl2codeArr.push("\ttpl2js.push(\""+staticStr+"\");");
						}
					}
					//console.log(match);
					matchStr = match[0]; containsBracket = false;
					exprStr = match[1]; containsDot = false; containsEqual = false;
					if(exprStr.indexOf(tplVarTag) == 0){
						//- functions and variables
						if(exprStr.match(/(\+|\-|\*|\/|=|~|!|\()/gm)){
							//- functions call
							bracketPos = exprStr.indexOf('(');
							dotPos = exprStr.indexOf('.');
							if( bracketPos > -1){ containsBracket = true;} 
							if( dotPos > -1){ containsDot = true; }
							if(exprStr.indexOf('=') > -1){ containsEqual = true; }
                            if(containsBracket && !containsDot && !containsEqual){
								//- private, $aFunc($a)
								exprStr = exprStr.substring(1);
								tpl2codeArr.push("\ttpl2js.push("+exprStr+");");
							}
							else if(containsDot && !containsEqual){
								if(dotPos < bracketPos){
									//- built-in, $a.substring(0, 5)
									tpl2codeArr.push("\ttpl2js.push("+exprStr+");");
								}
								else{
									//- built-in, $aFunc(0.5)
									exprStr = exprStr.substring(1);
									tpl2codeArr.push("\ttpl2js.push("+exprStr+");");
								}
							}
							else{
								//- variables operations, $a++
								tpl2codeArr.push(exprStr + ';');
							}
						}
						else{
							//- variables access, $a
							if(hasLoopElse){
								loopElseStr += "\"+"+exprStr+"+\""; // why only this? allow limited support for variables in xxxelse scope.
							}
							else{
								tpl2codeArr.push("\ttpl2js.push("+exprStr+");");
							}
						}
					}
					else if(exprStr.match(/.*({|;|}).*/gm)
						&& exprStr.indexOf('t;') == -1){ 
						// exceptions, &gt; &lt;
						tpl2codeArr.push("\ttpl2js.push(\""+matchStr+"\");");
						if(isDebug){
						console.log(logTag + "illegal tpl sentence:["+matchStr
							+"] for containing {, }, ;.  skip... 201812012201.");
						}
					}
					else{ //- directives
						needSemiComma = true; 
						if(exprStr.match(/(^( )?(if|for|while|switch|case|break))(.*)?/g)){
							blockBeginRe = /^(if|for|while|switch)(.*)/gm; // why re-init?
							if(tmpmatch = blockBeginRe.exec(exprStr)){
								//- blocks begin
								//console.log(tmpmatch);
								if(tmpmatch[2].indexOf('each ') == 0){ //- foreach
									tmpArr = tmpmatch[2].substring(5).split(' as ');
									tmpmatch[2] = 'var ' + tmpArr[1] + ' in ' + tmpArr[0];
								}
								else if(tmpmatch[2].indexOf('eachelse') == 0
									|| tmpmatch[2].indexOf('else') == 0){ 
									//- foreachelse, forelse, whileelse
									tpl2codeArr.push('\tblockLoopCount += 1;'); //- skip first else sentence
									exprStr = '';  hasLoopElse = true;
								}
								if(tmpmatch[2].indexOf('(') == -1 && !hasLoopElse){
									if(isDebug){
									console.log(logTag+"illegal tpl sentence:["
										+exprStr+"] but compatible.");
									}
									exprStr = tmpmatch[1] + '(' + tmpmatch[2] + ')';
								}
							}
							else{
								if(isDebug){ console.log("not blockBegin? ["+exprStr+"]"); }
							}
							if(!hasLoopElse){ 
								exprStr += '{'; needSemiComma = false;
							}
						}
						else if(exprStr.indexOf('else') == 0){ //- if branch
							tmpIfPos = exprStr.indexOf('if ');
							if( tmpIfPos > -1 && exprStr.indexOf('(') < 0){
								if(isDebug){
								console.log(logTag+"illegal tpl sentence:"+exprStr
									+" but compatible.");
								}
								exprStr = exprStr.substr(0, tmpIfPos+3) 
									+ '(' + exprStr.substr(tmpIfPos+3) + ')';
							}
							exprStr = '}\n' + exprStr + '{'; needSemiComma = false;
						}
						else if(exprStr.indexOf('/') == 0){ //- end of a block
							exprStr = '}'; needSemiComma = false;
							if(hasLoopElse){
								exprStr += '\n\tif(blockLoopCount < 1){ tpl2js.push("'
									+loopElseStr+'"); }';
								hasLoopElse = false; loopElseStr = ''; //- re-init
								exprStr += '\n\tblockLoopCount = 0;';
							}
						}
						if(exprStr != ''){
							if(exprStr.indexOf('t;') > -1){
								exprStr = exprStr.replace('&gt;', '>');
								exprStr = exprStr.replace('&lt;', '<');
							}
							if(needSemiComma){ exprStr += ';'; }
							if(exprStr.match(/ (eq|lt|gt) /)){
								exprStr = exprStr.replace('eq', '==')
									.replace('lt', '<')
									.replace('gt', '>');
							}
						}
						if(hasLoopElse){ // skip first sentence
							loopElseStr += exprStr;
						}
						else{
							tpl2codeArr.push("\n" + exprStr);
						}
					}
					lastpos = ipos + matchStr.length;
				}
				//- last static part
				staticStr = segStr.substring(lastpos);
				staticStr = staticStr.replace(/"/g, '\\"');
				if(staticStr != ''){
					tpl2codeArr.push("\ttpl2js.push(\""+staticStr+"\");");
				}
			}
		} // end of loop over tplSegment
		tplRaw = null; tplSegment = null;
		
		//- append returns to tpl2code
		tpl2codeArr.push("return tpl2js.join('');");
		tpl2code = tpl2codeArr.join("\n"); Hanjst.tpl2code = tpl2code; tpl2codeArr = null;
		tpl2code = "try{ " + tpl2code + "\n}\ncatch(e1635){ var errMsg=JSON.stringify(e1635, Object.getOwnPropertyNames(e1635)); console.log(e1635);"
            + "var tmpRegexp=/>\:([0-9]+)\:([0-9]+)/gm; var tmpRegexp2=/\"lineNumber\":([0-9]+)/gm; var tmpmatch=null; var tmpLineno=0; var tmpCharno=0; if(tmpmatch=tmpRegexp.exec(errMsg)){ tmpLineno=parseInt(tmpmatch[1]); tmpCharno=tmpmatch[2];}else if(tmpmatch=tmpRegexp2.exec(errMsg)){tmpLineno = parseInt(tmpmatch[1]);}; if(tmpLineno>0){ if(tmpLineno<4){ tmpLineno = 4;}; var tmpArr=Hanjst.tpl2code.split(\"\\n\");  errMsg += \"<p>Line \"+(tmpLineno-1)+\": \"+tmpArr[tmpLineno-4].replace(/</g, '&lt;')+\"</p>\";  errMsg += \"<p>Line \"+tmpLineno+\": \"+tmpArr[tmpLineno-3].replace(/</g, '&lt;')+\"</p>\"; errMsg += \"<p>Line \"+(tmpLineno+1)+\": \"+tmpArr[tmpLineno-2].replace(/</g, '&lt;')+\"</p>\"; console.log('errMsg:['+errMsg+']'); }else{ errMsg+=\"regExp:\"+tmpRegexp+\" failed.\";}\n"
			+ "errMsg=\"<p>"+ logTag +"template code exec failed.</p><p>\"+errMsg+\"</p>\";"
			+ "return errMsg; }\n";
		
		//- merge data and compile
		var tplParse = '';		
		if(isDebug){ console.log(logTag + "tpl2code:"+tpl2code); }
        try{
		    //tplParse = (function(){ return (new Function(tpl2code).apply(window)); }).apply();
		    tplParse = (new Function(tpl2code)).apply(window);
		    if(isDebug){ console.log("tplParse:"+tplParse); }
        }
        catch(e1200){
            console.log(JSON.stringify(e1200, Object.getOwnPropertyNames(e1200)));
        }
		Hanjst.tplObject.innerHTML = tplParse;
		//- release objects		
		tpl2code = null; Hanjst.tpl2code = null; tplParse = null;
	};
	
	//- append embedded scripts into current runtime
	var appendScript = function(myCode, myElement) {
		if(myCode == '' && myElement == ''){
			return ;
		}
		else{
		var s = document.createElement('script');
		s.type = 'text/javascript';
		var code = myCode;
        if((code == null || code == '') 
			&& myElement != null && myElement != ''){
            //- in case of, <script src=""/></script>
	        var srcRe = /<script[^>]* src=["|']+([^"]*)["|']+[^>]*>/gm;
			var tmpmatch, tmpmatch2, mySrc, tmpval;
            if(tmpmatch = srcRe.exec(myElement)){
                //console.log(tmpmatch);
                mySrc = tmpmatch[1];
				//- in case, vars in src
                var tmpTagRe = /\{\$([^\}]+)\}/gm;
                while(tmpmatch2 = tmpTagRe.exec(mySrc)){
                    //console.log(tmpmatch2);
                    tmpval = (new Function("return $"+tmpmatch2[1])).apply();
                    mySrc = mySrc.replace(tmpmatch2[0], tmpval);
                    if(isDebug){
                        console.log(logTag+"found vars in mySrc:"+mySrc+" tmpval:"+tmpval+" aft.");
                    }
                }
            }
            else{
				//console.log(logTag+" not found src?:["+myElement+"]");
                mySrc = '';
            }
			//console.log((new Date())+" appendScript: mySrc:"+mySrc);
            s.src = mySrc; 
        }
		try{
			if(code != null && code != ''){
				code = "try{"+code+"}catch(tmpErr){ if(true){console.log(\""+logTag
					+"append embed failed 201901151438:\"); console.log(tmpErr);} }";
			}
			s.appendChild(document.createTextNode(code));
			document.body.appendChild(s);
		}
		catch(e){
			s.text = code;
			document.body.appendChild(s);
		}
		if(isDebug){
			console.log(logTag+(new Date())+' appendScript: ['+myCode+']/['+myElement+'] has been appended.');
		}
		return ;
		}
	};
	//- export for async call
	Hanjst.appendScript = appendScript;
	
	//- inner methods
	//- search fields within a regexp match
	var _searchField = function(matchList){
		var fields = {};
		for(var $k in matchList){
			var tmpval = matchList[$k];
			if($k>=0 && $k != 'input' && $k != 'index'){
				tmpval = tmpval.replace(/\{\/(.+)/, '$1');
				if(tmpval.match(/(lt|gt|eq)/g)){
					fields['op'] = tmpval;
				}
				else if(tmpval.match(/=/g)){
					fields['result'] = tmpval;
				}
				else if(tmpval.match(/\$/g)){
					fields['condition'] = tmpval;
				}
				else{
					if(!tmpval.match(/(else|if)/) 
						&& tmpval != '{' && tmpval != '}'){
						tmpval = tmpval=='' ? '0' : tmpval; //- why 0?
						fields['val'] = tmpval.replace(/\{[\/]*(.+)\}/, '$1')
							.replace(/\}[ ]*/, '');
					}
					else{
						//console.log('unkown matchTag: k:'+$k+' val:'+tmpval);
					}
				}
			}
		}
		//console.log(matchList); console.log(fields);
		return fields;
	};
	
	//- inner methods
	//- parse tags embedded in an html element
	var _parseTagInElement = function(exprStr, match){
		//- only if support? @todo
		if(!exprStr){ return ''; }
		exprStr = exprStr.replace(/\}=""/g, '}')
			.replace(/\{="" /g, '{/')
			.replace(/="" (eq|lt|gt)=""/, ' $1')
			.replace(/=""/g, '');
		var hasInsertSpace = false; var tmpmatch;
		if(exprStr.match(/([\S]+)\{if/g)){
			exprStr = exprStr.replace(/([\S]+)\{if/g, '$1 {if');
			hasInsertSpace = true;
			console.log(logTag+" found illegal tpl sentence:["+match[0]
				+"], consider add space between element attribute name and tpl tag."
				+ "["+exprStr+"]");
		}
		var startIfPos = exprStr.indexOf('{if');
		var endIfPos = exprStr.indexOf('if}'); var needSortElement = false;
		if(hasInsertSpace){ //- test whether unsorted or not
			var tagsBfrEnd = ['else', 'lt', 'gt', 'eq', '}', '{/'];
			var tmpArr = exprStr.split(' ');
			var tmpEndIfi = 0; var tmptagi = 0;
			for(var $k in tmpArr){
				for(var $l in tagsBfrEnd){
					if(tmpArr[$k].indexOf(tagsBfrEnd[$l]) > -1){
						tmptagi = $k;
					}
					else if(tmpArr[$k] == 'if}'){
						tmpEndIfi = $k;
					}
					if(tmpEndIfi > tmptagi){
						needSortElement = true; break;
					}
				}
				if(needSortElement){ break; }
			}
		}
		//- parse all unsort tpl list
		if((endIfPos > 0 && startIfPos > 0 && startIfPos > endIfPos)
			|| needSortElement){
			//- supposed in MS Edge
			var tmpi = 0;
			var parts = exprStr.split(' ');
			for(var $k in parts){
				if(parts[$k] == 'if}' || parts[$k] == '{if'){
					tmpi = $k;
					break;
				}
			}
			tmpi--; if(hasInsertSpace){ tmpi = 1; } 
			var parts2 = []; var parts3 = [];
			for(var $k=tmpi; $k<parts.length; $k++){
				parts2.push(parts[$k]);
			}
			for(var $k=0; $k<tmpi; $k++){
				parts3.push(parts[$k]);
			}
			//parts2.sort(); console.log(parts2); console.log(parts3);
			if(isDebug){
			console.log("exprStr:["+exprStr+"] tmpi:"+tmpi+" aft sorted:["+parts2.join(' ')+"]");
			}
			exprStr = parts2.join(' '); // left parts3
			var fields = _searchField(parts2);
			var newExprStr = " {if "+fields['condition']+" "+fields['op']+" "+fields['val']+" } "
				+fields['result']+" {/if}";
			newExprStr = parts3.join(' ') + ' ' + newExprStr;  // add parts3
			console.log('regX ifStart:'+startIfPos+' ifEnd:'+endIfPos
				+' matched! reverse:['+newExprStr+'] in MS Edge.');
			exprStr = newExprStr;
		}
		else{
			//- supposed in Chrome/Firefox...
			if(isDebug){ console.log(logTag+'sorted tpl sentence:['+exprStr+'] in Chrome-likes.'); }
		}
		return exprStr;
	};

    //- inner method
    //- remedy for comment lines in JavaScript
    var _remedyMemoLine = function(myCont){
        var memoRe = /[^(:|"|'|=)]\/\/(.*?)[\n\r]+/gm; // "//-" patterns
        var match, matchStr, segStr; var myContNew = myCont;
		while(match = memoRe.exec(myCont)){
            //console.log("memoRe:match:"); console.log(match);
            matchStr = match[0]; segStr = match[1];
            myContNew = myContNew.replace(matchStr, "/*"+segStr+"*/");
        }
        myCont = myContNew;
		myCont = myCont.replace(/[\n\r]/g, '');
		myCont = myCont.replace(/<!--.*?-->/g, '');
        return myCont;
    };
	
	//- show image in async way
	//- 13:08 Friday, April 10, 2020, revised 12:25 Saturday, April 18, 2020
	var showImageAsync = function(imgId){
		var defSrc='data:image/png;base64,MA=='; //-?
		if(typeof imgId == "undefined" || imgId == null || imgId == ''){
			return ;	
		}
		else{
			Hanjst.asyncScriptArr.push('if(true){var imageTimerX=window.setTimeout(function(){var tmpObj=document.getElementById(\''+imgId+'\');if(tmpObj){var dataSrc=tmpObj.getAttribute(\'data-src\');if(dataSrc&&dataSrc!=\'\'){tmpObj.src=dataSrc;}}}, 100);}');
		}
		return ;
	}
	//- export to window and Hanjst
	if(typeof window.showImageAsync == 'undefined'){
		window.showImageAsync = Hanjst.showImageAsync = showImageAsync;
	}
	else{
		console.log(logTag + " function 'showImageAsync' conflicts. try to rename showImageAsync to another one.");
	}
	
	//- invoke the magic Hanjst
    var _callRender = function(){ //- wait longer?
        renderTemplate(window, document, null);
        if(isDebug){
            console.log(logTag + "parse time cost: "+(((new Date()).getTime() - timeCostBgn)/1000)+"s");
        }
		//- oncomplete and raise asyncScripts
        if(true){
            var loadingLayer;
            if(typeof Hanjst.LoadingLayerId != 'undefined'){
                loadingLayer = document.getElementById(Hanjst.LoadingLayerId);
            }
            else{
                loadingLayer = document.getElementById('Hanjstloading');
            }
            if(loadingLayer){
                loadingLayer.style.display = 'none';
				loadingLayer.style.width = 0; loadingLayer.style.height = 0;
				if(isDebug){ console.log((new Date())+" "+logTag+" loadingLayer is quiting...."); }
            }
            
			//(new Function(asyncScripts)).apply(window); //- ?
			//appendScript(asyncScripts, ''); //- in case of functions defined?
			if(Hanjst.asyncScriptArr.length > 0){
				var tmpTimerAsync=window.setTimeout(function(){try{
						Hanjst.appendScript(Hanjst.asyncScriptArr.join("\n"), '');
						Hanjst.asyncScriptArr = []; //- re-init
					}
					catch(tmpErr){
						if(Hanjst.isDebug){ 
							console.log("Hanjst: found error with asyncScripts:"+JSON.stringify(tmpErr));
						}
					};
				}, parseInt(Math.random()*100)+200);
			}
            
        }
    };
	
	//- set a trigger to Hanjst
    if(false){
        if(window.document.body){
            document.addEventListener('load', _callRender); //- earlier fire than the one below
            if(isDebug){ console.log(logTag + " fire with document.onload "+(new Date())); }
        }
        else{
            _callRender();
        };
    }
    else{
        //- exec without delay, sync mode, immediately...
        _callRender();
    }
	
})(window); //- anonymous Hanjst main func end
//- ----------------- MAGIC COMPLETE -----------------
/*
 *** Philosophy:
 * God's return to God, Caesar's return to Caesar; 
 * the backend runs in background, the frontend is executed in foreground.
 * 上帝的归上帝, 凯撒的归凯撒; 后端的归后台, 前端的归前台。
 * 
 *** Pros:
 1) Runtime in client-side, reduce computing render in server-side;
 2) Language-independent, not-bound with backend scripts/languages;
 3) Totally-isolated between MVC, data transfer with JSON;
 4) Full-support template tags with built-in logic and customerized JavaScript functions;
 5) No more tags language to be learned, just JavaScript;
 ...
 *** History:
 * Nov 24, 2018, +include with scripts
 * Dec 02, 2018, +variables, +functions
 * Dec 04, 2018, +tpl2code string to array, +foreach
 * Dec 08, 2018, +else if, +embedded tpl in <>
 * Dec 16, 2018, +literal
 * Jan 01, 2019, +foreachelse, forelse, whileelse
 * Fri Jan  4 03:59:42 UTC 2019, +remedyMemoLine
 * Fri Jan 11 13:48:28 UTC 2019, +codes refine
 * Tue Jan 15 11:53:30 UTC 2019, remove html comments, imprvs with appendScript
 * Mon Feb 11 06:18:18 UTC 2019, +callRender
 * 13:54 Friday, April 19, 2019, + check for undefined $xxx 
 * 12:48 Saturday, April 27, 2019, + readable error reporting for template erros
 * 19:19 Sunday, May 19, 2019, + renderTemplateRecurse for deep-in include files.
 * 18:44 Friday, May 31, 2019, + allow limited support for variables in xxxelse scope, bugfix for includeScript.
 * 07:58 6/2/2019, + imprvs with _appendScript to appendScript for async call.
 * 16:31 Wednesday, June 5, 2019, + imprvs with parentNode=BODY
 * 19:18 Monday, June 10, 2019, + bugfix for asyncScripts.
 * 22:29 Thursday, June 13, 2019, + loadingLayer. "<div id="Hanjstloading" style="width: 100%; height: 100%; z-index: 99;"> Xxxx Loading... 加载中... </div>" .
 * 21:36 Thursday, June 20, 2019, + warning for MSIE browsers.
 * Sun Nov 24 11:50:36 CST 2019, + undefined exceptions.
 * 10:12 Monday, December 2, 2019, + time_stamp.
 * 10:34 Friday, April 10, 2020, + func showImageAsync
 *** !!!WARNING!!! PLEASE DO NOT COPY & PASTE PIECES OF THESE CODES!
 */