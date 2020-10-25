//写Cookie
function setCookie(objName, objValue, objHours) {
    var str = objName + "=" + escape(objValue);
    if (objHours > 0) {//为0时不设定过期时间，浏览器关闭时cookie自动消失
        var date = new Date();
        var ms = objHours * 3600 * 1000;
        date.setTime(date.getTime() + ms);
        str += "; expires=" + date.toGMTString()+";path=/";
    }
    document.cookie = str;
}

//读Cookie
function getCookie(objName) {//获取指定名称的cookie的值
    var arrStr = document.cookie.split("; ");
    for (var i = 0; i < arrStr.length; i++) {
        var temp = arrStr[i].split("=");
        if (temp[0] == objName) return unescape(temp[1]);
    }
    return "";
}

function checkCookie(url){
    url=getCookie(url)
    if (url!=null && url!="") {
        alert('Welcome again '+url+'!')
    } else {
        url=prompt('Please enter your name:',"")
        if (url!=null && url!="") {
            setCookie('url', url, '365')
        }
    }
}

/**
 *日期比较 
 **/
function checkDateTime(beginValue, endValue) {
    var flag = 0;
    if (beginValue != null && beginValue != "" && endValue != null && endValue != "") 
    {
        var dateS = beginValue.split('-'); //日期是用'-'分隔,如果你日期用'/'分隔的话,你将这行和下行的'-'换成'/'即可
        var dateE = endValue.split('-');
        var beginDate = new Date(dateS[0], dateS[1], dateS[2]).getTime(); //如果日期格式不是年月日,需要把new Date的参数调整
        var endDate = new Date(dateE[0], dateE[1], dateE[2]).getTime();
        if (beginDate > endDate)
            {
                flag = 1;
            } else if (beginDate == endDate)
            {
                flag = 0;
            } 
            else
            {
                flag = -1;
            }
    }
    return flag;
}

$(function(){
    //解决栏目分类>一级分类不可选中样式问题
    $(".boxwrap .select-items ul li").each(function(){
        if($(this).css("cursor")=="default"){
            $(this).addClass("not_active");
            $(this).click(function(){
                $(document).unbind("click");
            })
        }
    })
})