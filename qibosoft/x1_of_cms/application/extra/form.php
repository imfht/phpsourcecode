<?php

//这个是系统的配置文件,请不要修改,不然下次升级会被替换,要修改的话,就修改my_form.php这个文件 教程http://help.php168.com/1915087

$form_array = [
   'text' => '单行文本',
   'textarea' => '多行文本',
    'ueditor' => 'UEditor 百度编辑器',
    'radio' => '单选按钮',
    'checkbox' => '多选按钮',
    'checkboxtree' => '树状多选按钮',
    'select' => '下拉框',
    'image' => '单张图片',
    'file' => '单个文件',
    'images' => '多张图片',
	'images2' => '多张图片附带介绍及网址',
    'files' => '多个文件',
	'files2' => '多个文件带标题',
    //'jcrop' => '图片裁剪',
	'money' => '金额',
    'number' => '数字',
    'time' => '时间',
 //'switch' => '开关',
  'date' => '日期',
  'datetime' => '日期+时间',
  'daytime' => '多日期',
    'static' => '只读文本',
    //'tags' => '标签',
  'hidden' => '隐藏',
  'array' => '数组',
  'shop_array' => '商品型号价格库存',
  'usergroup' => '按用户组填数值',
  'usergroup2' => '用户组多选',
  'usergroup3' => '用户组单选',
  'jftype' => '虚拟币种类',
  'jftype2' => '虚拟币种类(含余额)',
  'password' => '密码',

  //'linkage' => '普通联动下拉框',
  //'linkages' => '快速联动下拉框',
 // 'wangeditor' => 'wangEditor 编辑器',
  //'editormd' => 'markdown 编辑器',
  //'ckeditor' => 'ckeditor 编辑器',
  'icon' => '字体图标',
  'bmap' => '百度地图',
  'color' => '颜色选择',
  'links' => '链接导航',
  //'masked' => '格式文本',
  //'range' => '范围',

];

if (is_file(APP_PATH.'extra/my_form.php')) {
    $_form_array = include APP_PATH.'extra/my_form.php';
    $form_array = array_merge($form_array,$_form_array);
}

return $form_array;