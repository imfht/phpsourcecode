<?php

function smarty_core_write_file($params,&$smarty)
{
$_dirname = dirname($params['filename']);
if ($params['create_dirs']) {
$_params = array('dir'=>$_dirname);
require_once(SMARTY_CORE_DIR .'core.create_dir_structure.php');
smarty_core_create_dir_structure($_params,$smarty);
}
$_tmp_file = tempnam($_dirname,'wrt');
if (!($fd = @fopen($_tmp_file,'wb'))) {
$_tmp_file = $_dirname .DIRECTORY_SEPARATOR .uniqid('wrt');
if (!($fd = @fopen($_tmp_file,'wb'))) {
$smarty->trigger_error("problem writing temporary file '$_tmp_file'");
return false;
}
}
fwrite($fd,$params['contents']);
fclose($fd);
if (DIRECTORY_SEPARATOR == '\\'||!@rename($_tmp_file,$params['filename'])) {
@unlink($params['filename']);
@rename($_tmp_file,$params['filename']);
}
@chmod($params['filename'],$smarty->_file_perms);
return true;
}

?>