<h2>关键字回复列表：</h2>
<hr class="mb10"></hr>
<a href="{url('index/keywordadd')}" class="button mb10"><i class="fa fa-plus"></i> 添加关键字</a>

<FORM method="post" action="" target="_self">
  <div class="list_b">
    <TABLE width="100%">
        <TR>
          <TH>关键字</TH>
		  <TH width=100>回复类型</TH>
          <TH width=150>添加时间</TH>
          <TH width=180>管理操作</TH>
        </TR>
		{loop $list $vo}
        <TR>
          <TD><A href="#" target="_blank">{$vo['keyword']}</A></TD>
		  {if $vo['type']== '1'}
		  <TD>文字消息</TD>
		  {else}
		  <TD>图文消息</TD>
		  {/if}
          <TD>{$vo['createtime']}</TD>
          <TD>
		  <A href="{url('index/keywordedit',array(id=>$vo['id']))}" class="button"><i class="fa fa-edit fa-lg"></i> 修改</A>
		  <A onClick="return confirm('确定要删除吗？')" href="{url('index/keyworddel',array(id=>$vo['id']))}"  class="button"><i class="fa fa-trash-o fa-lg"></i> 删除</A>
		  </TD>
        </TR>
		{/loop}
    </TABLE>
  </FORM>