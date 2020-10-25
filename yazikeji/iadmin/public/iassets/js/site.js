layui.config({base: '/iassets/js/'}).use(['element', 'layer', 'tab', 'jquery', 'ajax'], function() {

    var element = layui.element(), $ = layui.jquery, layer = layui.layer, tab = layui.tab({elem: '.admin-nav-card'}), ajax = layui.ajax()

    $('#logout').on('click', function () {
        var url = $(this).data('route')
        ajax.set({
            url: url,
            data: 'logout=1',
            confirmTitle: '确定要退出系统吗?',
            loadingMessage: '正在退出系统......',
            method: 'POST'
        })
        ajax.exec(function (data) {
            if (data.status == 1) {
                location.reload()
            }
        })
        return
    })
})