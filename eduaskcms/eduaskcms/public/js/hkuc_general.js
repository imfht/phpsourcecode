$(document).ready(function(){
	$('body')
		.on('click','.javascript',function(ev){
			var callback;

			if(callback=$(this).attr('rel')){
				if(window[callback]){
					window[callback].call(this);
				}
			}
			if ( ev && ev.preventDefault ) ev.preventDefault(); else window.event.returnValue = false; return false;
		});
});

var HKUC={
	alert_dialog:function (message,option){
		var id=option&&option.id?option.id:'alert';
		var default_option={
			'title':'消息',
			'content':option && option.pre?message:HKUC.nl2br(message),
			'esc':false,
			'okValue':'确定',
			'cancelValue':'取消',
			'id':'alert',
			'width':300,
			'height':100
		};
		option=$.extend(default_option,option)

		var $dialog=$.dialog.get(id);
		if($dialog && !$dialog.closed){
			$dialog.size(option.width,option.height);
			$dialog.title(option.title);
			$dialog.content(option.content);
		}
		else{
			$.dialog(option);
		}
	},
	
	confirm_dialog:function (message,callback,option){
		HKUC.close_dialog('confirm');
		$.dialog($.extend({
			'title':'确认',
			'content':option && option.pre?message:HKUC.nl2br(message),
			'esc':false,
			'id':'confirm',
			'ok':$.proxy(callback,this),
			'cancel':true,
			'okValue':'确定',
			'cancelValue':'取消',
			'width':300
		},option));
	},
	
	close_dialog:function (id){
		if(!id)id='alert';
		if($.dialog.list[id])
			$.dialog.list[id].close();
	},
	
	prompt_dialog:function (message,value,option,callback){
		HKUC.close_dialog('prompt');
		var back_this=this;
		if(!value)value='';
		
		if(option.content)
			option.content='<form id="prompt_form">'+option.content+'<input class="submit" type="submit" /></form>';
		
		$.dialog($.extend({
			'title':'输入内容',
			'content':'<form id="prompt_form">'+(option && option.pre?message:HKUC.nl2br(message))+'<div><input class="prompt_input" name="value" type="text" value="'+value+'" /></div><input class="submit" style="display:none;" type="submit" /></form>',
			'esc':true,
			'ok':function(){
				if(callback){
					var input=this.dom.content.find('form#prompt_form').serializeArray();
					var val=HKUC.parse_serial_array(input,{});

					if(callback.call(back_this,val.value))
						return true;
					return false;
				}
				else{
					return true;
				}
			},
			'cancel':true,
			'okValue':'确定',
			'cancelValue':'取消',
			'lock':true,
			'id':'prompt'
		},option));
		
		$.dialog.list['prompt'].dom.content
			.find('form').submit(function(){
				if($.dialog.list['prompt'].config.ok.call($.dialog.list['prompt'])){
					HKUC.close_dialog('prompt');
				}
				return false
			})
			.find('input,textarea').keypress(function(e){
				if(e.keyCode==27 && $(this).val()==''){
					HKUC.close_dialog('prompt');
				}
				return true;
			}).filter(':first').focus().select();
	},
	
	prompt_form:function(cols,vals,option,callback){
		HKUC.close_dialog('form');
		var back_this=this;
		
		if(typeof(cols)=='string'){
			var template=cols;
			cols=null;
		}
		else{
			if(typeof(TPL.prompt_form)=='undefined'){
				TPL.prompt_form=function(){/*
<form id="prompt_form">
	<table><tbody>
	{each $cols as $col $variable}
{if $col.type!='hidden'}
		<tr><th><label for="setting_{$variable}">{$col.name}</label></th><td>
{if $col.type=='checker'}
			<input type="hidden" name="{$variable}" value="0" />
			<input id="setting_{$variable}" type="checkbox" name="{$variable}" value="1"{if $vals[$variable]} checked="checked"{/if} />
{else if $col.type=='integer' || $col.type=='text'}
			<input id="setting_{$variable}" type="text" name="{$variable}" value="{$vals[$variable]}" />
{else if $col.type=='password'}
			<input id="setting_{$variable}" type="password" name="{$variable}" />
{else if $col.type=='checkbox'}
			{each $col.options as $value $key}
			<input type="checkbox" name="{$variable}[]" value="{$key}"{if in_array($key,$vals[$variable])} checked="checked"{/if}><label>{$value}</label>
			{/each}
{else if $col.type=='radio'}
			{each $col.options as $value $key}
			<input type="radio" name="{$variable}" value="{$key}"{if in_array($key,$vals[$variable])} checked="checked"{/if}><label>{$value}</label>
			{/each}
{else if $col.type=='textarea'}
			<textarea id="setting_{$variable}" name="{$variable}" cols="40" rows="6">{echo $vals[$variable]}</textarea>
{else if $col.type=='show'}
			<span>{$vals[$variable]}</span>
{/if}
		</td></tr>
{/if}
	{/each}
	</tbody></table>
	{each $cols as $col $variable}
{if $col.type=='hidden'}
	<input type="hidden" name="{$variable}" value="{$vals[$variable]}" />
{/if}
	{/each}
</form>
				*/};
			}
			
			var template=TPL.prompt_form.render({
				$cols:cols,
				$vals:vals
			});
		}
		
		$.dialog($.extend({
			'title':'输入内容',
			'content':template,
			'esc':true,
			'ok':function(){
				if(callback){
					var cols_type={};
					if(cols && cols.length)
					for(var i=0; i<cols.length; i++){
						if(cols[i]['type'])
							cols_type[cols[i]['variable']]=cols[i]['type'];
					}
					
					var val=this.dom.content.find('form').serializeArray();
					if(callback.call(back_this,HKUC.parse_serial_array(val,cols_type)))
						return true;
					return false;
				}
				else{
					return true;
				}
			},
			'cancel':true,
			'okValue':'确定',
			'cancelValue':'取消',
			'lock':true,
			'id':'form',
			'width':'auto',
			'height':100
		},option));
		
		$.dialog.list['form'].dom.content
			.find('form').submit(function(){
				if($.dialog.list['form'].config.ok.call($.dialog.list['form'])){
					close_dialog('form')
				}
				return false
			})
			.find('input,textarea').filter(':first').focus();
	},
	
	nl2br:function (str){
		if(typeof(str)=='string')
			return str.replace(/\r?\n/g,'<br />');
		else
			return str;
	},
	
	parse_serial_array:function (input,cols_type){
		if(!cols_type)cols_type={}
		var tmp={};

		for(var i=0;i<input.length; i++){
			switch(cols_type[input[i].name]){
				case 'checker':
					input[i].value=!!parseInt(input[i].value);
					break;
				case 'integer':
					input[i].value=parseInt(input[i].value);
					break;
			}

			var eval_str='tmp.'+input[i].name;
			var append=false;

			if(eval_str.substr(eval_str.length-2)=='[]'){
				eval_str=eval_str.substring(0,eval_str.length-2);
				append=true;
			}

			eval_str=eval_str.replace(/\[/g,'["').replace(/\]/g,'"]');
			var checkpos=4;

			while((checkpos=eval_str.indexOf('[',checkpos))!==-1){
				if(!eval(eval_str.substr(0,checkpos))){
					eval(eval_str.substr(0,checkpos)+'={}');
				}
				checkpos+=1;
			}

			if(append){
				if(!eval(eval_str))eval(eval_str+'=[]');
				var max_index=eval('Array.prototype.push.call('+eval_str+',input[i].value)');
				if(!eval(eval_str+'.length'))
					eval(eval_str+'['+max_index+']=input[i].value')
			}
			else{
				eval(eval_str+'=input[i].value');
			}
		}

		return tmp;
	},
	
	isJsonValidate:function isJsonValidate(str){
		return str.match(/^(\[|\{).*(\}|\])$/);
	},
	
	default_successHandler:function (msg,data){
		if(msg)alert(msg);
		else alert('提交成功');//提交成功
		return true;
	},
	
	default_failHandler:function (msg,data){
		if(msg)alert(msg);
		else alert('提交失败');//提交失败
		return false;
	},
	
	ajax_request:function(url,data,successHandlers,errorHandlers){
		successHandlers=$.extend({},arguments.callee.defaultSuccessHandlers,successHandlers);
		errorHandlers=$.extend({},arguments.callee.defaultErrorHandlers,errorHandlers);
		
		return $.ajax({
			'url':url,
			'data':data,
			'type':data?'post':'get',
			'success':$.proxy(
				function(response){
					if(HKUC.isJsonValidate($.trim(response))){
						var rslt=eval('('+response+')');
						if(this.handler[rslt.result]){
							return this.handler[rslt.result].call(this.self,rslt.message,rslt.data,this.run);
						}
						return false;
					}
					else{
						if(this.handler['_']){
							this.handler['_'].call(this.self,response,this.run);
						}
						else{
							alert(response);
						}
					}
				},
				{
					'self':this,
					'handler':successHandlers?successHandlers:{},
					'run':$.proxy(
						function(){
							return this.arguments.callee.apply(this.self,this.arguments);
						},
						{
							'arguments':arguments,
							'self':this
						}
					)
				}
			),
			'error':$.proxy(
				function(XMLHttpRequest, textStatus, errorThrown){
					if(this.handler[XMLHttpRequest.status]){
						return this.handler[XMLHttpRequest.status].call(this.self,errorThrown,this.run);
					}
					else if(this.handler['_']){
						return this.handler['_'].call(this.self,errorThrown,this.run);
					}
				},
				{
					'self':this,
					'handler':errorHandlers?errorHandlers:{},
					'run':$.proxy(
						function(){
							return this.arguments.callee.apply(this.self,this.arguments);
						},
						{
							'arguments':arguments,
							'self':this
						}
					)
				}
			)			
		})
	},
	
	imgFit:function (obj,width,height,shrink){
		var imageRate1=0,imageRate2=0;
		if(!obj)return;
		var temp_img = new Image();
		temp_img.src=obj.src;
		if(temp_img.width>width || temp_img.height>height)
		{
			if(width)imageRate1=temp_img.width/width;
			if(height)imageRate2=temp_img.height/height;

			if(height){
				if(width){
					if(imageRate2>imageRate1){
						obj.style.height = temp_img.height/imageRate2+"px";
						obj.style.width = 'auto';
					}
					else{
						obj.style.width = temp_img.width/imageRate1 +"px";
						obj.style.height = 'auto';
					}
				}
				else{
					obj.style.height = temp_img.height/imageRate2+"px";
					obj.style.width = 'auto';
				}
			}
			else{
				obj.style.width = temp_img.width/imageRate1 +"px";
				obj.style.height = 'auto';
			}
		}

		
		if(shrink && temp_img.height<=obj.offsetHeight && temp_img.width<=obj.offsetWidth){
			obj.style.height = temp_img.height+"px";
			obj.style.width = temp_img.width+"px";
		}
	},
	
	imgCache:function(url){
		if(!arguments.callee.cache)arguments.callee.cache=[];
		var temp_img = new Image();
			temp_img.src=url;
		arguments.callee.cache.push(temp_img);	
	},
	
	hkuc_switch:function(selector,subfix,activeClass,eventClass){
		if(!subfix)subfix='content';
		if(!activeClass)activeClass='active';
		if(!eventClass)eventClass='click';
		
		var $selected=$(selector);
		$selected.each(function(){
			$(this).bind(eventClass,function(){
				var $org=$selected.filter('.'+activeClass);
				$('#'+$org.attr('id')+'_'+subfix).hide();
				$org.removeClass(activeClass);
				
				$(this).addClass(activeClass);
				$('#'+$(this).attr('id')+'_'+subfix).show();
			})
		})
	},	
	
	dummy:'dummy'
}

