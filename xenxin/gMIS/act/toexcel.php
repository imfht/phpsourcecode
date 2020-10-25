<?php

# do convert hm records to csv used in excel file, Sat Jun 23 13:21:46 CST 2012

# print_r($hm);

$dnld_dir = $appdir."/dnld";
$dnld_file = "data_".str_replace("gmis_","",$tbl)."_".date("Y-m-d-H-i").".csv";

$myfp = fopen($dnld_dir.'/'.$dnld_file, 'wb');
fwrite($myfp, chr(0xEF).chr(0xBB).chr(0xBF));
if($myfp){
    $fieldsname = array();
    $firstrow = $hm[0];
    foreach($firstrow as $k=>$v){
        $fieldsname[] = $gtbl->getCHN($k);
    }
    fputcsv($myfp, $fieldsname);
	/*
    foreach($hm as $fields){
        fputcsv($myfp, $fields);
    }
	*/
    # retrieve data
	foreach($hm as $k=>$v){
		$str = "";
		foreach($v as $k2=>$v2){
			#print "k2:$k2, v2:$v2\n";	
			if($gtbl->getInputType($k2) == "select"){
				$v2 = $gtbl->getSelectOption($k2, $v2,'',1, $gtbl->getSelectMultiple($k2));		
                if(preg_match("/([^\-|\(]+)[\(|\-]*/", $v2, $matchArr)){
                    $v2 = $matchArr[1];
                    #debug($matchArr);
                } 
			}
			else if(strpos($v2,",") !== false){
				#$v2 = str_replace(",", "_", $v2);
				$v2 = str_replace("\"", "\"\"", $v2);
				$v2 = '"'.$v2.'"'; # see https://stackoverflow.com/questions/4617935/is-there-a-way-to-include-commas-in-csv-columns-without-breaking-the-formatting
			}
			$str .= str_replace("\n", "<br/>", $v2).",";
		}
		$str = substr($str, 0, strlen($str)-1);
		fwrite($myfp, $str."\n");
	}
}
fclose($myfp);

$out .= "<script type=\"text/javascript\">";
$out .= "parent.window.open('".$rtvdir."/dnld/".$dnld_file."','Excel File Download','scrollbars,toolbar,location=0,status=yes,resizable,width=600,height=400');";
$out .= "</script>";

?>
