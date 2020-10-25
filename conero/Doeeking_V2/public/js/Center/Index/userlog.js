var app = Cro.extends(function(th){
    this.pageInit = function(){
        // 显示选择框
        $('#show_sels_link').click(function(){
            $('.logno_ckbox').toggleClass('hidden');
            var ckbox = $('.logno_ckbox');
            var cls = ckbox.attr('class');
            if(cls.indexOf('hidden') == -1){
                $('#del_sels_btn').removeClass('hidden');
            }
            else{
                $('#del_sels_btn').addClass('hidden');
            }
        });
        // 删除按钮
        $('#del_sels_btn').click(function(){
            var ckbox = $('.logno_ckbox');
            var ckboxList = [];
            for(var k=0; k<ckbox.length; k++){
                if(ckbox[k].checked == true) ckboxList.push($(ckbox[k]).val());
            }
            if(ckboxList.length == 0){
                th.modal_alert('您没有选择任何记录！');
                return;
            }
            var url = '/conero/center/index/save/userlog?uid='+th.bsjson({'mode':'D','list':ckboxList});
            location.href = url;
        });
        // 内容搜索跳转
        $('#search_lnk').click(function () {
            var search = $('#search_ipter').val();
            var data = {search:search};
            th.get('/conero/center/index/edit/userlog.html',data);
        });
    }
});
$(function(){
    app.pageInit();
});