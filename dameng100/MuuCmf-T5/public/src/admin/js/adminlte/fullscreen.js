$(function () {

    $(".main-header").on('click','#full-screen',function(){
        var fullscreenElement = document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement;
        //var fullscreenEnabled = document.fullscreenEnabled || document.mozFullScreenEnabled || document.webkitFullscreenEnabled;
        
        
        if (fullscreenElement) {
            //console.log('退出全屏');
            cancelFullScreen();
        
        } else {
            //console.log('进入全屏');
            fullScreen();
        }
    });

    //进入全屏
    function fullScreen(){

        var elem = document.body;  
        if (elem.webkitRequestFullScreen) {  
            elem.webkitRequestFullScreen();  
        } else if (elem.mozRequestFullScreen) {  
            elem.mozRequestFullScreen();  
        } else if (elem.requestFullScreen) {  
            elem.requestFullscreen();  
        } else {  
            notice.notice_show("浏览器不支持全屏API或已被禁用", null, null, null, true, true);  
        }
    }
    //退出全屏
    function cancelFullScreen(){
        var elem = document;  
        if (elem.webkitCancelFullScreen) {  
            elem.webkitCancelFullScreen();  
        } else if (elem.mozCancelFullScreen) {  
            elem.mozCancelFullScreen();  
        } else if (elem.cancelFullScreen) {  
            elem.cancelFullScreen();  
        } else if (elem.exitFullscreen) {  
            elem.exitFullscreen();  
        } else {  
            notice.notice_show("浏览器不支持全屏API或已被禁用", null, null, null, true, true);  
        }  
    }

    function upStat(ele) {

        if(ele.data('toggle')=='fullscreen'){
            
            ele.attr('data-toggle','cancel_fullscreen');
            
        }else{
            ele.attr('data-toggle','fullscreen');
        }
    }

});