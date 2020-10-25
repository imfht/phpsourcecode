$(function(){
    //---------------------------------------- 事件绑定
        // 表单帮助栏
    $('.helper_link').click(function(){
        // if($('#fset2week_panel').length > 0) return;
        location.href = location.pathname+'#fset2week_panel';
        var dataid = $(this).attr('dataid');
        switch(dataid){
            case '2week': // 获取近期数据
                $.post('/conero/finance/fincset/ajax.html',{item:'get2week4edit'},function(data){
                    // $('#fincset_main').append(data);
                    app.reback2WeekRpt(data);
                });
                break;
            case 'export':
                $('#export_controll').modal();
                break;                
            case 'import':  // 导入数据
                var xhtml = '<form class="form-inline" role="form" enctype="multipart/form-data" method="post">'
                    +'  <div class="checkbox">'
                    +'  <label>'
                    +'    <input type="radio" name="format" value="csv" dataid="csv" required> csv'
                    +'  </label>'
                    +'  <label>'
                    +'    <input type="radio" name="format" value="cro" dataid="cro" required> cro'
                    +'  </label>'
                    +'  </div>'
                    +'  <div class="form-group"><input type="file" name="fimp" onChange="app.fimpChange(this)" required><input type="hidden" name="formid" value="_impdata_"></div><div class="form_tip"></div></form>';
                Cro.modal({
                    title:'导入数据',
                    content:xhtml,
                    save:function(){
                        var mbody = $(this).parents('div[role="dialog"]').find("div.modal-body");
                        var form = mbody.find('form');
                        var format = form.find('input[name="format"]:checked').val();
                        if(Cro.empty(format)){
                            Cro.alert(form.find('div.form_tip'),'请选择文件类型!',3);
                            return;
                        }
                        var filename = form.find('input[type="file"]').val();      
                        if(filename) filename = filename.toLowerCase();
                        else{
                            Cro.alert(form.find('div.form_tip'),'您还没有上传文件！',3);
                            return;
                        }
                        if(filename.indexOf('.'+format) == -1){
                            Cro.alert(form.find('div.form_tip'),format+'/'+filename+'文件格式不匹配！',3);
                            return;
                        }
                        form.submit();
                    }
                });
                break;

        }
    });
        // 搜索新增作画
    $('#get_search_key > li >a').click(function(){
        var dataid = $(this).attr("dataid");
        var descrip = $(this).text();
        var value = $('#search_ipter').val();
        if(!Cro.empty(value) && dataid){
            var send = {};send[dataid] = ['like','%'+value+'%'];
            Cro.log(send);
            $.post('/conero/finance/fincset/ajax.html',{item:'searchmkchart4edit',map:Cro.bsjson(send)},function(data){
                $('#search_mkchart_panel').removeClass('hidden');
                var pbody = $('#search_mkchart_panel').find('div.panel-body');
                $('#search_mkchart_panel').find("div.panel-heading").find('h4').html(descrip+' <small>'+value+'</small>');
                pbody.removeClass("hidden");
                // var sChart = echarts.init(pbody.get(0));
                if(Cro.is_object(data)){
                    var barName = descrip+'金额';
                    data.backgroundColor = '#F5FFFA';
                    data.title = {text:descrip};                    
                    data.legend = {data:[barName]};
                    data.tooltip = {show:true};
                    data.toolbox = {
                        feature: {
                            dataZoom: {
                                yAxisIndex: 'none'
                            },
                            dataView: {show: true, readOnly: false},
                            magicType: {show: true, type: ['line', 'bar']},
                            restore: {show: true},
                            saveAsImage: {show: true}
                        }
                    };         
                    data.yAxis = {};
                    data.series.name = barName;
                    data.series.type = 'line';
                    echarts.init(pbody.get(0)).setOption(data);
                }
            });
        }
        //alert(dataid);
    });
        // 数据导出控制-导出选择
    $('#export_controll input[name="exptype"]').change(function(){
        var exptype = $('#export_controll input[name="exptype"]:checked').val();
        if(exptype == 'all'){
            $('#export_controll div.filter').hide();
        }
        else{
            $('#export_controll div.filter').show();            
        }
    });
        // 数据导出- 提交
    $('#export_save_btn').click(function(){
        $('#export_controll form').submit();
        $('#export_controll').modal('hide');
    });
        // 财务搜索
    $('#select_sflakey_btn>li>a').click(function(){
        var dataid = $(this).attr("dataid");
        var value = $(this).parents('div.input-group').find('[name="search_input"]').val();
        if(Cro.empty(value)){
            Cro.modal_alert('搜索值不可为空！');
            return;
        }
    });
        // 刷新页面
    $('#form_reset_btn').click(function(){location.href = location.pathname;});
    var formGrid = Cro.formListEvent({
        table:'#fset_form_list',
        pk:  'finc_no',
        url:'/conero/finance/fincset/save.html'
    },'');    
    // 文件导入时显示数据
    var record = Cro.getJsVar('record');
    // Cro.log(record);
    if(record){
        for(var k in record){
            if(!Cro.is_object(record[k])) continue;
            formGrid.addRowByRecord(record[k],null,function(after){
                formGrid.addRow(function(lastRow){
                    var master = lastRow.find('[name="master"]');
                    var td = master.parent('td');
                    var vmaster = master.val();
                    if(!Cro.empty(vmaster)){
                        var option = td.find('select[name="master_id"]').find('option');
                        var value,el;
                        for(var i=0; i<option.length; i++){
                            el = $(option[i]);
                            value = el.text();
                            if(value == vmaster){
                                el.attr("selected",true);
                            }
                        }
                    }
                });
            });            
        }
        formGrid.delRow();
        /*
        for(var i=0; i<record.length; i++){
            record[i]['finc_no'] = '';
            formGrid.addRowByRecord(record[i]);
        }
        */
    }
    app.pageInit(formGrid);
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var siderSelector;
    var stable = 'crofncefset';  // session 全局键值
    var session = th.storage().table(stable);
    var formGrid;
    this.pageInit = function(vformGrid){
        formGrid = vformGrid;
        // 最近两周登账记录-查看session        
        setTimeout(this.reback2WeekRpt(),5000);
    }
    // 两周登账记录 - 设置/获取 - session 记录
    this.reback2WeekRpt = function(xhtml){
        var skey2Week = '2week';
        if(xhtml){
            if($('#fset2week_panel').length > 0) $('#fset2week_panel').remove();
            $('#fincset_main').append(xhtml);
            session.update(skey2Week,Base64.encode(xhtml));
            return true;
        }
        else xhtml = session.get(skey2Week);
        if(xhtml){
            // alert($('#fset2week_panel').length);
            if($('#fset2week_panel').length > 0) $('#fset2week_panel').remove();
            $('#fincset_main').append(Base64.decode(xhtml));
            return true;
        }
        return false;
    }
    // 事务乙方 切换器
    this.siderToggler = function(dom){
        dom = $(dom);
        var span = dom.parents('span');
        var group = dom.parents('div.input-group');
        var ipter = group.find('input[name="sider"]');
        var xhtml;
        // 普通文本
        if(ipter.attr("type") == 'text'){
            siderSelector = siderSelector? siderSelector : th.getJsVar('siderSelector');
            ipter.remove();
            xhtml = siderSelector+'<input type="hidden" name="sider">';
            span.before(xhtml);
        }
        else if(ipter.attr("type") == 'hidden'){
            ipter.remove();
            group.find('select').remove();
            xhtml = '<input type="text" name="sider" class="form-control" required>';
            span.before(xhtml);
        }
    }
    // 事务乙方 改变监听器
    this.siderListener = function(dom){
        dom = $(dom);
        var descrip = dom.find('option:selected').text();
        if(descrip){
            dom.parents('div.input-group').find('[name="sider"]').val(descrip);
        }
        th.log(descrip);
    }
    // 事务乙方监听器
    this.masterListener = function(dom){
        dom = $(dom);
        var descrip = dom.find('option:selected').text();
        if(descrip){
            dom.parents('td').find('[name="master"]').val(descrip);
        }        
    }
    // 监听器 - 财务日志生成器 option => {id/descrip/dom}
    function listenRptLog(data,option){
        if(data){            
            option = option? option:{};
            var id = option.id;
            var descrip = option.descrip;
            var dom = option.dom;

            var row = dom.parents('tr').find('td.rowno').text();
            var pbody = dom.parents('div.panel').find('div.panel-body');

            var list = '';
            for(var k in data){
                if(!th.is_object(data[k]) || th.empty(data[k].tpl)) continue;
                list += '<p><a href="javascript:void(0);" class="alert-link" title="点击按模块生成">'+data[k].tpl+'</a></p>';
            }
            if(!th.empty(list)){
                pbody.find('div[role="alert"]').hide();
                if($('#'+id).length == 0){
                    var xhtml = '<div class="alert alert-success" role="alert" id="'+id+'">'
                        + '<strong> ['+row+'] - '+descrip+'</strong>'
                        + list 
                        + '</div>'
                        ;
                    pbody.append(xhtml);
                }
                else $('#'+id).html('<strong> ['+row+'] - '+descrip+'</strong>' + list);
                $('#'+id).show();
            }
        }
    }
    // 日期监听器
    this.useDateListener = function(dom){
        dom = $(dom);
        var row = dom.parents('tr').find('td.rowno').text();
        var pbody = dom.parents('div.panel').find('div.panel-body');
        var value = dom.val();
        if(value){
            $.post('/conero/finance/fincset/ajax.html',{item:'listener4edit',map:th.bsjson({'use_date':value})},function(data){
                listenRptLog(data,{id:'use_date_info',descrip:'日期',dom:dom});    
            });
        }
    }
    // 名称监听器
    this.nameListener = function(dom){
        dom = $(dom);
        var value = dom.val();
        if(value){
            $.post('/conero/finance/fincset/ajax.html',{item:'listener4edit',map:th.bsjson({name:value})},function(data){
                listenRptLog(data,{id:'name_info',descrip:'名称',dom:dom});               
            });
        }
    }
    // 事务乙方监听器
    this.siderListener = function(dom){
        dom = $(dom);
        var value = dom.val();
        if(value){
            $.post('/conero/finance/fincset/ajax.html',{item:'listener4edit',map:th.bsjson({sider:value})},function(data){
                listenRptLog(data,{id:'sider_info',descrip:'事务乙方',dom:dom});               
            });
        }
    }
    this.explaninListener = function(dom){
        dom = $(dom);
        var value = dom.val();
        if(value){
            $.post('/conero/finance/fincset/ajax.html',{item:'listener4edit',map:th.bsjson({explanin:value})},function(data){
                listenRptLog(data,{id:'explanin_info',descrip:'详情',dom:dom});               
            });
        }
    }
    // 详情监听器
    this.explaninHelper = function(dom){
        var dom = $(dom);
        var row = dom.parents('tr').find('td.rowno').text();
        var pbody = dom.parents('div.panel').find('div.panel-body');
        pbody.find('div[role="alert"]').hide();
        if($('#explanin_info').length == 0){
            var xhtml = 
                '<div class="alert alert-success" role="alert" id="explanin_info">'
                +'<strong> ['+row+'] - 详情</strong>'
                + '<a href="javascript:void(0);" class="alert-link">' +dom.val() + '</a>'
                +'</div>'
                ;
            pbody.append(xhtml);
        }
        else $('#explanin_info').html('<strong> ['+row+'] - 详情</strong> <a href="javascript:void(0);" class="alert-link">'+dom.val()+'</a>');
        $('#explanin_info').show();
    }
    // 版面控制与隐藏
    this.panelToggle = function(dom){
        dom = $(dom);
        var panel = dom.parents('div.panel');
        var pbody = panel.find('div.panel-body');
        pbody.toggleClass('hidden');
    }
    /*---------------------------------------------------- 表单处理 begin----------------------------------------------------*/
    // 删除
    this.del_btn = function(dom){
        dom = $(dom);
        th.confirm("您确定要删除该账单记录吗，删除数据后可能无法恢复！！",function(){
            var url = dom.attr('dataurl');
            location.href = url;
        });
    }
    // 编辑
    this.edit_btn = function(dom){
        dom = $(dom);        
        var dataid = dom.parents('tr[dataid]').attr("dataid");
        if(dataid){
            $.post('/conero/index/common/record.html',{map:th.bsjson(['finc_set','finc_no',dataid])},function(data){
                formGrid.addRowByRecord(data,null,function(lastRow){
                    lastRow.attr("class","success");
                });
            });
        }
    }
    // 详情
    this.fabout_btn = function(dom){
        dom = $(dom);        
    }
    /*---------------------------------------------------- 表单处理 end----------------------------------------------------*/    
    // 文件导入控制
    this.fimpChange = function(dom){
        dom = $(dom);
        var filename = dom.val();
        th.log(filename);
        if(filename){
            filename = filename.toLowerCase();
            var fileExt = ['csv','cro'];
            var name = '';
            var mbody = dom.parents('div.modal-body');            
            for(var i=0; i<fileExt.length; i++){                
                if(filename.indexOf('.'+fileExt[i]) > -1){                    
                    mbody.find('input[value="'+fileExt[i]+'"]').attr("checked",true);                  
                    return;
                }
            }
            Cro.alert(mbody.find('div.form_tip'),'系统不支持文件格式！',3);
            dom.val('');
        }
    }
});