var NEST_SELECT=function(selector,data,default_value){
	var back_this=this;
	this.data=data;
	this.$setter=$(selector);
	this.$container=$(selector+'_display');
	this.$container.empty().append('<a class="nest_select_level" rel="0"></a><span class="nest_select_container"><a class="nest_select_level" rel="1"></a></span>');					
	this.default_value=parseInt(default_value);
	
	var hasRoot=true;

	if(!this.data.children[-1]){
		hasRoot=false;
		var temp_childrens=$.extend({},this.data.list);
	}


	this.data.parent={};
	for(var parent_id in this.data.children){
		for(var i=0; i<this.data.children[parent_id].length; i++){
			this.data.parent[this.data.children[parent_id][i]]=parent_id;
			if(!hasRoot)temp_childrens[this.data.children[parent_id][i]]=false;
		}
	}
	
	if(!hasRoot){
		this.data.children[-1]=[];
		for(top_id in temp_childrens){
			if(temp_childrens[top_id])this.data.children[-1].push(top_id);
		}
	}

	this.$container.on('change','select.nest_selector',function(){
		back_this.show_children(this);
	});
	
	this.$setter.change($.proxy(this.set_value,this));

	if(default_value !== null){
		this.$setter.val(parseInt(default_value)).trigger('change');
	}
}

