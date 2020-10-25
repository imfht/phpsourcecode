$(document).ready(function(){
    bannerImageToggle();
});

function bannerImageToggle(){
    $('.gallery-home-banner').hover(function(){
        $('.gallery-home-banner .item .mask').fadeToggle(50);
        $('.gallery-home-banner .item .text').fadeToggle(50);
    });
}

function galleryImageFadeOut(id1,id2,id3){
     $(id1).hide();
     $(id2).hide();
     $(id3).hide();
}

function galleryImageFadeIn(id1,id2,id3){
     $(id1).show();
     $(id2).show();
     $(id3).show();
}

function galleryHomeCityImageHide(id1,id2){
    $(id1).hide();
    $(id2).hide();
}

function galleryHomeCityImageShow(id1,id2){
    $(id1).show();
    $(id2).show();
}

function shareListHover(id){
    $(id).css('background-color','rgba(0,0,0,0.02)');
}
function shareListHoverOut(id){
    $(id).css('background','none');
}
function accountDownImgShow(id){
    // $(id).parent('div').parent('div').css('background-color',"#f5f5f5");
    $(id).removeClass("hidden");
}
function accountDownImgOut(id){
    // $(id).parent('div').parent('div').css('background-color',"#fff");
    $(id).addClass("hidden");
}
function accountDownTrHide(){
    $('.account-down-tr').hide();
}
function accountDownTrToggle(id){
    $(id).nextAll('.account-down-tr').hide(400);
    $(id).prevAll('.account-down-tr').hide(400);
    $(id).slideToggle('slow');
}
function changeNavBottomColor(){
    var url=window.location.href.split("/");
    var cid='#c1';
    var sidTemp='#all';
    var cidPos;
     for(var i=url.length;i>0;i--){
        if(url[i]=='sid'){
             sidTemp=url[i+1];
             cidPos=i-1;
            break;
        }
     }
       var sid='#'+sidTemp;
       var sidDivId='#'+sidTemp+'-div';
       $('#all-div').addClass("hidden");
       $('#address-div').addClass("hidden");
       $('#search-div').addClass("hidden");
       $(sidDivId).removeClass('hidden');
       if(url[cidPos-1]=='cid'){
        $('#c1').addClass('hidden');
        $('#c2').addClass('hidden');
        $('#c3').addClass('hidden');
         cid='#'+url[cidPos];
         $(cid).removeClass("hidden");
       }
}
function countDown(secs,surl){           
 var jumpTime = $('#jumpTime');  
 jumpTime.html(secs);    
 if(--secs>0){       
     setTimeout("countDown("+secs+",'"+surl+"')",1000);       
     }       
 else{         
     location.href=surl;       
     }       
 }       
