function loadStat(){
    var h = WST.pageHeight();
    var cols = [
        {title:'商品', name:'goodsName', width: 300},
        {title:'商品编号', name:'goodsSn', width: 200},
        {title:'销量', name:'goodsNum', width: 30},
    ];

    mmg = $('.mmg').mmGrid({height: h-100,indexCol: true, cols: cols,method:'POST',
        url: WST.U('shop/reports/getTopSaleGoods'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid();
}
function loadGrid(){
    mmg.load({startDate:$('#startDate').val(),endDate:$('#endDate').val(),page:1});
}

function toExport(){
    var params = WST.getParams('.j-ipt');
    var box = WST.confirm({content:"您确定要导出该统计数据吗?",yes:function(){
        layer.close(box);
        location.href=WST.U('shop/reports/toExportTopSaleGoods',params);
    }});
}