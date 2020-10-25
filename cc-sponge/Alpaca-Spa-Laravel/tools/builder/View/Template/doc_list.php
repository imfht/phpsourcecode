<?php
$output = "";
$output .= "\n";
$output .= "**接口地址：**\n";
$output .= "\n";
$output .= "~/main/{$this->classNameLc}/lists{$this->className}\n";
$output .= "\n";
$output .= "\n";
$output .= "**请求参数：**\n";
$output .= "\n";
$output .= "\n";
$output .= "| 参数名 | 说明 |\n";
$output .= "| -------- | -------- |\n";
$output .= "| pageSize | 分页大小 |\n";
$output .= "| pageNum | 页码 |\n";
$output .= "\n";
$output .= "**返回参数：**\n";
$output .= "\n";
$output .= "\n";
$output .= "| 参数名 | 说明 |\n";
$output .= "| -------- | -------- |\n";
$output .= "| code | 代码 |\n";
$output .= "| msg | 提示语 |\n";
$output .= "| data | 数据 |\n";
$output .= "\n";
$output .= "**list参数说明：**\n";
$output .= "\n";
$output .= "\n";
$output .= "| 参数名 | 说明 |\n";
$output .= "| -------- | -------- |\n";
foreach ($this->fields as $f) {
    $output .= "| {$f['field']} | {$f['comment']} |\n";
}
$output .= "\n";
?>
<?php echo $output; ?>