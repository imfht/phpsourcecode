var global_menu = 0;
function control_menu(obj){
    var width = $(window).width();

    if(global_menu == 0){
        global_menu = 1;
        obj.innerHTML = '<i class="close_img iconfont">&#xe602;</i>';
        //document.getElementById('menu').style.width = '60px';
        //$('#main').width(width - 60 + 'px');
        $('#menu').animate({'width':'60px'},300);
        //$('#main').animate({'width':width - 60 + 'px'},300);
        if(document.all){

            $('#main').animate({'width': width - 70 + 'px'}, 300);
        }else {
            $('#main').animate({'width': width - 60 + 'px'}, 300);
        }
    }else{
        global_menu = 0;
        obj.innerHTML = '<i class="close_img iconfont">&#xe603;</i>&nbsp;&nbsp;隐藏菜单';
        //document.getElementById('menu').style.width = '160px';
        //$('#main').width(width - 160 + 'px');
        $('#menu').animate({'width':'160px'},300);
        if(document.all){

            $('#main').animate({'width': width - 170 + 'px'}, 300);
        }else {
            $('#main').animate({'width': width - 160 + 'px'}, 300);
        }

    }

}

function setDefaultValue(obj,id){
    for(var i in obj){
        var input = $('#'+id).find('input[name="'+i+'"]').length == 0 ? ($('#'+id).find('textarea[name="'+i+'"]').length == 0 ? ($('#'+id).find('select[name="'+i+'"]').length == 0 ? '' : $('#'+id).find('select[name="'+i+'"]')) : $('#'+id).find('textarea[name="'+i+'"]')) : $('#'+id).find('input[name="'+i+'"]');
        var type = '';
        if(input != ''){
            type  = input.attr('type');
        }
        if(input != ''){
            if(type == 'radio'){

                $.each(input,function(k,n){
                   if(n.value == obj[i]){
                       n.checked = true;
                   } else{
                       n.checked = false;
                   }
                });
            }else if(type == 'checkbox'){
                if(input.val() == obj[i]){
                    input.attr('checked','checked');
                } else {
                    input.removeAttr('checked');
                }
            }else if(typeof type == 'undefined' || type == ''){
                if(input.find('option').length != 0){
                    $.each(input.find('option'),function(k,n){
                        if(n.value == obj[i]){
                            $('#'+id).find(n).attr('selected','selected');
                        } else{
                            $('#'+id).find(n).removeAttr('selected');
                        }
                    });
                }else{
                    input.val(obj[i]);
                    input.html(obj[i]);
                }
            }else{
                input.val(obj[i]);
            }
        }else{
            $('#'+id).find('input[name="'+i+'[]"]').removeAttr('checked');
            var input = $('#'+id).find('input[name="'+i+'[]"]');
            $.each(input,function(k,n){
                if(typeof obj[i] == 'object' && obj[i] != null){
                    var str = '#' + obj[i].join('#') + '#';
                    if(str.indexOf(n.value) > -1){
                        n.checked = true;
                    }else{
                        n.checked = false;
                    }
                }
            });
        }
    }
}

function initWidth() {
    var width = $(window).width();
    $('#main').width(width - 162 + 'px');
}

$(document).ready(function(){
    initWidth();
});

$(window).resize(initWidth);

function close_nav(obj){
    $(obj.parentNode).remove();
}

function create_bg(){
    if($('#lock_bg').length != 0){
        $('#lock_bg').show();
        return false;
    }
    var lock_bg = document.createElement('div');
    lock_bg.className = 'lock_bg';
    lock_bg.setAttribute('id','lock_bg');
    lock_bg.style.height = $(window).height() + 'px';
    document.body.appendChild(lock_bg);
}
function remove_bg(){
    $('#lock_bg').remove();
}

