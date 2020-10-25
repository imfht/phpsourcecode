<?php
global $xml_conf,$xml_path,$xml_rootobj,$xml_listtags;
$xml_path 	=$GLOBALS['xml']['path'];
$xml_root	=$GLOBALS['xml']['root'];
/* tags that are always to be handled as lists */
$xml_listtags = explode(" ", "list");

function xml_startElement($parser, $name, $attrs) {
	global $depth, $curpath, $config, $havedata, $xml_listtags;
	
	array_push($curpath, strtolower($name));
	
	$ptr =& $config;
	foreach ($curpath as $path) {
		$ptr =& $ptr[$path];
	}
	
	/* is it an element that belongs to a list? */
	if (in_array(strtolower($name), $xml_listtags)) {
	
		/* is there an array already? */
		if (!is_array($ptr)) {
			/* make an array */
			$ptr = array();
		}
		
		array_push($curpath, count($ptr));
		
	} else if (isset($ptr)) {
		/* multiple entries not allowed for this element, bail out */
		die(sprintf("XML error: %s at line %d cannot occur more than once\n",
				$name,
				xml_get_current_line_number($parser)));
	}
	
	$depth++;
	$havedata = $depth;
}

function xml_endElement($parser, $name) {
	global $depth, $curpath, $config, $havedata, $xml_listtags;
	
	if ($havedata == $depth) {
		$ptr =& $config;
		foreach ($curpath as $path) {
			$ptr =& $ptr[$path];
		}
		$ptr = "";
	}
	
	array_pop($curpath);

	if (in_array(strtolower($name), $xml_listtags))
		array_pop($curpath);
	
	$depth--;
}

function xml_cData($parser, $data) {
	global $depth, $curpath, $config, $havedata;
	
	$data = trim($data, "\t\n\r");
	
	if ($data != "") {
		$ptr =& $config;
		foreach ($curpath as $path) {
			$ptr =& $ptr[$path];
		}

		if (is_string($ptr)) {
			$ptr .= $data;
		} else {
			if (trim($data, " ") != "") {
				$ptr = $data;
				$havedata++;
			}
		}
	}
}

function xml_parse_xml_config($cffile, $rootobj) { //解释XML文件

	global $depth, $curpath, $config, $havedata, $xml_listtags;

	$config = array();
	$curpath = array();
	$depth = 0;
	$havedata = 0;
	
	$xml_parser = xml_parser_create();
	
	xml_set_element_handler($xml_parser, "xml_startElement", "xml_endElement");
	xml_set_character_data_handler($xml_parser, "xml_cData");
	
	if (!($fp = fopen($cffile, "r"))) {
		die("Error: could not open XML input\n");
	}
	
	while ($data = fread($fp, 4096)) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf("XML error: %s at line %d\n",
						xml_error_string(xml_get_error_code($xml_parser)),
						xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);
	
	if (!$config[$rootobj]) {
		die("XML error: no $rootobj object found!\n");
	}
	
	fclose($fp);
	
	return $config[$rootobj];
}

function xml_xml_dump_xml_config_sub($arr, $indent) { 

	global $xml_listtags;
	
	$xmlconfig = "";

	foreach ($arr as $ent => $val) {
		if (is_array($val)) {
			/* is it just a list of multiple values? */
			if (in_array(strtolower($ent), $xml_listtags)) {
				foreach ($val as $cval) {
					if (is_array($cval)) {
						$xmlconfig .= str_repeat("\t", $indent);
						$xmlconfig .= "<$ent>\n";
						$xmlconfig .= xml_xml_dump_xml_config_sub($cval, $indent + 1);
						$xmlconfig .= str_repeat("\t", $indent);
						$xmlconfig .= "</$ent>\n";
					} else {
						$xmlconfig .= str_repeat("\t", $indent);
						if ((is_bool($cval) && ($cval == true)) ||
							($cval === ""))
							$xmlconfig .= "<$ent/>\n";
						else if (!is_bool($cval))
							$xmlconfig .= "<$ent>" . htmlspecialchars($cval) . "</$ent>\n";
					}
				}
			} else {
				/* it's an array */
				$xmlconfig .= str_repeat("\t", $indent);
				$xmlconfig .= "<$ent>\n";
				$xmlconfig .= xml_xml_dump_xml_config_sub($val, $indent + 1);
				$xmlconfig .= str_repeat("\t", $indent);
				$xmlconfig .= "</$ent>\n";
			}
		} else {
			if ((is_bool($val) && ($val == true)) || ($val === "")) {
				$xmlconfig .= str_repeat("\t", $indent);
				$xmlconfig .= "<$ent/>\n";
			} else if (!is_bool($val)) {
				$xmlconfig .= str_repeat("\t", $indent);
				$xmlconfig .= "<$ent>" . htmlspecialchars($val) . "</$ent>\n";
			}
		}
	}
	
	return $xmlconfig;
	echo $xmlconfig;
}

function xml_dump_xml_config($arr, $rootobj) { //生成一个XML文件

	$xmlconfig = "<?xml version=\"1.0\"?" . ">\n";
	$xmlconfig .= "<$rootobj>\n";
		
	$xmlconfig .= xml_xml_dump_xml_config_sub($arr, 1);
	
	$xmlconfig .= "</$rootobj>\n";
	
	return $xmlconfig;
}
/* save the system configuration */
function xml_conf_save() {

	global $xml_conf,$xml_path,$xml_root;

	if (time() > mktime(0, 0, 0, 9, 1, 2004))	/* make sure the clock settings is plausible */
		$xml_conf['lastchange'] = date("Y-m-d H:s:i",time());
	
	/* generate configuration XML */
	$xmlconfig = xml_dump_xml_config($xml_conf, $xml_root);
	
	/* write configuration */
	$fd = fopen("{$xml_path}/config.xml", "wb");
	
	if (!$fd)
		die("Unable to open config.xml for writing in xml_conf_save()\n");
		
	fwrite($fd, $xmlconfig);
	fclose($fd);
	

	/* re-read configuration */
	$xml_conf   = xml_parse_xml_config("{$xml_path}/config.xml", "{$xml_root}");//解析转成数组
	
}


function createFolder($path)
{
	if (!file_exists($path))
	{
		createFolder(dirname($path));
		mkdir($path, 0777);
	}
}

//当文件不存在时表时第一次运行系统，需要配置相关参数
if(!file_exists("{$xml_path}/config.xml")){
	if(!is_dir($xml_path)) createFolder($xml_path);
	$xmlconfig = <<< EOD
<?xml version="1.0"?>
<niaomuniao>
	<version>1.9</version>
	<lastchange>2013-05-15 10:14:36</lastchange>
	<system>
		<title>AAA认证计费系统</title>
		<tel>13666271969</tel>
		<desc/>
		<soon_start_ip>172.30.0.0</soon_start_ip>
		<soon_end_ip>172.30.255.255</soon_end_ip>
		<stop_start_ip>172.31.0.0</stop_start_ip>
		<stop_end_ip>172.31.255.255</stop_end_ip>
		<days>15</days>
	</system>
</niaomuniao>
EOD;
	/* write configuration */
	$fd = fopen("{$xml_path}/config.xml", "wb");
	
	if (!$fd) {
		die("Unable to open config.xml for writing in xml_conf_save()\n");
	}
	fwrite($fd, $xmlconfig);
	fclose($fd);
}
return xml_parse_xml_config("{$xml_path}/config.xml", "{$xml_root}");//解析转成数组




?>
