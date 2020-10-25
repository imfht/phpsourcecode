{include file="news/header.tpl"}
	
{literal}
<style>
table{font-size:12px;}
</style>
{/literal}

<!-- 头部// -->
{include file="news/top.tpl"}

<!-- 左侧// -->


<!-- 头部// -->
{include file="news/nav.tpl"}


<!-- start: Content -->
<div id="content" class="">


<div class="well">


<div class="box-content"><!-- Default panel contents -->

<div class="form-inline suoding">
动作监控：
{foreach from=$flag item=l}
<a href="./index.php?log/chart/?event={$l.event}" class="label">{$l.event|cntruncate:6}</a>
{/foreach}
</div>

      <div style="background:#666;color:#ccc;font-size:12px;padding: 10px;"> 

<ul>

								{foreach from=$log item=l name=g}
<li>	{$l.action_time|date_format:"%Y-%m-%d %H:%M:%S"} {$l.username} {$l.event} </li> 
						{/foreach}
</ul>
</div>
<div class="pagination pagination-centered">
<ul id="pager"></ul></div>
</div>
</div></div>

{literal}
<script language='javascript'>

function myrefresh() 
{ 
       window.location.reload(); 
} 
var options = {
currentPage: {/literal}{$num.current}{literal},
totalPages: {/literal}{$num.page}{literal},
numberOfPages:5,
bootstrapMajorVersion:3,
pageUrl: function(type, page, current){
    return "./index.php?log/listing/?p="+page;
}
}
$('#pager').bootstrapPaginator(options);
</script>
{/literal}
{include file="news/footer.tpl"}
