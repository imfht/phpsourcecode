{include file="header.tpl" jsload = "ajax"}
<h1>{$projectname}<span>/ {#task#}{if $task.title != ""}&nbsp;({$task.title}){/if}</span></h1>
<div class ="content_left">
<input type = "hidden" name = "selectedid" id  = "selectedid"/> {*required object for focus cells*}
<a href = "javascript:change('managetask.php?action=showtask&tid=248&id=1','content');">chk</a>
<div id = "content">content area</div>


</div> {*Content_left end*}
{include file="footer.tpl"}