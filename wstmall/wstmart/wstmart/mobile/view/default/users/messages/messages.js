//消息列表
function getMessages(){
  $('#Load').show();
  loading = true;
  var param = {};
  param.pagesize = 12;
  param.page = Number( $('#currPage').val() ) + 1;
  $.post(WST.U('mobile/messages/pageQuery'), param, function(data){
      var json = WST.toJson(data);
      var mhtml = '';
      if(json && json.data && json.data.length>0){
          $('#footer').show();
          var gettpl = document.getElementById('msgList').innerHTML;
          laytpl(gettpl).render(json.data, function(html){
            $('#info-list').append(html);
          });
          $('#currPage').val(json.current_page);
          $('#totalPage').val(json.last_page);
          
      }else{
    	  mhtml += '<div style="text-align:center"><div class="wst-prompt-icon"><img src="'+ window.conf.MOBILE +'/img/nothing-message.png"></div><div class="wst-prompt-info"><p>暂无消息</p></div></div>';
		  $('.info-prompt').append(mhtml);
      $('#footer').hide();
      }
      loading = false;
      $('#Load').hide();
  });
}
//返回消息列表
function returnInfo(){
    $('#footer').show();
    $('#info_details').hide();
    $('#info_list').show();
    $('#nav-msg').show();
    $('.wst-info_detime').html('');
    $('.wst-info_decontent').html('');
    $('#info-list').empty();
    $('#currPage').val(0);
    $('#totalPage').val(0);
    getMessages();
}

// 全选
function checkAll(obj){
  var chk = $(obj).attr('checked');
  $('.active').each(function(k,v){
    $(this).prop('checked',chk);
  });
}
//消息详情
function getMsgDetails(id){
  $('#footer').hide();
	$('#info_list').hide();
	$('#info_details').show();
  $('#nav-msg').hide();
	$('.j-icon_'+id).addClass('wst-info_ico1').removeClass('wst-info_ico');
    $.post(WST.U('mobile/messages/getById'), {msgId:id}, function(data){
        var json = WST.toJson(data);
        if(json){
            $('.wst-info_detime').html(json.createTime);
            $('.wst-info_decontent').html(json.msgContent);
        }
        json = null;
    });
}
var msgIdsToDel=new Array();//要删除的消息的id 数组
//去删除商城消息
function toDelMsg(){
  var msgIds = new Array();
  $('.active').each(function(k,v){
    if($(this).attr('checked')){
      msgIds.push($(this).attr('msgid'));
    }
  });
  msgIdsToDel = msgIds;
  if(msgIds.join(',')==''){
    WST.msg('请选择要删除的消息','info');
    return false;
  }
  WST.dialog('确定要删除选中的消息吗？','delMsg()');
}
var vn ='';
//删除商城消息
function delMsg(){
  WST.dialogHide('prompt');
  $.post(WST.U('mobile/messages/del'), {ids:msgIdsToDel}, function(data){
      var json = WST.toJson(data);
      if(json.status==1){
		  WST.msg(json.msg,'success');
      $('#currPage').val(0)
      $('#info-list').html(' ');
      getMessages();
      }else{
    	  WST.msg(json.msg,'warn');
      }
  });
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
	getMessages();
    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	getMessages();
            }
        }
    });
});