var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'名称', name:'name', width: 60},
            {title:'描述', name:'hookRemarks', width: 300},
            {title:'对应插件', name:'addons' ,width:70, align:'center'},
            {title:'操作', name:'op' ,width:20, align:'center',renderer: function(val,item,rowIndex){
                if(item['addons']!=''){
                    return '<a class="btn btn-blue btn-mright" href="javascript:hookBox('+item['hookId']+',\''+item['addons']+'\')"><i class="fa fa-search"></i>调整顺序</a>';
                }
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/hooks/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    hooksQuery(p);
}

function hookBox(id,addons){
    addons = addons.replace(',,',',');
    var str = addons.split(',');
    var h = (str.length<=2)?220:(str.length-2)*15+220;
    h = (h>500)?500:h;
    var html = ['<table class="hook">'];
    for(var i=0;i<str.length;i++){
        html.push('<tr><td width="60%" class="hookval" val="'+str[i]+'">'+str[i]+'</td><td><button type="button" class="btn btn-primary btn-mright" onclick="javascript:moveUp(this)">上移</button><button type="button" class="btn btn-primary btn-mright" onclick="javascript:moveDown(this)">下移</button></td></tr>')
    }
    html.push('</table>');
    var w=WST.open({
        type: 1,
        title:"插件监听顺序调整",
        content:html.join(''),shade: [0.6, '#000'],
        area: ['400px', h+'px'],
        btn: ['确定'],
        yes: function(index, layero){
            var hook = [];
            $('.hookval').each(function(){
                hook.push($(this).attr('val'));
            });
            if(hook.length<=1){
                WST.msg('无需调整插件监听顺序', {icon: 2});   
                return; 
            }
            var ll = WST.msg('数据处理中，请稍候...');
            $.post(WST.U('admin/hooks/changgeHookOrder'),{id:id,hook:hook.join(',')},function(data){
                layer.close(w);
                layer.close(ll);
                var json = WST.toAdminJson(data);
                if(json.status>0){
                    WST.msg(json.msg, {icon: 1});
                    hooksQuery();
                }else{
                    WST.msg(json.msg, {icon: 2});
                }
            });
        }
    });
}
function moveUp(obj){
    var tr = $(obj).parents("tr");
    if (tr.index() != 0)tr.prev().before(tr);
}
function moveDown(obj){
    var down = $(obj).parents("tr").parent();
    var len = down.children().size();
    var tr = $(obj).parents("tr");
    if (tr.index() != len - 1)tr.next().after(tr);
}
//查询
function hooksQuery(p){
    p=(p<=1)?1:p;
	var query = WST.getParams('.query');
    query.page = p;
	mmg.load(query);
}

