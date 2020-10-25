<?php
# extra name and function 
# wadelau@ufqi.com on Sun Jan 31 10:22:15 CST 2016
#

require("../comm/header.inc.php");


$gtbl = new GTbl($tbl, array(), $elementsep);

include("../comm/tblconf.php");

# main actions

$out = 'my output content...';

# or

$data['respobj'] = array('output'=>'content');

# module path
$module_path = '';
include_once($appdir."/comm/modulepath.inc.php");

# without html header and/or html footer
$isoput = false;

require("../comm/footer.inc.php");

?>
