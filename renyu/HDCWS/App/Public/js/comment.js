$(function(){
	
	$form = $('form.comment-form');
	
	$form.find('img.check-code,a.replace-img').click(function(){
		
		var $img = $form.find('img.check-code');
		
		$img.attr('src', $img.attr('src') + '?t=1');
		
	});

	$('#sure_btn').click(function(){
		
		var $warn = $('p.warn').text(''),
		
			title = $.trim($('input[name=title]').val()),
		
			content = $.trim($('textarea[name=content]').val()),
			
			verify = $.trim($('input[name=verify]').val()),
			
			msg = false;

		if(title == '') msg = '标题不能为空';
		
		else if(content == '') msg = '内容不能为空';
		
		else if(verify == '') msg = '验证码不能为空';
		
		if(msg === false){
			
			$form.submit();

		}else $warn.text(msg);
		
	});
	
});