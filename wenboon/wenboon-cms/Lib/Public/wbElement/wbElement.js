/*
    wbElement version 1.0
*/
var wbElementPath=document.scripts;wbElementPath=wbElementPath[wbElementPath.length-1].src.substring(0,wbElementPath[wbElementPath.length-1].src.lastIndexOf("/")+1);

$(init);
function init(){
    init_select();
    init_checkbox();
    init_dialog();
}
function init_select(){
    $(".select").each(function(){
        //select属性
        var wheight=$(this).css('height');
        var wwidth=(parseInt($(this).css('width'))-parseInt(wheight)-10).toString()+'px';
        var wname=$(this).attr('wname');
        //添加箭头，和文本
        $(this).prepend("<img src='"+wbElementPath+"images/select_img.gif' style='height:"+wheight+";width:"+wheight+";'/>");
        $(this).prepend("<input class='select_input_s' readOnly='true' style='height:"+wheight+";width:"+wwidth+";'/>");
        $(this).prepend("<input class='select_input_h' type='hidden' name='"+wname+"'/>");
    });
    $(".checked").each(function(){
        obj=$(this).parent().parent().find('.select_input_s');
        obj.val($(this).html());
        
        obj=$(this).parent().parent().find('.select_input_h');
        obj.val($(this).attr('value'));
    });
    
    $('.select img').click(function(){
        var obj=$(this).parent();
        if(obj.find('.option').css('display')=='none'){
            $(this).attr('src',''+wbElementPath+'images/select_img1.gif');
            obj.find('.option').css({height:'0px',display:'block'});
            var count=obj.find('.option p').length;
            var heights=count*22;
            obj.find('.option').stop().animate({height:heights},200);
        }
        else{
            $(this).attr('src',''+wbElementPath+'images/select_img.gif');
                obj.find('.option').stop().animate({height:'0px'},200,function(){
                obj.find('.option').css({display:'none'}); 
            });
                                
        }
     });
                        
     $('.option p').click(function(){
        var obj=$(this).parent().parent();
        obj.find('.select_input_s').val($(this).html());
        obj.find('.select_input_h').val($(this).attr('value'));
        obj.find('.option').css('display','none');
        obj.find('img').attr('src',''+wbElementPath+'images/select_img.gif');                  
     });   
}
//checkbox Element
function init_checkbox(){
    $(".checkbox").each(function(){
        var obj=$(this);
        var wheight=parseInt($(this).css('height'));
        var wwidth=parseInt($(this).css('width'));
        var wname=$(this).attr('wname');
        if(wheight>=wwidth){
                wwidth=wheight;
        }
        else{
                wheight=wwidth;
        }
        $(this).prepend('<input type="hidden" value="'+obj.attr('value')+'"/>');
        if(obj.attr('checked')=='checked'){
          obj.find('input').attr('name',wname);
          $(this).prepend("<img src='"+wbElementPath+"images/checkbox_img.gif' style='height:"+wheight+"px;width:"+wwidth+";'/>");  
          
        }
        else{
            $(this).css({height:wheight-2,width:wwidth-2,border:'1px solid #959595'});
        }
    });
    
    $(".checkbox").hover(function(){
        var obj=$(this);
        if(obj.attr('checked')=='checked'){
            obj.find('img').attr('src',''+wbElementPath+'images/checkbox_img1.gif');
        }
        else{
           $(this).css({border:'1px solid #278cde'}); 
        }
    },function(){
        var obj=$(this);
        if(obj.attr('checked')=='checked'){
            obj.find('img').attr('src',''+wbElementPath+'images/checkbox_img.gif');
        }
        else{
           $(this).css({border:'1px solid #959595'}); 
        }
    });
    
    $(".checkbox").click(function(){
        var obj=$(this);
        if(obj.attr('checked')=='checked'){
            
            var wheight=parseInt(obj.css('height'));
            var wwidth=parseInt(obj.css('width'));
            obj.removeAttr('checked');
            if(wheight>=wwidth)
                wwidth=wheight;
            else
                wheight=wwidth;
                
            obj.find('img').remove();
            obj.css({height:wheight-2,width:wwidth-2,border:'1px solid #959595'});
            
            obj.find('input').removeAttr('name');
        }
        else{
            var wheight=parseInt(obj.css('height'))+2;
            var wwidth=parseInt(obj.css('width'))+2;
            var wname=obj.attr('wname');
            
            if(wheight>=wwidth)
                wwidth=wheight;
            else
                wheight=wwidth;
                
            obj.attr('checked','checked');
            obj.css({height:wheight,width:wwidth,border:'0px'});
            $(this).prepend("<img src='"+wbElementPath+"images/checkbox_img.gif' style='height:"+wheight+"px;width:"+wwidth+";'/>"); 
            
            obj.find('input[type=hidden]').attr('name',wname);
        }
    });
}
//dialog Element
function init_dialog(){
    $('.dialog').each(function(){
        var dheight=parseInt($(this).css('height'))-20;
        var dwidth=parseInt($(this).css('width'))-20;
        
        var obj_title=$(this).find('.dialog-title');
        obj_title.append('<a class="dialog-close" href="javascript:void(0);" onclick="dialog_close(this)"></a>');;
        var twidth=dwidth-10;
        obj_title.css({width:twidth,paddingLeft:'10px'});
        
        var html=$($(this).html());
        $(this).html('');
        
        var obj=$("<div class='dialog-centent' style='height:"+dheight+"px;width:"+dwidth+"px;'></div>");
        $(obj).prepend(html);
        $(this).prepend(obj);
        //居中
        var sheight=parseInt($(window).height());
        var swidth=parseInt($(window).width());
               
        var hpos=(sheight-(dheight+20))/2;
        var wpos=(swidth-(dwidth+20))/2;
        $(this).css({top:hpos,left:wpos});
        //绑定事件
        $(this).bind('hides',function(){
            $(this).hide();
        });
        
        $(this).bind('shows',function(){
            var sheight=parseInt($(window).height());
            var swidth=parseInt($(window).width());
                   
            var hpos=(sheight-(dheight+20))/2;
            var wpos=(swidth-(dwidth+20))/2;
            $(this).css({top:hpos,left:wpos});
            $(this).show();
        });
    });
}
function dialog_close(_this){
    var obj=$(_this).parents('.dialog');
    obj.trigger('hides');
}