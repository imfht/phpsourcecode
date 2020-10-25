<?php !defined('_Amysql') && exit; ?>

<h2>AMH » ALiOSS </h2>
<div id="category">
<a href="index.php?c=ALiOSS&a=ALiOSS_list" id="ALiOSS_list">ALiOSS</a>
<a href="index.php?c=ALiOSS&a=Bucket_list" id="Bucket_list" >Bucket</a>
</div>
<script>
var action = '<?php echo $_GET['a'];?>';
var action_dom = G(action) ? G(action) : G('ALiOSS_list');
action_dom.className = 'activ';
</script>
