$(function(){
    // 财务账目-----------------------------------------------------------------------------> THE BEGIN
    $('.fincset_add_btn').click(function(){
        var dtpl = $(this).text();
        dtpl = dtpl.replace('日期',(new Date()).sysdate('y-m-d'));
        var txt = $('#fincset_tpl_text').val();
        txt += dtpl+",\r\n";
        $('#fincset_tpl_text').val(txt);
    });
        //* 快速财务账单增加
    $('#fincset_add_btn').click(function(){
        var aEl = $('.fincset_add_btn');
        var dtpl = $(aEl[0]).text();
        dtpl = dtpl.replace('日期',(new Date()).sysdate('y-m-d'));
        var txt = $('#fincset_tpl_text').val();
        txt += dtpl+",\r\n";
        $('#fincset_tpl_text').val(txt);
    });
        //* 快速财务账单减去
    $('#fincset_del_btn').click(function(){
        var text = $('#fincset_tpl_text').val();
        text = text.trim();
        if(Cro.empty(text)){
            $('#fincset_tpl_text').focus();return;
        }
        var arr = text.split(',');
        if(arr[arr.length-1] == '') arr.pop();
        arr.pop();
        text = arr.join(",").trim();
        if(text) text += ",\r\n";
        $('#fincset_tpl_text').val(text);
    });
        //* 财物账单加载更多
    $('#fincset_more_load').click(function(){
        var button = $(this);
        var btn = button.button('loading');
        var table = button.parents('div.panel-body').find('table');
        var row = table.find('tr').length;
        var div = button.parent('div');
        var ctt = parseInt(div.find('[dataname="pages"]').text()),no = parseInt(div.find('[dataname="currno"]').text());
        if(ctt <= no){
            btn.button('reset');
            button.attr('data-loading-text','没有更多数据了!');
            //btn.button('没有更多数据了!');
            btn.button('loading');
            return;
        }
        no = no+1;
        $.post('/Conero/finance/fevthome/ajax.html',{item:'index/fincsetList',listno:Cro.getQuery('listno'),row:row,no:no},function(data){
            if('N' == data){
                btn.button('reset');
                button.attr('data-loading-text','数据获取失败...!');
                btn.button('loading');
                return;
            }
            data = JSON.parse(data); var trs = data.trs;
            table.append(trs);
            div.find('[dataname="currno"]').text(data.no);
            btn.button('reset');
        });
        //btn.button('reset');
    });
        //* 财务登账pop窗
    $('.popup').click(function(){
        var option = {title:'账单选择器',field:{use_date:'日期',finc_no:'hidden',name:'名称'},post:{table:'finc_set',order:'use_date desc',map:'center_id="'+Cro.uInfo.cid+'"'}};
        var fn = {          
            save:function(){
                var id = '#'+option.post.table;
                var box = $(id).find('[name="popupchecked"]:checked');
                var data = new Array(), tr;
                var content = '';
                for(var i=0; i<box.length; i++){
                    tr = $(box[i]).parents('tr');
                    data.push(tr.find('td.hidden').text());
                    content += '<li>'+tr.find('td.name').text()+'</li>';
                }
                if(content) content = '<ul>'+content+'</ul>';
                var str = Base64.encode(data.join(','));
                //Cro.log(str,data);
                var func = {
                    bindEvent:'save',
                    save: function(){
                        var vdata = $(this).attr('v-data');
                        if(Cro.empty(vdata)){
                            return;
                        }
                        var listno = Cro.getQuery('listno');
                        $.post('/Conero/finance/fevthome/save.html',{fincset_update:vdata,listno:listno},function(data){
                            if(data == 'N'){
                                alert('是修改失败！');
                            }
                            else{
                                alert(data);
                            }
                            location.reload();
                        });
                    }
                }; 
                Cro.modal({
                    title:'数据提交确认',
                    content:content,
                    footer:'<button type="button" class="btn btn-default" dataid="save" v-data="'+str+'">确认</button>'
                },{keyboard:false},func);
            }
        };
        Cro.pupop(option,fn);
    });
    // 财务账目-----------------------------------------------------------------------------> THE END
  
    // 财务计划-----------------------------------------------------------------------------> THE BEGIN
    // * 新建计划
    $('#fincplan_add_btn').click(function(){
        var func = {
            bindEvent:'fincplan_save',
            fincplan_save: function(){
                var body = $(this).parents('div.modal-content').find("div.modal-body");
                var nameEl = body.find('[name="name"]');var name = nameEl.val();
                if(Cro.empty(name)){Cro.modal_alert("财务计划【名称】不可为空");return;}
                var descEl = body.find('[name="descrip"]');var descrip = descEl.val();
                if(Cro.empty(descrip)){Cro.modal_alert("财务计划【描述】不可为空");return;} 
                $.post('/conero/finance/fevthome/index_fplan.html',{mode:'A',name:name,descrip:descrip,listno:Cro.getQuery("listno")},function(data){                
                    try{
                        data = JSON.parse(data);
                        if(Cro.is_object(data)){
                            if(data.error == '1'){Cro.modal_alert(data.desc);return;}
                            else if(data.error == '0'){alert(data.desc);location.reload();return;}
                        }                        
                    }catch(e){Cro.modal_alert("抱歉，数据维护失败.");Cro.log(e);}   
                });                
            }
        };
        var content = '<div class="input-group"><span class="input-group-addon">名称*</span> <input type="text" class="form-control" name="name" placeholder="名称"></div>'
                    + '<label>描述*</label><textarea class="form-control" name="descrip" placeholder="内容描述"></textarea>'
            ;
        Cro.modal({
            title:'新建财务计划',
            content:content,
            footer:'<button type="button" class="btn btn-default" dataid="fincplan_save">确认</button>'
        },{keyboard:false},func);
    });
    // 修改计划
    $('.fplan_editcld_link').click(function(){        
        app.fplanEdit('M',$(this));
    });
    // 删除计划
    $('.fplan_delcld_link').click(function(){        
        app.fplanEdit('D',$(this));
    });
    // 财务计划-----------------------------------------------------------------------------> THE END

    // 日志记录-----------------------------------------------------------------------------> THE BEGIN
    // * 新建日志内容
    var logdata = $('#log_form_dialog');
    $('#logdata_new_btn').click(function(){app.logEdit('A');});
    logdata.find('button[dataid="save"]').click(function(){
        var form = logdata.find('form');
        var data = Cro.formRequired(form);
        if(!Cro.is_object(data)) return;
        data['item'] = 'index4log';        
        var cldNo = form.find('[name="cld_no"]');
        if(cldNo.length>0) data['mode'] = cldNo.attr("model");
        else data['related_fn'] = Cro.getQuery('listno');
        $.post('/Conero/finance/fevthome/save.html',data,function(data){
            try{
                data = JSON.parse(data);
                if(Cro.is_object(data)){
                    if(data.error == '1'){Cro.modal_alert(data.desc);return;}
                    else if(data.error == '0') alert(data.desc);
                }
                location.reload();
            }catch(e){Cro.modal_alert("抱歉，数据维护失败.");Cro.log(e);}            
        });
        //Cro.log(data);
    });
    // * 新建日志内容- 表单操作
    $('#log_form_dialog').find('input[name="plandt_active"]').change(function(){
        var plandt = $('#log_form_dialog').find('input[name="plandt_active"]:checked').val();
        var div = $(this).parents('.input-group');
        if(Cro.empty(plandt)){
            div.find('[name="plan_dt"]').attr('disabled','disabled');
        }
        else{
            div.find('[name="plan_dt"]').removeAttr('disabled');
        }
    });
    // * 查看日志内容
    $('.logdata_about').click(function(){
        var tr = $(this).parents("tr");
        var dataid = tr.attr("dataid");
        var title = $(this).text();
        $.post('/conero/finance/fevthome/ajax.html',{item:"index/logdata_about",dataid:dataid},function(data){
            if(Cro.empty(data)) return;
            Cro.modal({
                title: title,
                large: true,
                content: data
            });
        });        
    });
    // * 修改日志内
    $('.logdata_edit_link').click(function(){
        var tr = $(this).parents("tr");
        var dataid = tr.attr("dataid");   
        app.logEdit('M',dataid);
    });
    // * 删除日志内
    $('.logdata_del_link').click(function(){
        var tr = $(this).parents("tr");
        var dataid = tr.attr("dataid");   
        app.logEdit('D',dataid);
    });
    // 日志记录-----------------------------------------------------------------------------> THE END
    
    // 其他 -----------------------------------------------------------------------------> THE END
    // *左菜单
    $('.nav-sidebar').find('a').click(function(){
        $(this).parents('ul').find('li.active').removeClass('active');
        $(this).parents('li').addClass('active');
        //Cro.alertTest();
    });
    Cro.pageInit();
    // 其他 -----------------------------------------------------------------------------> THE END
});
var Cro = new Conero();
Cro.__APP = function(){
    var myApp = function(th){
        // 财务计划-----------------------------------------------------------------------------> THE BEGIN
            // 财物计划编辑窗
        this.fplanEdit = function(mode,dom)
        {
            var dataid = dom.parents("tr").attr("dataid");
            //$.post('',{},function(data){
            $.post('/conero/finance/fevthome/ajax.html',{item:'index/fplanEdit',dataid:dataid},function(data){
                if(th.empty(data)){th.modal_alert("数据获取失败");return;};
                data = JSON.parse(Base64.decode(data));
                var func = {
                    bindEvent: 'save',
                    save: function(){
                        var savedate = {mode:mode,dataid:dataid};
                        var body = $(this).parents('div.modal-content').find("div.modal-body");
                        savedate['name'] = body.find('[name="name"]').val();
                        savedate['descrip'] = body.find('[name="descrip"]').val();
                        $.post('/conero/finance/fevthome/index_fplan.html',savedate,function(data){
                            try{
                                data = JSON.parse(data);
                                if(Cro.is_object(data)){
                                    if(data.error == '1'){Cro.modal_alert(data.desc);return;}
                                    else if(data.error == '0'){alert(data.desc);location.reload();return;}
                                }                        
                            }catch(e){Cro.modal_alert("抱歉，数据维护失败.");Cro.log(e);}   
                        });
                    }
                }
                var content = '<div class="input-group"><span class="input-group-addon">名称*</span> <input type="text" class="form-control" name="name" value="'+data.name+'" placeholder="名称"></div>'
                            + '<label>描述*</label><textarea class="form-control" name="descrip" placeholder="内容描述">'+data.descrip+'</textarea>'
                    ;             
                Cro.modal({
                    title:(mode == 'M'? '计划修改':'删除计划'),
                    content:content,
                    footer:'<button type="button" class="btn btn-default" dataid="save">确认</button>'
                },null,func);
            });
        }
        // 财务计划-----------------------------------------------------------------------------> THE END

        // 日志记录-----------------------------------------------------------------------------> THE BEGIN
            // 日志样式控制
        this.logAboutToggle = function(dataid,dom){
            dom = $(dom);
            var article = dom.parents('div.modal-body').find("article.logdata_about");
            var p = dom.parent('p');
            var match = p.attr('dataid');
            var post = {item:"index/logdata_about",dataid:dataid,getcontent:true};
            if(match == 'unmatch') post['unmatch'] = '1';
            $.post('/conero/finance/fevthome/ajax.html',post,function(data){
                if(Cro.empty(data)) return;
                article.html(data);
                if(match == "unmatch"){p.attr("dataid","match");dom.text('修饰内容');}
                else{p.attr("dataid","unmatch");dom.text('原始内容');}
            }); 
        }
        // 日志编辑- 修改与删除
        this.logEdit = function(mode,cldno){
            var form = $('#log_form_dialog').find('div.modal-body').find('form');
            var idLoader = form.find('[dataid="cldno4id_load"]');
            if(mode == 'A'){
                $('#log_fdia_title').text("新建日志");
                form.find('[name="name"]').val('');
                form.find('[name="keyword"]').val('');
                form.find('[name="content"]').val('');                
                idLoader.html('');
                Cro.modal('#log_form_dialog');
            }
            else{                
                idLoader.html('<input type="hidden" name="cld_no" value="'+cldno+'" model="'+mode+'">');
                var title = (mode == 'M')? '修改日志':'删除日志';
                $.post('/conero/finance/fevthome/ajax.html',{item:'index/editlog',cldno:cldno},function(data){
                    if(th.empty(data)){th.modal_alert("数据获取失败");return;};
                    data = JSON.parse(Base64.decode(data));
                    for(var k in data){
                        form.find('[name="'+k+'"]').val(data[k]);
                    }
                    title = title +'-'+data.name;
                    $('#log_fdia_title').text(title);
                    Cro.modal('#log_form_dialog');
                });
            }            
        }
        // 日志记录-----------------------------------------------------------------------------> THE END
    };
    return new myApp(this); 
};
var app = Cro.__APP();
Cro.pageInit = function(){
    /*
    var hash = location.hash.replace("#",'')
    if(hash){        
    }
    */
}
Cro.uInfo = Cro.getJsVar('uInfo');