$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var _self = this;
    var _pid;
    this.pageInit = function(){
        _pid = th.getUrlBind('pid');
        // 父亲设置
        $('#father_set_btn').click(function(){
            var id = 'father_set_pup';
            th.pupop({
                title: '选择其父亲',
                field: {pers_id:'hidden',name:'姓名',mtime:'编辑时间'},
                post: {table:'gen_node',order:'mtime desc',map:'sex="M" and gen_no="'+_self.getGenNo()+'"'},
                pupopId: id,
                single: true
            },{
                selected:function(){
                    var datarow = $(this).parents('tr.datarow');
                    var pid = datarow.find('td.hidden').text();
                    var name = datarow.find('td.name').text();
                    $('#father_ipter').val(pid);
                    $('#father_desc').val(name);
                    $('#'+id).modal('hide');
                    _self.paretsLister(pid,'M');
                }
            });
        });
        // 父手动设置机数据不存在是时提醒是否后台新增
        $('#father_desc').change(function(){
            var mName = $(this).val();
            var father = $('#father_ipter').val();
            if(!th.empty(mName) && th.empty(father)){
                var tip = '其父【' + mName + '】还未存在，是否将其保存到数据库中！';
                th.confirm(tip,function(){
                    var data = {sex:'M',name:mName};
                    _self.parentInsertTip(data);
                    $('#btsp_modal_confirm').modal('hide');
                });
            }
        });
        // 母亲设置
        $('#mother_set_btn').click(function(){
            var id = 'mother_set_pup';
            th.pupop({
                title: '选择其母亲',
                field: {pers_id:'hidden',name:'姓名',mtime:'编辑时间'},
                post: {table:'gen_node',order:'mtime desc',map:'sex="F" and gen_no="'+_self.getGenNo()+'"'},
                pupopId: id,
                single: true
            },{
                selected:function(){
                    var datarow = $(this).parents('tr.datarow');
                    var pid = datarow.find('td.hidden').text();
                    var name = datarow.find('td.name').text();
                    $('#mother_ipter').val(pid);
                    $('#mother_desc').val(name);
                    $('#'+id).modal('hide');
                    _self.paretsLister(pid,'F');
                }
            });
        });
        // 母手动设置机数据不存在是时提醒是否后台新增
        $('#mother_desc').change(function(){
            var mName = $(this).val();
            var mother = $('#mother_ipter').val();
            if(!th.empty(mName) && th.empty(mother)){
                var tip = '其母【' + mName + '】还未存在，是否将其保存到数据库中！';
                th.confirm(tip,function(){
                    var data = {sex:'F',name:mName};
                    _self.parentInsertTip(data);
                    $('#btsp_modal_confirm').modal('hide');
                });
            }
        });
        // 首页进入是查看是否为存在已经保存的数据
        var fatherId,motherId;
        if(_pid){
            fatherId = $('#father_ipter').val();
            motherId = $('#mother_ipter').val();
        }
        // 关系解除重置
        $('.relieve_lnk').click(function(){
            var formGroup = $(this).parents('div.form-group');
            var inputGroup = formGroup.find('div.input-group');
            var input = inputGroup.find('input[type="hidden"]');
            var id = input.attr("id");
            var relieveFn = function(){
                input.val('');
                var newid = id == 'father_ipter'? '#father_desc':'#mother_desc';
                $(newid).val('');
            };
            if(_pid){
                var json = {
                    pid:_pid,
                    type: (id == 'father_ipter'? 'F':'M'),
                    refid:  (id == 'father_ipter'? fatherId:motherId)
                };
                var tipRequest = function(){
                    json.item = 'relieve_request';
                    $.post(th._baseurl + 'clan/node/ajax.html',json,function(data){
                        $('#btsp_modal_confirm').modal('hide');
                        if(data.code === 0) relieveFn();
                        th.modal_alert(data.msg);                        
                    });
                };
            }
            if(id == 'father_ipter'){ // 父 
                if(fatherId){
                    return th.confirm("您要删除他们的父子(女)关系吗？",tipRequest);
                }
            }
            else{
                if(motherId){
                    return th.confirm("您要删除他们的母子(女)关系吗？",tipRequest);
                }
            }
            relieveFn();
        });
        // 字辈设置
        $('#zibei_set_btn').click(function(){
            var id = 'zibei_set_pup';
            th.pupop({
                title: '选择其父亲',
                field: {zibei_no:'hidden',zibei:'字辈',mtime:'编辑时间'},
                post: {table:'gen_zibei',order:'mtime desc',map:'gen_no="'+_self.getGenNo()+'"'},
                pupopId: id,
                single: true
            },{
                selected:function(){
                    var datarow = $(this).parents('tr.datarow');
                    var zno = datarow.find('td.hidden').text();
                    var zibei = datarow.find('td.zibei').text();
                    $('#zibeino_ipter').val(zno);
                    $('#zibei_ipter').val(zibei);
                    $('#'+id).modal('hide');
                }
            });
        });
        // 姓名监视器
        $('#name_ipter').change(function(){
            var name = $(this).val();
            if(!th.empty(name)){
                // 字辈自动设置 - 汉字有效
                var isZhReg = /^[\u2E80-\u9FFF]+$/;
                if(isZhReg.test(name)){
                    if(name.length == 3){
                        var tmpArr = name.split('');
                        var zibei = tmpArr[1];
                        var post = {zibei:zibei,gen_no:_self.getGenNo(),item:'get_zibei_4_nameLister'};
                        $.post('/conero/clan/node/ajax.html',post,function(data){
                            if(data != -1){
                                $('#zibei_ipter').val(zibei);
                                $('#zibeino_ipter').val(data);
                            }
                            else{
                                $('#zibei_ipter').val('');
                                $('#zibeino_ipter').val('');
                            }
                        });
                    }
                }
            }
        });
        /*
        // 父亲监视器 - father_ipter - father_desc
        $('#father_desc').change(function(){
            var father = $('#father_ipter').val();
            if(!th.empty(father)){
                var send = {item:'get_relation',pid:father};
                $.post('/conero/clan/node/ajax.html',send,function(data){
                    th.log(data);
                });
            }
            alert(father);
        });
        */
        th.panelToggle(".panel-toggle");
        // 快速设置子女
        $('#fset_child_lnk').click(function(){
            var span = $(this).parent('span');
            var pEl = span.prev('p');
            var text = pEl.text();
            var textArr = text.split(',');
            
            var xhtml = '';
            for(var k=0; k<textArr.length; k++){
                xhtml += '<li>'+textArr[k]+'</li>';
            }
            if(xhtml) xhtml = '<ul>'+xhtml+'</ul>';
            th.log(textArr,xhtml);
            var content = xhtml;
            // content += '<textarea class="form-control"></textarea>';
            content += '<div class="form-group">' +
                       '     <label for="exampleInputEmail1">文本快速输入：" 姓名 + 性别/默认男性 ;"</label>' +
                       '     <textarea class="form-control" id="fset_txt_ipt" rows="10"></textarea>' +
                       ' </div><div id="fset_child_fmtip"></div>'
            ;
            th.modal({
                title: '快速设置子女',
                id: 'fset_child_mdl',
                content:content,
                save:function(){
                    var text = $('#fset_txt_ipt').val();
                    text = $.trim(text);
                    if(th.empty(text)){
                        th.alert('#fset_child_fmtip','文本不可为空！');
                        $('#fset_txt_ipt').focus();
                        return;
                    }                    
                    var json = {pid:_pid};
                    json['context'] = text;
                    th.post(th._baseurl + 'clan/node/fimport.html',json);
                    $('#fset_child_mdl').modal('hide');
                }
            });
        });
        // 初始化
        if(_pid){
            var father = $('#father_ipter').val();
            if(!th.empty(father)){
                $('#father_desc').attr("readonly",true);
            }
            var mother = $('#mother_ipter').val();
            if(!th.empty(mother)){
                $('#mother_desc').attr("readonly",true);
            }
            
        }
    }
    // 获取 家谱标识
    var _genNo;    
    this.getGenNo = function(){
        if(th.empty(_genNo)){
            _genNo = th.getUrlBind('edit');
        }
        return _genNo;
    }
    // 父母监视器
    this.paretsLister = function(pid,type){
        if(th.empty(pid)) return;
        var send = {item:'get_relation',pid:pid};
        $.post(th._baseurl+'clan/node/ajax.html',send,function(data){
            // th.log(data);
            var panel = $('#parest_relation');
            var dom;
            if(type == 'M') dom = panel.find('div.father');
            else dom = panel.find('div.mother');
            dom.html(data);
            panel.removeClass('hidden');            
        });
    }
    // 父母不存在是是否写入数据库
    this.parentInsertTip = function(json){
        json['gen_no'] = this.getGenNo();
        if(_pid) json['pid'] = _pid;
        json['item'] = 'parents_insert';
        $.post(th._baseurl+'clan/node/ajax.html',json,function(data){
            if(data.error == 0){
                if(json.sex == 'M'){
                    $('#father_desc').attr("readonly",true);
                    $('#father_ipter').val(data.data);
                }
                else{
                    $('#mother_desc').attr("readonly",true);
                    $('#mother_ipter').val(data.data);
                }
            }
            th.modal_alert(data.msg);
        });
    }
});