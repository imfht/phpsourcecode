(function($){
	/**
	 * 显示、隐藏加载
	 */
	function hideLoading() {
		$('#loading').addClass('hide');
		$('#loading_bg').hide();
	}
	function showLoading() {
		$('#loading').removeClass('hide');
		$('#loading_bg').show();
	}
	function setActive(el) {
		var p = $(el).parent('ul');
		var now = p.children('.active');
		now.removeClass('active');
		now.children('.collapsible-header').removeClass('active');
		now.children('.collapsible-body').hide();
		el.addClass('active');
		el.children('.collapsible-header').addClass('active');
		el.children('.collapsible-body').show();
	}
	//上一步
	$('.prev').bind('click',
	function(){
		setActive($('#' + $(this).attr('data-prev')));
	});
	//协议
	$('#license_check').bind('change',
	function(){
		if (this.checked) {
			$('#license_submit').removeAttr('disabled');
		} else {
			$('#license_submit').attr('disabled', 'true');
		}
	});
	//同意协议，环境检查
	$('#license_submit').bind('click',
	function(){
		showLoading();
		$.ajax({
			'url': window.sy.env,
			'type': 'GET',
			'success': function(response){
				hideLoading();
				var envcheck = true;
				$(response).each(function(i, n){
					var me = $('#env_' + n.name.toLowerCase());
					if (n.support) {
						me.addClass('mdi-check');
						me.css({'color':'green'});
					} else {
						me.addClass('mdi-close');
						me.css({'color':'red'});
						envcheck = false;
					}
				});
				setActive($('#env_check'));
				if (!envcheck) {
					$('#env_submit').attr('disabled', 'true');
				}
			}
		});
	});
	$('#env_submit').bind('click',
	function(){
		setActive($('#info'));
	});
	//提交信息，进行检测
	$('#info_submit').bind('click',
	function(){
		var data = {};
		$('#info').find('input').each(function(i, n){
			data[$(n).attr('name')] = $(n).val();
		});
		if (data.password.length < 5) {
			alert('密码不能小于五个字符');
			return;
		}
		if (data.cookie.length < 5) {
			alert('Cookie密钥不合法');
			return;
		}
		showLoading();
		$.ajax({
			'url': window.sy.testdb,
			'type': 'POST',
			'data': data,
			'success': function(response){
				hideLoading();
				if (response === 'success') {
					processing(data);
				} else {
					alert(response);
				}
			}
		})
	});
	function processing(data) {
		setActive($('#processing'));
		$('#processing_submit').hide();
		//加载动画
		if ($('#processing_img').length === 0) {
			$('#processing_submit').before('<div id="processing_img" class="preloader-wrapper big active"><div class="spinner-layer spinner-blue"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div><div class="spinner-layer spinner-red"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div><div class="spinner-layer spinner-yellow"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div><div class="spinner-layer spinner-green"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div>');
		} else {
			$('#processing_img').show();
		}
		$.ajax({
			'url': window.sy.processing,
			'type': 'POST',
			'data': data,
			'success': function(response){
				$('#processing_img').hide();
				if (response === 'success') {
					$('#processing_submit').show();
				} else {
					alert(response);
					setActive($('#info'));
				}
			}
		});
	}
	$('#processing_submit').bind('click',
	function(){
		setActive($('#finish'));
	});
	hideLoading();
	setActive($('#license'));
})(jQuery);