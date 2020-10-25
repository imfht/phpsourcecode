$(function() {
  var bn_status_more = $('.bn-status-more');
  var status_cate = $('.status-cate');
  bn_status_more.click(function(e) {
	var el = $(this);
	el.parent().toggleClass('status-more-active');
	return false;
  });
  $('html').click(function(e) {
	if (!$(e.target).hasClass('cate-list-title')) {
		status_cate.removeClass('status-more-active');
	}
  });
});
$(function(){
	var l = $("#isay-label");
	var h = $("#isay-cont");
	var p = $("#db-isay");
	var B = $(".btn-group");
	var count_text = $('#isay-counter');
	var subtn = $('#isay-submit');
	var pic = $('#isay-upload-inp');
	var pic_act = $('#isay-act-field');
	var url_act = $('#isay-url-field');
	var closebtn = $('#closex');
	var action = '';
	function s(z) {
		var y = z.target; // html element
		var x = z.type; // object
		var w = $(y);

		p.addClass('active focus');
		l.hide();
		h.height(h.attr('data-minheight'));
		h.focus();
	}
	function j(i) {
		s && s(i)
	}	
	setTimeout(function() {
		if (h.val()) {
			l.hide()
		}
	}, 50);
	$("#isay-upload-inp").one("change", j);
	h.one("focus", j);
	p.one("click", j);
	h.bind('blur',function(){
		if(h.val()==0){
			l.show();
			p.removeClass('focus');
		}
	});
	h.bind('focus',function(){
		p.addClass('focus');
		l.hide();		
	});
	h.bind('keyup',function(){
		checktext()
	});
	function checktext(){
		if(action=='sharesite'){
			if(h.val()!=0 && $('input[name=share_name]').val()!=undefined){
				p.removeClass('isay-disable');
				subtn.removeAttr('disabled');				
			}else{
				p.addClass('isay-disable');
				subtn.attr('disabled', true);
			}
		}else
		{
			if(h.val()!=0){
				p.removeClass('isay-disable');
				subtn.removeAttr('disabled');	
			}else{
				p.addClass('isay-disable');
				subtn.attr('disabled', true);			
			}
		}
	}
	p.find('a').live('click',function(){
		var t = $(this),len = h.val().length,isaytype = $('#isaytype');
		action = t.attr('data-action');
		t.parent().siblings().removeClass('active');
		t.parent().addClass('active');		
		if(action=='topic'){
			h.focus();
			isaytype.val('topic');
			var val = h.val(),
			selection = h.get_selection(),
			sel = selection.text,
			start = selection.start,
			end = selection.end;
			
			if (val.charAt(start - 1) == '#' && val.charAt(end) == '#') return;
			
			var rep = '#' + (sel || '热门话题') + '#';
			h.value = val.substring(0, start) + rep + val.substring(end, len);
			h.val(h.value)
			if (sel === '') {
				h.set_selection(start + 1, start + 5);
			}
			
		}
		//分享网址
		if(action=='sharesite'){
			var in_url = '';
			var html = '<div class="field"><div class="bd active"><input type="text" name="url" class="inp-text url" value="http://" id="isay-inp-url"><span class="bn-flat"><input type="button" class="bn-preview" value="输入网址"></span></div></div>';
			url_act.html(html);
			in_url = url_act.find('input[name=url]');
			h.blur();
			in_url.focus();
			isaytype.val('sharesite');
			p.removeClass('focus');
			p.addClass('act-share field-up acting isay-disable');
			in_url.live('focus',function(){
				url_act.find('.bd').addClass('active');
			});
			in_url.live('blur',function(){
				url_act.find('.bd').removeClass('active');
			});
			pic_act.html('')
			subtn.attr('disabled', true);
		}
		//分享动态
		if(action == 'main'){
			isaytype.val('topic');
			url_act.html('')
			checktext();
			p.removeClass('act-share field-up acting');
		}
		
	});
	//网址解析
	p.find('.bn-preview').live('click',function(){
		var g = $("#isay-inp-url"), v = $.trim(g.val());
		if(v!=0 && v !='' && v!='http://'){
			$.post('index.php?app=space&c=update&a=sharesite',{url:v},function(res){
				if(res.r==1){
					url_act.find('.bd').before('<div class="hd"><input value="'+res.html.title+'" name="share_name" class="field-title" style="width: 563px;"><input value="'+res.html.url+'" name="share_link" style="width: 10px;display:none"></div>');
					url_act.find('.bd').html(res.html.url);
					url_act.find('.bd').after('<a class="bn-x isay-cancel" href="javascript:void(0);" style="display:block" data-action="sharesite">×</a>');
					url_act.find('.bd').removeClass('active');
					p.removeClass('isay-disable');	
					subtn.removeAttr('disabled');
				}else{
					url_act.find('.bd').html('<div class="error">这个网址无法识别。 <a data-action="sharesite" href="javascript:void(0);">重新输入</a></div>');
					subtn.attr('disabled', true);
				}			
			},'json');

		}else{
			url_act.find('.bd').addClass('active');
			return;
		}
	})
	
	IK.uplaodPic = function(){
		var ajaxurl = $('#isay-upload').attr('action');
		//开始ajax
		$.ajaxFileUpload(
            {
                url : ajaxurl,
                fileElementId : 'isay-upload-inp',
                dataType : 'json',
                allowType : 'jpg|png|gif|jpeg',
                begin : function(){
					var html = '<div class="field"><div class="bd"><div class="waiting">正在上传中...</div></div><a class="bn-x isay-cancel" href="javascript:void(0);" id="closex">×</a></div>';
 					pic_act.html(html);
                },
                success : function(data, status){
					
					if(data.r==1){
						var html = '<div class="field"><div class="bd">'+
									'<div style="padding-left:0;" class="waiting"><img src="'+data.html.photo_url+'"></div>'+
									'<input type="hidden" value="'+data.html.photo_name+'" name="photo_name[]"></div>'+
									'<a class="bn-x isay-cancel" href="javascript:void(0);" id="closex">×</a></div>';
						pic_act.html(html);
						h.blur();
						p.addClass('acting');
					}
                },
                error : function(data, status, e){
                    // console.log(e);
                }
            }
       ); 

	}
	//关闭
	closebtn.live('click',function(){
		$(this).parents('.isay-act').html('');
	});

	
});
//检查提交
function checkFrom(that){
	var comment = $(that).find('textarea[name=comment]').val();
	if(comment == '' || comment==0){tips('发布内容不能为空'); return false;}
	if(comment.length>150){ tips('发布内容字数太多了；最多150个字'); return false; }
	$(that).find('input[type=submit]').val('正在提交^_^').attr('disabled',true);
}

$(function(){
	//附件图片放大缩小
	var attach = $('.attachments');
	$('.upload-pic').live('click',function(){
		var _self = $(this), org_img = _self.attr('data-src'),small_img = _self.attr('small-src');
		if(_self.hasClass('big')){
			_self.removeClass('big').addClass('small');
			_self.attr('src',org_img);
		}else{
			_self.removeClass('small').addClass('big');
			_self.attr('src',small_img);
		}
	});
	//删除话题
	$('.btn-action-reply-delete').live('click',function(){
			var _this = $(this), _parent = _this.parents('.status-item');
			var feedid = _parent.attr('data-object-id'), url = _this.attr('data-object-url');
			
			$.post(url,{feedid:feedid},function(res){
				if(res.r==1){
					_parent.slideUp(200,function(){ _parent.remove(); })
				}
			},'json')
			
	});
})

