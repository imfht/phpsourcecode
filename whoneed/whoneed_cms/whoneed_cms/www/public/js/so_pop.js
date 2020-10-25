var oSoClear = ai.i("js-so-clear"),
	oSoPopText = ai.i("js-so-pop-text"),
	oSoPopBtnCancel = ai.i("js-so-pop-btn-cancel"),
	oSoPopGuessSlip = ai.i("js-so-pop-guess-slip"),
	oSoPop= ai.i("js-so-pop"),
	oSoPopBtn = ai.i("js-so-pop-btn"),
	oSoPopGuess = ai.i("js-so-pop-guess"),
	oSoPopPromote = ai.i("js-so-pop-promote"),
	iSlipjsYesOrNo = 1,
	slipjs,
	num,
	oSoPopFormShell = ai.i("js-so-pop-form-shell");
	
ai.touchClick(oSoClear,function(){
	oSoPopText.value = "";
	oSoPopText.focus();
	oSoPopText.style.webkitBorderRadius = "3px";
	oSoPopBtnCancel.style.display = "block";
	oSoPopPromote.style.display = "block";
	oSoPopBtn.style.display = "none";
	oSoPopGuess.style.display = "none";
});

oSoPopBtnCancel.addEventListener('click', function(e){
	e.preventDefault();
	oSoPopBtn.focus();
	oSoPopText.value = "搜索";
	oSoPop.className = "so-pop-latent";
	oSoPop.style.display = "none";
	oSoPopPromote.style.display = "none";
	$('#js-so-pop-guess-slip').html('');
	setTimeout(function() {
		oSoPopText.style.webkitBorderRadius = "3px";
		oSoPop.style.display = "block";
	},100);
});
ai.touchClick(oSoPop,function(e){
	
	oSoPopBtn.focus();
	oSoPopText.value = "搜索";
	oSoPop.className = "so-pop-latent";
	oSoPopGuess.style.display = "none";
	oSoPopText.style.webkitBorderRadius = "3px";
	oSoPopBtnCancel.style.display = "block";
	oSoPopBtn.style.display = "none";
	oSoPopPromote.style.display = "none";
	$(document).one('click',function(){
		return false;
	});			
	
});

ai.touchMovePreventDefault(oSoPop);
ai.touchMovePreventDefault(oSoPopFormShell);
oSoPopFormShell.addEventListener('touchstart', function(e){
	e.stopPropagation(); 
});
oSoPopGuess.addEventListener('touchstart', function(e){
	e.stopPropagation(); 
});
oSoPopGuessSlip.addEventListener('touchstart',function(e){
	e.preventDefault();
	oSoPopBtn.focus();
});
oSoPopPromote.addEventListener('touchstart', function(e){
	e.stopPropagation(); 
});

oSoPopText.addEventListener('input',function(){
	num = oSoPopText.value.getBytes();
	fnInputIng();
	if(num>0){
		oSoPopBtnCancel.style.display = "none";
		oSoPopPromote.style.display = "none";
		oSoPopBtn.style.display = "block";
	}else{
		oSoPopBtnCancel.style.display = "block";
		oSoPopPromote.style.display = "block";
		oSoPopBtn.style.display = "none";
	}
});


function fnInputIng(){
	if(num>2){
		
		var platform = $("#id_search_type").val() == "andsearch" ? "2" : "3";
        var site="i";
		if(platform=="2")
		site="a";
		
		var limit = typeof($("#id_limit")) !== "undefined" ? $("#id_limit").val() : 10;
		
		var filter = typeof($("#id_filter")) !== "undefined" ? $("#id_filter").val() : 0;
		// jquery 异步调用服务器代码
		try {
			$.ajax({
			type: "get",
			dataType: "json",
			url: "/searchname.html",
			data: {"kwd":oSoPopText.value,"platform":platform,"random":Math.random(),"limit" : limit, "filter" : filter},
			success: function(result){
					$('#js-so-pop-guess-slip').html('');
					for(var i in result) {
						$('#js-so-pop-guess-slip').append('<li><a href="/game/detail_' + result[i].gameid + '.html" data-statis="text:txt_'+site+'_search_'+oSoPopText.value+'_'+ result[i].name + '_' + result[i].gameid + '">'+ result[i].name + '</a><span></span></li>');
					}
					
					oSoPopGuess.style.display = "block";
					oSoPopText.style.webkitBorderRadius = "3px 3px 0px 0px";
					oSoPopText.style.borderBottom = "1px solid #dddddd";
					
					if(iSlipjsYesOrNo){
						iSlipjsYesOrNo = 0;
						var iWinH = ai.wh();
						oSoPopGuess.style.height = iWinH-48+"px";
						ai.touchClick(oSoPopGuessSlip,function(e){
							if(oSoPopGuessSlip.webkitMatchesSelector.call( e.target, 'ul span') ) {
								var that =  e.target;
								oSoPopText.value = that.parentNode.firstChild.innerHTML;
								that.style.backgroundColor = "#f2f2f2";
								setTimeout(function() {
									that.style.backgroundColor = "#fff";
								},200);
							}
						});
						ai.resize(function(){
							oSoPopGuess.style.height = ai.wh()-48+"px";
						});
					}
				}
		   });
		} catch(e) {
			oSoPopText.style.webkitBorderRadius = "3px";
			oSoPopGuess.style.display = "none";
		}
	}else{
		
		oSoPopText.style.webkitBorderRadius = "3px";
		oSoPopGuess.style.display = "none";
	}
}

String.prototype.getBytes = function () {
	var value = this.match(/[^\x00-\xff]/ig);
	return this.length + (value == null ? 0 : value.length);
}
