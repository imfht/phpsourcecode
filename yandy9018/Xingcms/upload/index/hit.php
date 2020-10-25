<?php

if (!defined('APP_IN')) exit('Access Denied');
$id = isset($_GET['id']) ?intval($_GET['id']) : showmsg('È±ÉÙID',-1);
$rs = $db->query_unbuffered("update ".$db->tb_prefix."news set n_hits = n_hits+1 where n_id=".$id);
$data = $db->row_select_one('news',"n_id=".$id);
echo "document.write('".$data['n_hits']."');";

?>