//kineditor 在线编辑器
function kineditorWork(){

    var editor;
    KindEditor.ready(function(K) {
    editor = K.create('textarea[name="content"]', {
        themeType : 'simple',
        resizeType: 1,
        height : "200px", //编辑器的高度为100px
        width:"100%",
        filterMode : false, //不会过滤HTML代码
        dialogAlignType:"page",
        items: [
            'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
            'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
            'insertunorderedlist', '|', 'emoticons', 'image', 'link'
        ]
        });
    }); 
}
function noteDiscussLike(userid,discussid){
        var postUrl=appUrl+"/Home/Index/noteDiscussLike";
        $.post(postUrl,{
            'discussid':discussid,
            'userid':userid,
                },function(data){
                    //2 -  
                    //1 +
                     if(data.updateFlag==1){
                       $('#'+discussid+'span').text(data.discusslikecount);
                       $('#'+discussid).removeClass("fa-thumbs-o-up");
                       $('#'+discussid).addClass("fa-thumbs-up");
                    }else if(data.updateFlag==2){
                       $('#'+discussid+'span').text(data.discusslikecount);
                       $('#'+discussid).removeClass("fa-thumbs-up");
                       $('#'+discussid).addClass("fa-thumbs-o-up");
                    }
          });
}
function noteCollectWork(userid,noteid,usernotename){
    var postUrl=appUrl+"/Home/Index/noteCollectWork";
    if(userid==0){
        window.location.href=appUrl+"/Home/User/index/";
    }
    $.post(postUrl,{
        'noteid':noteid,
        'userid':userid,
        'usernotename':usernotename,
            },function(data){
                //2 -  
                //1 +
                 if(data.updateFlag==1){
                   $('#'+noteid+'font').text(data.notecollectcount);
                   $('#'+noteid+'small').text('取消收藏 → ');
                   $('.'+noteid+'-id').attr("title","取消收藏");
                   $('.'+noteid+'-id').removeClass("fa-heart-o");
                   $('.'+noteid+'-id').addClass("fa-heart");
                }else if(data.updateFlag==2){
                   $('#'+noteid+'font').text(data.notecollectcount);
                   $('#'+noteid+'small').text('点击收藏 → ');
                   $('.'+noteid+'-id').attr("title","点击收藏");
                   $('.'+noteid+'-id').removeClass("fa-heart");
                   $('.'+noteid+'-id').addClass("fa-heart-o");
                }else if(data.updateFlag==3){
                    $('#collectWarningNoPersonal').removeClass("hidden");
                }
      });
}
function noteDiscussDelete(userid,discussid){
    var postUrl=appUrl+"/Home/Index/noteDiscussDelete";
        $.post(postUrl,{
            'discussid':discussid,
            'userid':userid,
                },function(data){
                    //0 error  
                    //1 success
                     if(data.updateFlag==1){
                        $divHtml="<div class='media'><div class='media-body'><h4 ><font color='#BBB'><s>该评论已删除</s></font></h4></div></div><hr>";
                        $('#'+discussid+"-media-div").html($divHtml);
                    }else{
                        ;
                    }
          });
}
function judgeInputValue(){
  $('#budgetBtn').click(function(){
    var inputValue=$('#budgetInput').val();
    if((/^(\+|-)?\d+$/.test( inputValue ))&& inputValue>0){  
      $('#messageSmall').html('<font color="red">输入正确！<font>');
    }else{  
        $('#messageSmall').html('<font color="red">只能输入正整数！<font>');
        return false;  
    }  
  });
}