function close_win(){
    $('#alert_win').fadeOut(500);
    remove_bg();
}
function move_win(){
    return false;
    $('#alert_win').css('margin-left','-400px');
    $('#alert_win').css('margin-top','-250px');
    $('#alert_win_title').mousedown(function(e){
        var x0 = 0,x1 = 0,y0 = 0,y1 = 0;
        var e = e || window.event;
        x0 = e.clientX;
        y0 = e.clientY;
        $('#alert_win').mousemove(function(e){
            var e = e || window.event;
            y1 = e.clientY;
            x1 = e.clientX;
            var left = parseInt($('#alert_win').css('margin-left'));
            var top = parseInt($('#alert_win').css('margin-top'));
            var ml = left + (x1  - x0);
            var mt = top + (y1 - y0);
            $('#alert_win').css({'margin-left': ml + 'px','margin-top' : mt + 'px'});
            x0 = x1;
            y0 = y1;
        });
        $(document).mouseup(function(){
            $('#alert_win').unbind('mousemove');
        });
    })
}
/*
弹出窗口
*/
var global_win_lock = 0;
function open_win(obj){
    if(typeof obj != 'object'){
        alert('参数必须是一个对象！');
        return false;
    }
    if(typeof obj.title == 'string'){
        $('#alert_win .title_text').html(obj.title);
    }
    var width = typeof obj.width != 'undefined' ? obj.width : 800;
    var height = typeof obj.height != 'undefined' ? obj.height : 600;
    var center_height = height - 94;
    if(typeof obj.html == 'string'){
        if(obj.html.search('<') < 0){
            $('#alert_win_content').html('<iframe class="iframe_style" src="'+obj.html+'" border="none" width="100%" height="'+center_height+'"></iframe>');
        }else{
            $('#alert_win_content').html(obj.html);
        }
    }else if(typeof obj.html == 'object'){
        $('#alert_win_content').html('');
        $('#alert_win_content').append(obj.html);
    }
    if(typeof obj.show_footer != 'undefined' && obj.show_footer == false){
        $('#alert_win_bottom').hide();
        center_height += 41;
    }else{
        $('#alert_win_bottom').show();
    }

    if(typeof obj.show_header != 'undefined' && obj.show_header == false){
        $('#alert_win_title').hide();
        center_height += 51;
    }else{
        $('#alert_win_title').show();
    }
    $('#alert_win_content').height(center_height);
    $('#alert_win').width(width).height(height).css({'margin-top':-(height/2) + 'px','margin-left':-(width/2)+'px'});
    $('#alert_win').fadeIn(500);
    if(typeof obj.top != 'undefined'){
        $('#alert_win').css({'top':obj.top});
    }
    create_bg();
    move_win();
    $('#alert_win_clear,#alert_win_submit').unbind('click');
    $('#alert_win_submit').click(function(){
        if(global_win_lock == 1){
            return false;
        }
        global_win_lock = 1;
        if(typeof obj.close_on == 'undefined' || obj.close_on == false) close_win();
        if(typeof obj.request_url == 'string'){
            if(typeof obj.accept != 'function'){
                obj.accept = function(){};
            }
            if(typeof obj.cancel != 'function'){
                obj.cancel = function(){};
            }
            if(typeof obj.download != 'undefined' && obj.download){
                $('#alert_win_content').append('<div style="display: none"><iframe src="'+obj.request_url+'?'+get_form_data('alert_win_content')+'"></iframe></div> ');
                obj.accept('1');

                global_win_lock = 0;
            }else{
                send_request(obj.request_url,get_form_data('alert_win_content'),obj.accept,obj.cancel);
            }
        }
    });
    $('#alert_win_clear').click(function(){
        if(typeof obj.close_on == 'undefined' || obj.close_on == false) close_win();
        if(typeof obj.cancel == 'function'){
            obj.cancel('error');
        }
    });
}
function get_form_data1(id){
    var request_param = '';
    var input = $('#' + id + ' input');
    if(input != null){
        $.each(input,function(i,n){
            if(typeof n.name != 'undefined' && n.name != '' ){
                if(((n.type == 'checkbox' || n.type == 'radio') && n.checked == true) || (n.type != 'checkbox' && n.type != 'radio')){
                    request_param += n.name + '=' + encodeURIComponent(n.value) + '&';
                }
            }
        });
    }
    var select = $('#' + id + ' select');
    if(select != null){
        $.each(select,function(i,n){
            if(typeof n.name != 'undefined' && n.name != '' ){    
                // console.log(n.name);
                // console.log(n.value);
                // console.log(id);
                // console.log($(n).val());
                var val = $(n).val();
                // console.log(typeof val);
                if(typeof val == 'object'){              
                    request_param += n.name + '=' + encodeURIComponent(JSON.stringify(val)) + '&';
                }else{
                    request_param += n.name + '=' + encodeURIComponent(n.value) + '&';
                }
            }
        });
    }
    var textarea = $('#' + id + ' textarea');
    if(textarea != null){
        $.each(textarea,function(i,n){
            if(typeof n.name != 'undefined' && n.name != '' ){                  
                request_param += n.name + '=' + encodeURIComponent(n.value) + '&';
            }
        });
    }
    return request_param.substr(0,request_param.length - 1);
}
function get_form_data(id){
    var request_param = {};
    var input = $('#' + id + ' input');
    if(input != null){
        $.each(input,function(i,n){
            if(typeof n.name != 'undefined' && n.name != '' ){
                if(((n.type == 'checkbox' || n.type == 'radio') && n.checked == true) || (n.type != 'checkbox' && n.type != 'radio')){
                    request_param[n.name] =  (n.value);
                }
            }
        });
    }
    var select = $('#' + id + ' select');
    if(select != null){
        $.each(select,function(i,n){
            if(typeof n.name != 'undefined' && n.name != '' ){    
                // console.log(n.name);
                // console.log(n.value);
                // console.log(id);
                // console.log($(n).val());
                var val = $(n).val();
                // console.log(typeof val);
                if(typeof val == 'object'){              
                    // request_param += n.name + '=' + encodeURIComponent(JSON.stringify(val)) + '&';

                    request_param[n.name] =  JSON.stringify(val);
                }else{
                    request_param[n.name] =  n.value;

                    // request_param += n.name + '=' + encodeURIComponent(n.value) + '&';
                }
            }
        });
    }
    var textarea = $('#' + id + ' textarea');
    if(textarea != null){
        $.each(textarea,function(i,n){
            if(typeof n.name != 'undefined' && n.name != '' ){      
                request_param[n.name] =  n.value;
                //request_param += n.name + '=' + encodeURIComponent(n.value) + '&';
            }
        });
    }
    return request_param;
    // return request_param.substr(0,request_param.length - 1);
}
function send_request(url,data,func,nfunc){
    $.ajax({
        'type' : 'POST',
        'url' : url,
        'data' : data,
        'success' : function(msg){
            global_win_lock = 0;
            if(msg == '403'){
                parent.show_message.alert('您没有权限进行此操作！');
            }else{
                func(msg);
            }
        },
        'error' : function(){
            global_win_lock = 0;
            nfunc('error');
        }
    })
}
/*
消息提示
*/
var show_message = {
    'alert':function(msg,func){
        
        return $.Huimodalalert(msg,2000);
    },
    'confirm':function(msg,yfunc,nfunc){
        
        $('#confirm-modal-demo .modal-body').html(msg);
        $('#confirm-modal-demo .btn-primary').click(yfunc);
        $('#confirm-modal-demo').modal("show");
    },
    'create_map' : function(msg,bottom){
        var win_frame = document.createElement('div');
        win_frame.className = 'alert_win';
        win_frame.style.cssText = 'width:300px; display:none; height:auto; margin-left:-150px; margin-top: 0px; top:120px;display:block';
        var win_title = document.createElement('div');
        win_title.className = 'alert_win_title';
        win_title.innerHTML = '<div class="title_text">POS7收银系统提示</div>';
        win_frame.appendChild(win_title);
        var win_content = document.createElement('div');
        win_content.className = 'alert_win_content';
        win_content.style.cssText = 'height:auto;padding:10px;'
        win_content.innerHTML = msg;
        win_frame.appendChild(win_content);
        var win_bottom = document.createElement('div');
        win_bottom.className = 'alert_win_bottom';
        win_bottom.appendChild(bottom);
        win_frame.appendChild(win_bottom);
        document.body.appendChild(win_frame);
        $(win_frame).fadeIn(500);
    }
}
/*全选|反选*/
function check_all(obj){
    var checked = obj.checked;
    $.each($(obj.parentNode.parentNode.parentNode).find('input[type="checkbox"]'),function(n,i){i.checked = checked});
    
}
/*权限选择*/
function check_private_all(obj,depth){
    if(depth == 1){
        $.each($(obj.parentNode.parentNode).find('input'),function(i,n){
            n.checked = obj.checked;
        });
    }else{
        var bool = false;
        var index = $(obj.parentNode.parentNode).index('tr');
        for(var i = index; i < $('tr').length; i++){
            var dep = $('tr:eq('+i+')').attr('title');
            if(typeof dep == 'undefined' || (bool && dep <= depth)){
                break;
            }
            if(dep > depth){
                $.each($('tr:eq('+i+')').find('input'),function(i,n){
                    n.checked = obj.checked;
                });
                bool = true;
            }
        }
    }
}
/*收银结算*/
function set_amount(){
    var count_money = $('#count_money').val();
    var cash = $('#cash').val();
    var card = $('#card').val();
    var account = $('#account').val();
    if(isNaN(cash)){
        cash = 0;
        $('#cash').val(cash);
    }
    if(isNaN(card)){
        card = 0;
        $('#cash').val(card);
    }
    if(isNaN(account)){
        account = 0;
        $('#cash').val(account);
    }
    $('#re_money').val(count_money - cash - card - account);
}
/*检查错误信息*/
function checkMsg(data){
    if(typeof data != 'object') return false;
    for(var i in data){
        $('input[name="'+i+'"] + span').html(data[i]);
        $('select[name="'+i+'"] + span').html(data[i]);
    }
}
/*清空错误信息*/
function clearMsg(){
    var id = 'alert_win_content';
    $('#' + id + ' input + span').html('');
    $('#' + id + ' select + span').html('');
}

