$(function(){
    // 导航栏自动变化
    //$('#email_nav_tabs')
    // Cro.modal_alert();
    EM.pageInit();
});
var EM = Cro.extends(function(th){
    // 收件选择删除以后 - 提交表单
    function eboxAfterSelect(dataid){
        var ebox = $('input[name="ebox_list"]');
        var ebxList = [];
        for(var k =0; k<ebox.length; k++){
            if(ebox[k].checked) ebxList.push($(ebox[k]).val());
        }            
        if(ebxList.length > 0){
            // th.modal_alert(ebxList.join(','));
            var vlist = ebxList.join(',');
            var json = {dataid:dataid,vlist:vlist};
            th.post(th._baseurl + 'center/index/save/email.html',json);
            return;
        }
        th.modal_alert('请选择邮件！');
    }

    this.pageInit = function()
    {
        // 路由 - 导航栏自适应
        var qstring = location.search;
        var nav = $('#email_nav_tabs');
        var li,dataid = 'ebox';
        if(qstring.indexOf('&write=Y') > -1){     
            dataid = 'write';
            th.tinymce('#contentlIpter');
            this.writeTabs();
        }
        else if(qstring.indexOf('&send=Y') > -1){
            dataid = 'send';
            // this.baseListGrid();  
            this.eboxTabs();
        }
        else if(qstring.indexOf('&read=') > -1){
            dataid = 'read';
        }
        else if(qstring.indexOf('&recycle=') > -1){
            dataid = 'recycle';
            this.eboxTabs();
        }
        if(dataid == 'ebox') this.eboxTabs();
        var li = nav.find('li[dataid="'+dataid+'"]');       
        li.addClass('active');
        li.find('a').addClass('text-danger');        
    }
    // 收件箱
    this.eboxTabs = function(){
        this.baseListGrid();     
        // 移除列表到回收站
        $('#ebox_push_bak').click(function(){
            var dataid = $(this).attr("dataid");
            eboxAfterSelect(dataid);
        });
        // 邮件删除
        $('#ebox_del_lnk').click(function(){
            var dataid = $(this).attr("dataid");
            eboxAfterSelect(dataid);
        });
        // 回收撤销
        $('#ebox_reset_bak').click(function(){
            // eboxAfterSelect('emrsv_rvRstBk');
             var dataid = $(this).attr("dataid");
            eboxAfterSelect(dataid);
        });       
    }
    // 写信tab
    this.writeTabs = function()
    {
        // 自动设置
        $('.addRsvList_lnk').click(function(){
            var dataid = $(this).attr('dataid');
            var oldVal = $('#rcvemailIpter').val();
            oldVal = $.trim(oldVal);
            if(!th.empty(oldVal) && oldVal.indexOf(dataid)> -1) return;
            else{
                if(!th.empty(oldVal)){
                    var tmpArray = oldVal.split(';');
                    tmpArray.push(dataid);
                    $('#rcvemailIpter').val(tmpArray.join('; '));
                }
                else $('#rcvemailIpter').val(dataid);
            }
        });
    }
    // 基本时间时间绑定
    this.baseListGrid = function(){
        // 选择器现显隐切换
        $('#ebox_bck_toggle').click(function(){
            $('.ebox_bck').toggleClass('hidden');
        });
        // 全选切换
        $('#ebox_bck_all').change(function(){
            var isChecked = $(this).is(':checked');
            var ebox = $('input[name="ebox_list"]');
            for(var k =0; k<ebox.length; k++){
                ebox[k].checked = isChecked? true:false;
            } 
        });
    }
});