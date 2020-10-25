$(function(){
	toggleAESKeyOnCase( $('select[name=weixin_cryptType]').val() );

	$(document).delegate('select[name=weixin_cryptType]', 'change', function(){
		var typeCase = $(this).val();
		toggleAESKeyOnCase(typeCase);
		updateSelect(typeCase);
	});

	function toggleAESKeyOnCase(typeCase){
		$target = $('input[name=weixin_EncodingAESKey]');
		(typeCase!=0)? $target.removeAttr('disabled') : $target.attr('disabled', 'disabled');
	}

	/* 修正select组件的bug */
	function updateSelect(typeCase){
		$('select[name=weixin_cryptType]').children('option').removeAttr('selected');
		$('select[name=weixin_cryptType]').children('option').eq(typeCase).attr('selected', 'selected');
	}
});