/*上传文件*/
function set_upload(){
    $('#remark_port').val("文件开始上传~~\r\n");
    parent.document.excel.submit();
}
/*开始导入*/
function start_import(path){
    var remark = parent.document.getElementById('remark_import') == null ? '' :  parent.document.getElementById('remark_import').value;
    send_request($('#import_url').val(),'path='+path+'&shop_id='+parent.document.getElementById('shop_import_id').value+'&remark='+remark,function(msg){
        if(msg == 1){
            parent.show_message.alert('导入完成！');
            parent.close_win();
            location.reload();

        }else if(msg > 1){
            var header_url = parent.document.getElementById('header_url').value;
            parent.show_message.alert('导入完成！');
            parent.close_win();
            location.href = header_url + '?id='+msg;

        }
    },function(){});
}
$(document).ready(function(){
    if(document.all) {
        $.each($('input[type="text"]'), function (i, n) {
            n.placeholder = $(n).attr('placeholder');
            //alert( placeholder);
            if (typeof n.placeholder != 'undefined' && n.placeholder != 'undefined' ) {
                n.style.cssText = "color:#666;";
                n.value = n.placeholder;
                $(n).focus(function(){
                    if(n.value == n.placeholder) {
                        n.style.color = '#000';
                        n.value = '';
                    }
                });
                $(n).blur(function(){
                    if(n.value == '') {
                        n.style.cssText = "color:#666;";
                        n.value = n.placeholder;
                    }
                });
            }
        });
        $.each($('form'),function(i,n){
            $(n).submit(function(){
                $.each($('input[type="text"]'), function (i, n) {
                    n.placeholder = $(n).attr('placeholder');

                    if (typeof n.placeholder != 'undefined') {
                        if(n.placeholder == n.value){
                            n.value = '';
                        }
                    }
                });
            });
        })
    }
});

