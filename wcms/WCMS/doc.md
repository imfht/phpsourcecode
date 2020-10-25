#本章节为WCMS开发文档#  

#说明   
实际使用过程中首页和内容页都为静态，列表页为动态  
专题页面  ./index.php?news/i/?cid=2  
列表页面 ./index.php?news/c/?cid=2  
内容页面 ./index.php?news/v/?id=2014  

#路径  
所有在后台模板引入的文件如css,js,图片以及文章链接 前提都应该是./  
如果调用本身的文章 应该是 .{$l.html}  

#QQ一键登录  
1.配置后台网站域名 注意不带http://
2.在QQ互联中设置回调地址为 域名/qq.html  

#模型
其他模型都集成了文章模型的最基本字段  
##基本模型
<table>
<tr><th>字段</th><th>说明</th></tr>
<tr><td>id</td><td></td></tr>
<tr><td>title</td><td></td></tr>
<tr><td>date</td><td></td></tr>
<tr><td>html</td><td></td></tr>
<tr><td>views</td><td></td></tr>
<tr><td>summary</td><td></td></tr>
<tr><td>flag</td><td>数组id,nid,name</td></tr>
<tr><td>author</td><td></td></tr>
<tr><td>extend</td><td>扩展字段</td></tr>

</table>
##产品模型
<table>
<tr><th>字段</th><th>说明</th></tr>
<tr><td>module</td><td>数组 id,nid,sku,name,weight</td></tr>
</table>


#全局SEO变量名
#{$config.website}  {$config.website_name}  {$config.keywords}  {$config.description}