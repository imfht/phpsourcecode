$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var _self = this;
    this.pageInit = function(){
        // 父字辈设置
        $('#zibei_set_btn').click(function(){
            var id = 'fzb_setter_pop';
            th.pupop({
                title:'父字辈选择',
                field: {zibei_no:'hidden',zibei:'字辈',mtime:'编辑时间'},
                post: {table:'gen_zibei',order:'ser_no asc,mtime desc',map:'gen_no="'+_self.getGenNo()+'"'},
                pupopId: id,
                single: true
            },{
                selected:function(){
                    var datarow = $(this).parents('tr.datarow');
                    var pid = datarow.find('td.hidden').text();
                    var name = datarow.find('td.zibei').text();
                    $('#pzbno_ipter').val(pid);
                    $('#pzibei_ipter').val(name);
                    $('#'+id).modal('hide');
                    // _self.paretsLister(pid,'M');
                }
            });
        });
    }
    // 获取 家谱标识
    var _genNo;    
    this.getGenNo = function(){
        if(th.empty(_genNo)){
            _genNo = th.getUrlBind('edit');
        }
        return _genNo;
    }
});