function accountIndexShowPie(){
    var postUrl=appUrl+"/Home/Account/indexShowPie/";
        $.post(postUrl,{
            'showPieRequest':1,
                },function(data){
                    if(eval(data['pieArray']).length <1){
                      $html="<div class='col-md-12 '><i class='fa fa-warning fa-2x'></i>  <font>本月无消费，无比例图！</font></div>";
                      $('#noShowPie').html($html);
                    }else{
                      // alert(data[0].label);
                        var html='';
                        var i=0;
                        for(i=0;i<eval(data['pieArray']).length;i++){
                         html=html+'<li class="list-group-item" style="background-color:'+data['pieArray'][i].highlight+';"><font>'+data['pieArray'][i].label+'</font><font class="pull-right">：<i class="fa fa-rmb"></i> '+data['pieArray'][i].value+'</font></li>';
                        }
                        $('#pieShowItem').html(html);
                        var ctx = document.getElementById("chart-area1").getContext("2d");
                        window.myPie = new Chart(ctx).Pie(data['pieArray']);
                        var ctx = document.getElementById("chart-area2").getContext("2d");
                        window.myPie = new Chart(ctx).Pie(data['pieArray']);
                    }
                    // alert(data['barArray'].datasets[0].data[0]);
                    $html='<div class="text-center col-md-3 visible-lg">￥ '+data['barArray'].datasets[0].data[0]+'</div><div class="text-center col-md-3 visible-lg">￥ '+data['barArray'].datasets[0].data[1]+'</div><div class="text-center col-md-3 visible-lg">￥ '+data['barArray'].datasets[0].data[2]+'</div><div class="text-center col-md-3 visible-lg">￥ '+data['barArray'].datasets[0].data[3]+'</div>';
                    $('#barItemMoney').html($html);
                    var ctx = document.getElementById("chart-area-bar").getContext("2d");
                      window.myBar = new Chart(ctx).Bar(data['barArray'], {
                        responsive : true
                    });


                    var ctx = document.getElementById("chart-area-line").getContext("2d");
                      window.myBar = new Chart(ctx).Line(data['lineArray'], {
                        responsive : true
                    });
                    html='<div class="list-group-item"><i class="fa fa-clock-o"></i>&nbsp;时间：<font class="pull-right">'+data['monthMostMoney'].time+' 周'+data['monthMostMoney'].week+'</font></div><div class="list-group-item"><i class="fa fa-rmb"></i>&nbsp; 金额：<font class="pull-right" color="red">'+data['monthMostMoney'].money+'</font></div>';
                    $('#monthMostMoney').html(html);
          });
}
function allAccountReportInput(type){
  if(type=='todayMonth'){
     var value=$('#pickMonthInput').val();
     allAccountReport(type,value);
  }else if(type=='todayYear'){
     var value=$('#pickYearInput').val();
     allAccountReport(type,value);    
  }

}
function allAccountReport(type,value1){
  var postUrl=appUrl+"/Home/Account/showAllReport/";
  if(type=="todayMonth" || type=="pickMonth" || type=="pickYear" || type=="todayYear" || type=="todayQuarter"){
      todayMonth=value1;
      $('#fade1').hide();
      $('#fade2').hide();
      $('#fade3').hide();
      $('#dataFlagDiv .jumbotron').fadeOut();
      $('#pieShowLg').html('<canvas id="chart-area1" width="150px" height="150px"/>');
      $('#pieShowXs').html('<canvas id="chart-area2" width="150px" height="150px"/>');
      $('#canvas-holder-line').html(' <canvas id="chart-area-line" width="300px" heigth="50px"></canvas>');
      $('#pieShowItem').html('');
      $('#listItem').html('');
      $.post(postUrl,{
            'showType':type,
            'showValue':value1
                },function(data){
                  if(data==0){
                  $('#dataFlagDiv .jumbotron').fadeIn();
                  // $('.dataFlagDiv').hide();
                  return false;
                  }else{
                  $('#fade1').show();
                  $('#fade2').show();
                  $('#fade3').show();
                  if(type=="todayQuarter"){
                    data['monthHeading'].dateHeading="本季度";
                  }
                  $('#dateHeading').html(data['monthHeading'].dateHeading);
                  $('#countHeading').html(data['monthHeading'].accountcount);
                  $('#moneyHeading').html(data['monthHeading'].moneycount);
                  var ctx = document.getElementById("chart-area1").getContext("2d");
                  window.myPie = new Chart(ctx).Pie(data['monthPie']);
                  var ctx = document.getElementById("chart-area2").getContext("2d");
                  window.myPie = new Chart(ctx).Pie(data['monthPie']);
                  var html='';
                  for(i=0;i<eval(data['monthPie']).length;i++){
                   html=html+'<li class="list-group-item" style="background-color:'+data['monthPie'][i].highlight+';"><font>'+data['monthPie'][i].label+'</font><font class="pull-right">：<i class="fa fa-rmb"></i> '+data['monthPie'][i].value+'</font></li>';
                  }
                  $('#pieShowItem').html(html);
                  if(type=="todayMonth"){
                    var titleHtml="每 日 消费情况";
                  }else if(type=="todayYear" || type=="todayQuarter"){
                    var titleHtml="每 月 消费情况";
                  }
                 
                  $('#downTitle').html(titleHtml);
                  titleHtml="详细消费记录";
                  $('#downItemTitle').html(titleHtml);
                  var ctx = document.getElementById("chart-area-line").getContext("2d");
                  window.myBar = new Chart(ctx).Line(data['monthLine'], {
                    responsive : true
                   });
                  html="";
                  for(var i=0;i<eval(data['monthRows']).length;i++){
                    html=html+'<div onclick="accountDownTrToggle(&apos;#monthAccountTr'+data['monthRows'][i].accountid+'&apos;);">'+
                           '<div class="ui-grid-b">'+
                              '<div class="ui-block-a text-center" ><li class="no-border list-group-item over-hidden"><font>'+data['monthRows'][i].accountname+'</font></li></div>'+
                              '<div class="ui-block-b text-center"><li class="no-border list-group-item over-hidden" ><font color="#FF0066">'+data['monthRows'][i].typename+'</font></li></div>'+
                              '<div class="ui-block-c text-center"><li class="no-border list-group-item over-hidden"><font color="#CC3366"><strong>'+data['monthRows'][i].accountmoney+'</strong></font> <i class="fa fa-caret-down"></i></li></div>'+
                          '</div>'+
                      '</div>'+
                        '<div class="account-down-tr well " hidden id="monthAccountTr'+data['monthRows'][i].accountid+'" >'+
                           ' <div class="list-group">'+
                             ' <div class="list-group-item">'+
                                '<h5><small>账目名称: '+data['monthRows'][i].accountname+'</small></h5>'+
                            '  </div>'+
                            '  <div class="list-group-item">'+
                            '    <h5><small>账目日期: '+data['monthRows'][i].accountdate+'</small></h5>'+
                             ' </div>'+
                             ' <div class="list-group-item">'+
                             '   <h5><small>记录时间: '+data['monthRows'][i].notetime+'</small></h5>'+
                             ' </div>'+
                             ' <div class="list-group-item">'+
                             '   <h5><small>相关备注： '+data['monthRows'][i].accountother+'</small></h5>'+
                             ' </div>'+
                             ' <div class="list-group-item">'+
                             '  <a href='+appUrl+'/Home/Account/deleteAccount/id/'+data['monthRows'][i].accountid+' data-ajax="false" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-xs width100"><font class="color-fff">删除</font></a>'+
                              '  <h5><small><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</small></h5>'+
                             ' </div>'+
                           ' </div>'+
                       ' </div>';
                  }
                  $('#listItem').html(html);
                }//else end
          }//fuunction end

        );
      // alert(todayMonth);
  }else if(type=="pickLength"){
      todayMonth=value1;
      var value2=$('#pickLenghInput2').val();
      value1=$('#pickLenghInput1').val();
      $('#fade1').hide();
      $('#fade2').hide();
      $('#fade3').hide();
      $('#dataFlagDiv .jumbotron').fadeOut();
      $('#pieShowLg').html('<canvas id="chart-area1" width="150px" height="150px"/>');
      $('#pieShowXs').html('<canvas id="chart-area2" width="150px" height="150px"/>');
      $('#canvas-holder-line').html(' <canvas id="chart-area-line" width="300px" heigth="50px"></canvas>');
      $('#pieShowItem').html('');
      $('#listItem').html('');
      $.post(postUrl,{
            'showType':type,
            'showValue':value1,
            'showValue2':value2,
                },function(data){
                  if(data==0){
                  $('#dataFlagDiv .jumbotron').fadeIn();
                  // $('.dataFlagDiv').hide();
                  return false;
                  }else{
                  $('#fade1').show();
                  $('#fade2').show();
                  $('#fade3').show();

                  $('#dateHeading').html(data['monthHeading'].dateHeading);
                  $('#countHeading').html(data['monthHeading'].accountcount);
                  $('#moneyHeading').html(data['monthHeading'].moneycount);
                  var ctx = document.getElementById("chart-area1").getContext("2d");
                  window.myPie = new Chart(ctx).Pie(data['monthPie']);
                  var ctx = document.getElementById("chart-area2").getContext("2d");
                  window.myPie = new Chart(ctx).Pie(data['monthPie']);
                  var html='';
                  for(i=0;i<eval(data['monthPie']).length;i++){
                   html=html+'<li class="list-group-item" style="background-color:'+data['monthPie'][i].highlight+';"><font>'+data['monthPie'][i].label+'</font><font class="pull-right">：<i class="fa fa-rmb"></i> '+data['monthPie'][i].value+'</font></li>';
                  }
                  $('#pieShowItem').html(html);
                  if(type=="todayMonth"){
                    var titleHtml="每 日 消费情况";
                  }else if(type=="todayYear" || type=="todayQuarter"){
                    var titleHtml="每 月 消费情况";
                  }
                 
                  $('#downTitle').html(titleHtml);
                  titleHtml="详细消费记录";
                  $('#downItemTitle').html(titleHtml);
                  var ctx = document.getElementById("chart-area-line").getContext("2d");
                  window.myBar = new Chart(ctx).Line(data['monthLine'], {
                    responsive : true
                   });
                  html="";
                  for(var i=0;i<eval(data['monthRows']).length;i++){
                    html=html+'<div onclick="accountDownTrToggle("#monthAccountTr'+data['monthRows'][i].accountid+'");">'+
                           '<div class="ui-grid-b">'+
                              '<div class="ui-block-a text-center" ><li class="no-border list-group-item over-hidden"><font>'+data['monthRows'][i].accountname+'</font></li></div>'+
                              '<div class="ui-block-b text-center"><li class="no-border list-group-item over-hidden" ><font color="#FF0066">'+data['monthRows'][i].typename+'</font></li></div>'+
                              '<div class="ui-block-c text-center"><li class="no-border list-group-item over-hidden"><font color="#CC3366"><strong>'+data['monthRows'][i].accountmoney+'</strong></font> <i class="fa fa-caret-down"></i></li></div>'+
                          '</div>'+
                      '</div>'+
                        '<div class="account-down-tr well " hidden id="monthAccountTr'+data['monthRows'][i].accountid+'" >'+
                           ' <div class="list-group">'+
                             ' <div class="list-group-item">'+
                                '<h5><small>账目名称: '+data['monthRows'][i].accountname+'</small></h5>'+
                            '  </div>'+
                            '  <div class="list-group-item">'+
                            '    <h5><small>账目日期: '+data['monthRows'][i].accountdate+'</small></h5>'+
                             ' </div>'+
                             ' <div class="list-group-item">'+
                             '   <h5><small>记录时间: '+data['monthRows'][i].notetime+'</small></h5>'+
                             ' </div>'+
                             ' <div class="list-group-item">'+
                             '   <h5><small>相关备注： '+data['monthRows'][i].accountother+'</small></h5>'+
                             ' </div>'+
                             ' <div class="list-group-item">'+
                             '   <a href='+appUrl+'/Home/Account/deleteAccount/id/'+data['monthRows'][i].accountid+' data-ajax="false" onclick="javascript:return myNoteDeleteConfirm();" class="btn btn-sm btn-danger btn-xs width100"><font class="color-fff">删除</font></a>'+
                              '  <h5><small><i class="fa fa-info"></i>  ： 删除后相应账目自动计算！</small></h5>'+
                             ' </div>'+
                           ' </div>'+
                       ' </div>';
                  }
                  $('#listItem').html(html);
                }//else end
          }//fuunction end

        );
  }else{
  }
}

function showTypeOption(id){
  var postUrl=appUrl+"/Home/Account/showTypeOption/";
        $.post(postUrl,{
            'showTypeOptionRequest':1,
                },function(data){
                  var html="";
                  for(i=0;i<eval(data.length);i++){
                    html=html+'<option value='+(i+1)+'>'+data[i].typename+'</option>';
                  }
                  $(id).html(html);
                });
}

function myNoteDeleteConfirm(){
  var msg = "操作不可逆,确认删除？"; 
  if (confirm(msg)==true){ 
    return true; 
  }else{ 
    return false; 
  } 
}

function myNoteShareConfirm(){
  var msg = "操作不可逆,可能造成相关评论或相关数据的失效！"; 
  if (confirm(msg)==true){ 
    return true; 
  }else{ 
    return false; 
  } 
}
