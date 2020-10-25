<?php
function dump_table($table)
{
    $need_close = false;
$a=mysql_query("show create table `{$table}`");//显示创建mysql数据的的语句结构。
$row=mysql_fetch_assoc($a);//导出表结构
    $rs = mysql_query("SELECT * FROM `{$table}`");
	
	$dom = new DOMDocument("1.0","utf-8");   //创建xml对象
	$dom->formatOutput = true;
	
	$root = $dom->createElement("xmls");    //创建xmls节点
	$dom->appendChild($root);               //将xmls节点加入xml对象
	
	$tableNode = $dom->createElement("table");     //创建table节点
	$tableValue = $dom->createAttribute("value");  //创建value属性
	$tableNode->appendChild($tableValue);          //将value属性加入table节点
	$tableNode->setAttribute("value",$row['Create Table']);  //设置table节点中value属性的值
	$root->appendChild($tableNode);                //将table节点加入xmls节点，意思就是成为xmls的子节点
	 
	
    while ($row = mysql_fetch_row($rs)) {           //如上，每有一行数据就创建一个row节点，每个row节点都有一个value属性，
		$insert = $dom->createElement("row");       //他的值就是insert语句，每个row节点都成为xmls的子节点
		$in = $dom->createAttribute("value");
		$insert->appendChild($in);
		$insert->setAttribute("value",get_insert_sql($table, $row));
		$root->appendChild($insert);
    }	
	$dom->save("{$table}.xml");       //最后保存xml文件，括号内是保存的路径
    mysql_free_result($rs);//释放内存

}

//将表中每一行拼接成sql语句
function get_insert_sql($table, $row)
{
    $sql = "INSERT INTO `{$table}` VALUES (";
    $values = array();
    foreach ($row as $value) {
        $values[] = "'" . mysql_real_escape_string($value) . "'";
    }
    $sql .= implode(', ', $values) . ");";
    return $sql;
}

/////////////导入数据库
function Import($xmlPath,$yprefix,$nprefix){

	/*mysql_connect($hostname,$username,$password) or die("不能连接数据库!");
	mysql_select_db($database)or die("数据库名称错误!");
	mysql_query("set names utf8");*/
	$xml = new DOMDocument();//创建xml对象
	$xml->load($xmlPath);//根据括号内路径载入文件
	
	$tables = $xml->getElementsByTagName("table");//取得节点名称为table的节点集合
	$SqlCreateTable = "";             //新建创建表的sql语句
	foreach($tables as $table){       //遍历名称为table的节点集合
			$SqlCreateTable = $table->getAttribute("value");//取得每个table节点中value属性的值，并拼接到sql语句上
		}
        
     
     $SqlCreateTable=str_replace($yprefix,$nprefix,$SqlCreateTable);
     
	 mysql_query($SqlCreateTable); //运行创建表的sql语句
	
	$rows = $xml->getElementsByTagName("row");//取得节点名称为row的节点集合
	$SqlInsert = "";        //新建插入数据的sql语句
	foreach($rows as $row){    //遍历名称为row的节点集合
			$SqlInsert=$row->getAttribute("value");     //取得每个row节点中value属性的值，并拼接到sql语句上
            $SqlInsert=str_replace($yprefix,$nprefix,$SqlInsert);
            
		    mysql_query($SqlInsert);
	}		
}

/////////////////
function traverse($path = '.',$yprefix,$nprefix) {
                $current_dir = opendir($path);    //opendir()返回一个目录句柄,失败返回false
                while(($file = readdir($current_dir)) !== false) {    //readdir()返回打开目录句柄中的一个条目
                    $sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //构建子目录路径
                    if($file == '.' || $file == '..') {
                        continue;
                    } else if(is_dir($sub_dir)) {    //如果是目录,进行递归
                        echo 'Directory ' . $file . ':<br>';
                        traverse($sub_dir);
                    } else {    //如果是文件,直接输出
                        Import($path.'/'.$file,$yprefix,$nprefix);
                    }
                }
}

