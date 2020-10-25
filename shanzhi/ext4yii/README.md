#ext4yii说明
**目前为开发第一版，里面有很多BUG，不可作为线上正式环境使用！**

1、首先要在框架导入扩展

2、view文件按照自定义标签编写

3、在controller里面EXT::display($this,"index2",array("user"=>$user));

$this 是当前对象，
index2 是view文件，
和renderFile一样
效果：
![输入图片说明](http://git.oschina.net/uploads/images/2015/0720/212942_e8a50d3e_124841.jpeg "在这里输入图片标题")

=》

![输入图片说明](http://git.oschina.net/uploads/images/2015/0720/212957_cedf8cb4_124841.jpeg "在这里输入图片标题")
