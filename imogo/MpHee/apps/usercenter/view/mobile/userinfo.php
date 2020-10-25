<script type="text/javascript" src="__APPURL__/js/mymain.js"></script>
<script type="text/javascript" src="__APPURL__/js/dater_min.js"></script>
<script type="text/javascript" src="__APPURL__/js/aSelect.js"></script>
<script type="text/javascript" src="__APPURL__/js/aLocation.js"></script>
<style>
.select {
	width: 100%;
}

.list_ul_card .forms dt {
	padding: 5px;
}
</style>
<script>
function bd(){
	var d = new iDialog();
		d.open({
			classList: "valid_phone bd",
			title:"",
			close:"",
			content:'<ul class="list_ul_card">\
				<form id="form2" action="javascript:;" method="post">\
					<li data-card>\
						<table>\
							<tr class="input wrapInput">\
								<td>\
									<label class="pre" style="color:#333333;"> 实体卡手机号：</label> \
								</td>\
								<td style="width:100%;">\
									<input type="tel" name="entry_telephone" placeholder="请输入手机号码" maxlength="15" class="input" />\
								</td>\
							</tr>\
							<tr class="input wrapInput" style="">\
								<td style="width:100%;">\
									<input type="number" placeholder="验证码：" name="entry_checkcode" maxlength="10" class="input" />\
								</td>\
								<td>\
								<input type="button" onclick="getCardVCode(this, event,\'form2\', \'entry_telephone\' , \'getBindValidCode\');" class="button vcode" value="获取验证码" />\
								</td>\
							</tr>\
							<tr class="input wrapInput" style="display:none">\
							<td>\
								<label class="pre" style="color:#333333;"> 实体卡号：</label> \
							</td>\
							<td style="width:100%;">\
								<input type="text" name="offline_number" placeholder="请输入实体卡号" maxlength="30" class="input" />\
							</td>\
						</tr>\
						</table>\
					</li>\
				</form>\
			</ul>',
			btns:[
					{id:"", name:"确定", onclick:"fn.call();", fn: function(self){
						//alert("queding");
						bind(self);
					}},
					{id:"", name:"取消", onclick:"fn.call();", fn: function(self){
						self.die();
					}}
				]
		});
	}
	$().ready(function(){
		
		var sex='1';
		if(!isNaN(sex)){
			$('select[name="sex"]').val(sex);
		}
		
		try{
			var curDate=new Date("1990/09/08");
			new dater({
				selectYear:document.getElementById("selectYear"),
				selectMonth:document.getElementById("selectMonth"),
				selectDate:document.getElementById("selectDate"),
				minDat: new Date("1900/01/01"),
				maxDat: new Date(),
				curDat: curDate			
			}).init();
		}catch(e){
			//do nothing
		}
		
		var sel = aSelect({data: aLocation});
		
		sel.bind('#selectProvince', '0');
		
		
		sel.bind('#selectCity', '0');
		
		
		sel.bind('#selectArea', '0');
		
		
		
				
		
		
		
		
		
		
	});

	function submit1(){
		
		var form = document.getElementById("form1");
		
		if(form.username.value.length<2){
			alert("请输入姓名,不少于2个字符", 1500);
			return;
		}
		if(form.telephone.value.length==0){
			alert("请输入正确的手机号", 1500);
			return;
		}
		
		if(form.address && form.address.required){
			if(!form.addr_prov.value || !form.addr_city.value || !form.addr_area.value){
				alert("请输入地区", 1500);
				return;
			}
			if(form.address.value.length==0){
				alert("请输入详细地址", 1500);
				return;
			}
		}	
		
		//校验自定义字段
		var $defined=$('div#defined dl');
		if($defined.length>0){
			var validPass=true;
			$defined.each(function(index){
				var $self=$(this);
				var $el=$self.find('input').length>0?$(this).find('input'):$(this).find('select');
				if($el.attr('required') && (($el.is('input') && $el.val().length==0) || ($el.is('select') && $el.prop('selectedIndex')==0))){
					var tipName=$self.find('dt').html();
					tipName=tipName.substring(0,tipName.length-1);
					alert("请输入"+tipName, 1500);
					validPass=false;
					return validPass;
				}
			});
			if(!validPass){
				return;
			}
		}
		
		loading(true);
		$.ajax({
			url: "/mobile/mcard?action=saveCardInfo&todo=update&wuid=389&uid=120&mcid=4&talker=ozdP9jkbKUglHNvd751CA99n4Xog",
			type:"POST",
			data:$("#form1").serialize(),
			dataType:"json",
			success: function(res){
				loading(false);
				if(res.success){
					alert("提交成功", 1500);
					setTimeout("location.reload()",1500);
				}else{
					if(res.result=='inValidPhone'){
						alert('提交失败，该手机号码已被占用',1500);		
					}else{
						alert('提交失败',1500);						
					}
				}
			}
		});

	}
