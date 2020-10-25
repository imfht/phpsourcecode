$(function(){
    // 左菜单
    $('ul.nav-sidebar').find('a').click(function(){
        $('ul.nav-sidebar').find('.active').removeClass("active");
        $(this).parents("li").addClass("active");
    });
    // 选择账单记录- 模板
    $('.selected').click(function(){
        var text = $('#fincset_tpl_text').val();
        text += $(this).text();
        text += "\r\n";        
        $('#fincset_tpl_text').val(text);
        //var tab = $('#fastfincset').tab();tab.show();// tab 手动切换
        location.href = '/conero/finance/fincset/fast.html#fincset_tpl_text';
    });
    // 快速财务 -----------------------------------------------------------------------------> THE BEGIN
    $('.fincset_add_btn').click(function(){
        var dtpl = $(this).text();
        dtpl = dtpl.replace('日期',(new Date()).sysdate('y-m-d'));
        var txt = $('#fincset_tpl_text').val();
        txt += dtpl+",\r\n";
        $('#fincset_tpl_text').val(txt);
    });
        //* 快速财务账单增加
    $('#fincset_add_btn').click(function(){
        var aEl = $('.fincset_add_btn');
        var dtpl = $(aEl[0]).text();
        dtpl = dtpl.replace('日期',(new Date()).sysdate('y-m-d'));
        var txt = $('#fincset_tpl_text').val();
        txt += dtpl+",\r\n";
        $('#fincset_tpl_text').val(txt);
    });
        //* 快速财务账单减去
    $('#fincset_del_btn').click(function(){
        var text = $('#fincset_tpl_text').val();
        text = text.trim();
        if(Cro.empty(text)){
            $('#fincset_tpl_text').focus();return;
        }
        var arr = text.split(',');
        if(arr[arr.length-1] == '') arr.pop();
        arr.pop();
        text = arr.join(",").trim();
        if(text) text += ",\r\n";
        $('#fincset_tpl_text').val(text);
    });
    // 快速财务 -----------------------------------------------------------------------------> THE END
});
var Cro = new Conero();