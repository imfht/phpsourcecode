$(function(){app.pageInit();});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function()
    {
        // 表单监视器
        var pk = 'pers_id';
        var formGrid = th.formListEvent({
            table:'#node_groupby_parents',
            url: th._baseurl + 'clan/node/chid_save.html',
            pk:  pk
        });
    }
});