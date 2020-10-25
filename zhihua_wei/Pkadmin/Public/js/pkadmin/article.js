/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/12/1 0020
 * Time: 下午 3:08
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: article.js
 * ==========================================
 */

//表单提交验证
var Script = function() {

	'use strict';

	$.validator.setDefaults({
		submitHandler: function(form) {
			form.submit();
		}
	});

	$().ready(function() {
		$("#articleForm").validate({
			rules: {
				category_id: {
					required: true,
				},
				keywords: {
					required: true,
				},
				article_title: {
					required: true,
				},
				article_desc: {
					required: true,
				},
				content: {
					required: true,
				},
			},

			messages: {
				category_id: "请选择文章分类",
				keywords: "请为分类输入简要关键字！",
				article_title: "请输入文章名称！！",
				article_desc: "请输入文档的简单摘要！",
				content:"请输入文章正文内容！"
			}
		});
	});

}();

$('.wysihtml').wysihtml5({
	"font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
    "emphasis": true, //Italics, bold, etc. Default true
    "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
    "html": true, //Button which allows you to edit the generated HTML. Default false
    "link": true, //Button to insert a link. Default true
    "image": true, //Button to insert an image. Default true,
    "color": true //Button to change color of font
});