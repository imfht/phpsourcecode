(function ($) {
    $.fn.imgZoom = function () {
        var mask =
            "<div style = 'position: fixed;width: 100%;z-index: 5555;height: 100%;top: 0;left: 0;background: rgba(0,0,0,0.5);display:none;' id='imgZoomMask'><img class='loadimg' style='position:absolute;top:50%;left:50%;' src='fileadmin/templates/v2/public/images/loading.gif'></img></div>";
        $("html").append(mask);
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();
        $(window).resize(function () {
            windowWidth = $(window).width();
            windowHeight = $(window).height();
        });
        var oldwidth = 0;
        var oldheight = 0;
        this.each(function () {
            $(this).click(function () {
                $("#imgZoomMask").show();
                // var src = $(this).data("src") == undefined ? $(this).attr("src") : $(this).data("src");
                var src = $(this).parent().next('#imgsrc').attr('data-src');
                var img = new Image();
                img.src = src;
                oldwidth = img.width;
                oldheight = img.height;
                img.onload = function() {
                    var dom = "";
                    var displayWidth = 0;
                    var displayHeight = 0;
                    var style = "";
                    if (img.width > img.height) {
                        displayWidth = windowWidth / 2;
                        displayHeight = img.height * displayWidth / img.width;
                        style = "z-index:6666;position:absolute;top:" +
                            windowHeight / 2 +
                            "px;margin-top:-" +
                            displayHeight / 2 +
                            "px;left:" +
                            windowWidth / 2 +
                            "px;margin-left:-" +
                            displayWidth / 2 +
                            "px;cursor:pointer;";
                        dom = "<img draggable='true' src = '" +
                            src +
                            "' width = '50%' style='" +
                            style +
                            "' id='imgZoomImg'>";
                    } else {
                            var width = img.width*windowHeight/img.height;
                            displayHeight = windowHeight / 2;
                            displayWidth = displayHeight * img.width / img.height;
                            style = "z-index:6666;position:absolute;top:"+($(window).height()-windowHeight)/2+"px;left:" +
                                (windowWidth - width) / 2 +
                                "px;cursor:pointer;height:"+$(window).height()+"px";
                            dom = "<img draggable='true' src = '" +
                                src +
                                "' height = "+$(window).height()+"px' style=' " +
                                style +
                                "' id='imgZoomImg'>";
                    }
                    $('.loadimg').remove();
                    $("body").append(dom);
                    $("#imgZoomImg").dragging({
                        move: "both", //拖动方向，x y both
                        randomPosition: false //初始位置是否随机
                    });
                    
                }
            });
        });
        $(document).on("click", "#imgZoomMask", function () {
            $("#imgZoomMask").hide();
            $("#imgZoomImg").fadeOut().remove();
            $("body").removeAttr('style');
        });
        $(document).on("mousewheel",function(e,d) {
            //d 1 上 -1 下
            if (d === 1) {
                var width = $("#imgZoomImg").width();
                var height = $("#imgZoomImg").height();
                /*$("#imgZoomImg").css({ "width": width * 1.2, "height": height * 1.2 });*/

                displayHeight = windowHeight / 2;
                displayWidth = displayHeight * $("#imgZoomImg").width() / $("#imgZoomImg").height();
                var style = "";
                 var newheight = height * 1.2;
                style = "z-index:6666;position:absolute;top:"+($(window).height()-newheight)/2+"px;left:" +
                (windowWidth - $("#imgZoomImg").width()* 1.2) / 2 +
                "px;cursor:pointer;width:"+
                width * 1.2+"px;height:"+height * 1.2+"px;";
                $("#imgZoomImg").attr('style',style);
            }
            if (d === -1) {
                var width = $("#imgZoomImg").width();
                var height = $("#imgZoomImg").height();
                // $("#imgZoomImg").css({ "width": width / 1.2, "height": height / 1.2 });
                displayHeight = windowHeight / 2;
                displayWidth = displayHeight * $("#imgZoomImg").width() / $("#imgZoomImg").height();
                var newheight = height / 1.2;
                style = "z-index:6666;position:absolute;top:"+($(window).height()-newheight)/2+"px;left:" +
                (windowWidth - $("#imgZoomImg").width()/ 1.2) / 2 +
                "px;cursor:pointer;width:"+
                width / 1.2+"px;height:"+height / 1.2;+"px;";
                $("#imgZoomImg").attr('style',style);
            }
        });
    }
})(window.jQuery)