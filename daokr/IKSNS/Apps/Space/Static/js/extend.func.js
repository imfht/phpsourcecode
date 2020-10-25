function tips(c){ $.dialog({content: '<font style="font-size:14px;">'+c+'</font>',fixed: true, width:300, time:2000});}
function succ(c){ $.dialog({icon: 'succeed',content: '<font  style="font-size:14px;">'+c+'</font>' , time:2000});}
function error(c){$.dialog({icon: 'error',content: '<font  style="font-size:14px;">'+c+'</font>' , time:2000});}

//日志首页 伸缩效果
$(function(){
	$('.note_list dt span').bind('click',function(){
		$(this).toggleClass('close');
		$(this).parent().parent().find('.action').slideToggle('fast');
	});
});

//提交新建
function checkNoteForm(that)
{
	var title = $(that).find('input[name=title]').val();
	var content = $(that).find('textarea[name=content]').val();
	
	if(title == '' || content == ''){tips('请填写标题和内容'); return false;}
	if(title.length < 2){ tips('标题太短了'); return false;}
	if(content.length < 10){ tips('日记内容太少了'); return false;}

	$(that).find('input[type=submit]').val('正在提交^_^').attr('disabled',true);
}