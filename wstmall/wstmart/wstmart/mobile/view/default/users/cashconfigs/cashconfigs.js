jQuery.noConflict();
// 获取提现记录
function getCashConfigs(){
  $('#Load').show();
    loading = true;
    var param = {};
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/cashconfigs/pageQuery'), param, function(data){
        var json = WST.toJson(data.data);
        var html = '';
        if(json && json.data && json.data.length>0){
          var gettpl = document.getElementById('list').innerHTML;
          laytpl(gettpl).render(json.data, function(html){
            $('#listBox').append(html);
          });

          $('#currPage').val(json.current_page);
          $('#totalPage').val(json.last_page);
        }else{
           html += '<div class="wst-prompt-icon"><img src="'+ window.conf.MOBILE +'/img/no_data.png"></div>';
 	       html += '<div class="wst-prompt-info">';
 	       html += '<p>暂无数据</p>';
 	       html += '</div>';
          $('#listBox').html(html);
        }
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
    });
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
  getCashConfigs();
  WST.initFooter('user');
  // 弹出层
  $('#modal-large').css({'top':0,'margin-top':0});
  var h = WST.pageHeight();
  $("#frame").css('bottom','-'+h/2);
  var listh = h/2-106;
  $(".wst-fr-box .list").css('overflow-y','scroll').css('height',listh+'px');


    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	getCashConfigs();
            }
        }
    });
});





//新增或编辑提现账号
function editAddress(id,accBank){
    $('#accid').html('');
    $('#areaName').html('');
    $('#accTargetId').val('');
    $('#accUser').val('');
    $('#accNo').val('');
    $('#accAreaId').val('');
    $('#addresst').html('请选择地址');
    $('#addAddress').attr('onclick','javascript:saveConfig('+id+');');
    $('#addAddress').html('保存');
    if(id>0){
    	$('#header-title').html('修改提现账号');
        $.post(WST.U('mobile/cashConfigs/getById'), {id:id}, function(data){
            var info = WST.toJson(data);
            if(info){
                $('#accTargetId').val(info.accTargetId);
                $('.ui-checkbox').each(function(k, item){
                    var _obj = $(item).find('input');
                    var _val = _obj.attr('value');
                    if(info.accTargetId==_val){
                        $('#accid').html(_obj.data('value'));
                        _obj.prop('checked', true);
                    }
                })

                $('#accUser').val(info.accUser);
                $('#accNo').val(info.accNo);
                $('#accAreaId').val(info.accAreaId);
                $('#areaName').html($('#addr_'+id).val());
            }
            addressInfo= null;
        });
    }else{
    	$('#header-title').html('新增提现账号');
    }
    switchPage(1);
}

function switchPage(n){
	if(n==1){
		$('.js-account').hide();
		$('.js-account2').show();
	}else{
		$('.js-account2').hide();
		$('.js-account').show();
        $('#addAddress').attr('onclick','javascript:editAddress();');
        $('#addAddress').html('添加账户');
	}
}


/**
 * 循环创建地区
 * @param id            当前分类ID
 * @param val           当前分类值
 * @param className     样式，方便将来获取值
 */
WST.ITAreas = function(opts){
  opts.className = opts.className?opts.className:"j-areas";
  var obj = $('#'+opts.id);
  obj.attr('lastarea',1);
  $.post(WST.U('mobile/areas/listQuery'),{parentId:opts.val},function(data,textStatus){
       var json = WST.toJson(data);
       if(json.data && json.data.length>0){
         json = json.data;
           var html = [],tmp;
           var tid = opts.id+"_"+opts.val;
         var level = parseInt(obj.attr('level'),10);
         $('.area_'+level).addClass('hide');
         var level = level+1;
           html.push('<div id="'+tid+'" class="list '+opts.className+' area_'+level+'" areaId="0" level="'+level+'">');
         for(var i=0;i<json.length;i++){
           tmp = json[i];
             html.push("<p onclick='javascript:inChoice(this,\""+tid+"\","+tmp.areaId+","+level+");'>"+tmp.areaName+"</p>");
         }
           html.push('</div>');
         $(html.join('')).insertAfter('#'+opts.id);
         var h = WST.pageHeight();
         var listh = h/2-106;
         $(".wst-fr-box .list").css('overflow-y','scroll').css('height',listh+'px');
         $(".wst-fr-box .option").append('<p class="ui-nowrap-flex term active_'+level+' active" onclick="javascript:inOption(this,'+level+')">请选择</p>');
       }else{
         opts.isLast = true;
         opts.lastVal = opts.val;
         $('#accAreaId').val(opts.lastVal);
         var ht = '';
      $('.wst-fr-box .term').each(function(){
        ht += $(this).html();
      });
      $('#areaName').html(ht);
      dataHide();
       }
  });
}