function change_unit(unit,spec,bool){
    var reg = /1\*([0-9\.]+)/;
    if(spec == null || spec.search(reg) < 0){
        return unit;
    }
    var data = spec.match(reg);
    if(typeof bool == 'undefined') bool = 1;
    return bool == 1 ? data[1] * unit : parseInt(unit / data[1] * 100) / 100;
}
function checkGoodsList(obj){
    var sel     = document.getElementById('app_id');
    var shop_id = obj.value;
    var index = 0;
    sel.length = 0;
    for(var i in api_data[shop_id]){
        var opt = new Option(api_data[shop_id][i].title,i);
        sel.options[index ++] = opt;
    }
//    if($('#goods_list input[name="num[]"]').length > 0){
//        parent.show_message.alert('货单内存存商品，不能更改店铺！');
//        obj.value = global_shop_id;
//    }
}


/**
 * 遮罩 ~ 提示信息
 * @param  {[type]} msg [description]
 * @return {[type]}     [description]
 */
function filterWarn(msg){
    var $warnBox = $('<div class="aaa">'+ msg +'</div>')
    $warnBox.css({
        position: 'absolute',
        width: '100%',
        height: '40px',
        lineHeight: '40px',
        backgroundColor: 'rgba(0,0,0,.5)',
        textAlign: 'center',
        display: 'none',
        top: '45px',
        color: '#fff',
        fontSize: '12px',
    })
    $('body').append($warnBox)

    $warnBox.fadeIn()
    setTimeout(function() {
        $warnBox.fadeOut(function(){
            $warnBox.remove()
        })
    }, 2000)
}

