$(function(){
    $('#_key_master_id').change(function(){
        /*
        var desc = $(this).find('option:selected').text();
        if(desc){
            $('#_key_master').val(desc);
        }
        */
        Cro.selectBindEl($(this),'#_key_master');       
    });    
    // 明细增加
    $('#addList_btn').click(function(){app.addList();});
    // 明细删减
    $('#delList_btn').click(function(){app.delList();})
    // 数据行提交
    $('#saveList_btn').click(function(){
        var form = $('form');
        // 利用表单的自身非空检测功能
        var notNull = form.find('[required]'),el,value;
        for(var i=0; i<notNull.length; i++){
            el = $(notNull[i]);
            if(el.is('select')){
                value = el.find('option:selected').val();
                if(value == "") return true;
            }
            else{
                value = el.val();
                if(value == "") return true;
            }
        }
        app.saveList();
        return false;
    });
    // 财务选择 porp窗
    $('#fset_porp_btn').click(function(){
        Cro.pupop({
            title: '财务账单选择',
            field:{use_date:'日期',finc_no:'hidden',name:'名称'},
            post:{table:'finc_set',order:'use_date desc',map:'center_id="'+Cro.getJsVar('cid')+'"'},
            single:true
        },{
            selected:function(){
                var row = $(this).parents('tr.datarow');
                var fno = row.find('.hidden').text();
                location.href = '/conero/finance/flist/edit/p/'+fno;
            }
        });
    });
    app._load();
});
var Cro = new Conero();
Cro._App = function(){
    function edit_App(_th){    
        /*
        // 未载入jQuery
        var dtl = $('#detail').find('table');
        var summy = $('#summary');
        */
        var dtl,summy;
        this._load = function(){
            dtl = $('#detail').find('table');
            summy = $('#summary');
        }
        // 新增明细
        this.addList = function(){            
            var len = this.getRowLen(true);
            var tr = dtl.find('tr[dataid="'+len+'"]');
            len = len +1;
            var html = '<tr dataid="'+len+'">'+tr.html()+'</tr>';
            dtl.append(html);       
            tr = dtl.find('tr[dataid="'+len+'"]').find(".rIndex").text(len);
            this.getRowLen(true);
        }
        // 删除明细
        this.delList = function(){
            var len = this.getRowLen(true);
            if(len == 1) return;
            var tr = dtl.find('tr[dataid="'+len+'"]');
            var shopNo = tr.find('[name="shop_no"]');
            if(shopNo.length>0){
                var delist = dtl.attr('delist');
                if(_th.empty(delist)){
                    delist = shopNo.val();
                }
                else{
                    delist += ','+shopNo.val();
                }
                dtl.attr('delist',delist);
            }
            len = len -1;
            tr.remove();
            this.getRowLen(true);
        }
        // 数据保存
        this.saveList = function(){
            var saveData = {'summy':'','dtl':'dtl'};
            saveData['summy'] = _th.formJson("#summary");
            var len = this.getRowLen(true);
            var detail = $('#detail'), tr,dtl = new Array();
            for(var i=1; i<=len; i++){
                tr = detail.find('[dataid="'+i+'"]');  
                dtl.push(_th.formJson(tr));              
            }
            saveData['dtl'] = dtl;
            var post = {'summy':JSON.stringify(saveData['summy']),'dtl':JSON.stringify(saveData['dtl'])};
            var delist = detail.find('table').attr("delist");
            if(!_th.empty(delist)) post['delist'] = delist;
            //_th.alertTest();return;
            _th.form('/conero/finance/flist/save.html',post);
        }
        // 获取列表长度
        this.getRowLen = function(rid){
            if(typeof(rid) == 'number'){
                dtl.attr("maxTr",rid);
                return rid;
            }
            else if(dtl){
                var len = dtl.find('tr').length - 1;
                dtl.attr("maxTr",len);
                return len;
            }
            return dtl.attr("maxTr");
        }
        this.rowObj = function(rid){
            var tr;
            if(_th.is_object(rid)) tr = rid;
            if(_th.is_string(rid)){// 数字计算
                tr = dtl.find['dataid="'+rid+'"'];
            }
            var _rowObj = function(){
                // 设置值
                this.value = function(name,value){
                    if(_th.undefind(value)) return tr.find('[name="'+name+'"]').val();
                    tr.find('[name="'+name+'"]').val(value);
                }
            }
            return new _rowObj();
        }
        // 事务乙方切换
        this.siderHelper = function(){
            var el = $('#_key_sider_id');
            var td = el.parents('td');
            var div = td.find(".input-group");
            var html = "";
            if(el.is('select')){
                html = Cro.cacheAttr("sider","ipthtml");
                Cro.cacheAttr("sider","selhtml",div.html());
                if(Cro.empty(html)){
                    html =  '<div class="input-group-addon"><a href="javascript:void(0);" id="_key_sider_helper" onClick="app.siderHelper()">I/S</a></div>'
                            + '<input type="text" name="sider" id="_key_sider_id" class="form-control input-sm" required>';
                    Cro.cacheAttr("sider","ipthtml",html);                
                }
                $("#_key_sider_id").remove();$("#_key_sider").remove();
                div.html(html);
            }        
            else{
                html = Cro.cacheAttr("sider","selhtml");
                $("#_key_sider_id").remove();
                div.html(html);
            }
        }
        // 事务乙方改变时监听
        this.siderChange = function(){Cro.selectBindEl('#_key_sider_id','#_key_sider');}
        this.singleCheck = function(dom){
            var _this = $(dom);
            var val = _this.val();
            if(_th.empty(val)) return;
            this.figureCal();
        }
        this.amountCheck = function(dom){
            var _this = $(dom);
            this.figureCal();
        }
        // 资金自动计算
        this.figureCal = function(){
            var detail = $('#detail'), tr,
                len = this.getRowLen(true),
                dtl = new Array(),figure = 0,single,amount;
            for(var i=1; i<=len; i++){
                tr = detail.find('[dataid="'+i+'"]');  
                single = parseFloat(tr.find('[name="single"]').val());
                amount = parseInt(tr.find('[name="amount"]').val());
                figure = figure + single*amount; 
            }
            $('#_key_figure').val(figure);
            $('#_key_figure_show').html(figure);
            return figure;
        }
    }
    return new edit_App(this);
}
var app = Cro._App();