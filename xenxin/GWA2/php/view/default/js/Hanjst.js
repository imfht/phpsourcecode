//-
//- Hanjst
//- 汉吉斯特
/* 
 * Han JavaScript Template Engine
 * --- The template semantic, syntax and its engine ---
 * 基于JavaScript通用HTML页面模板引擎
 * --- 模板语义, 语法及解析引擎 ---
 *
 * @Born with GWA2， General Web Application Architecture
 * @Xenxin@ufqi.com, Wadelau@hotmail.com
 * @Since July 07, 2016, refactor on Oct 10, 2018
 * @Ver 1.1
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
	"IncludeScriptTag": "Hanjst_INCLUDE_SCRIPT", //- inner usage
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
	var timeCostBgn = (new Date()).getTime();
	
	//- constants
	const parseTag = window.Hanjst.ParseTag; const unParseTag = '__NOT' + parseTag;
	const tplVarTag = window.Hanjst.TplVarTag; const jsonDataId = window.Hanjst.JsonDataId; 
	const logTag = window.Hanjst.LogTag+" "; const isDebug = window.Hanjst.IsDebug; 
	const includeScriptTag = window.Hanjst.IncludeScriptTag;
	const includeScriptTagBgn = includeScriptTag + '_BGN';
	const includeScriptTagEnd = includeScriptTag + '_END';
	
	//- handle server response in json, 
	//- parse it into global variables starting with this tplVarTag, ie, $ as the default.
	var pageJsonElement = document.getElementById(jsonDataId);
	var tplData = {}; // data holder
	if(pageJsonElement){
		var tplDataStr = pageJsonElement.innerText;
		try{
			tplData = JSON.parse(tplDataStr);
		}
		catch(e0939){ console.log(e0939);}
		if(!tplData['copyright_year']){ tplData['copyright_year'] = (new Date()).getFullYear(); }
		//- parse json keys as global variables
		//- variables starting with tplVarTag, i.e., $ as default
		for(var $k in tplData){
			//console.log("k:"+$k+" v:"+tplData[$k]);
			if($k != null && $k != ''){
				var $v = tplData[$k];
				$k = tplVarTag + $k;
				if(window){ window[$k] = $v; }
				else{ console.log('window undefined error. 201812011122.'); }
			}
		}
		//- hide raw data
		//pageJsonElement.style.height = '0px';
		pageJsonElement.style.visibility = 'hidden'; // hide json data element
		tplDataStr = null;
	}
	else{
        window.$copyright_year = (new Date()).getFullYear();
		console.log(logTag+'pageJsonElement:['+jsonDataId+'] has error. 201812010927'); 
	}
	tplData = null;
	
	//- main function
	//- parse all tag blocks
	if(isDebug){ console.log(logTag+"aft parse copyright_year:"+$copyright_year); }
	var renderTemplate = function(window, document, tplHTML){
		
		//- tpl keywords and patterns
		var tplRe = /\{((for|if|while|else|switch|break|case|\$|\/|var|let)[^}]*)\}/gm;
		
		//- collect tpl content
		var match, tplRaw, tplObject;
		tplObject = document.body || document; 
		if(!tplHTML || tplHTML == ''){
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
        tplRaw = _remedyMemoLine(tplRaw);
		//tplRaw = tplRaw.replace(/[\n\r]/g, '');
		//tplRaw = tplRaw.replace(/<!--.*?-->/g, '');
		//console.log(tplRaw);
		
		var tplSegment = []; var lastpos = 0;
		var staticStr, ipos, matchStr, exprStr;	
		
		//- prepare-1
		//- parse include parts
		var includeRe = /\{include [file|content]*="([^\}]*?)"\}/gm;
		var segi, segStr, tplRawNew, tmpCont;
		lastpos = 0; tplRawNew = tplRaw;
		while(match = includeRe.exec(tplRaw)){
			//console.log(match);
			matchStr = match[0]; exprStr = match[1];
			tmpCont = (new Function("return "+exprStr+";")).apply();
            tmpCont = _remedyMemoLine(tmpCont);
			//tmpCont = tmpCont.replace(/[\n\r]/g, '');
			//tmpCont = tmpCont.replace(/<!--.*?-->/g, '');
			if(tmpCont.indexOf('<script') > -1){
				tmpCont = includeScriptTagBgn + tmpCont + includeScriptTagEnd;
			}
			tplRawNew = tplRawNew.replace(matchStr, tmpCont);
		}
		tplRaw = tplRawNew;
		//console.log(tplRaw);
		
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
		var asyncScriptArr = []; var isAsync = false; var srcPos = -1; var endTagPos = -1;
		for(var $prei in tplSegmentPre){
			tplRawNew = tplSegmentPre[$prei];
			if(tplRawNew.indexOf(unParseTag) > -1){ // literal scripts
				tplSegment.push(tplRawNew);
			}
			else{
				lastpos = 0; srcPos = -1; endTagPos = -1;
				while(match = scriptRe.exec(tplRawNew)){
					//console.log(match);
					ipos = match.index;
					staticStr = tplRawNew.substring(lastpos, ipos);
					if(staticStr.indexOf(includeScriptTagBgn) > -1){
						isIncludeScript = true;
						staticStr = staticStr.replace(includeScriptTagBgn, '');
					}
					matchStr = match[0];
					exprStr = match[1];
					srcPos = matchStr.indexOf(' src='); 
                    endTagPos = staticStr.indexOf(includeScriptTagEnd);
					if(matchStr.indexOf(' async') > -1){ isAsync = true; }else{ isAsync = false; }
					if(isIncludeScript){
						if(isDebug){ console.log(logTag+"includeScript:"+exprStr+" matchStr:"+matchStr); }
						_appendScript(exprStr, matchStr);
					}
					if(endTagPos > -1){
						isIncludeScript = false;
						staticStr = staticStr.replace(includeScriptTagEnd, '');
					}
					tplSegment.push(parseTag + staticStr);
					//- exclude src= in parent tpl
                    if(isIncludeScript || srcPos < 0){
                        if(exprStr != null && exprStr != ''){
                            if(exprStr.indexOf('document.write') > -1){
                                /* should skip */
                                if(isDebug){ console.log(logTag+"found 'document.write' and skip..."); }
                            }
                            else{
                                if(isAsync){
                                    //asyncScriptArr.push(exprStr); // @todo
                                    tplSegment.push('var tmpTimerI=window.setTimeout(function(){try{'+exprStr
                                        +'}catch(tmpErr){if('+isDebug+'){console.log("'+logTag
                                        +' found error with embed scripts:\"+JSON.stringify(tmpErr)+\"")}};}, '
                                        + 'parseInt(Math.random()*2000+500));'); //- why two seconds?
                                }
                                else{
                                    tplSegment.push(exprStr);
                                }
                            }
                        }
                    }
					lastpos = ipos + matchStr.length;
					hasScript = true;
				}
				if(hasScript){
					staticStr = tplRawNew.substring(lastpos); // remainings
					if(staticStr.indexOf(includeScriptTagEnd) > -1){
						isIncludeScript = false;
						staticStr = staticStr.replace(includeScriptTagEnd, '');
					}
					tplSegment.push(parseTag + staticStr);
				}
				else{
					if(isDebug){ console.log(logTag + "no scripts:"+tplRawNew); }
					tplSegment.push(parseTag + tplRawNew);
				}
			}
		}
		//console.log(tplSegment);
		
		//- main body of the main function
		//- loop over tplSegment for tags interpret
		var tpl2code, tpl2codeArr; segStr = ''; segi = 0;
		tpl2codeArr = []; tpl2codeArr.push("var tpl2js = []; var blockLoopCount = 0;");
		var blockBeginRe, tmpmatch, needSemiComma, containsDot, containsBracket;
		var tmpArr, containsEqual, tmpIfPos, hasLoopElse, loopElseStr;
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
							loopElseStr = staticStr;
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
							if(exprStr.indexOf('(') > -1){ containsBracket = true;} 
							if(exprStr.indexOf('.') > -1){ containsDot = true; }
							if(exprStr.indexOf('=') > -1){ containsEqual = true; }
							if(containsBracket && !containsDot && !containsEqual){
								//- private, $aFunc($a)
								exprStr = exprStr.substring(1);
								tpl2codeArr.push("\ttpl2js.push("+exprStr+");");
							}
							else if(containsDot && !containsEqual){
								//- built-in, $a.substring(0, 5)
								tpl2codeArr.push("\ttpl2js.push("+exprStr+");");
							}
							else{
								//- variables operations, $a++
								tpl2codeArr.push(exprStr + ';');
							}
						}
						else{
							//- variables access, $a
							tpl2codeArr.push("\ttpl2js.push("+exprStr+");");
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
									hasLoopElse = true;
									exprStr = '\tblockLoopCount += 1';
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
								if(isDebug){ console.log("not blockBegin? "+exprStr+""); }
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
								hasLoopElse = false;
								exprStr += '\n\tblockLoopCount = 0;';
							}
						}
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
						tpl2codeArr.push("\n" + exprStr);
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
		
		//- append returns to tpl2code
		tpl2codeArr.push("return tpl2js.join('');");
		tpl2code = tpl2codeArr.join("\n"); tpl2codeArr = null;
		tpl2code = "try{ " + tpl2code + "\n}\ncatch(e1635){ console.log(\""
			+ logTag + "code exec failed.\"); console.log(e1635); "
			+ " return ''+JSON.stringify(e1635); }\n";
		
		//- merge data and compile
		var tplParse = '';		
		if(isDebug){ console.log(logTag + "tpl2code:"+tpl2code); }
		//tplParse = (function(){ return (new Function(tpl2code).apply(window)); }).apply();
		tplParse = (new Function(tpl2code)).apply(window);
		if(isDebug){ console.log("tplParse:"+tplParse); }
		tplObject.innerHTML = tplParse;
		//- release objects		
		tpl2code = null; tpl2codeArr = null; 
		tplRaw = null; tplParse = null; tplSegment = null;
		
		//- oncomplete? @todo
        if(true){
            if(isDebug){
                asyncScriptArr.push("console.log((new Date())+' "+logTag+" async scripts exec....');");
            }
            var asyncScripts = asyncScriptArr.join("\n");
            if(isDebug){
                console.log(logTag+"asyncScripts: "+asyncScripts);
            }
            try{
				//- exec async scripts... @todo 
		        (new Function(asyncScripts)).apply(window);
            }
            catch(e190115){};
        }
		
	};
	
	//- inner methods
	//- append embedded scripts into current runtime
	var _appendScript = function(myCode, myElement) {
		var s = document.createElement('script');
		s.type = 'text/javascript';
		var code = myCode;
        if((code == null || code == '') 
			&& myElement != null && myElement != ''){
            //- in case of, <script src=""/></script>
	        var srcRe = /<script .*? src="([^"]*)"[^>]*>/gm;
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
                mySrc = '';
            }
            s.src = mySrc; 
        }
        if(true){
            try{
                if(code != null && code != ''){
                    code = "try{"+code+"}catch(tmpErr){ if("+isDebug+"){console.log(\""+logTag
                        +"append embed failed 201901151438:\"+JSON.stringify(tmpErr)); } }";
                }
                s.appendChild(document.createTextNode(code));
                document.body.appendChild(s);
            }
            catch(e){
                s.text = code;
                document.body.appendChild(s);
            }
        }
		if(isDebug){
			console.log('_appendScript: '+myCode+'/'+myElement+' has been appended.');
		}
	};
	
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
	
	//- invoke the magic Hanjst
    var _callRender = function(){ //- wait longer?
        renderTemplate(window, document, null);
        if(isDebug){
            console.log(logTag + "parse time \
                cost: "+(((new Date()).getTime() - timeCostBgn)/1000) + "s");
        }
    };
    if(window.document.body){
        document.body.onload = _callRender; //- earlier fire than the one below
        if(isDebug){ console.log(logTag + " fire with document.onload "+(new Date())); }
    }
    else{
        window.onload = _callRender;
    };
	
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
 *** !!!WARNING!!! PLEASE DO NOT COPY & PASTE PIECES OF THESE CODES!
 */
