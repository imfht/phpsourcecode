<?php
/**
 * 对应模块URL：m/模块名/动作名
 * 命名规则：动作名首之母大写 + Action
 * 文件名规则：动作名 + ".php"
 */
class IndexAction extends FWAction
{

	public function run()
	{
        // 带主题调用
//        $this->render("hello",array(
//            "title" => "Hello World",
//        ));

//        print_r($this->db);exit;

        // 获取book表到对象的映射
        $m = Table::model("book");
        // 找到主键ID为1的记录
        $r = $m->findByPk(1);

        // 对象方式调用字段
//        $r->title = $r->title . "1";
        // 更改查询的记录
//        $r->save();

        // 创建新纪录
//        $m = new Table("book");
//        $m->title = "abc";
//        $m->author = "abc";
//        $m->save();

        // 不带主题调用
        $this->renderPartial("hello",array(
           "title" => $r->author,
        ));
	}
}