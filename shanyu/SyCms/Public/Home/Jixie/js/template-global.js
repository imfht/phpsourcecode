function loadImage(elem, width, height) {
    $(elem).css({ width: "auto", height: "auto" });

    var smallWidth = $(elem).width();
    var smallHeight = $(elem).height();
    var iwidth = width;
    var iheight = height; 
    if (smallWidth > 0 && smallHeight > 0) {

        if (smallWidth / smallHeight >= iwidth / iheight) {
            if (smallWidth > iwidth) {
                $(elem).width(iwidth).height((smallHeight * iwidth) / smallWidth).css("padding", Math.floor(Math.abs((iheight - $(elem).height()) / 2)) + "px 0px");
            } else {
                $(elem).width(smallWidth).height(smallHeight).css("padding", Math.floor(Math.abs((iheight - $(elem).height()) / 2)) + "px " + Math.floor(Math.abs((iwidth - $(elem).width()) / 2)) + "px");
            }
        }
        else {
            if (smallHeight > iheight) {
                $(elem).width((smallWidth * iheight) / smallHeight).height(iheight).css("padding", "0px " + Math.floor(Math.abs((iwidth - $(elem).width()) / 2)) + "px");
            } else {
                $(elem).width(smallWidth).height(smallHeight).css("padding", Math.floor(Math.abs((iheight - $(elem).height()) / 2)) + "px " + Math.floor(Math.abs((iwidth - $(elem).width()) / 2)) + "px");
            }
        }
    }
}

function loadImageError(elem, selector) {
    selector == undefined ? $(elem).remove() : $(elem).parents(selector + ":first").remove();
}

function addFavorite() {
    try {
        window.external.addFavorite(window.location.href, document.title);
    } catch (e) {
        try {
            window.sidebar.addPanel(document.title, window.location.href, "");
        } catch (ex) {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
} 