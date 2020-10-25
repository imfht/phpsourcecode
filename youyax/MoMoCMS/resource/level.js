function Move_level(id, w, h, con)
 {
    var obj = document.getElementById(id);
    obj.style.width = w + "px";
    obj.style.height = h + "px";
    obj.style.overflow = "hidden";
    obj.onmouseover = function()
    {
        level_stop();

    }
    obj.onmouseout = function()
    {
        level_start();

    }
    obj.innerHTML = '' + 
    '<table width="' + w + '"  height="' + h + '"  border="0" cellspacing="0" cellpadding="0" id="scrollimg">' + 
    '<tr>' + 
    '	<td id="simg1">' + 
    '		<TABLE width="' + w + '"  height="' + h + '"  border="0" cellspacing="0" cellpadding="0">' + 
    '		<tr>' + 
    '		<td>' + con + '</td>' + 
    '		</tr>' + 
    '		</TABLE>' + 
    '	</td>' + 
    '	<td id="simg2"></td>' + 
    '</tr>' + 
    '</table>';
    simg2.innerHTML = simg1.innerHTML
    tm = setInterval('level_scroll()', 20)

}

var tm = null
function level_scroll() {
    if (scrollimg.parentNode.scrollLeft != (scrollimg.clientWidth / 2))
    scrollimg.parentNode.scrollLeft++;
    else
    scrollimg.parentNode.scrollLeft = 0

}
function level_stop()
 {
    clearInterval(tm)

}
function level_start()
 {
    tm = setInterval('level_scroll()', 20)

}