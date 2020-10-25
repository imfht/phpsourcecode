/**
 * common
 */
$(function(){
	//全选的实现
	$(".check-all").click(function(){
		$(".ids").prop("checked", this.checked);
		url = 'index.php?s=/Home/Cart/changeStatus.html';
        ids ='';
        $('.ids').each(function(){
            ids += $(this).val()+',';
        });
        if (ids.length > 0) {
            ids = ids.substring(0,ids.length - 1);
        }

        query='ids='+ids+'&check='+this.checked;
		$.post(url,query).success(function(data){
            $('.total').html(data);
        });
	});
	$(".ids").on('change',function(){
			url = 'index.php?s=/Home/Cart/changeStatus.html';
			query = {'id':$(this).val(),'check':this.checked};

			$.post(url,query).success(function(data){
                $('.total').html(data);
            });
            var option = $(".ids");
            option.each(function(){
                if(!this.checked){
                    $(".check-all").prop("checked", false);
                    return false;
                }else{
                    $(".check-all").prop("checked", true);
                }
            });
	});
	

	/**顶部警告栏*/
	
	var top_alert = $('#top-alert');
	top_alert.find('.am-close').on('click', function () {
		top_alert.removeClass('block').slideUp(200);
		// content.animate({paddingTop:'-=55'},200);
	});
	$('#message').find('.close').on('click',function(){
		$(this).removeClass('block').slideUp(200);
	});

    window.updateAlert = function (text,c) {
		text = text||'default';
		c = c||false;
		if ( text!='default' ) {
            top_alert.find('.am-alert-content').text(text);
			if (top_alert.hasClass('block')) {
			} else {
				top_alert.addClass('block').slideDown(200);
				// content.animate({paddingTop:'+=55'},200);
			}
		} else {
			if (top_alert.hasClass('block')) {
				top_alert.removeClass('block').slideUp(200);
				// content.animate({paddingTop:'-=55'},200);
			}
		}
		if ( c!=false ) {
            top_alert.removeClass('am-alert-danger am-alert-warn am-alert-info am-alert-success').addClass(c);
		}
	};
	//ajax get请求
    $('.ajax-get').click(function(){
        var target;
        var that = this;
        if ( $(this).hasClass('confirm') ) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            $.get(target).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','am-alert-success');
                    }else{
                        updateAlert(data.info,'am-alert-success');
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1500);
                }
            });

        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();
            }else if (form.get(0)==undefined){
            	return false;
            }else if ( form.get(0).nodeName=='FORM' ){
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                	target = $(this).attr('url');
                }else{
                	target = form.get(0).action;
                }
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            }else{
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','am-alert-success');
                    }else{
                        updateAlert(data.info,'am-alert-success');
                    }
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info)
                    setTimeout(function(){
                    	$(that).removeClass('disabled').prop('disabled',false);
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1500);
                }
            });
        }
        return false;
    });
    updateCart();
});
function addToCart(id){
	if(id==null || id == '' || typeof(id)!='number'){
		updateAlert('无效的商品');
	}else{
		target = 'index.php?s=/Home/Cart/add.html';
		query  = {'id':id};
		$.post(target,query).success(function(data){
	        if (data.status==1) {
	            if (data.url) {
	                updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
	            }else{
	                updateAlert(data.info ,'alert-success');
	            }
	            setTimeout(function(){
	            	
	                if (data.url) {
	                    location.href=data.url;
	                    location.reload();
	                }else {
	                    $('#top-alert').find('button').click();
	                }
	            },1500);
	        }else{
	            updateAlert(data.info);
	            setTimeout(function(){
	            	
	                if (data.url) {
	                    location.href=data.url;
	                }else{
	                    $('#top-alert').find('button').click();
	                }
	            },1500);
	        }
	    });	
		updateCart();
	}
	
}

