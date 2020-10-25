// JavaScript Document
var isIe = (document.all) ? true: false;
//设置select的可见状态
function setSelectState(state)
 {
    var objl = document.getElementsByTagName('select');
    for (var i = 0; i < objl.length; i++)
    {
        objl[i].style.visibility = state;

    }

}
function mousePosition(ev)
 {
    if (ev.pageX || ev.pageY)
    {
        return {
            x: ev.pageX,
            y: ev.pageY
        };

    }
    return {
        x: ev.clientX + document.body.scrollLeft - document.body.clientLeft,
        y: ev.clientY + document.body.scrollTop - document.body.clientTop

    };

}
//弹出方法
function showMessageBox(wTitle, content, pos, wWidth)
 {
    closeWindow();
    var bWidth = parseInt(window.screen.width);
    var bHeight = parseInt(window.screen.height);
    if (isIe) {
        setSelectState('hidden');
    }
    var back = document.createElement("div");
    back.id = "back";
    var styleStr = "top:0px;left:0px;position:absolute;background:#666;width:" + bWidth + "px;height:" + bHeight + "px;";
    styleStr += (isIe) ? "filter:alpha(opacity=0);": "opacity:0;";
    back.style.cssText = styleStr;
    document.body.appendChild(back);
    showBackground(back, 50);
    var mesW = document.createElement("div");
    mesW.id = "mesWindow";
    mesW.className = "mesWindow";
    mesW.innerHTML = "<div class='mesWindowTop'><table width='100%' height='100%'><tr><td>" + wTitle + "</td><td style='width:1px;'><a alt='点击关闭' class='closebt' onclick='closeWindow();'></a></td></tr></table></div><div class='mesWindowContent' id='mesWindowContent'>" + content + "</div><div class='mesWindowBottom'></div>";
		
    var v_top = document.documentElement.scrollTop+120;

    styleStr = "top:" + (v_top) + "px;left:" + (document.body.clientWidth / 2 - mesW.clientWidth / 2) + "px;position:absolute;width:600px;margin-left:-300px;left:50%;z-index:9999;";
    mesW.style.cssText = styleStr;
    document.body.appendChild(mesW);
}
//让背景渐渐变暗
function showBackground(obj, endInt)
 {
    if (isIe)
    {
        obj.filters.alpha.opacity += 5;
        if (obj.filters.alpha.opacity < endInt)
        {
            setTimeout(function() {
                showBackground(obj, endInt)
            },
            5);

        }

    } else {
        var al = parseFloat(obj.style.opacity);
        al += 0.05;
        obj.style.opacity = al;
        if (al < (endInt / 100))
        {
            setTimeout(function() {
                showBackground(obj, endInt)
            },
            5);
        }

    }

}
//关闭窗口
function closeWindow()
 {
    if (document.getElementById('back') != null)
    {
        document.getElementById('back').parentNode.removeChild(document.getElementById('back'));

    }
    if (document.getElementById('mesWindow') != null)
    {
        document.getElementById('mesWindow').parentNode.removeChild(document.getElementById('mesWindow'));

    }
    if (isIe) {
        setSelectState('');
    }

}
//弹出

function createField()
{
    var el=document.createElement('input');
	el.setAttribute('name','fields[]');
	el.setAttribute('type','text');
	el.setAttribute('class','txttc');
	
	var el1=document.createElement('label');
	el1.innerHTML='属性名称：';
	document.getElementById('setfield').appendChild(el1);
	document.getElementById('setfield').appendChild(el);

}
function createTab()
 {
	var el=document.createElement('input');
	el.setAttribute('name','tabs[]');
	el.setAttribute('type','text');
	el.setAttribute('class','txttc');
	
	var el1=document.createElement('label');
	el1.innerHTML='选项卡名称：';
	document.getElementById('settab').appendChild(el1);
	document.getElementById('settab').appendChild(el);

}