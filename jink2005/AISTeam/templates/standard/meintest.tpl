{include file="header.tpl"  jsload = "ajax" }
<script type = "text/javascript" src = "include/js/mycalendar.js"></script>

<div id = "timers">

</div>
<a href = "javascript:makeTimer();">aaa</a>


{literal}
<script type = "text/javascript">

function makeTimer()
{
thetimer = new timetracker();
var thenum = $$('#timers a').length;
thenum = thenum + 1;
$('timers').innerHTML += "<a id = \"time"+thenum+"\" href = \"javascript:void(0);\">bbbb</a><br />"
Event.observe("time"+thenum,"click",thetimer.toggleTracker);
}

function handleTimer(theEvent)
{

}
function getTimerObj()
{

return new timetracker();
}
</script>
{/literal}
{include file="footer.tpl"}