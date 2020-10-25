$(function(){
    Cro.pageInit();
    // 新增输入列
    $('#addRowBtn').click(function(){
        if(App.rowCheck()) return;
        var table = $('#set_data_fm').find('table');
        var tr = table.find('tr');
        var len = tr.length-1;        
        var maxTr = $(tr[len]);
        table.append('<tr dataid="'+len+'">'+maxTr.html()+'</tr>');
        table.find('[dataid="'+len+'"]').find('td.no').text(len+1);
    });
    // 删除输入列
    $('#delRowBtn').click(function(){
        var len = App.getRowLen();
        if(len>1){
            $('#set_data_fm').find('table').find('tr[dataid="'+(len-1)+'"]').remove();
            return;
        }
        else App.putout('第一行不能删除！');
    });
    // 提交表单
    $('#saveBtn').click(function(){
        var len = $('#set_data_fm').find('table').find('tr').length;
        if(len == 2){            
            return true;
        }
        else if(len>2){
            if(App.rowCheck()) return;
            App.getSaveData();
        }
        return false;
    });
    // 信息部分 tab 特效
    $('.tab_nav').click(function(){
        $(this).parents('.title').find('a.active').removeClass('active');
        $(this).addClass('active');
        var dataid = $(this).attr('dataid');
        var curDance = $('div.fincset').find('.dance');
        curDance.removeClass('dance');curDance.addClass('hidden');
        $('.'+dataid).attr('class',dataid+' dance');
    });
    // 删除帐单数据
    $('.del_btn').click(function(){
        var r = confirm('您确定要删除本条数据账单吗,如果此处删除的相关数据将无法恢复？');
        if(r == true){
            location.href = $(this).attr('dataurl');
        }
    });
    // 修改账单数据
    $('.edit_btn').click(function(){
        App.edit_btn(this);
        /*
        var tr = $(this).parent('td').parent('tr');
        var fno = tr.attr('dataid');
        tr.css({"border":"1px solid red","background":"green"});
        var curMap = $('#set_data_fm').find('table').find('tr[dataid="'+(App.getRowLen()-1)+'"]').find('input[name="map"]').length;
        if($('#fno_'+fno).length>0 || curMap>0){
            $('#addRowBtn').click();
        }
        App.editMode('M',fno);
        */
    });
    // 财务检索
    $('#svalue').blur(function(){
        var svalue = $('#svalue').val();
        svalue = $.trim(svalue);
        if(Cro.empty(svalue)) return;
        var skey = $('#search_key option:selected').val();
        $.post('/Conero/finance/fincset/ajax.html',{'item':'getSearchData','skey':skey,'svalue':svalue},function(data){
            // Cro.log(data);
            $('div.search').find('div.container').html(data)
        });
    });
});
var Cro = new Conero();
Cro.pageInit = function(){
    var tip = this.getJsVar('csl_delete');
    $('#page_console').append(tip);
}
// -> 页面app 
Cro.app = function(){
    var _app = function(_this){
        // 事务甲方改变监听
        this.masterChange = function(dom){
            var ts = $(dom);
            var master = ts.children(':selected').text();
            ts.next('.master').val(master);
        }
        // 事务乙方转换输入方式
        this.siderToggle = function(dom){
            var ts = $(dom);
            var td = ts.parent('td');
            var hasing = td.find('select').length;
            if(hasing>0){
                td.find('select').remove();
                td.find('input[type="hidden"]').remove();
                $('#sider_tpl').find('input').clone().prependTo(td);
            }else{
                td.find('input').remove();
                $('#sider_tpl').find('select').clone().prependTo(td);
                ts.before('<input type="hidden" name="sider" class="sider">');
            }
        }
        // 事务乙方改变监听
        this.siderChange = function(dom){
            var ts = $(dom);
            var sider = ts.children(':selected').text();
            var hasing = ts.next('.sider').length;//alert(hasing);
            if(hasing > 0) ts.next('.sider').val(sider);
            else{
                ts.after('<input type="hidden" name="sider" class="sider" value="'+sider+'">');
            }
        }
        // 表单编辑模式
        this.editMode = function(mode,fno){
            if(mode == 'A'){
                if(this.getMode() == 'M'){
                    $('#addRowBtn').removeAttr('disabled');
                    $('#delRowBtn').removeAttr('disabled');
                    $('#saveBtn').text('新增');
                    $('#saveBtn').attr('mode','A'); 
                    $('#backModeA').remove();
                    $('.finc_map').remove();
                }
            }
            else if(mode == 'M'){
                if(this.getMode() == 'A'){
                    $('#addRowBtn').attr('disabled','disabled');
                    $('#delRowBtn').attr('disabled','disabled');
                    $('#saveBtn').text('修改');
                    $('#saveBtn').attr('mode','M'); 
                    if($('#backModeA').length == 0)
                        $('#saveBtn').after('<a href="javascript:void(0);" onClick="App.editMode(\'A\')" id="backModeA">退出修改</a>');     
                }           
                $.post('/Conero/finance/fincset/ajax',{'item':'getDataByNo','no':fno},function(data){
                    if('N' == data){}
                    else{
                        var cTr = App.getRowLen('ctr');
                        data = JSON.parse(data);
                        var id = 'fno_'+data['finc_no'];
                        // 不重复赋值
                        if($('#'+id).length>0) return;
                        for(var k in data){
                            if(k == 'master'){
                              cTr.find('[name="'+k+'"]').after('<input type="hidden" name="map" id="'+id+'" value="'+data['finc_no']+'" class="finc_map">');
                            }
                            cTr.find('[name="'+k+'"]').val(data[k]);
                        }
                    }
                });
            }
        }
        // 获取数据列
        this.getRowLen = function(index){
            var table = $('#set_data_fm').find('table');
            var tr = table.find('tr');            
            var len = tr.length-1;
            if(index == 'ctr') index = len;
            if(index && parseInt(index)<=len) return $(tr[index]);
            return len;
        }
        // 获取编辑模式
        this.getMode = function(){
            return $('#saveBtn').attr('mode');
        }
        // console 控制台信息输出
        this.putout = function(text,html,reset){
            html = html? html : '';
            $('div.fincset').find('p.title').find('[dataid="console"]').click();
            var time = (new Date()).sysdate();
            if(html){
                html = '<p>'+time+' => </p>'
                       + '<b>'+text+'</b>'
                       + '<div>'+text+'</div>'
                       ;
            }
            else{
                html = '<p>'+time+' => <b>'+text+'</b></p>'
            }

            if(reset) $('#page_console').html(html);
            else $('#page_console').prepend(html);
        }
        // 行必填检测
        this.rowCheck = function(rIdx){
            rIdx = rIdx? rIdx:(this.getRowLen() -1);
            var tr = $('#set_data_fm').find('table').find('tr[dataid="'+rIdx+'"]');
            var ipt = tr.find('[required="required"]');
            //_this.log(ipt);return true;
            var el;
            var jel;
            var len = ipt.length;
            for(var i=0; i<len;i++){
                el = ipt[i];
                if(el.value == ""){
                    jel = $(el);
                    jel.focus();
                    this.putout('【'+this.getDesc(jel)+'】不可为空！');
                    return true;
                }
                //console.log(el,el.value,Math.random());
            }
            return false;
        }
        // 获取字段的描述
        this.getDesc = function(name)
        {
            if(_this.empty(name)) return '';
            var obj = _this.is_object(name)? name:$('#set_data_fm').find(['name="'+name+'"']);
            var index = obj.parents('td').index();
            var trs = $('#set_data_fm').find('table tr');
            //_this.log(trs,index,obj);return '';
            var th = ($(trs[0]).find('th'))[index];
            return $(th).text();
        }
        // 获取多行保存数组
        this.getSaveData = function(){
            var table = $('#set_data_fm').find('table');
            //_this.log(form.serializeArray());
            //_this.log(form);return;
            var len = this.getRowLen();
            var savedata = new Array();
            var rowData;
            var tmp = ['select','input'];
            var input,select,el;
            for(var i=0;i<len;i++){
               input = table.find('tr[dataid="'+i+'"]').find('input');
               rowData = {};
               for(var x=0; x<input.length; x++){
                   el = input[x];
                   rowData[el.name] = el.value;
               }
               select = table.find('tr[dataid="'+i+'"]').find('select');
               for(x=0; x<select.length; x++){
                   el = select[x];
                   rowData[el.name] = el.value;
               }
               savedata.push(rowData);
            }
            _this.post('/conero/finance/fincset/doIndex',{'multi':'Y','mode':this.getMode(),'data':JSON.stringify(savedata)});
        }
        // 修改账单数据
        this.edit_btn = function(dom){
            var _this = $(dom);
            var tr = _this.parent('td').parent('tr');
            var fno = tr.attr('dataid');
            tr.css({"border":"1px solid red","background":"green"});
            var curMap = $('#set_data_fm').find('table').find('tr[dataid="'+(App.getRowLen()-1)+'"]').find('input[name="map"]').length;
            if($('#fno_'+fno).length>0 || curMap>0){
                $('#addRowBtn').click();
            }
            App.editMode('M',fno);
        }
        // 详情
        this.fabout_btn = function(dom){
            //var _this = $(dom);
        }
        //---------------------------------------------------------blur 事件群 begin
        this.usedateBlur = function(dom){
            var dom = $(dom);
        }
        this.nameBlur = function(dom){
            var dom = $(dom);
        }
        this.figureBlur = function(dom){
            var dom = $(dom);
        }
        this.siderBlur = function(dom){
            var dom = $(dom);
        }
        this.explaninBlur = function(dom){
            var dom = $(dom);
        }
        //---------------------------------------------------------blur 事件群 end 
    }
    return new _app(this);
};
var App = Cro.app();