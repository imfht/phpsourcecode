$(function(){
    var isPlanEl = Cro.getUrlBind('el');
    isPlanEl = isPlanEl? true:false;
    if(isPlanEl){
        app.elPageInit();
    }
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        th.tinymce('#descripIpter');
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
        th.panelToggle('.panel_tab_lnk');        
    }
    // 计划项页面初始化
    this.elPageInit = function(){
        var formEvent = th.formListEvent({
            table : '#p_el_list',
            pk  : 'listno',
            url : th.getJsVar('actionUrl')
        },{
            beforeSaveData:function(data){
                /*
                // th.log(data);
                // alert(JSON.stringify(data));
                data['dataid'] = 'plan_el';
                // data.push(JSON.stringify({"dataid":"8555"}));
                return data;
                */
                var p_listno = th.getUrlBind('lfplan');
                var savedata = {};
                for(var k=0; k<data.length; k++){
                    savedata[k] = data[k];
                }
                savedata['dataid'] = 'plan_el';
                savedata['p_listno'] = p_listno;
                return savedata;
            }
        });
    }
});