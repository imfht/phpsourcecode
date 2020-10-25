/*
提醒首页 所属模块&按钮
 */

var Nt = {
    updateQueryParam: function(param){
        var query = Ibos.app.g('queryParam');
        query[param.type] = param.value;
        Ibos.app.s('queryParam', query);
    },
    op : {
        /**
         * 移除提醒
         * @method deleteNotify
         * @param  {Object} param 传入JSON格式数据
         * @return {Object}       返回deffered对象
         */
        deleteNotify : function(param){
            var url = Ibos.app.url('message/notify/delete');
            return $.get(url, param, $.noop, "json");
        },
        /**
         * 标记阅读
         * @method markRead
         * @param  {Object} param 传入JSON格式数据
         * @return {Object}       返回deffered对象
         */
        markRead : function(param){
            var url = Ibos.app.url('message/notify/setIsRead');
            return $.get(url, param, $.noop, "json");
        },
        /**
         * 标记所有阅读
         * @method markAllRead
         * @param  {Object} param 传入JSON格式数据
         * @return {Object}       返回deffered对象
         */
        markAllRead : function(param){
            var url = Ibos.app.url('message/notify/setAllRead');
            return $.get(url, param, $.noop, "json");
        }
    },
    /**
     * 移除提醒
     * @method remove
     * @param  {String} ids 传入要删除的IDs
     */
    remove: function(ids){
        var param = {id: ids};
        Nt.op.deleteNotify(param).done(function(res) {
            if (res.IsSuccess) {
                $.each(ids.split(','), function(n, i) {
                    $('#remind_' + i).fadeOut(function() {
                        $('#remind_' + i).remove();
                        window.location.reload();
                    });
                });
                Ui.tip(Ibos.l('DELETE_SUCCESS'));
            } else {
                Ui.tip(Ibos.l('DELETE_FAILED'), 'danger');
            }
        });
    },
    /**
     * 标记已读
     * @method markRead
     * @param  {String} ids 传入要删除的IDs
     */
    markRead: function(ids){
        var param = {id: ids};
        Nt.op.markRead(param).done(function(res) {
            if (res.IsSuccess) {
                $.each(ids.split(','), function(n, i) {
                    $('#remind_' + i).fadeOut(function() {
                        $('#remind_' + i).remove();
                        window.location.reload();
                    });
                });
                Ui.tip(Ibos.l('OPERATION_SUCCESS'));
            } else {
                Ui.tip(Ibos.l('OPERATION_FAILED'), 'danger');
            }
        });
    }
};


// 请求参数
$(function(){
    Ibos.evt.add({
        // 展开模块列表
        "toggleModuleList": function(){
            $("#module_list_wrap").slideToggle();
            $(this).toggleClass('module-query--toggle__down');
        },
        toggleQueryParam: function(param, elem){
            // var $curItem = $(elem).closest("li"),
            $searchInput = $("#notify_manage_search");
            // $curItem.addClass("active").siblings().removeClass("active");
            $searchInput.val('');
            Nt.updateQueryParam(param);
            window.location.href = Ibos.app.url('message/notify/index', Ibos.app.g('queryParam'));
        },
        // 标记全部已读
        "markNoticeRead": function(){
            var ids = U.getCheckedValue("remind");
            if (ids) {
                Nt.markRead(ids);
                Ibosapp.dropnotify.getCount();
            } else {
                Ui.tip(Ibos.l('SELECT_AT_LEAST_ONE_ITEM'), 'warning');
            }
        },
        "markAllRead": function(param, elem){
            Nt.op.markAllRead(null).done(function(res){
                if(res.IsSuccess){
                    $('span.bubble').hide();
                    $(elem).parent().hide();
                    Ibosapp.dropnotify.getCount();
                    Ui.tip(Ibos.l('OPERATION_SUCCESS'), 'success');
                } else {
                    Ui.tip(Ibos.l('OPERATION_FAILED'), 'danger');
                }
            });
        },
        // 批量删除
        "removeNotices": function() {
            var ids = U.getCheckedValue("remind");
            if (ids) {
                Ui.confirm(Ibos.l("MSG.NOTIFY_REMOVE_CONFIRM"), function(){
                    Nt.remove(ids);
                });
            } else {
                Ui.tip(Ibos.l('SELECT_AT_LEAST_ONE_ITEM'), 'warning');
            }
        },
        "removeNotice": function(param) {
            Ui.confirm(Ibos.l("MSG.NOTIFY_REMOVE_CONFIRM"), function(){
                Nt.remove(param.id);
            });
        }
    });

    // 搜索框回车
    $('#notify_manage_search').keydown(function(e) {
        if (e.keyCode == 13) {
            param = {"type": "search", "value": $(this).val()};
            Nt.updateQueryParam(param);
            window.location.href = Ibos.app.url('message/notify/index', Ibos.app.g('queryParam'));
        }
    });
});

