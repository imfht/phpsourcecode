<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>添加频道栏目</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="container-layout">
	<!-- 按钮组 -->
	<div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
		<button type="button" onClick="location.href='<?php echo U('index');?>'" class="button">
			<i class="icon-th-list"></i>
			频道列表
		</button>
		<button type="button" onClick="location.href='<?php echo U('add');?>'" class="button bg">
			<i class="icon-edit"></i>
			添加频道
		</button>
	</div>
	<!-- 按钮组 End -->

	<!-- 标签页 Start -->
	<div class="tab padding-top">
		<!-- 标签页切换按钮组 Start -->
		<div class="tab-head bg border">
			<ul class="tab-nav padding-top">
				<li class="active"><a href="#tab-one" style="outline:none;">基本设置</a></li>
				<li><a href="#tab-two" style="outline:none;">模板设置</a></li>
				<li><a href="#tab-three" style="outline:none;">静态HTML</a></li>
				<li><a href="#tab-four" style="outline:none;">SEO 设置</a></li>
			</ul>
		</div>
		<!-- 标签切换按钮组 End -->
		<form action="http://localhost/PHPUnion/index.php?m=Admin&c=Cate&a=add" method="POST">
			<!-- 标签内容组 Start -->
			<div class="tab-body" style="padding-top:1px;">
				<!-- 基本设置 Start -->
				<div class="tab-panel active" id="tab-one">
					<table class="table table-bordered table-hover table-condensed">
						<tr>
							<td align="right">上级 <span class="text-dot">*</span></td>
	                        <td>
	                            <select name="pid">
	                                <option value="0">一级栏目</option>
	                                        <?php
        //初始化
        $hd['list']['c'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($cate)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($cate as $c) {
                //开始值
                if ($listId<0) {
                    $listId++;
                    continue;
                }
                //步长
                if($listId!=$listNextId){$listId++;continue;}
                //显示条数
                if($listShowNum>=100)break;
                //第几个值
                $hd['list'][c]['index']++;
                //第1个值
                $hd['list'][c]['first']=($listId == 0);
                //最后一个值
                $hd['list'][c]['last']= (count($cate)-1 <= $listId);
                //总数
                $hd['list'][c]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
	                                    <option value="<?php echo $c['cid'];?>"
	                                        <?php if($hd['get']['pid'] == $c['cid']){ ?>selected='selected'<?php } ?>><?php echo $c['_name'];?></option>
	                                <?php }}?>
	                            </select>
	                        </td>
						</tr>
						<tr>
							<td align="right">栏目名称 <span class="text-dot">*</span></td>
							<td><input type="text" name="catname" required="" /></td>
						</tr>
						<tr>
	                        <td align="right">栏目类型</td>
	                        <td>
	                            <label><input type="radio" name="cattype" checked="checked" value="1"/> 普通栏目</label>
	                            <label><input type="radio" name="cattype" value="2"/> 频道封面</label>
	                            <label><input type="radio" name="cattype" value="3"/> 外部链接(在跳转Url处填写网址)</label>
	                        </td>
	                    </tr>
	                    <tr>
	                    	<td align="right">静态目录 <span class="text-dot">*</span></td>
	                    	<td><input type="text" name="catdir" required=""/></td>
	                    </tr>
	                    <tr>
	                        <td align="right">跳转Url</td>
	                        <td>
	                            <input type="text" name="cat_redirecturl"/>
	                            <span>栏目类型选择为“外部链接”才有效</span>
	                        </td>
	                    </tr>
	                        <tr>
	                            <td align="right">栏目访问</td>
	                            <td>
	                                <label><input type="radio" name="cat_url_type" value="1"/> 静态</label>
	                                <label><input type="radio" name="cat_url_type" value="2" checked="checked"/> 动态</label>
	                            </td>
	                        </tr>
	                        <tr>
	                            <td align="right">内容访问</td>
	                            <td>
	                                <label><input type="radio" name="arc_url_type" value="1"/> 静态</label>
	                                <label><input type="radio" name="arc_url_type" value="2" checked="checked"/> 动态</label>
	                            </td>
	                        </tr>
	                        <tr>
	                            <td align="right">导航显示</td>
	                            <td>
	                                <label><input type="radio" name="cat_show" value="1" checked="checked"/> 是</label>
	                                <label><input type="radio" name="cat_show" value="0"/> 否</label>
	                                <span class="hd-validate-notice">前台使用&lt;channel&gt;标签时是否显示</span>
	                            </td>
	                        </tr>
					</table>
				</div>
				<!-- 基本设置 End -->

				<!-- 模板设置 Start -->
				<div class="tab-panel" id="tab-two">
					<table class="table table-bordered table-hover table-condensed">
                        <tr>
                            <td width="200" align="right">封面模板 <span class="text-dot">*</span></td>
                            <td>
                                <input type="text" name="index_tpl" required="" id="index_tpl" value="article_index.html"/>
                                <button type="button" class="button bg" onClick="Theme('index_tpl');">选择封面模板</button>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">列表页模板 <span class="text-dot">*</span></td>
                            <td>
                                <input type="text" name="list_tpl" required="" id="list_tpl" value="article_list.html"/>
                                <button type="button" class="button bg" onClick="Theme('list_tpl');">选择列表模板</button>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">内容页模板 <span class="text-dot">*</span></td>
                            <td>
                                <input type="text" name="arc_tpl" required="" id="arc_tpl" value="article_default.html"/>
                                <button type="button" class="button bg" onClick="Theme('arc_tpl');">选 择 内 容 页</button>
                            </td>
                        </tr>
                    </table>
				</div>
				<!-- 模板设置 End -->

				<!-- 静态HTML设置 Start -->
				<div class="tab-panel" id="tab-three">
					<table class="table table-bordered table-hover table-condensed">
                        <tr>
                            <td width="200" align="right">栏目页URL规则 <span class="text-dot">*</span></td>
                            <td>
                                <input type="text" name="cat_html_url" required="" value="{catdir}/{page}.html"/>
                                <span>{cid} 栏目ID, {catdir} 栏目目录, {page} 列表的页码</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">内容页URL规则 <span class="text-dot">*</span></td>
                            <td>
                                <input type="text" name="arc_html_url" required="" value="{catdir}/{y}/{m}{d}/{aid}.html"/>
                                <span>{y}、{m}、{d} 年月日,{timestamp}UNIX时间戳 {catdir}栏目目录 {cid}栏目cid {aid}文章ID</span>
                            </td>
                        </tr>
                    </table>
				</div>
				<!-- 静态HTML设置 End -->

				<!-- SEO 设置 Start -->
				<div class="tab-panel" id="tab-four">
					<table class="table table-bordered table-hover table-condensed">
                        <tr>
                            <td width="200" align="right">关键字</td>
                            <td>
                                <input type="text" name="cat_keyworks" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">描述</td>
                            <td>
                                <textarea name="cat_description"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">SEO标题</td>
                            <td>
                                <input type="text" name="cat_seo_title" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right">SEO描述</td>
                            <td>
                                <textarea name="cat_seo_description"></textarea>
                            </td>
                        </tr>
                    </table>
				</div>
				<!-- SEO 设置 End -->
			</div>
			<!-- 标签页内容组 End -->

			<!-- 提交按钮 Submit Start -->
			<div class="container-layout bg" style="padding:0px;">
				<table class="table table-hover margin-little-top border">
					<tr>
						<td width="200"></td>
						<td>
							<button type="submit" class="button bg-sub radius-none">
								<i class="icon-check-square-o"></i>
								提交保存
							</button>
						</td>
					</tr>
				</table>
			</div>
			<!-- 提交按钮组 Submit End -->
		</form>
	</div>
</div>
<!-- 标签页 End -->
</body>
</html>