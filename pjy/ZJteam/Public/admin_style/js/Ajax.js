//recvType 有三个值HTML、XML和JSON , 默认为HTML,传值时不区分大小写
//bool 是一个boolean类型的值，true表示异步传输方式，false表示同步传输方式，默认为true
function Ajax(recvType, bool) {
    var aj = new Object();
    aj.targetUrl = '';    //请求的地址 可以是PHP也可以XML文件
    aj.sendString = '';   //请求服务器传递的字符串  ？ & 格式 url 

    if(typeof(bool)=="undefined")
        aj.async=true;
    else
        aj.async=bool;

    aj.recvType=recvType ? recvType.toUpperCase() : 'HTML';//HTML XML JSON
    aj.resultHandle = null;
    aj.ff;
    aj.createXMLHttpRequest = function() {
        var request = false;
        if(window.XMLHttpRequest) {
            aj.ff=true;
            request = new XMLHttpRequest();
            if(request.overrideMimeType) {
                request.overrideMimeType('text/xml');
            }
        } else if(window.ActiveXObject) {
            aj.ff=false;
            var versions = ['Microsoft.XMLHTTP', 'MSXML.XMLHTTP', 'Microsoft.XMLHTTP', 'Msxml2.XMLHTTP.7.0', 'Msxml2.XMLHTTP.6.0', 'Msxml2.XMLHTTP.5.0', 'Msxml2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP'];
            for(var i=0; i<versions.length; i++) {
                try {
                    request = new ActiveXObject(versions[i]);
                    if(request) {
                        return request;
                    }
                } catch(e) {
                    request=false;
                }
            }
        }
        return request;
    }

    aj.XMLHttpRequest = aj.createXMLHttpRequest();

    aj.processHandle = function() {
        if(aj.XMLHttpRequest.readyState == 4) {
            aj.ff=false; //如果是IE7以上则不再执行一次调用
            if(aj.XMLHttpRequest.status == 200) {
                if(aj.recvType == 'HTML') {
                    aj.resultHandle(aj.XMLHttpRequest.responseText);
                }else if(aj.recvType == 'JSON'){ 
                    aj.resultHandle(eval('('+aj.XMLHttpRequest.responseText+')'));
                }else if(aj.recvType == 'XML') {
                    aj.resultHandle(aj.XMLHttpRequest.responseXML);
                }
            }
        }
    }

    aj.get = function(targetUrl, resultHandle) {
        aj.targetUrl = targetUrl;
        if(resultHandle!=null){
            aj.XMLHttpRequest.onreadystatechange = aj.processHandle;
            aj.resultHandle = resultHandle;
        }
        if(window.XMLHttpRequest) {
            aj.XMLHttpRequest.open('GET', aj.targetUrl, aj.async);
            aj.XMLHttpRequest.send(null);
            
        } else {
                aj.XMLHttpRequest.open("GET", targetUrl, aj.async);
                aj.XMLHttpRequest.send();
        }
        if(!aj.async && aj.ff)
            aj.processHandle();
    }

    aj.post = function(targetUrl, sendString, resultHandle) {
        aj.targetUrl = targetUrl;

        if(typeof(sendString)=="object"){
            var str="";
            for(var pro in sendString){
                str+=pro+"="+sendString[pro]+"&";
            }
            
            aj.sendString=str.substr(0, str.length-1);
        }else{
            aj.sendString = sendString;
        }

    
        
        if(resultHandle!=null){
            aj.XMLHttpRequest.onreadystatechange = aj.processHandle;
            aj.resultHandle = resultHandle;
        }
        aj.XMLHttpRequest.open('POST', targetUrl, aj.async);
        aj.XMLHttpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        aj.XMLHttpRequest.send(aj.sendString); 
        if(!aj.async && aj.ff)
            aj.processHandle();
    }
    return aj;
}
