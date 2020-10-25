jQuery.noConflict();

function save(){
  var linkPhone = $.trim($('#linkPhone').val());
  if(linkPhone==''){
    WST.msg('请填写联系方式','info');
    return;
  }
  var linkman = $.trim($('#linkman').val());
  if(linkman==''){
    WST.msg('请填写联系人','info');
    return;
  }
  var applyIntention = $.trim($('#applyIntention').val());
  if(applyIntention == ''){
      WST.msg('请填写营业范围','info');
      return;
  }
  var param = {};
  param.linkman = linkman;
  param.linkPhone = linkPhone;
  param.applyIntention = applyIntention;
  $.post(WST.U('mobile/shopapplys/add'),param,function(data){
    var json = WST.toJson(data);
    if(data.status==1){
      location.reload();
    }else{
      WST.msg(json.msg,'info');
    }
  });
}