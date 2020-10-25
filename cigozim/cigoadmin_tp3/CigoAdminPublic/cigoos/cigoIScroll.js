var CigoIScroll = function (opt) {
    var cigoIScrollInstance = this;
    var pageIScroll;
    var currPage = 1;
    var onLoadingFlag = false;
    var noMoreDataFlag = false;

    cigoIScrollInstance.defaults = {
        'wrapper': '#wrapper',
        'datalist_container': '#datalist_container',
        'tip_container': '#iscroll-tip',
        'load_url': ''
    };
    cigoIScrollInstance.options = $.extend({}, cigoIScrollInstance.defaults, opt);

    //****************************************

    cigoIScrollInstance.showLoadMoreTip = function () {
        $(cigoIScrollInstance.options.tip_container).html(
            '<span class="tip">上划加载更多...</span>'
        );
    };

    cigoIScrollInstance.showNoMoreTip = function () {
        $(cigoIScrollInstance.options.tip_container).html(
            '<span class="tip">数据加载完毕!</span>'
        );
    };

    cigoIScrollInstance.showLoadIcon = function () {
        $(cigoIScrollInstance.options.tip_container).html(
            '<div class="iscroll-spinner">' +
            '   <div class="bounce1"></div>' +
            '   <div class="bounce2"></div>' +
            '   <div class="bounce3"></div>' +
            '</div>'
        );
    };

    cigoIScrollInstance.loadMoreData = function () {
        $.get(
            cigoIScrollInstance.options.load_url,
            {"p": ++currPage},
            function (data) {
                if ("" == data) {
                    cigoIScrollInstance.showNoMoreTip();
                    noMoreDataFlag = true;
                } else {
                    $(cigoIScrollInstance.options.datalist_container).append(data);
                    cigoIScrollInstance.showLoadMoreTip();
                }

                pageIScroll.refresh();
                onLoadingFlag = false;
            }
        );
    };

    cigoIScrollInstance.init = function () {
        cigoIScrollInstance.showLoadMoreTip();

        pageIScroll = new IScroll(cigoIScrollInstance.options.wrapper, {
            preventDefault: false,
            bounceEasing: 'elastic',
            bounceTime: 800
        });

        pageIScroll.on('scrollEnd', function () {
            if (noMoreDataFlag) {
                return;
            }

            if (onLoadingFlag) {
                return;
            }

            if (isNaN(pageIScroll.directionX / pageIScroll.directionY)) {
                return;
            }

            if (
                (pageIScroll.y < 0) &&
                (pageIScroll.y == pageIScroll.maxScrollY)
            ) {
                onLoadingFlag = true;
                cigoIScrollInstance.showLoadIcon();
                cigoIScrollInstance.loadMoreData();
            }
        });
    };
};
