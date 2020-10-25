<?php
require_once('../../config/doc-config.php');
$mm = new DatabaseInfo();
foreach($mm->tables as $table_name)
{
	if(substr($table_name,0,strlen(TB_PREFIX))==TB_PREFIX)
	{
		$rst = "<?php\nclass c_".str_replace(TB_PREFIX,'',$table_name)." extends DtDatabase\n{\n";
		$rst .= create_declare($mm->get_fields($table_name),$table_name);
		$rst .=	create_func_construct($table_name);
		$rst .= create_func_get_request($mm->get_fields($table_name),$table_name);
		$rst .= create_func_addnew($mm->get_fields($table_name));
		$rst .= create_func_save($mm->get_fields($table_name),$table_name);
		html2File($rst."}\n?>",str_replace(TB_PREFIX,'',$table_name).'.php');
	}
}


/**
 * 这个类用于获取数据库的表结构信息
 * ver 1.0
 * Created Date : 2007-1-13 
 * Author:Maxazure
 * Mail:maxazure@gmail.com
 */
class DatabaseInfo
{
	private $dh,$dbname;
	public $tables;
	function __construct()
	{
		$this->dh=mysql_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD);
		$this->dbname = DB_DBNAME;
		$this->tables = $this->get_tables();
	}
	/**
	 * get_tables
	 * 根据数据库表结构，得到表名数组
	 *
	 * Date:2007-1-13 Author:Maxazure
	 * 
	 * @return array
	 */
	function get_tables()
	{
		$tempArr = array();

		$result = mysql_query("SHOW TABLES FROM ".$this->dbname.";",$this->dh);

		if (!$result) {
			print "DB Error, could not list tables\n";
			print 'MySQL Error: ' . mysql_error();
			exit;
		}
		else
		{

			while ($row = mysql_fetch_row($result))
			{
				$tempArr[] = $row[0];
			}
		}
		mysql_free_result($result);
		return $tempArr;
	}
	/**
	 * get_fields
	 * 根据数据库表结构，得到表结构数组 
	 * 返回的数组的每个元素都包含
	 * $o->field_name 字段名称
	 * $o->field_type 字段类型
	 * 
	 * Date:2007-1-13 Author:Maxazure
	 *
	 * @param string $table
	 * @return array
	 */
	function get_fields($table)
	{
		$tempArr = array();
		$fields = mysql_list_fields($this->dbname, $table, $this->dh);
		$columns = mysql_num_fields($fields);

		for ($i = 0; $i < $columns; $i++) {
			$tempObj = new stdClass();
			$tempObj->field_name = mysql_field_name($fields, $i);
			$tempObj->field_type = mysql_field_type($fields, $i);
			$tempArr[] = $tempObj;
		}
		return $tempArr;
	}
	function conv2PhpType($mysqlType)
	{
		switch ($mysqlType)
		{
			case 'string':
			case 'int':
			case 'datetime':
				return $mysqlType;
				break;
			case 'blob':
				return 'string';
				break;

		}
	}

}

/**
 * create_declare
 * 用来创建类变量声明字符串 
 * 
 * Date:2007-1-13 Author:Maxazure
 * 
 * @param array $fields
 * @return string
 */
function create_declare($fields,$table_name)
{
	$rst ="";
	foreach ($fields as $field)
	{
		$rst .="\tpublic \$$field->field_name;\n";
	}
	//添加一些判断状态的声明
	$rst .="\n\tpublic \$primary_key='id';\n";  	//默认主键
	$rst .="\n\tprotected \$table_name;\n";  	//默认表名

	$rst .="\tprivate \$im_virgin=false;\n";			//这个对象是否是空的

	return $rst;
}

