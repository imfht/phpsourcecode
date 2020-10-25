/**
 * 判断云端是否有新的更新
 * @param  {[type]} ){var version       [description]
 * @return {[type]}        [description]
 */
;$(function(){
    var can_update = $('[data-toggle="can_update"]').val();
    var version = $('[data-toggle="version"]').val();
    if(can_update==1){
        $.get("http://www.muucmf.cn/muucmf/sysupdate/index/enable_version/"+version, function(result){
            if(result.code){
                new $.zui.Messager('有新的版本更新！', {
                    type: 'danger',
                    icon: 'bell', // 定义消息图标
                    placement: 'bottom',
                    time: 10000,
                    actions: [{
                        name: 'update',
                        icon: 'chevron-right',
                        text: '去更新',
                        action: function() {  // 点击该操作按钮的回调函数
                            window.location.href="/admin/update/index.html"; 
                            return false; // 通过返回 false 来阻止消息被点击时隐藏
                        }
                    }]
                }).show();
            }
        });
    }
});

/**
 * 反馈数据，用户统计安装量
 * @param  {Object} ){                 var data [description]
 * @return {[type]}     [description]
 */
$(function(){
    var data = {
        host:$('[data-toggle="server_name"]').val(),
        ip:$('[data-toggle="server_ip"]').val(),
        version:$('[data-toggle="version"]').val(),
        v:'T5'
    };

    var url = 'http://www.muucmf.cn/muucmf/index/feedback';
    $.post(url,data,function(result){});
});