$(function(){app.pageInit();})
var Cro = new Conero();
var app = Cro.extends(function(th){
    var _self = this;
    this.pageInit = function(){
        th.panelToggle(".panel-toggle");
        th.relieve_lnk('.relieve_lnk');
        // 富文本
        th.tinymce('#event_ipter');
        // 日期控件       
        $('.form_date').datetimepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });    
        // pupop窗
        $('#persid_set_btn').click(function(){
            var id = 'persid_set_pup';
            th.pupop({
                title: '人物关联',
                field: {pers_id:'hidden',name:'姓名',mtime:'编辑时间'},
                post: {table:'gen_node',order:'mtime desc',map:'gen_no="'+_self.getGenNo()+'"'},
                pupopId: id,
                single: true
            },{
                selected:function(){
                    var datarow = $(this).parents('tr.datarow');
                    var pid = datarow.find('td.hidden').text();
                    var name = datarow.find('td.name').text();
                    $('#persid_ipter').val(pid);
                    $('#persid_desc').val(name);
                    $('#'+id).modal('hide');
                    _self.paretsLister(pid,'F');
                }
            });
        });
        // 保存处理按钮
        $('#save_form_btn').click(function(){
            if(th.empty($('#title_ipter').val())) return;
            var content = tinymce.get('event_ipter').getContent();
            if(th.empty(content)){
                th.modal_alert('【事例】不可为空!');
                return false;
            }
        });
    }
    var _genno;
    this.getGenNo = function(){
        if(th.empty(_genno)){
            _genno = th.getUrlBind('genno');
        }
        return _genno;
    }
});