$(function() {
    if (!$('.builder')) {
        return false;
    }

    //给数组增加查找指定的元素索引方法
    Array.prototype.indexOf = function(val) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] == val) return i;
        }
        return -1;
    };

    //给数组增加删除方法
    Array.prototype.remove = function(val) {
        var index = this.indexOf(val);
        if (index > -1) {
            this.splice(index, 1);
        }
    };



    //全选/反选/单选的实现
    $(".builder .check-all").click(function() {
        $(".ids").prop("checked", this.checked);
    });

    $(".builder .ids").click(function() {
        var option = $(".ids");
        option.each(function() {
            if (!this.checked) {
                $(".check-all").prop("checked", false);
                return false;
            } else {
                $(".check-all").prop("checked", true);
            }
        });
    });



    //搜索功能
    $('body').on('click', '.builder #search', function() {
        var url = $(this).attr('url');
        var query = $('.builder .search-form').find('input').serialize();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
        query = query.replace(/(^&)|(\+)/g, '');
        if (url.indexOf('?') > 0) {
            url += '&' + query;
        } else {
            url += '?' + query;
        }
        window.location.href = url;
    });

     //回车搜索
    $(".builder .search-input").keyup(function(e) {
        if (e.keyCode === 13) {
            $("#search").click();
            return false;
        }
    });

});