<?php $vars = json_decode('{"title":"Wetpl Demo"}',true); ?>
<?php foreach($vars as $k => $v){ ?>
<?php $$k = $v; ?>
<?php } ?>