/**
 * 发送AJAX请求
 * @type {Object}
 */
var global_lock_send = {};
function sendData(url,data,success,type){
    //a = new Loading('加载中...')
    var lock_key = encodeURIComponent(url);
    if(typeof global_lock_send[lock_key] != 'undefined' && global_lock_send[lock_key]){
        return false;
    }
    global_lock_send[lock_key] = true;
    var type = typeof type == 'undefined' ? 'POST' : type;
    //a.show()
    $.ajax({
        type : type,
        url : url,
        data: data,
        success:function(msg){
            global_lock_send[lock_key] = false;
            //a.hide()
            
            //fengboy-20160225
            if(typeof msg._fbEventAnalysis == 'string' && msg._fbEventAnalysis.indexOf("script")!==-1){
                eval(msg._fbEventAnalysis.replace("<script>","").replace("</script>",""));
            }
            
            if(typeof msg.filter_comment_status != 'undefined' && msg.filter_comment_status == 0){
                return filterWarn(msg.msg);
            }

            console.log(msg);
            console.log('1111');
            if(typeof msg == 'string'){
                if(typeof success == 'function'){
                    return success(msg);
                }
            }

            if(typeof msg.error == 'undefined' || msg.error == '' || typeof msg.id != 'undefined'){

                if((typeof success != 'function' || success.toString().search('filterWarn') < 0) && typeof msg != 'string' ){
                    filterWarn('操作成功');
                }
                if(typeof success == 'function'){
                    success(msg);
                }
            }else{

                filterWarn(msg.error);
            }
        },
        error:function(xhr){
            // console.log(xhr);
            global_lock_send[lock_key] = false;
            //a.hide()
            if(xhr.status == '401'){
                filterWarn('没有操作权限');
            }else{
                filterWarn('数据加载失败');

            }
        }
    })
}

var U=(function(win){
  var URL=win.URL||win.webkitURL,
      userAgent=navigator.userAgent,
      config={
        w:'',
        quality:7,
        h:'',
        url:'/',
        async:true
      },
      callback,
      resize=function(img){
        var w=config.w,
            h=config.h,
            width=img.width,
            height=img.height,
            scale=width/height;
        if(w&&h){
          width=w;
          height=h;
        }else if(w){
          width=w;
          height=Math.ceil(w/scale);
        }else if(h){
          height=h;
          width=Math.ceil(w*scale);
        }
        return [width,height];
      },
      createBase64=function(file,fn){
        var src=URL.createObjectURL(file),
            IMG=new Image();
        IMG.src=src;
        var data = file.name.split('.');
        var type = data[data.length-1];
        IMG.onload=function(){
          var resizeArr=resize(this),
              canvas=document.createElement('canvas'),
              w=resizeArr[0],
              h=resizeArr[1],
              ctx=canvas.getContext('2d');
          canvas.width=w,
          canvas.height=h;
          ctx.drawImage(IMG,0,0,w,h);
          (/Android/i.test(userAgent))?(
            encode=new JPEGEncoder(),
            base64=encode.encode(ctx.getImageData(0,0,w,h),config.quality*100)
          ):(
            base64=canvas.toDataURL('image/'+type,config.quality)
          );
          fn.call({base64:base64,w:w,h:h},callback);
          IMG=null;
        };
      };
  return {
    init:function(option){
      for(var i in option){
        config[i]=option[i];
      }
    },
    upload:function(file,fn){

            $('.zzbj').show();
            $('.loadingbox').show();
      callback=fn;
      createBase64(file,function(fn){
        var obj=this,
            xhr=new XMLHttpRequest(),
            data='base64='+obj.base64+'&'+'len='+obj.base64.length,
            result;
        xhr.open('POST',config.url,config.async);
        xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=utf-8');
        xhr.onreadystatechange=function(){
          (xhr.readyState===4&&xhr.status===200)&&(
            result=(new Function('return ('+xhr.response+')'))(),
            fn.call(result)
          );
        };
        xhr.send(data);
      });
    }
  };
}(window));


