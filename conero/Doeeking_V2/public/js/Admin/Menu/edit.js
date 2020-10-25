$(function(){app.pageInit();});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var formGrid,editMode;
    var _self = this;
    this.pageInit = function(){
        var mode = _self.getMode();
        var table = '#set_menu_table';
        var pk = 'listno';
        // M 模式下 描红已经存在的数据
        if(mode == 'M'){
            var pks = $(table).find('input[name="'+pk+'"]');
            var tr,clsStr;
            for(var k=0; k<pks.length; k++){
                tr = $(pks[k]).parents('tr');
                clsStr = (k%2 == 0)?'warning' : 'success';
                tr.attr("class",clsStr);
            }
        }
        // 表单监视器
        formGrid = Cro.formListEvent({
            table:table,
            form:'#set_menu_form',
            pk:  pk
        },{
            afterAddRow: function(lastRow){
                if(mode == 'M'){
                    lastRow.find('input[name="descrip"]').val('');
                    lastRow.find('input[name="url"]').val('');
                    lastRow.find('input[name="groupid"]').attr("readonly",true);
                }
                else if(mode == 'A'){
                    var dataid = lastRow.attr("dataid");
                    dataid = parseInt(dataid) - 1;
                    var currRow = lastRow;
                    lastRow = $('tr[dataid="'+dataid+'"]');
                    lastRow.find('input[name="groupid"]').attr("readonly",true);
                    var gid = lastRow.find('input[name="groupid"]').val();
                    currRow.find('input[name="groupid"]').val(gid);
                    currRow.find('input[name="groupid"]').attr("readonly",true);
                }
            }
        });
        // 自动读取模块信息
        $('#module_name_btn').click(function(){
            var md = $('#module_name').val();
            if(th.empty(md)) return th.modal_alert('模块名称为空，自动获取数据失败！');
            $.post('/conero/admin/menu/ajax.html',{'__:':th.bsjson({item:'get_menu_relymd',name:md,'$rd':Math.random()})},function(data){
                var addRowFn = function(tr){
                    tr.find('[name="groupid"]').val(md);
                    tr.find('[name="descrip"]').val(data[i]['node_name']);
                    tr.find('[name="url"]').val(data[i]['url']);
                };
                for(var i=0; i<data.length; i++){
                    if(i == 0) addRowFn(formGrid.getRow());
                    else formGrid.addRow(addRowFn);
                }
                // formGrid.selCkbox();
            });
        });
    }
    // 获取编辑模式
    this.getMode = function(){
        if(th.empty(editMode)){
            var gid = th.getUrlBind('groupid');
            editMode = gid? 'M':'A';
        }
        return editMode;
    }
});