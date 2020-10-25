$(function(){
	
	$form = $('form');
	
	$form.find('img.check-code,a.replace-img').click(function(){
		
		var $img = $form.find('img.check-code');
		
		$img.attr('src', $img.attr('src') + '?t=1');
		
	});

	var lock = false;
	
	$('#sure_btn').click(function(){
		
		if(lock) return;
		
		$('div.has-error').removeClass('has-error');
		
		var $title = $('input[name=title]'),
		
			title = $.trim($title.val()),
			
			$content = $('textarea[name=content]'),
		
			content = $.trim($content.val()),
			
			$verify = $('input[name=verify]'),
			
			verify = $.trim($verify.val()),
			
			$in, msg = false;

		if(title == ''){
			
			$in = $title;
			
			msg = true;
			
		}else if(content == ''){
			
			$in = $content;
			
			msg = true;
			
		}else if(verify == ''){
			
			$in = $verify;
			
			msg = true;
			
		}

		if(msg === false){
			
			lock = true;
			
			$('div.loading').show();
			
			$.post(APP + '/Comment/add', {title : title, content : content, verify : verify}, function(data){
				
				$('div.loading').hide();
				
				lock = false;
				
				var msg;
				
				if(data == 1){
					
					msg = '留言成功';
					
					$form.get(0).reset();
				
				}else if(data == -2) msg = '验证码错误';
				
				else msg = '留言失败';
				
				$form.find('img.check-code').click();
				
				$('#myModal').modal('show').find('div.modal-body').text(msg);
				
			});

		}else $in.parent().addClass('has-error');
		
	});
	
	$('div.modal-footer button.btn-primary').click(function(){
		
		$('#myModal').modal('hide');
		
	});
	
});