var mmg;
function gridInit(id){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#month',
        type: 'month'
    });
    var h = WST.pageHeight();
    var cols = [
            {title:'会员', name:'userName', width: 160,renderer: function (val,item,rowIndex){
                return '<img src="'+item['userPhoto']+'" height="50"/>'+"【"+item['loginName']+"】"+WST.blank(item['userName']);
            }},
            {title:'最后签到时间', name:'createTime',width: 160},
            {title:'本月连续签到', name:'dataId',width: 60},
            {title:'累计签到数', name:'signCount',width: 40}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-87),indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/userscores/pageQueryByRanking'), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    }); 
}
function loadGrid(id){
	mmg.load({page:1,month:$('#month').val()});
}