NEST_SELECT.prototype={
	$setter:null,
	$container:null,
	default_value:0,
	data:null,
	show_children:function(obj,set_value,no_trigger){
		var level=parseInt($(obj).parent().children('a.nest_select_level').attr('rel'));
		var $sub_container=this.$container.find('a.nest_select_level[rel='+(level)+']').siblings('.nest_select_container');

		if(!$sub_container.length){
			this.$container.find('a.nest_select_level[rel='+(level)+']').parent().append('<span class="nest_select_container"></span>');
			$sub_container=this.$container.find('a.nest_select_level[rel='+(level)+']').siblings('.nest_select_container');
		}

		var html='<a class="nest_select_level" rel="'+(level+1)+'"></a>';
		var value=set_value?set_value:$(obj).val();
		
		if(this.data.children[value] && this.data.children[value].length){
			html+='<select class="nest_selector">';
			
			html+='<option value="0">≡请选择≡</option>';
			for(var i=0; i<this.data.children[value].length;i++){
				html+='<option value="'+this.data.children[value][i]+'">'+this.data.list[this.data.children[value][i]]+'</option>';
			}
			html+='</select>';
		}
		else if(value==0){
			$parent_selector=$(obj).parent().siblings('select.nest_selector');
			if($parent_selector.length)
				value=$parent_selector.val();
		}
		
		this.$setter.val(value);
		if(!no_trigger && set_value){
			this.$setter.trigger('change');
		}
		html+='<span class="nest_select_container"></span>';
		$sub_container.html(html);
		
		this.$setter.trigger('blur');

		if(set_value)$sub_container.siblings('select.nest_selector').val(set_value).trigger('change');
	},
	set_value:function(){
		var family=[],cur_id=this.$setter.val();
		var root_exists=false;
		
		if(parseInt(cur_id)){
			while(parseInt(cur_id)){
				if(cur_id==-1)root_exists=true;
				
				family.unshift(cur_id);
				cur_id=this.data.parent[cur_id];
			}
		}
		if(!root_exists)family.unshift(-1);

		for(var i=0; i<family.length; i++){
			this.show_children(this.$container.find('a.nest_select_level[rel='+i+']'),family[i],true);
		}
	}
}
