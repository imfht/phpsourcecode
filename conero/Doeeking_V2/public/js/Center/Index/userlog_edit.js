$(function(){
    if(location.search.indexOf('gid') > -1) app.gidPageStart();
    else if(location.search.indexOf('search') > -1) app.searchPageStart();
    else app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    // 公共页面 - 两个页面
    function pageCommon(){
        // 富文本
        th.tinymce('#detalIpter');
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
    }
    // 普通保存页面
    this.pageInit = function(){
        pageCommon();
        //  分组 pupop窗
        $('#groupid_modal_btn').click(function(){
            $.post('/conero/center/index/ajax/userlog.html',{item:'groupid_modal'},function(content){
                th.modal({
                    title:'分组选择',
                    content: content
                },null,{
                    bindEvent:'groupid_opts',
                    groupid_opts:function(){
                        var dom = $(this);
                        $('#groupidIpter').val(dom.text());
                        dom.parents('div.modal').modal('hide');
                    }
                });
            });
        });
    };
    // 分组页面
    this.gidPageStart = function(){
        pageCommon();        
        th.panelToggle('.ptoggle');
        // th.tinymce('.detal_tinymce');

        // 快速数据保存
        $('#fast_write_link').click(function(){
            th.modal('#fast_write_panel');
        });

        // 页面提交
        $('#fast_save_btn').click(function(){
            var data = th.formJson($('#fast_write_panel').find('div.modal-body'));
            var detal = tinymce.get('detalIpter').getContent();            
            data['formid'] = 'fast_save';
            data['detal'] = detal;
            th.post('/conero/center/index/save/userlog.html',data);
        });
        var curMceId = null,        // 当前富文本id
            curIdeLogno = null,     // 当前富文本id 对应的 数据标识码
            rowText = null          // 当前富文本的原始内容
            ;
        // 撤销或撤销保存
        function quitEdit(save_mk,notCloseArea){
            save_mk = th.empty(save_mk)? false:true;
            notCloseArea = th.empty(notCloseArea)? true:false;
            if(curMceId){
                var detalDv = $('#'+curMceId).parents('div.detal_dance');
                var content = save_mk? tinymce.get(curMceId).getContent() : rowText;
                if(save_mk == true){
                    $.post('/conero/center/index/ajax/userlog.html',{'__:':th.bsjson({item:'fast_edit_save',content:content,logno:curIdeLogno}),'$rd':Math.random()},function(data){
                        if(data == 1) th.modal_alert('数据更新成功！','提示');
                    });
                }
                if(notCloseArea) detalDv.html(content);
                else return;
            }
            curMceId = null;
            curIdeLogno = null;
            rowText = null;
        }
        //快速编辑
        $('.edit_links').click(function(){
            var dom = $(this);
            var pheader = dom.parents('div.page-header');
            var detalDv = pheader.next('div.detal_dance');
            var textarea = detalDv.find('textarea');
            // 文件存在时提交数据则快速快速保存
            if(textarea.length > 0){
                var content = tinymce.get(curMceId).getContent();
                detalDv.html(content);
            }
            else{
                var xhtml = detalDv.html();                
                var alertHtml = ""
                    + ' <div class="alert alert-warning alert-dismissible fade in" role="alert"> '
                    + '     <h4>您正在进行快速编辑模式</h4> '
                    + '     <p>该模式下你可快速编辑日志内容，编辑完成后只需要点击对应的标题便可保存内容(Alt+G)。点击下面按钮时则不会提交数据，仅仅撤销更变(Alt + Q).或保存且保持表示值保存内容不关闭编辑框， 也可使用快捷键(Alt + S)!</p> '
                    + '     <p>'
                    + '         <button type="button" class="btn btn-danger" id="fquit_btn">撤销更改</button> '
                    + '         <button class="btn btn-default" type="button" id="fsave_btn">或直接保存</button>'
                    + '         <button class="btn btn-success" type="button" id="fsaveon_btn">或保存且保持</button>'
                    + '     </p>'
                    + '</div>';
                var newXhtml = alertHtml + '<textarea class="form-control detal_tinymce" rows="10">'+xhtml+'</textarea>';
                detalDv.html(newXhtml);   
                th.tinymce('.detal_tinymce');  
                textarea = detalDv.find('textarea');
                // 如果有文本存在就 将其提交并保存
                if(curMceId){
                    quitEdit();
                }
                rowText = xhtml;
                curMceId = textarea.attr("id");
                curIdeLogno = dom.attr("dataid");
                // 时间保存
                $('#fquit_btn').off();
                $('#fquit_btn').click(function(){
                    quitEdit();
                });
                $('#fsave_btn').off();
                $('#fsave_btn').click(function(){
                    quitEdit(true);
                });
                $('#fsaveon_btn').off();
                $('#fsaveon_btn').click(function(){
                    quitEdit(true,true);               
                });
                
            }               
        });
        // 快捷保存内容，只保存不关闭
        $(document).keypress(function(key){
            //th.log(key.which);
            if(key.altKey && key.which == 115){ // alt + s
                quitEdit(true,true);
            }else if(key.altKey && key.which == 113){   // alt + Q    
                quitEdit();
            }else if(key.altKey && key.which == 103){   // alt + G
                quitEdit(true);
            }        
        });
    };
    // 搜索页面处理
    this.searchPageStart = function () {
        // 搜索列表显示
        $('.slist_lnk').click(function () {
            $.post(th._baseurl+"center/index/ajax/userlog.html",{item:'slist_get_content',no:$(this).attr("data-no")},function (data) {
                data = data? JSON.parse(data):{};
                var xhtml = '<p class="text-right bg-success" style="padding-right: 10px;">'
                    + '<a href="'+th._baseurl+'center/index/edit/userlog/'+data.code+'.html" target="_blank">编辑</a> '
                    +'内容</p>'
                    + '<div>'+data.detal+'</div>'
                    + (data.addr? '<p class="text-right bg-success" style="padding-right: 10px;">地址</p>' + data.addr:'')
                    ;
                th.modal({
                    title: data.title + ' <small>'+data.life_date+'</small>',
                    content: xhtml,
                    large: true
                });
            });
        });
    };
});