//地址选择
function inOption(obj,n){
  $(obj).addClass('active').siblings().removeClass('active');
  $('.area_'+n).removeClass('hide').siblings('.list').addClass('hide');
  var level = $('#level').val();
  var n = n+1;
  for(var i=n; i<=level; i++){
    $('.area_'+i).remove();
    $('.active_'+i).remove();
  }
}

function inChoice(obj,id,val,level){
  $('#level').val((level+1));
  $(obj).addClass('active').siblings().removeClass('active');
  $('#'+id).attr('areaId',val);
  $('.active_'+level).removeClass('active').html($(obj).html());
  WST.ITAreas({id:id,val:val,className:'j-areas'});
}

function saveAccId() {
    var flag=false,chk;
    $('.active').each(function(k,v){

        if($(this).prop('checked')){
            flag = true
            $('#accTargetId').val($(this).val());
            $('#accid').html($(this).attr('data-value'));
        }
    });
    if(!flag){
        WST.msg('请选择账户类型');
        return;
    } else {
        dataHide(2)
    }
}


//弹框
function dataShow(n = 1){

    if (n==2) {
        jQuery('#cover').attr("onclick","javascript:dataHide(2);").show();
        jQuery('#frame1').show();
        jQuery('#frame1').animate({"bottom": 0}, 500);
    } else {
        jQuery('#cover').attr("onclick","javascript:dataHide();").show();
        jQuery('#frame').show();
        jQuery('#frame').animate({"bottom": 0}, 500);
    }



}
function dataHide(n = 1){
    var dataHeight = $("#frame").css('height');
    jQuery('#cover').hide();
    if(n == 2) {
        jQuery('#frame1').animate({'bottom': '-100%'}, 500);

    } else {

        jQuery('#frame').animate({'bottom': '-'+dataHeight}, 500);

        setTimeout(function(){
            jQuery('#frame').hide();
        },500);
    }

}



//保存
function saveConfig(cId){
    var accUser = $('#accUser').val();
    var accNo = $('#accNo').val();
    var areaId = $('#areaId').val();
    var accAreaId = $('#accAreaId').val();
    var accTargetId = $('#accTargetId').val();

    if(accTargetId==''){
      WST.msg('请选择账户类型','info');
      $('#accTargetId').focus();
        return false;
    }

    if(accAreaId==''){
      WST.msg('请选择地址','info');
        return false;
    }

    if(accUser==''){
      WST.msg('持卡人不能为空','info');
      $('#accUser').focus();
        return false;
    }
    if(accNo==''){
      WST.msg('卡号不能为空','info');
        return false;
    }
    
    var param = {};
    param.id = cId;
    param.accAreaId = accAreaId;
    param.accUser = accUser;
    param.accNo = accNo;
    param.accTargetId = accTargetId;
  $('.wst-ad-submit .button').addClass("active").attr('disabled', 'disabled');
    var act = (cId>0)?'edit':'add';
    $.post(WST.U('mobile/cashconfigs/'+act), param, function(data){
        var json = WST.toJson(data);
        if( json.status == 1 ){
          WST.msg(json.msg,'success');
            setTimeout(function(){
            	location.href = WST.U('mobile/cashconfigs/index');
            },1000);
        }else{
          WST.msg(json.msg,'warn');
          setTimeout(function(){
              $('.wst-ad-submit .button').removeAttr('disabled').removeClass("active");
            },1500);
        }
        data = json = null;
    });
}

//删除提现账号
function del(id){
  WST.dialog('确定删除吗？','toDel('+id+')');
}
//删除提现账号
function toDel(id){
    $.post(WST.U('mobile/cashconfigs/del'), {id:id}, function(data){
        var json = WST.toJson(data);
        if(json.status==1){
          WST.msg(json.msg,'success');
            $('#listBox').html(' ');
            $('#currPage').val(0)
            getCashConfigs();
        }else{
          WST.msg(json.msg,'warn');
        }
        WST.dialogHide('prompt');
        data = json = null;
    });
}