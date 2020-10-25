define(['jquery', 'sent', 'form'], function($, sent, form){
	var database = {
		export: function(){
			var $form = $("#export-form"), $export = $("#export"), tables
			$optimize = $("#optimize"), $repair = $("#repair");
	
			$optimize.add($repair).click(function(){
				$.post(this.href, $form.serialize(), function(data){
					if(data.code){
						sent.msg(data.msg,'success');
					} else {
						sent.msg(data.msg,'error');
					}
					setTimeout(function(){
						$('#top-alert').find('button').click();
						$(this).removeClass('disabled').prop('disabled',false);
					},1500);
				}, "json");
				return false;
			});
		
			$export.click(function(){
				$export.parent().children().addClass("disabled");
				$export.html("正在发送备份请求...");
				$.post(
					$form.attr("action"),
					$form.serialize(),
					function(data){
						if(data.code){
							tables = data.data.tables;
							$export.html(data.msg + "开始备份，请不要关闭本页面！");
							backup(data.data.tab);
							window.onbeforeunload = function(){ return "正在备份数据库，请不要关闭！" }
						} else {
							sent.msg(data.msg,'error');
							$export.parent().children().removeClass("disabled");
							$export.html("立即备份");
							setTimeout(function(){
								$('#top-alert').find('button').click();
								$(this).removeClass('disabled').prop('disabled',false);
							},1500);
						}
					},
					"json"
				);
				return false;
			});
		
			function backup(tab, status){
				status && showmsg(tab.id, "开始备份...(0%)");
				$.get($form.attr("action"), tab, function(data){
					if(data.code){
						var info = data.data;
						showmsg(tab.id, data.msg);
		
						if(!$.isPlainObject(info.tab)){
							$export.parent().children().removeClass("disabled");
							$export.html("备份完成，点击重新备份");
							window.onbeforeunload = function(){ return null }
							return;
						}
						backup(info.tab, tab.id != info.tab.id);
					} else {
						updateAlert(data.msg,'alert-error');
						$export.parent().children().removeClass("disabled");
						$export.html("立即备份");
						setTimeout(function(){
							$('#top-alert').find('button').click();
							$(that).removeClass('disabled').prop('disabled',false);
						},1500);
					}
				}, "json");
		
			}
		
			function showmsg(id, msg){
				$form.find("input[value=" + tables[id] + "]").closest("tr").find(".info").html(msg);
			}
		},
		import: function(){
			$(".db-import").click(function(){
				var self = this, status = ".";
				$.get(self.href, success, "json");
				window.onbeforeunload = function(){ return "正在还原数据库，请不要关闭！" }
				return false;
			
				function success(data){
					if(data.code){
						if(data.data.gz){
							data.msg += status;
							if(status.length === 5){
								status = ".";
							} else {
								status += ".";
							}
						}
						$(self).parent().prev().text(data.msg);
						if(data.data.part){
							$.get(self.href, 
								{"part" : data.data.part, "start" : data.data.start}, 
								success, 
								"json"
							);
						}  else {
							window.onbeforeunload = function(){ return null; }
						}
					} else {
						sent.msg(data.msg,'error');
					}
				}
			});
		}
	}
	return database;
})