//- remedy by xenxin@ufqi, Mon Jun  1 15:43:29 HKT 2020
function show(obj,text,isdyn,tag){
    var objorig = obj;
    if(typeof obj == 'string'){
        obj = document.getElementById(obj);
    }
   
    var divid = "divDetail_201201282102";
    var rightbelow = 15;
    var div=document.createElement('div');

    var posobj = getOffset(obj);
    //var posobj = getScrollXY();
    //var left = obj.offsetLeft;   
    var left = posobj.left;   
    //var top = obj.offsetTop;   
    var top = posobj.top;   
    var w = obj.offsetWidth;   
    var h = obj.offsetHeight; 
    var winSize = getWindowSize();
    var winWidth = winSize.winW;
    var winHeight = winSize.winH;
    //window.alert("top:["+top+"] left:["+left+"] w:["+w+"] h:["+h+"] myW:["+winWidth+"] myH:["+winHeight+"]");
    if(left > winWidth/2){
        left = winWidth - left;
    }
    //window.alert("22:top:["+top+"] left:["+left+"] w:["+w+"] h:["+h+"] myW:["+winWidth+"] myH:["+winHeight+"]");

    div.setAttribute('id',divid);
    document.body.appendChild(div);

    var div2=document.getElementById(divid);

    if(!tag){
        div2.style.visibility="hidden";
    }
    else{
        var contdivW =  (winWidth * 0.65 - rightbelow * 2);
        if(left + contdivW > winWidth - rightbelow * 6){
            left = winWidth - contdivW - rightbelow * 6;
        }

        //window.alert(" 333 : top:["+top+"] left:["+left+"] w:["+w+"] h:["+h+"] myW:["+winWidth+"] myH:["+winHeight+"] contdivW:["+contdivW+"]");

        div2.style.visibility="visible";
        var surl = text;
        if(isdyn){
            text = 'Loading....';
        }
        div2.innerHTML = '<table style="width:100%;height:100%;"><tr><td width="100%" style="text-align:right"><a href="javascript:show(\''+objorig+'\',\'\',false);"><b>X</b></a></td></tr><tr style="height:100%;"><td width="100%"><div id="'+divid+'_inside" style="height:100%;">'+text+'</div></td></tr></table>';

        div2.style.position="absolute";
        div2.style.top= (top + h + rightbelow) +"px";
        div2.style.left= (left + rightbelow) +"px";
        div2.style.height= (winHeight * 0.5 - rightbelow)+"px"; // "300px";
        div2.style.padding="3px";
        //div2.style.color='#0000FF';

        div2.setAttribute("class","divStyle");

        div2.style.fontSize="14";
        div2.style.borderWidth="2";
        //var length=text.toString().length;
        //div2.style.width=length*14 +"px";
        div2.style.width= contdivW + "px" ; // "500px";
        div2.style.background='silver';

        if(isdyn){
            doActionEx(surl, divid+'_inside');
        }

    }

}

//- equivalent to getScrollXY
function getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        if(el.offsetLeft > el.scrollLeft){
            _x += el.offsetLeft - el.scrollLeft;
        }else{
            _x += - el.offsetLeft + el.scrollLeft;
        }
        if(el.offsetTop > el.scrollTop){
            _y += el.offsetTop - el.scrollTop;
        }else{
            _y += - el.offsetTop + el.scrollTop;
        }
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
    // e.g. var x = getOffset( document.getElementById('yourElId') ).left; 
}

//- equivlent to getOffset
function getScrollXY(){
        var scrOfX = 0, scrOfY = 0;
        if( typeof( window.pageYOffset ) == 'number' ) {
            //Netscape compliant
            scrOfY = window.pageYOffset;
            scrOfX = window.pageXOffset;
        } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
            //DOM compliant
            scrOfY = document.body.scrollTop;
            scrOfX = document.body.scrollLeft;
        } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
            //IE6 standards compliant mode
            scrOfY = document.documentElement.scrollTop;
            scrOfX = document.documentElement.scrollLeft;
        }
        return { top:scrOfX, left:scrOfY };
}

//- tell the window size, wadelau, Tue Mar  6 20:55:38 CST 2012
function getWindowSize() {
    var myWidth = 0, myHeight = 0;
    if( typeof( window.innerWidth ) == 'number' ) {
        //Non-IE
        myWidth = window.innerWidth;
        myHeight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        //IE 6+ in 'standards compliant mode'
        myWidth = document.documentElement.clientWidth;
        myHeight = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        //IE 4 compatible
        myWidth = document.body.clientWidth;
        myHeight = document.body.clientHeight;
    }
    return {winW:myWidth, winH:myHeight};
}
