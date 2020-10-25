<script type="text/javascript" src="__APPURL__/js/calendar.js"></script>
	<style type="text/css">
.Calendar {
	font-family: Verdana;
	background-color: #EEE;
	text-align: center;
	height: 300px;
	line-height: 1.5em;
}

.Calendar .icons {
	display: block;
	width: 40px;
	height: 40px;
	background: url(__APPURL__/image/icons4.png)
		no-repeat center -300px;
	-webkit-background-size: 50px auto;
}

.Calendar .icons_after {
	background-position: center -350px;
}

.Calendar header {
	font-size: 14px;
	color: #888e8e;
	line-height: 50px;
	height: 50px;
	background: #ffffff;
	box-shadow: 0 5px 5px rgba(100, 100, 100, 0.1);
}

.Calendar a {
	color: #0066CC;
}

.Calendar table {
	width: 280px;
	margin: auto;
	border: 0;
}

.Calendar table thead {
	color: #acacac;
}

.Calendar table td {
	color: #989898;
	border: 1px solid #ecf9fa;
	width: 40px;
	height: 40px;
	margin: 1px;
	background: #ffffff;
	-webkit-box-sizing: border-box;
}

.Calendar thead td,.Calendar td:empty {
	background: none;
	border: 0;
}

.Calendar thead td {
	color: #72bec9;
	font-size: 13px;
	font-weight: bold;
}

#idCalendarPre {
	cursor: pointer;
	float: left;
}

#idCalendarNext {
	cursor: pointer;
	float: right;
}

#idCalendar td a.checked {
	display: block;
	height: 100%;
	border: 1px solid #58c4d1;
	line-height: 38px;
	color: #989898;
}

#idCalendar td.onToday,#idCalendar td.onToday a {
	color: #ff3600 !important;
}
</style>
	
	
	
	<script>
	
		var CurrentMonthRecord='';
		var flag=[];
		if(CurrentMonthRecord){
			var records=CurrentMonthRecord.split(',');
			for(var i=0;i<records.length;i++){
				var d=new Date();
				d.setTime(records[i]);
				flag.push(d.getDate());
			}
		}
	
		/**
		 * 积分签到
		 */
		function dosignin(on) {
			if(on){
				loading(true);
				$.ajax({
					url: "/mobile/mcard",
					type:"get",
					data:{
						action:'signin',
						mcid:4,
						wuid:389,
						talker:'ozdP9jkbKUglHNvd751CA99n4Xog'
					},
					dataType:"json",
					success: function(res){
						if(res.success){
							loading(false);
							alert('签到成功',1500);
							var $point=$('.tbox.tbox_1 p.pre').eq(0).find('label');
							$point.html(Number($point.html())+Number(res.result)); 
							$('li#signStatus a').eq(0).hide();
							$('li#signStatus a').eq(1).show();
						}
					}
				});
			}else{
				alert('商户未开放签到功能',1500);
			}

		}
	</script>
	
	<div class="container integral">
		<header>
			<ul class="tbox tbox_1">
				<li>
					<p class="pre">
						<label>0</label> 可用积分
					</p>
				</li>
				<li id="signStatus">					
					<a href="javascript:dosignin(true);"><label>签到</label></a>
					<a href="javascript:void(0)" style="display:none;"><label>已签到</label></a>					
				</li>
				<li>
					<p class="pre">
						<label>10</label> 今日奖励
					</p>
				</li>
			</ul>
			<nav class="nav_integral">
				<ul class="box">
					<li><a href="http://www.weijuju.com/mobile/mc/cardChange.jsp?wuid=389&uid=120&talker=ozdP9jkbKUglHNvd751CA99n4Xog&mcid=4"><span class="icons icons_prize">&nbsp;</span><label>兑换礼品</label></a></li>
					<li><a href="http://www.weijuju.com/mobile/mc/cardSignRecord.jsp?wuid=389&uid=120&talker=ozdP9jkbKUglHNvd751CA99n4Xog&mcid=4"><span class="icons icons_record">&nbsp;</span><label>签到记录</label></a></li>
					<li><a href="http://www.weijuju.com/mobile/mc/cardSignGuide.jsp?wuid=389&uid=120&talker=ozdP9jkbKUglHNvd751CA99n4Xog&mcid=4"><span class="icons icons_teach">&nbsp;</span><label>积分攻略</label></a></li>
				</ul>
			</nav>
		</header>
		<div class="body">
			<div>
				<div class="Calendar">
					<header>
						<div id="idCalendarPre">
							<span class="icons icons_before">&nbsp;</span>
						</div>
						<div id="idCalendarNext">
							<span class="icons icons_after">&nbsp;</span>
						</div>
						<span id="idCalendarYear">2014</span>年 <span id="idCalendarMonth">10</span>月
					</header>
					<table cellspacing="0">
						<thead>
							<tr>
								<td>日</td>
								<td>一</td>
								<td>二</td>
								<td>三</td>
								<td>四</td>
								<td>五</td>
								<td>六</td>
							</tr>
						</thead>
						<tbody id="idCalendar"><tr><td></td><td></td><td></td><td>1</td><td>2</td><td>3</td><td>4</td></tr><tr><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td></tr><tr><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td></tr><tr><td>19</td><td>20</td><td class="onToday">21</td><td>22</td><td>23</td><td>24</td><td>25</td></tr><tr><td>26</td><td>27</td><td>28</td><td>29</td><td>30</td><td>31</td><td></td></tr></tbody>
					</table>
				</div>
				<script language="JavaScript">
				    var dt='201410';
					var year,month;
					if(dt && 'null'!=dt){
						year=dt.substring(0,4);
						month=dt.substring(4);
					}else{
						dt=new Date();
						year=dt.getFullYear();
						month=dt.getMonth()+1;
					}
					
					//获取上个月与下个月
					var last=new Date();
					last.setFullYear(year);
					last.setMonth(month-2);
					var next=new Date();
					next.setFullYear(year);
					next.setMonth(month);
					
					var cale = new Calendar(
							"idCalendar",
							{
								Year : year,
								Month : month,
								onToday : function(o) {
									o.className = "onToday";
								},
								onFinish : function() {
									this.Year = year;
									this.Month = month;
									$$("idCalendarYear").innerHTML = this.Year;
									$$("idCalendarMonth").innerHTML = this.Month;
									for ( var i = 0, len = flag.length; i < len; i++) {
										this.Days[flag[i]].innerHTML = "<a href='javascript:void(0);' class='checked'>"
												+ flag[i] + "</a>";
									}
								}
							});

					$$("idCalendarPre").onclick = function() {
						console.log("cardSign.jsp?wuid=389&uid=120&talker=ozdP9jkbKUglHNvd751CA99n4Xog&mcid=4&date="+last.getFullYear()+(last.getMonth()+1));
						location.href = "cardSign.jsp?wuid=389&uid=120&talker=ozdP9jkbKUglHNvd751CA99n4Xog&mcid=4&date="+last.getFullYear()+""+(last.getMonth()+1);
					}
					$$("idCalendarNext").onclick = function() {
						console.log("cardSign.jsp?wuid=389&uid=120&talker=ozdP9jkbKUglHNvd751CA99n4Xog&mcid=4&date="+next.getFullYear()+""+(next.getMonth()+1));
						location.href = "cardSign.jsp?wuid=389&uid=120&talker=ozdP9jkbKUglHNvd751CA99n4Xog&mcid=4&date="+next.getFullYear()+""+(next.getMonth()+1);
					}
				</script>
			</div>
		</div>