</script>

	<div class="container info_tx">
		<div class="body pt_10">
			<ul class="list_ul_card">
				<form id="form1" action="javascript:;" method="post">
					<li data-card=""><header class="center">
							<label style="display: inline-block;"><span>&nbsp;</span>填写会员卡资料</label>
						</header>
						<div class="forms">
							<!-- 隐藏字段 -->
							<input type="hidden" name="id" value="2169">
							<input type="hidden" name="cardNum" value="102163">
							<dl>
								<dt>姓 名：</dt>
								<dd>
									<input type="text" name="username" placeholder="请输入姓名" value="吴海" maxlength="30" class="input">
								</dd>
							</dl>
							<dl>
								<dt>手 机：</dt>
								<dd>
									<input type="text" name="telephone" placeholder="请输入手机号码" value="15305605631" maxlength="30" class="input">
								</dd>
							</dl>
							<!-- 系统字段性别是否必填-->
							<dl>
								<dt>性别：</dt>
								<dd>
									<select name="sex" class="select">
										<option value="1">男</option>
										<option value="0">女</option>
									</select>
								</dd>
							</dl>
							<!-- 系统字段生日是否必填-->
							<dl>
								<dt>生 日：</dt>
								<dd>
									<div class="box select_box">
										
										<div>
											<select name="birth_year" readonly="readonly" class="select" id="selectYear" value="">
												<!--auth Eric_wu-->
											<option value="1900">1900年</option><option value="1901">1901年</option><option value="1902">1902年</option><option value="1903">1903年</option><option value="1904">1904年</option><option value="1905">1905年</option><option value="1906">1906年</option><option value="1907">1907年</option><option value="1908">1908年</option><option value="1909">1909年</option><option value="1910">1910年</option><option value="1911">1911年</option><option value="1912">1912年</option><option value="1913">1913年</option><option value="1914">1914年</option><option value="1915">1915年</option><option value="1916">1916年</option><option value="1917">1917年</option><option value="1918">1918年</option><option value="1919">1919年</option><option value="1920">1920年</option><option value="1921">1921年</option><option value="1922">1922年</option><option value="1923">1923年</option><option value="1924">1924年</option><option value="1925">1925年</option><option value="1926">1926年</option><option value="1927">1927年</option><option value="1928">1928年</option><option value="1929">1929年</option><option value="1930">1930年</option><option value="1931">1931年</option><option value="1932">1932年</option><option value="1933">1933年</option><option value="1934">1934年</option><option value="1935">1935年</option><option value="1936">1936年</option><option value="1937">1937年</option><option value="1938">1938年</option><option value="1939">1939年</option><option value="1940">1940年</option><option value="1941">1941年</option><option value="1942">1942年</option><option value="1943">1943年</option><option value="1944">1944年</option><option value="1945">1945年</option><option value="1946">1946年</option><option value="1947">1947年</option><option value="1948">1948年</option><option value="1949">1949年</option><option value="1950">1950年</option><option value="1951">1951年</option><option value="1952">1952年</option><option value="1953">1953年</option><option value="1954">1954年</option><option value="1955">1955年</option><option value="1956">1956年</option><option value="1957">1957年</option><option value="1958">1958年</option><option value="1959">1959年</option><option value="1960">1960年</option><option value="1961">1961年</option><option value="1962">1962年</option><option value="1963">1963年</option><option value="1964">1964年</option><option value="1965">1965年</option><option value="1966">1966年</option><option value="1967">1967年</option><option value="1968">1968年</option><option value="1969">1969年</option><option value="1970">1970年</option><option value="1971">1971年</option><option value="1972">1972年</option><option value="1973">1973年</option><option value="1974">1974年</option><option value="1975">1975年</option><option value="1976">1976年</option><option value="1977">1977年</option><option value="1978">1978年</option><option value="1979">1979年</option><option value="1980">1980年</option><option value="1981">1981年</option><option value="1982">1982年</option><option value="1983">1983年</option><option value="1984">1984年</option><option value="1985">1985年</option><option value="1986">1986年</option><option value="1987">1987年</option><option value="1988">1988年</option><option value="1989">1989年</option><option value="1990">1990年</option><option value="1991">1991年</option><option value="1992">1992年</option><option value="1993">1993年</option><option value="1994">1994年</option><option value="1995">1995年</option><option value="1996">1996年</option><option value="1997">1997年</option><option value="1998">1998年</option><option value="1999">1999年</option><option value="2000">2000年</option><option value="2001">2001年</option><option value="2002">2002年</option><option value="2003">2003年</option><option value="2004">2004年</option><option value="2005">2005年</option><option value="2006">2006年</option><option value="2007">2007年</option><option value="2008">2008年</option><option value="2009">2009年</option><option value="2010">2010年</option><option value="2011">2011年</option><option value="2012">2012年</option><option value="2013">2013年</option><option value="2014">2014年</option></select>
										</div>
										<div>
											<select name="birth_month" readonly="readonly" class="select" id="selectMonth" value="">
												<!--auth Eric_wu-->
											<option value="01">1月</option><option value="02">2月</option><option value="03">3月</option><option value="04">4月</option><option value="05">5月</option><option value="06">6月</option><option value="07">7月</option><option value="08">8月</option><option value="09">9月</option><option value="10">10月</option><option value="11">11月</option><option value="12">12月</option></select>
										</div>
										<div>
											<select name="birth_date" readonly="readonly" class="select" id="selectDate" value="">
												<!--auth Eric_wu-->
											<option value="01">1日</option><option value="02">2日</option><option value="03">3日</option><option value="04">4日</option><option value="05">5日</option><option value="06">6日</option><option value="07">7日</option><option value="08">8日</option><option value="09">9日</option><option value="10">10日</option><option value="11">11日</option><option value="12">12日</option><option value="13">13日</option><option value="14">14日</option><option value="15">15日</option><option value="16">16日</option><option value="17">17日</option><option value="18">18日</option><option value="19">19日</option><option value="20">20日</option><option value="21">21日</option><option value="22">22日</option><option value="23">23日</option><option value="24">24日</option><option value="25">25日</option><option value="26">26日</option><option value="27">27日</option><option value="28">28日</option><option value="29">29日</option><option value="30">30日</option><option value="31">31日</option></select>
										</div>
										
									</div>
								</dd>
							</dl>

							<!-- 系统字段地址是否必填-->
							<dl>
								<dt>地区:</dt>
								<dd>
									<div class="box select_box">
										<div>
											<select name="addr_prov" class="select" id="selectProvince" selectedindex="0"><option value="">请选择</option><option value="110000">北京</option><option value="120000">天津</option><option value="130000">河北省</option><option value="140000">山西省</option><option value="150000">内蒙古自治区</option><option value="210000">辽宁省</option><option value="220000">吉林省</option><option value="230000">黑龙江省</option><option value="310000">上海</option><option value="320000">江苏省</option><option value="330000">浙江省</option><option value="340000">安徽省</option><option value="350000">福建省</option><option value="360000">江西省</option><option value="370000">山东省</option><option value="410000">河南省</option><option value="420000">湖北省</option><option value="430000">湖南省</option><option value="440000">广东省</option><option value="450000">广西壮族自治区</option><option value="460000">海南省</option><option value="500000">重庆</option><option value="510000">四川省</option><option value="520000">贵州省</option><option value="530000">云南省</option><option value="540000">西藏自治区</option><option value="610000">陕西省</option><option value="620000">甘肃省</option><option value="630000">青海省</option><option value="640000">宁夏回族自治区</option><option value="650000">新疆维吾尔自治区</option><option value="710000">台湾省</option><option value="810000">香港特别行政区</option><option value="820000">澳门特别行政区</option><option value="990000">海外</option></select>
										</div>
										<div>
											<select name="addr_city" class="select" id="selectCity"><option value="">请选择</option></select>
										</div>
										<div>
											<select name="addr_area" class="select" id="selectArea"><option value="">请选择</option></select>
										</div>
									</div>
								</dd>
							</dl>
							<dl>
								<dt>详细地址:</dt>
								<dd>
									<input type="text" name="address" id="Js-address" value="" placeholder="请输入详细地址" maxlength="100" class="input">
								</dd>
							</dl>

							<!-- 自定义字段-->
							<div id="defined">
															
									<dl>
										<dt>QQ：</dt>
										<dd>
											<input type="text" name="tValue1" placeholder="请填写您的QQ号码" readonly="readonly" maxlength="80" class="input" value="1234567890" required="true">
										</dd>
									</dl>								
							
							
							
							
							
							
							
							
							
							
							
							
							</div>
							
						</div></li>
					<ul class="add_op">
						<li style="padding: 10px 0 0;"><a href="javascript:submit1();" style="width: 100%;">提&nbsp;&nbsp;&nbsp;交</a>
						</li>
						
						<li><a href="javascript:bd();" class="btn_2" style="width: 100%;">绑定已有实体卡</a></li>
						
					</ul>
				</form>
			</ul>
		</div>
	</div>
	<div mark="stat_code" style="width: 0px; height: 0px; display: none;">
	</div>
	<script type="text/javascript">
	var intervalId,buttonObj,bindPhone;
	//发送下一条短信需要间隔的秒数
	var seconds = 60;
	var bindType = 1;
	function getCardVCode(clickObj, evt, formId, teleName , action){
		var form = document.getElementById(formId);
		var tel = $.trim(form[teleName].value);
		if(tel.length==0){
			alert("请输入手机号码", 1000);return;
		}
		if(!/^[0-9]{8,20}$/.test(tel)){
			alert("请输入正确格式的手机号码", 1000);return;
		}
		clickObj.setAttribute("disabled", "disabled");
		clickObj.value = "正在发送，请稍候...";
		$.ajax({
			url: "/mobile/mcard",
			type:"get",
			data:{
				action:action,
				wuid:'389',
				talker:'ozdP9jkbKUglHNvd751CA99n4Xog',
				uid:'120',
				phone:tel,
				mcid : 4
			},
			dataType:"json",
			success: function(res){
				if(res.success){
					clickObj.value = '验证码发送成功';
					buttonObj = clickObj;
					bindPhone = res.phone;
					intervalId = setInterval("ticker()",1000);
				}else{
					if('errorNum'==res.result){
						alert('手机号码不正确', 1500);					
					}else if(1 == res.statu){
						alert('发送失败,暂无短信流量包。' , 1500);
					}else if(2 == res.statu){
						alert('您发送过于频繁，请稍后再尝试。');
					}else if(3 == res.statu){
						alert('获取验证码失败', 1500);		
					}
					clickObj.removeAttribute("disabled");
					clickObj.value = "获取验证码";
					seconds = 60;
				}
			}
		});
	}
	var binding;
	//实体卡绑定
	function bind(){
		if(binding){
			return ;
		}
		var form = document.getElementById("form2");
		var tel = $.trim(form['entry_telephone'].value);
		if(tel.length==0){
			alert("请输入手机号码", 1000);return;
		}
		if(!/^[0-9]{8,20}$/.test(tel)){
			alert("请输入正确格式的手机号码", 1000);return;
		}
		
		var code = $.trim(form['entry_checkcode'].value);
		if(bindType==1 && code.length==0){
			alert("请输入验证码。",1500);
			return;
		}
		
		var offline_number = $.trim(form['offline_number'].value);
		if(bindType==2 && offline_number.length==0){
			alert("请输入实体卡号。",1500);
			return;
		}
		
		loading(true);
		$.ajax({
			url: "/mobile/mcard",
			type:"post",
			data:{
				action:'bindForUpdate',
				bindType : bindType,
				wuid:'389',
				talker:'ozdP9jkbKUglHNvd751CA99n4Xog',
				uid:'120',
				phone:bindPhone || tel,
				code:code,
				offline_number : offline_number
			},
			dataType:"json",
			success: function(res){
				if(res.success){
					alert('绑定实体卡成功', 1500);
					location.href = "/mobile/mc/cardMain.jsp?uid=120&wuid=389&talker=ozdP9jkbKUglHNvd751CA99n4Xog";
				}else if(1 == res.statu){
					alert('验证码无效', 1500);
				}else if(2 == res.statu){
					alert('找不到对应实体卡', 1500);
				}else if(3 == res.statu){
					alert('您还没有领过会员卡', 1500);
				}
			},
			complete: function(){
				binding = false;
				loading(false);
			}
		});
	}
	function ticker(){
		seconds --;
		if(seconds > 55){
			//提示消息显示5秒钟
		}else if(seconds>0){
			buttonObj.value = seconds+"秒后可重新获取";
		}else{
			clearInterval(intervalId);
			buttonObj.removeAttribute("disabled");
			buttonObj.value = "获取验证码";
			seconds = 60;
			buttonObj = null;
		}
	}
	</script>