function create_func_get_request($fields,$table)
{
	$allowAry = array('title','content','txtHeight','summary','keyword','description');

	$rst ="\n\tpublic function get_request(\$request=array())\n\t{\n";
	$rst .="\t\tif(!empty(\$request)){\n";
	foreach ($fields as $field)
	{
			if(in_array($field->field_name,$allowAry))
			{
				$rst .="\t\t\$this->$field->field_name=\$request['$field->field_name'];\n";
			}
			else
			{
				$rst .="\t\tif(\$request['$field->field_name'])\$this->$field->field_name=\$request['$field->field_name'];\n";
			}
	}
	$rst .="\t\t}\n\t\t}\n";
	return $rst;
}
function create_func_addnew($fields)
{

	$rst ="\n\tpublic function addnew(\$request=array())\n\t{\n";
	$rst .="\t\t\$this->im_virgin =true;";
	$rst .="\t\tif(!empty(\$request)){\n";
	$rst .="\t\t\$this->get_request(\$request);\n";
	$rst .="\t\t}\n\t\t}\n";
	return $rst;

}

function create_func_construct($table_name)
{
	$table_name =str_replace(TB_PREFIX,'',$table_name);
	$rst ="\n\tpublic function __construct()\n\t{\n";
	$rst .="\t\t\$this->table_name = TB_PREFIX.'$table_name';\n";
	$rst .="\t\t\$this->DtDatabase();\t\t\n}";
	return $rst;

}
/**
 * create_func_save
 * 用来创建类方法字符串
 *
 * Date:2007-1-13 Author:Maxazure
 * 
 * @param array $fields
 * @param string $table
 * @return string
 */
function create_func_save($fields,$table)
{
	if(!empty($fields))
	{
		$rst ="\n\tpublic function save()\n\t{\n";
		$rst .="\t\tif(\$this->im_virgin){\n";
		$rst .="\t\teval(\"\\\$this->\$this->primary_key=NULL;\");\n";
		$rst .="\t\t\$sql=\"INSERT INTO `\$this->table_name` (\";\n";
		foreach ($fields as $o)
		{
			if($o->field_name!='id')
			{
				$rst .="\t\t\$sql.=isset(\$this->$o->field_name)?\"$o->field_name,\":'';\n";
			}
		}
		$rst .="if(substr(\$sql,strlen(\$str)-1,1)==',')\$sql=substr(\$sql,0,strlen(\$str)-1);";
		$rst .="\t\t\$sql.=\")VALUES (\";\n";
		foreach ($fields as $o)
		{
			if($o->field_name!='id' )
			{
				$rst .="\t\t\$sql.=isset(\$this->$o->field_name)?\"'\$this->$o->field_name',\":'';\n";
			}

		}
		$rst .="if(substr(\$sql,strlen(\$str)-1,1)==',')\$sql=substr(\$sql,0,strlen(\$str)-1);";
		$rst .="\t\t\$sql.=')';\n";
		$rst .="\n\t\t}\n\t\telse{\n";
		$rst .="\n\t\teval('\$pid=\$this->'.\$this->primary_key.';\$this->'.\$this->primary_key.'=NULL;');\n";
		$rst .="\n\t\t\$sql.=\"UPDATE `\$this->table_name` SET \";\n";
		foreach ($fields as $o)
		{
			
			
			$rst .="\t\t\$sql.=isset(\$this->$o->field_name)?\"`$o->field_name`='\$this->$o->field_name',\":'';\n";
			
		}
		$rst .="if(substr(\$sql,strlen(\$str)-1,1)==',')\$sql=substr(\$sql,0,strlen(\$str)-1);";
		$rst .="\n\t\t\$sql.=\" WHERE `\$this->primary_key` ='\$pid' LIMIT 1\";\n";
		$rst .="\t\t}\n";
		//sql语句构造完毕
		$rst .="\t\treturn \$this->query(\$sql);\n";


		return $rst."\t}\n";
	}
	else
	{
		return "\tpublic function save()\n{}\n";
	}
}
//生成新的文件($str为字符串,$filePath为生成时的文件路径包括文件名)
function html2File($str,$filePath)
{
	$fp=fopen($filePath,'w+');
	fwrite($fp,$str);
	fclose($fp);
}
//从文件中读取字符
function file2String($filePath)
{
	$fp = fopen($filePath,"r");
	$content_= fread($fp, filesize($filePath));
	fclose($fp);
	return $content_;

}
?>