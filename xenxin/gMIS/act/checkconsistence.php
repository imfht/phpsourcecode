<?php

# embedded in jdo.php?act=list-dodelete

$hmdelete = $gtbl->getBy("*", implode(" and ", $fieldargv));
if($hmdelete[0]){
    $hmdelete = $hmdelete[1][0];
}
#print __FILE__.":hmdelete: ";
#print_r($hmdelete);

for($hmi=$min_idx; $hmi<=$max_idx; $hmi++){
    $field = $gtbl->getField($hmi);
    if($field == null | $field == '' 
            || $field == 'id'){
        continue;
    }

    $inputtype = $gtbl->getInputType($field);
    if($inputtype == 'file'
		|| inString('file', $field)
			|| inString('path', $field)
			|| inString('img', $field)
			|| inString('image', $field)
			|| inString('pic', $field)){
        if($hmdelete[$field] != ''){
    		if(strpos($hmdelete[$field], "$shortDirName/") !== false){ 
    		    $hmdelete[$field] = str_replace("$shortDirName/", "", $hmdelete[$field]); 
    		}
            unlink($appdir."/".$hmdelete[$field]); 
            $out .= __FILE__.": file:[".$appdir."/".$hmdelete[$field]."] has been deleted.";
        }
    }

} 

?>
