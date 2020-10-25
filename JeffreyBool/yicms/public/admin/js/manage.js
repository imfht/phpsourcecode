var manage = {
    wxpicmsg : function(){
      var dataid=$('#mp_create_bench_list_content').data('count');
      var dataeq=0;
      var mid=0;

      // 生成编辑器
      var textarea = document.getElementById('content');
      var editor = new wangEditor(textarea);
      editor.config.menus = $.map(wangEditor.config.menus, function(item, key) {
         if (item === 'emotion') {
             return null;
         }
         return item;
      });
      wangEditor.config.printLog = false;
      editor.create();

    
      window.onload = function(){
        editor.$txt.html(window.localStorage._yuanshengcontent);
      }

      //新建图文
      $("#mp_create_bench_plus").click(function(){  
        dataid++; 
        if(dataid>8){
          layer.msg("多图文最多创建8篇");
          return false;
        }
        $(".mp_create_bench_active").attr('class','mp_create_bench_div');
        var html='<section class="mp_create_bench_active">';
        html+='<div class="mp_create_bench_info">';
        html+='<div class="mp_create_bench_border">';
        html+='<div class="mp_create_bench_image"></div>';
        html+='<div class="mp_create_bench_title"></div>';
        html+='<input type="hidden" class="mp_create_bench_cover">';
        html+='<input type="hidden" class="mp_create_bench_thumb_media_id">';
        html+='<input type="hidden" class="mp_create_bench_author">';
        html+='<textarea class="mp_create_bench_article"></textarea>';
        html+='<input type="hidden" class="mp_create_bench_abstract">';
        html+='<input type="hidden" class="mp_create_bench_link">';
        html+='</div></div>';
        html+='<div class="mp_create_bench_protect">';
        html+='<div class="mp_create_bench_up"></div>';
        html+='<div class="mp_create_bench_down"></div>';
        html+='<div class="mp_create_bench_remove"></div>';
        html+='</div></section>';

        $("#mp_create_bench_list_content").append(html);

        $("#mp_cover_advise").html("(小图片建议尺寸：200像素 * 200像素，1M以内)");

        for (var i = 0; i < dataid; i++) {
          $("#mp_create_bench_list_content section").eq(i).attr("data-id",i);
        }

        mp_save(dataeq);//保存之前的图文
        dataeq=dataid-1;//标记当前的图文
        mp_show();//选中的图文显示在编辑器中
        return false;
      }); 

      //选中图文
      $("#mp_create_bench_list_content").on('click','section',function(){
        if(dataid>1){
          $(".mp_create_bench_active").attr('class','mp_create_bench_div');
          $(this).attr('class','mp_create_bench_active');
          var dataindex=parseInt($(this).attr("data-id"));
          mp_save(dataeq);//保存之前的图文
          dataeq=dataindex;//标记当前的图文
          mp_show();//选中的图文显示在编辑器中
          if(dataindex==0){
            $("#mp_cover_advise").html("(大图片建议尺寸：900像素 * 500像素，1M以内)");
          }else{
            $("#mp_cover_advise").html("(小图片建议尺寸：200像素 * 200像素，1M以内)");
          }
          return false;
        }
      });

      //删除图文
      $("#mp_create_bench_list_content").on('click','.mp_create_bench_remove',function(){
        dataid--;

        $(".mp_create_bench_active").attr('class','mp_create_bench_div');

        var dataindex=parseInt($(this).parent().parent().attr("data-id"))-1;
        if(dataindex==0){
          $("#mp_cover_advise").html("(大图片建议尺寸：900像素 * 500像素，1M以内)");
        }else{
          $("#mp_cover_advise").html("(小图片建议尺寸：200像素 * 200像素，1M以内)");
        }

        $(this).parent().parent().remove();

        for (var i = 0; i < dataid; i++) {
          if(dataindex==i){
            $("#mp_create_bench_list_content section").eq(i).attr('class','mp_create_bench_active');
          }
          $("#mp_create_bench_list_content section").eq(i).attr("data-id",i);
        }

        dataeq=dataindex;//标记当前的图文
        mp_show();//选中的图文显示在编辑器中

        return false;
      });

      //图文上移
      $("#mp_create_bench_list_content").on('click','.mp_create_bench_up',function(){
        $(".mp_create_bench_active").attr('class','mp_create_bench_div');

        var dataindex=parseInt($(this).parent().parent().attr("data-id"))-1;
        if(dataindex==0){
          $("#mp_cover_advise").html("(大图片建议尺寸：900像素 * 500像素，1M以内)");
        }else{
          $("#mp_cover_advise").html("(小图片建议尺寸：200像素 * 200像素，1M以内)");
        }

        var phtml=$(this).parent().parent().html();

        var thtml=$("#mp_create_bench_list_content section").eq(dataindex).html();

        $(this).parent().parent().html(thtml);

        $("#mp_create_bench_list_content section").eq(dataindex).html(phtml);

        $("#mp_create_bench_list_content section").eq(dataindex).attr('class','mp_create_bench_active');

        for (var i = 0; i < dataid; i++) {
          $("#mp_create_bench_list_content section").eq(i).attr("data-id",i);
        }

        dataeq=dataindex;//标记当前的图文
        mp_show();//选中的图文显示在编辑器中
        return false;
      });

      //图文下移
      $("#mp_create_bench_list_content").on('click','.mp_create_bench_down',function(){
        $(".mp_create_bench_active").attr('class','mp_create_bench_div');

        var dataindex=parseInt($(this).parent().parent().attr("data-id")) + 1;
        if(dataindex==0){
          $("#mp_cover_advise").html("(大图片建议尺寸：900像素 * 500像素，1M以内)");
        }else{
          $("#mp_cover_advise").html("(小图片建议尺寸：200像素 * 200像素，1M以内)");
        }

        var phtml=$(this).parent().parent().html();

        var thtml=$("#mp_create_bench_list_content section").eq(dataindex).html();

        $(this).parent().parent().html(thtml);

        $("#mp_create_bench_list_content section").eq(dataindex).html(phtml);

        $("#mp_create_bench_list_content section").eq(dataindex).attr('class','mp_create_bench_active');

        for (var i = 0; i < dataid; i++) {
          $("#mp_create_bench_list_content section").eq(i).attr("data-id",i);
        }

        dataeq=dataindex;//标记当前的图文
        mp_show();//选中的图文显示在编辑器中

        return false;
      });

      //保存图文到数据库
      $("#mp_btn_save").on('click',function(){
        layer.msg('保存中...', {icon: 16});
        mp_save(dataeq);//保存之前的图文
        var saveid = $('#mp_create_bench_list_content').data('saveid'),//身份ID
            image,//图文封面
            thumb_media_id,//封面ID
            title,//图文标题
            author,//图文作者
            content,//图文内容
            digest,//图文摘要
            content_source_url;//原文链接
        var flag=true;
        var articles = new Array();
        for (var i = 0; i < dataid; i++) {
          image=$("#mp_create_bench_list_content section:eq(" + i + ") .mp_create_bench_cover").val();//图文封面
          thumb_media_id=$("#mp_create_bench_list_content section:eq(" + i + ") .mp_create_bench_thumb_media_id").val();//封面ID
          title=$("#mp_create_bench_list_content section:eq(" + i + ") .mp_create_bench_title").html();//图文标题
          author=$("#mp_create_bench_list_content section:eq(" + i + ") .mp_create_bench_author").val();//图文作者
          content=$("#mp_create_bench_list_content section:eq(" + i + ") .mp_create_bench_article").text();//图文内容
          digest=$("#mp_create_bench_list_content section:eq(" + i + ") .mp_create_bench_abstract").val();//图文摘要
          content_source_url=$("#mp_create_bench_list_content section:eq(" + i + ") .mp_create_bench_link").val();//原文链接
          if((image.length==0) || (title.length==0) || (content.length==0)){
            flag=false;
            break;
          }else{
            articles.push({
              'title':title,//图文标题
              'thumb_media_id':thumb_media_id,//图文封面
              'author':author,//图文作者
              'content':content,//图文内容
              'digest':digest,//图文摘要
              "show_cover_pic":1,//是否显示封面
              'content_source_url':content_source_url//原文链接
            });
          }
        }
        if(flag) {
          var url = "/admin/wxpicmsg/insert";
          if(saveid == 0){
            url = "/admin/wxpicmsg/insert";
            $.post(url,{'articles':articles},function(data) {
              if(data.status==1){
                layer.msg("图文保存成功");
              }else{
                layer.msg("图文保存失败");
              }
            });
          }else{
            url = "/admin/wxpicmsg/update";
            $.post(url,{'articles':articles,'id':saveid},function(data) {
              if(data.status==1){
                layer.msg("图文保存成功");
                $.ajax({url:'/admin/wxpicmsg/index',async:false,success:function(data) {$('#content-main').html(data);}});
              }else{
                layer.msg("图文保存失败");
              }
            });
          }

        }else{
          layer.msg("图文消息不完整，请先编辑好图文消息的标题、正文和封面图！");
        }
        
      });
  

      //页面关闭提醒
      $(window).bind('beforeunload',function(){return '请确认保存后离开此页面!';});

      // 控制标题字数及同步
      $('#title').on('focus keyup input paste',function(){
        var title=$("#title").val();
        if(title.length>=64){
          $("#title").val(title.substr(0,64));
        }
        $(".mp_create_bench_active .mp_create_bench_title").html(title);
      });

      // 保存图文
      function mp_save(eq){
        var author = $("#author").val();
        var content = editor.$txt.html();
        var digest = $("#digest").val();
        var content_source_url = $("#content_source_url").val();
        $("#mp_create_bench_list_content section:eq(" + eq + ") .mp_create_bench_author").val(author);//图文作者
        $("#mp_create_bench_list_content section:eq(" + eq + ") .mp_create_bench_article").text(content);//图文内容
        $("#mp_create_bench_list_content section:eq(" + eq + ") .mp_create_bench_abstract").val(digest);//图文摘要
        $("#mp_create_bench_list_content section:eq(" + eq + ") .mp_create_bench_link").val(content_source_url);//原文链接
      }

      // 显示图文
      function mp_show(){
        var title=$(".mp_create_bench_active .mp_create_bench_title").html();
        var author=$(".mp_create_bench_active .mp_create_bench_author").val();//图文作者
        var content=$(".mp_create_bench_active .mp_create_bench_article").text();//图文内容
        var digest=$(".mp_create_bench_active .mp_create_bench_abstract").val();//图文摘要
        var content_source_url=$(".mp_create_bench_active .mp_create_bench_link").val();//原文链接

        $("#title").val(title);
        $("#author").val(author);
        editor.$txt.html(content);
        $("#digest").val(digest);
        $("#content_source_url").val(content_source_url);
      }

      // setInterval(function(){
      //   window.localStorage._yuanshengcontent = ue_mpcontent.getContent();
      // },10000);

      $("#mp_preview").on('click',function(){
        var html=editor.$txt.html();
        $("#preContent").html(html);
      });

      //获取图片素材
      $('#matterManag').click(function(){
        if($("#mymatter").text()==""){
          getimglist(1);
        }
        $('body .mymatter').on('click','.frm_checkbox_label', function () {
          var _this = $(this),mid = _this.data('mid'),path = _this.data('path');
          _this.addClass('selected').siblings().removeClass('selected');
          $('.mp_create_bench_active .mp_create_bench_image').html('<img src="'+path+'" width="100%" height="100%">');
          $('.mp_create_bench_active .mp_create_bench_cover').val(path);
          $('.mp_create_bench_active .mp_create_bench_thumb_media_id').val(mid);
        });

      })

      function getimglist(page){
        var mymatter = $('#mymatter');
        var pagestr ='';
        $.ajax({
          url:'/admin/wximg/getlist',
          type:'get',
          data:'p='+page,
          success:function(re){
            var data = re.data;
            var html = '';
            pagestr = re.page;
            //if(!data){
              for(var i in data){
                html += '<div class="col-xs-3 col-sm-2 frm_checkbox_label" data-mid="'+data[i]['media_id']+'" data-path="'+data[i]['path']+'">';
                html += '<label class="img_item_bd">';
                html +=       '<div class="pic">';
                html +=           '<img src="'+data[i]['path']+'">';
                html +=           '<div class="selected_mask">';
                html +=               '<div class="selected_mask_inner"></div>';
                html +=               '<div class="selected_mask_icon"><i class="fa fa-check"></i></div>';
                html +=           '</div>';
                html +=       '</div>';
                html +=       '<span class="lbl_content">'+data[i]['media_id']+'</span>';
                html +=   '</label>';
                html += '</div>';
              }
              mymatter.html(html);
              $('.page').html(pagestr);

              $('body').delegate('.page a','click',function(event){
                event.preventDefault();
                var purl = $(this).attr('href');
                purl = purl.split('p=')[1];
                getimglist(purl);
              });
              //分页

            //}
          }
        })
      }

    }
	};