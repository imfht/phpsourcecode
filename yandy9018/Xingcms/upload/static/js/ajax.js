/**
 * ajax方法
 * @author  张宏伟 <mail@zhwphp.com>
 */

//ajax浏览器兼容函数
function ajaxFunction()
{
    var xmlHttp;
    try
    {
        xmlHttp=new XMLHttpRequest();
    }
    catch (e)
    {
        try
        {
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
            try
            {
                xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e)
            {
                alert("您的浏览器不支持AJAX！");
                return false;
            }
        }
    }
    return xmlHttp;
}

//ajax 发送数据
function ajaxSend(method,url,data)
{
    var xmlHttp = ajaxFunction();
    url += '&ajax=1';
    xmlHttp.open(method,url,true);
    //如果为POST则需要在open后加下面这一句
    if (method == 'POST')
    {
        xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        xmlHttp.send(data);
    }
    else xmlHttp.send(null);
    return xmlHttp;
}

//ajax request
function ajaxRequest(method,url,data,callback)
{
    var xmlHttp = ajaxSend(method,url,data);
    xmlHttp.onreadystatechange = function()
    {
        if(xmlHttp.readyState == 4)
        {
            if(xmlHttp.status == 200)
            {
                if (callback != undefined) { eval(callback+'(xmlHttp);'); }
            }
            else if(xmlHttp.status == 404) alert("Requested URL is not found.");
            else if(xmlHttp.status == 403) alert("Access denied.");
        }
    }
}

//ajax post 方式
function ajaxPost(url,data,callback)
{
    ajaxRequest('POST',url,data,callback);
}

//ajax get 方式
function ajaxGet(url,callback)
{
    ajaxRequest('GET',url,'',callback);
}

//回调函数示例
//function callback(xmlHttp)
//{
//    var text = xmlHttp.responseText;
//}