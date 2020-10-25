



function storeSalestatistics(p){
  $('#loading').show();
  var params = WST.getParams('.s-query');
  params.page = p;
  $.post(WST.U('shop/stores/pageQuerySalestatistics'),params,function(data,textStatus){
    $('#loading').hide();
      var json = WST.toJson(data);
      console.log(json);
      $('.j-order-row').remove();
      if(json.status==1){
        json = json.data;
        if(params.page>json.last_page && json.last_page >0){
            storeSalestatistics(json.last_page);
            return;
        }
        var gettpl = document.getElementById('tblist').innerHTML;
        laytpl(gettpl).render(json.data, function(html){
            $("#statOderMoney").html(json.totalMoney);
            $(html).insertAfter('#loadingBdy');
            $('.gImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+WST.conf.GOODS_LOGO});
        });
        laypage({
            cont: 'pager', 
            pages:json.last_page, 
            curr: json.current_page,
            skin: '#1890ff',
            groups: 3,
            jump: function(e, first){
               if(!first){
                   storeSalestatistics(e.curr);
               }
            } 
        });
     } 
  });
}