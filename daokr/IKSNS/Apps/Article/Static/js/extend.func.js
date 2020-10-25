function tips(c){ $.dialog({content: '<font style="font-size:14px;">'+c+'</font>',fixed: true, width:300, time:1500}); }
function succ(c){ $.dialog({icon: 'succeed',content: '<font  style="font-size:14px;">'+c+'</font>' , time:2000});}
function error(c){$.dialog({icon: 'error',content: '<font  style="font-size:14px;">'+c+'</font>' , time:2000});}



//提交新建
function checkForm(that)
{
	var title = $(that).find('input[name=title]').val();
	var arrimport = $(that).find('select[name=cateid]').val();
	var content = $(that).find('textarea[name=content]').val();
	if(arrimport == 0){ tips('请选择一个分类再发表吧！'); return false;}
	if(title == '' || content == ''){tips('请填写标题和内容'); return false;}

	$(that).find('input[type=submit]').val('正在提交^_^').attr('disabled',true);
}