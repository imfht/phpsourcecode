<<?php
$s2 = new SaeStorage();
$name =$_FILES['myfile']['name'];
print_r($_FILES);
exit;
echo $s2->upload('upload',$name,$_FILES['myfile']['tmp_name']);//把用户传到SAE的文件转存到名为test的storage
echo $s2->getUrl("upload",$name);//输出文件在storage的访问路径
echo '<br/>';