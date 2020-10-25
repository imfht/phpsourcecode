$(function(){
    // 删除确认
    $('.delinker').click(function(){
        var url = $(this).attr("href");
        Cro.confirm('您确定要删除数据吗？',function(){
            location.href = url;
        });
        return false;
    });
    // 计划项显示
    $('.plan_el_lnk').click(function(){
        var dataid = $(this).parents('tr').attr('dataid');
        // alert(dataid);
        $.post(Cro._baseurl + 'center/index/ajax/lfplan.html',{item:'get_plan_els',dataid:dataid},function(data){
            data = '<div class="panel panel-danger"><div class="panel-body">'+data+'</div></div>';
            $('#el_display_dance').html(data);
        });
    });
});
var LP = Cro.extends(function(th){
    // 阅读内容
    this.readElContent = function(dataid,dom)
    {
        dom = $(dom);
        var title = dom.parents('ol').find('li > span[dataid="name"]').text();
        // th.log(dom,title,dom.parents('li').find('span[dataid="name"]'));
        // th.log(dom.parents('ol'),dom);     // js Onclick 时间时传入 dom 有效， javascript:function 无效
        $.post(Cro._baseurl + 'center/index/ajax/lfplan.html',{item:'get_plan_eltext',dataid:dataid},function(data){
            data = '<div class="panel panel-default"><div class="panel-heading">'+title+'</div><div class="panel-body">'+data+'</div></div>';
            $('#el_log_dance').html(data);
        });
    }
});