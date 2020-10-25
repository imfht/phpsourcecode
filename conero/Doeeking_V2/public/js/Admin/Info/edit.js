$(function(){    
    $('#save_form_btn').click(function(){
        if(app.checkForm()){return true;} 
        var content = tinymce.get('content_ipter').getContent();
        if(Cro.empty(content)){
            alert('内容不可为空');
            return false;
        }
        return true;
    });
    // 删除信息
    $('#delete_infor_btn').click(function(){
        app.adjustWin().confirm("您确定要删除该条信息记录吗？",function(){
            var deleteurl = Cro.getJsVar('deleteurl');
            if(deleteurl) location.href = deleteurl;
            else{
                $(this).parents('div.modal').modal('hide');
            }
        });
    });
    app.pageInit();
    // popup 窗口
    $('#type_select').click(function(){
        // {title:'账单选择器',field:{use_date:'日期',finc_no:'hidden',name:'名称'},post:{table:'finc_set',order:'use_date desc',map:'center_id="'+Cro.uInfo.cid+'"'}};
        var option = {
            title: "类型选择",
            single: true,
            field:{"plus_name":"值","plus_desc":"描述"},
            post:{table:"sys_site",map:'user_name=\'CONST\' and gover_name=\'infor_type\'',order:'plus_name asc'}
        };
        app.adjustWin().pupop(option,{
            //save:function(){},
            selected:function(){
                var datarow = $(this).parents('tr.datarow');
                var name = datarow.find('.plus_name').text();
                var desc = datarow.find('.plus_desc').text();
                $('#type_ipter').val(name);
                $('#type_desc').text(desc);                
                $('#sys_site').modal('hide');   // iframe 模式时关闭无效
            }
        });
    });
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    this.pageInit = function(){
        /*
         tinymce.init({
            selector: '#content_ipter',
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code'
            ]
         });
         */
         th.tinymce('#content_ipter');
    }
    // 为空市将非空检测让浏览器自动检测
    this.checkForm = function(){
        var els = ['title_ipter','type_ipter','descrip_ipter'];
        var value = '';
        for(var i=0; i < els.length; i++){
            value = $('#'+els[i]).val();
            if(th.empty(value)) return true;
        }
        return false;
    }
    var curWin = null;
    this.adjustWin = function(){
        if(curWin && th.is_object(curWin)) return curWin;
        var win = th.uWin().pWin();
        // 为父类窗口名称
        if(win.location.pathname == '/conero/admin.html'){
            curWin = win.Cro;
            return curWin;
        }
        // 当前独立窗口
        curWin = th;
        return curWin;
    }
});