function showmsg(msg){
	$("#tip").remove();
	$tip = $('<div id="tip" style="font-weight:bold;position:fixed;top:240px;left: 50%;z-index:9999;background:rgb(255, 45, 94);padding:18px 30px;border-radius:8px;color:#fff;font-size:16px;">'+msg+'</div>');
    $('body').append($tip);
	$tip.stop(true).css('margin-left', -$tip.outerWidth() / 2).fadeIn(500).delay(2000).fadeOut(500);
}
//导航高亮
function highlight_subnav(url){	
	$('#doc-topbar-collapse').children('ul').eq(0).find('a[href="'+url+'"]').closest('li').addClass('am-active');
}
function setCookie(j, k)
{
    document.cookie = j + "=" + encodeURIComponent(k.toString()) + "; path=/";
}

function getCookie(l)
{
    var m = (" " + document.cookie).split(";"),
        j = "";
    for (var k = 0; k < m.length; k++) {
        if (m[k].indexOf(" " + l + "=") === 0) {
            j = decodeURIComponent(m[k].split("=")[1].toString());
            break
        }
    }
    return j
}
function get_et(){
    var s = new Date(),
        l = +s / 1000 | 0,
        r = s.getTimezoneOffset() * 60,
        p = l + r,
        m = p + (3600 * 8),
        q = m.toString().substr(2, 8).split(""),
        o = [6, 3, 7, 1, 5, 2, 0, 4],
        n = [];
    for (var k = 0; k < o.length; k++) {
        n.push(q[o[k]])
    }
    n[2] = 9 - n[2];
    n[4] = 9 - n[4];
    n[5] = 9 - n[5];
    return n.join("")
}
function get_pgid(){
    var l = "",
        k = "",
        n,
        o,
        t,
        u,
        s = location,
        m = "",
        q = Math;
    function r(x, z) {
        var y = "",
            v = 1,
            w;
        v = Math.floor(x.length / z);
        if (v == 1) {
            y = x.substr(0, z)
        } else {
            for (w = 0; w < z; w++) {
                y += x.substr(w * v, 1)
            }
        }
        return y
    }

    n = (" " + document.cookie).split(";");
    for (o = 0; o < n.length; o++) {
        if (n[o].indexOf(" cna=") === 0) {
            k = n[o].substr(5, 24);
            break
        }
    }

    if (k === "") {
        cu = (s.search.length > 9) ? s.search: ((s.pathname.length > 9) ? s.pathname: s.href).substr(1);
        n = document.cookie.split(";");
        for (o = 0; o < n.length; o++) {
            if (n[o].split("=").length > 1) {
                m += n[o].split("=")[1]
            }
        }
        if (m.length < 16) {
            m += "abcdef0123456789"
        }
        k = r(cu, 8) + r(m, 16)
    }
    for (o = 1; o <= 32; o++) {
        t = q.floor(q.random() * 16);
        if (k && o <= k.length) {
            u = k.charCodeAt(o - 1);
            t = (t + u) % 16
        }
        l += t.toString(16)
    }
    setCookie('amvid', l);
    var p = getCookie('amvid');
    if (p) {
        return p
    }
    return l
}
function isWeiXin(){
    var ua = window.navigator.userAgent.toLowerCase();
    console.log(ua);
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}
function nextPage(){
    if(haveData){
        var url = $('.btn-get-more').data('url');
        console.log(url);
        if(url!=undefined){
            $.get(url,{},function(result){
                // loadingHide()
                if(result.error==0){
                    $('#content-list').append(result.content);
                    $('.btn-get-more').data('url',result.next_url);
                    if(typeof(response)=='function'){
                        response();
                    }
                }else {
                    showMessage('没有了，亲！');
                    $('.btn-get-more').text('没有下一页咯。。。');
                    $('.btn-get-more').addClass('disabled');
                    haveData=false;
                }
            },'json');
        }else {
            showMessage('没有了，亲！');
            $('.btn-get-more').text('没有下一页咯。。。');
            $('.btn-get-more').addClass('disabled');
            haveData=false;
        }

    }
}