$(function(){
    Cro.panelToggle(['.panel-toggle-link']);
    $('#ycheck_btn').click(function(){
        var ipt = $('#ycheckipt');
        var year = parseInt(ipt.val());
        var min = parseInt(ipt.attr("min"));
        var max = parseInt(ipt.attr("max"));
        var tipDom = $(this).parents('div.panel-body').find('div.tip_info');
        if(year<min || year>max){
            Cro.alert(tipDom,'输入值无效，应该在【'+min+','+max+'】之间');
            return;
        }
        if(year){
            $.post('/conero/finance/report/ajax.html',{item:'check_by_year',year:year},function(data){
                if(Cro.empty(data)){
                    Cro.alert(tipDom,'为查询到相关数据！！');
                    return;
                }
                var xhtml = '';
                var year = null;
                for(var k in data){
                    if(isNaN(k)) continue;
                    if(!Cro.empty(data[k]['hasdata'])) continue;
                    xhtml += '<a href="?accno='+data[k]['accno']+'" class="col-md-2">'+data[k]['accno']+'</a>';
                    if(Cro.empty(year)) year = data[k]['accno'].substring(0,4);
                }
                if(xhtml){
                    xhtml = '<p class="text-danger text-center">可生产报表如下</p><div class="row">'+xhtml+'<a href="?ayms='+year+'" class="col-md-2">一键生成全年月报表</a></div>'
                    tipDom.html(xhtml);
                }
                else Cro.alert(tipDom,'您的财务报表可能全部生成！');
            });
        }
    });
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){}
});