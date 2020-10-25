$(function(){
    // 语言详情
    $('.about_lang_link').click(function(){
        var dom = $(this);
        var lang = dom.text();
        $.post('/conero/geek/lang/ajax.html',{lang:lang,'item':'index/lang'},function(data){
            var panel = dom.parents('div.panel');
            var xhtml =                 
                '    <div class="panel-heading">'+data.name+'</div>'                
                + '    <div class="panel-body">'
                + '     <div class="page-header text-right"><h4>'+data.name+(data.author? ' <small>'+data.author+'</small>':'')+(data.year? ' <em>'+data.year+'<em> ':'')+'</h4></div>'
                + (data.about? '<div class="well">'+data.about+'</div>':'')
                + '        '+data.content
                + '    </div>'
            ;            
            var footer = '';
            if('DEV' == Cro.getJsVar('admin')) footer = ' <a href="javascript:app.deleteLang(\'/conero/geek/lang/save.html?uid='+Cro.bsjson({lang:lang,mode:'D'})+'\');" class="btn btn-info">删除</a>';
            if('Y' == Cro.getJsVar('ulogin')) footer += ' <a href="/conero/geek/lang/edit.html?uid='+Cro.bsjson({lang:lang,mode:'M'})+'" class="btn btn-warning">修改</a>';
            if(footer) xhtml += '<div class="panel-footer">'+footer+'</div>';
            if($('#about_lang_dance').length > 0) $('#about_lang_dance').html(xhtml);
            else{
                xhtml = '<div class="panel panel-info" id="about_lang_dance">'+xhtml+'</div>';
                panel.after(xhtml);
            }
            location.href = location.pathname+'#about_lang_dance';
        });
    });    
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.deleteLang = function(url){
        // alert(url);
        th.confirm('您确定要删除数据吗？',function(){
            location.href = url;
        });
    }
});