/**
 * 
 */
KindEditor.plugin('goods', function(K) {
	var self = this;
	var name = 'goods',lang = self.lang(name + '.');
	var dialog ;

	self.clickToolbar(name, function() {
		dialog  = K.dialog({
	        width : 500,
	        title : '插入商品',
	        body : '<div style="margin:10px;">'+
	        			'<div class="search_box">'+
	        				'<input name="keywords" value="">'+
	        				'<input name="searchBtn" type="button" value="'+lang.search+'"/>'+
	        			'</div>'+
						'<div class="goods_list">'+
						'<select name="goods_id"><option value="0">请选择</option></select>'+
						'</div>'+
	        		'</div>',
	        closeBtn : {
	                name : '关闭',
	                click : function(e) {
	                        dialog.remove();
	                }
	        },
	        yesBtn : {
	                name : '确定',
	                click : function(e) {
	                        goods_id = select.val();
	                        url = 'admin.php?s=/Goods/ajaxGoodsInfo.html';
	        				$.post(url,{'id':goods_id},function(result){
	        					if(result.errno<=0){
	        						goods = result.content;
	        						html = "<p ><a class='article-goods' name='"+goods.id+"' href='"+goods.url+"'>"+goods.name+"</a</p>";
	     	                        self.insertHtml(html);
	     	                        dialog.remove();
	        					}else{
	        						alert(result.message);
	        					}
	        				},'json')
	                       
	                }
	        },
	        noBtn : {
	                name : '取消',
	                click : function(e) {
	                        dialog.remove();
	                }
	        }
		});
        dialog.show();
		div = dialog.div,
		searchBtn = K('[name="searchBtn"]', div);
		keywordBox = K('[name="keywords"]', div);
		select = K('[name="goods_id"]', div);
		searchBtn.click(function(){
			keyword = keywordBox.val();
			if(keyword==''||keyword=='undefined'){
				alert(lang.keywordempty);
			}else{
				url = 'admin.php?s=/Goods/searchGoods.html';
				$.post(url,{'k':keyword},function(result){
					if(result.errno<=0){
						obj = result.content;
						
						select.empty();
						for(i=0;i<obj.length;i++){
							option = '<option value="'+obj[i].id+'">'+obj[i].name+'</option>';
							select.append(option);
						}
					}else{
						alert(result.message);
					}
				},'json')
			}
		});
		
    });
	
	
	
});