<?php

if (empty($_GET['user_id'])) die;
$user_id = intval($_GET['user_id']);

$archive = dt_query_one("SELECT * FROM archive WHERE id = $user_id");
if (!$archive) {
	if (1 > dt_count('user_info', "WHERE id = $user_id")) die('用户不存在！^_^');

	$rs = dt_query("INSERT INTO archive (id, c_at) VALUES ($user_id , ".time().")");
	if (!$rs) die('新建archive数据失败！');
}

?>

<style type="text/css" media=screen>
.archive-show {
	background-color:#fff;
}
.archive-dd {
	height: 34px; 
}
</style>

<div class="well center-well" id="archive_show">
	<div class="row clearfix">
		<?php if (!empty($_SESSION['auth']) && $user_id == $_SESSION['auth']['id']) { ?>
		<div class="col-md-10 column">
			<legend><span class="glyphicon glyphicon-list"></span> 个人档</legend>
		</div>
		<div class="col-md-2 column">
			<a class="btn btn-default btn-block" href="javascript:load_archive_edit()"><span class="glyphicon glyphicon-edit"></span> 编辑</a>
		</div>
		<?php } else { ?>
		<div class="col-md-12 column">
			<legend><span class="glyphicon glyphicon-list"></span> 个人档</legend>
		</div>
		<?php } ?>
	</div>
	<div class="well archive-show">
		<legend> 基本信息</legend>
		<div class="row clearfix">
			<dl class="dl-horizontal col-md-10">
				<dt> 名字 </dt> <dd class="archive-dd"> <?php echo $archive['name_real'] ?>  </dd>
				<br>
				<dt> 性别 </dt> <dd class="archive-dd"> <?php echo $archive['sex'] ?> </dd>
				<br>
				<dt> 城市 </dt> <dd class="archive-dd"> <?php echo $archive['local'] ?> </dd>
				<br>
				<dt> 生日 </dt> <dd class="archive-dd"> <?php echo $archive['birthday'] ?> </dd>
			</dl>
		</div>
	</div>
	<div class="well archive-show">
		<legend> 联系信息</legend>
		<div class="row clearfix">
			<dl class="dl-horizontal col-md-10">
				<dt> 邮箱 </dt> <dd class="archive-dd"> <?php echo $archive['email'] ?>  </dd>
				<br>
				<dt> QQ </dt> <dd class="archive-dd"> <?php echo $archive['qq'] ?>  </dd>
				<br>
				<dt> 手机 </dt> <dd class="archive-dd"> <?php echo $archive['mobile'] ?> </dd>
			</dl>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('li#archive').addClass('active');
});
</script>
<?php if (!empty($_SESSION['auth']) && $user_id == $_SESSION['auth']['id']) { ?>
<div class="well" style="display:none" id="archive_edit">
	<div class="row clearfix">
		<div class="col-md-9 column">
			<legend><span class="glyphicon glyphicon-edit"></span> 编辑</legend>
		</div>
		<div class="col-md-3 column">
			<a class="btn btn-default" href="javascript:do_archive_edit()"><span class="glyphicon glyphicon-save"></span> 保存</a>
			<a class="btn btn-default" href="javascript:back_archive_show()"><span class="glyphicon glyphicon-fast-backward"></span> 返回</a>
		</div>
	</div>
	<div class="well archive-show">
		<legend> 基本信息</legend>
		<div class="row clearfix">
			<dl class="dl-horizontal col-md-10">
				<dt> 名字 </dt> <dd> <input id="archive_name_real" name="name_real" class="form-control" value="<?php echo $archive['name_real'] ?>"/>  </dd>
				<br>
				<dt> 性别 </dt> <dd class="archive-dd">
				<label class="radio-inline"> <input type="radio" name="sex" id="sex_1" value="男" <?php if ('男' == $archive['sex']) { ?> checked <?php } ?>> 男 </label>
				<label class="radio-inline"> <input type="radio" name="sex" id="sex_2" value="女" <?php if ('女' == $archive['sex']) { ?> checked <?php } ?>> 女 </label>
				</dd>
				<br>
				<dt> 城市 </dt> <dd> 
				<div class="row clearfix">
					<div class="col-md-6 column">
						<select name="local" class="form-control">
<?php $citys = array( 'A', '鞍山', '安阳', '安庆', '安康', '阿克苏', '安顺', '阿勒泰', '阿拉善',
	'阿坝', '阿里', '阿拉尔澳门', 'B', '北京', '保定', '滨州', '包头', '宝鸡',
	'本溪', '蚌埠', '北海', '巴彦淖尔', '白城', '白山', '亳州', '巴中', '白银',
	'百色', '毕节', '巴音郭楞', '保山', '博尔塔拉', 'C', '成都', '重庆', '长沙',
	'长春', '常州', '沧州', '赤峰', '承德', '常德', '长治', '郴州', '滁州',
	'巢湖', '潮州', '昌吉', '池州', '楚雄', '崇左', '昌都', '朝阳', 'D', '大连',
	'东莞', '德州', '东营', '大庆', '大同', '丹东', '儋州', '德阳', '达州',
	'大理', '大兴安岭', '定西', '德宏', '迪庆钓鱼岛', 'E', '鄂尔多斯', '恩施',
	'鄂州', 'F', '福州', '佛山', '抚顺', '阜阳', '阜新', '抚州', '防城港', 'G',
	'广州', '贵阳', '桂林', '赣州', '广元', '广安', '贵港', '固原', '甘南',
	'甘孜', '果洛', 'H', '杭州', '惠州', '哈尔滨', '合肥', '呼和浩特', '海口',
	'邯郸', '菏泽', '衡水', '淮安', '衡阳', '葫芦岛', '淮南', '汉中', '怀化',
	'淮北', '黄冈', '湖州', '黄石', '呼伦贝尔', '河源', '鹤壁', '鹤岗', '黄山',
	'红河', '河池', '哈密', '黑河', '贺州', '海西', '和田', '海北', '海东',
	'黄南', 'J', '济南', '济宁', '吉林', '锦州', '金华', '嘉兴', '江门', '荆州',
	'焦作', '晋中', '佳木斯', '九江', '晋城', '荆门', '鸡西', '吉安', '揭阳',
	'景德镇', '济源', '酒泉', '金昌', '嘉峪关', 'K', '昆明', '开封', '喀什',
	'克拉玛依', '库尔勒', '克孜勒苏', 'L', '兰州', '拉萨', '廊坊', '临沂', '洛阳',
	'聊城', '柳州', '连云港', '临汾', '漯河', '辽阳', '乐山', '泸州', '六安',
	'娄底', '莱芜', '龙岩', '吕梁', '丽水', '凉山', '丽江', '六盘水', '辽源',
	'来宾', '临沧', '陇南', '临夏', '林芝', 'M', '绵阳', '牡丹江', '茂名', '梅州',
	'马鞍山', '眉山', 'N', '南京', '宁波', '南宁', '南昌', '南通', '南阳', '南充',
	'内江', '南平', '宁德', '怒江', '那曲', 'P', '平顶山', '濮阳', '盘锦', '莆田',
	'攀枝花', '萍乡', '平凉', '普洱', 'Q', '青岛', '琼海', '秦皇岛', '泉州',
	'齐齐哈尔', '清远', '曲靖', '衢州', '庆阳', '七台河', '钦州', '潜江',
	'黔东南', '黔南', '黔西南', 'R', '日照', '日喀则', 'S', '上海', '深圳',
	'沈阳', '石家庄', '苏州', '汕头', '商丘', '三亚', '宿迁', '绍兴', '十堰',
	'四平', '三门峡', '邵阳', '上饶', '遂宁', '三明', '绥化', '石河子', '宿州',
	'韶关', '松原', '随州', '汕尾', '双鸭山', '朔州', '石嘴山', '商洛', '神农架',
	'山南', 'T', '天津', '太原', '唐山', '泰安', '台州', '泰州', '铁岭', '通辽',
	'通化', '天水', '铜陵', '铜川', '铜仁', '天门', '塔城', '吐鲁番', '图木舒克',
	'W', '武汉', '无锡', '乌鲁木齐', '威海', '潍坊', '温州', '芜湖', '渭南',
	'乌海', '梧州', '乌兰察布', '武威', '文山', '吴忠', '五家渠', '五指山', 'X',
	'西安', '厦门', '西宁', '徐州', '咸阳', '邢台', '襄阳', '新乡', '湘潭',
	'许昌', '信阳', '孝感', '忻州', '咸宁', '新余', '宣城', '仙桃', '锡林郭勒',
	'湘西', '兴安', '西双版纳香港', 'Y', '银川', '宜昌', '烟台', '扬州', '盐城',
	'营口', '岳阳', '运城', '榆林', '宜宾', '阳泉', '延安', '益阳', '永州',
	'玉林', '宜春', '阳江', '延边', '玉溪', '伊犁', '云浮', '伊春', '雅安',
	'鹰潭', '玉树', 'Z', '郑州', '珠海', '淄博', '中山', '枣庄', '张家口', '株洲',
	'镇江', '周口', '湛江', '驻马店', '肇庆', '自贡', '遵义', '漳州', '舟山',
	'张掖', '资阳', '张家界', '昭通', '中卫' ) ?>
							<?php foreach ($citys as $c) { ?>
							<option value="<?php echo $c ?>"><?php echo $c ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				</dd>
				<br>
				<dt> 生日 </dt> <dd>
				<div class="row clearfix">
					<div class="col-md-4 column">
						<select name="year" class="form-control">
							<option value="0">- - 年 - -</option>
							<?php for ($i = 1900; $i < 2015; $i++) { ?>
							<option value="<?php echo $i ?>"><?php echo $i ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-md-4 column">
						<select name="month" class="form-control">
							<option value="0">- - 月 - -</option>
							<?php for ($i = 1; $i < 10; $i++) { ?>
							<option value="0<?php echo $i ?>">0<?php echo $i ?></option>
							<?php } ?>
							<?php for ($i = 10; $i < 13; $i++) { ?>
							<option value="<?php echo $i ?>"><?php echo $i ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-md-4 column">
						<select name="day" class="form-control">
							<option value="0">- - 日 - -</option>
							<?php for ($i = 1; $i < 10; $i++) { ?>
							<option value="0<?php echo $i ?>">0<?php echo $i ?></option>
							<?php } ?>
							<?php for ($i = 10; $i < 32; $i++) { ?>
							<option value="<?php echo $i ?>"><?php echo $i ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				</dd>
			</dl>
		</div>
	</div>
	<div class="well archive-show">
		<legend> 联系信息</legend>
		<div class="row clearfix">
			<dl class="dl-horizontal col-md-10">
				<dt> 邮箱 </dt> <dd> <input id="archive_email" name="email" class="form-control" value="<?php echo $archive['email'] ?>"/>  </dd>
				<br>
				<dt> QQ </dt> <dd> <input id="archive_qq" name="qq" class="form-control" value="<?php echo $archive['qq'] ?>"/>  </dd>
				<br>
				<dt> 手机 </dt> <dd> <input id="archive_mobile" name="mobile" class="form-control" value="<?php echo $archive['mobile'] ?>"/> </dd>
			</dl>
		</div>
	</div>
	<input type="hidden" name="token" value="<?php echo get_token() ?>"/>
</div>


	<script type="text/javascript">

	$(document).ready(function(){
		//默认生日
		var birthday = "<?php echo $archive['birthday'] ?>";
		var birthday_year = birthday.substring(0,4);
		var birthday_month = birthday.substring(5,7);
		var birthday_day = birthday.substring(8,10);
		$("select[name='year']").find("option[value='"+birthday_year+"']").attr("selected", true);
		$("select[name='month']").find("option[value='"+birthday_month+"']").attr("selected", true);
		$("select[name='day']").find("option[value='"+birthday_day+"']").attr("selected", true);
		//默认所在地
		$("select[name='local']").find("option[value='<?php echo $archive['local'] ?>']").attr("selected", true);
	});
//显示个人档 ------------------->
function back_archive_show()
{
	$("div#archive_edit").hide();
	$("div#archive_show").show();
}
//编辑个人档 ------------------->
function load_archive_edit()
{
	$("div#archive_show").hide();
	$("div#archive_edit").show();
}
//保存个人档的编辑结果 ------------------->
function do_archive_edit()
{
	var birthday = $('select[name="year"]').find("option:selected").val()+"-"+$('select[name="month"]').find("option:selected").val()+"-"+$('select[name="day"]').find("option:selected").val();

	$.post("?c=center&a=do_archive_edit", { name_real: $("input#archive_name_real").val(), sex: $('input[name="sex"]:checked').val(), local: $('select[name="local"]').find("option:selected").text(), birthday: birthday, email: $("input#archive_email").val(), qq: $("input#archive_qq").val(), mobile: $("input#archive_mobile").val(), token: $('input[name=token]').val()}, function (rs) { 
		if ('s0' != rs) { 
			rs = $.parseJSON(rs);
			$('input[name=token]').val(rs.token);
			alert(rs.msg);
			return;
		}
		alert ("保存成功了！^_^");
		get_archive_index();
	})
}

</script>
<?php } ?>

