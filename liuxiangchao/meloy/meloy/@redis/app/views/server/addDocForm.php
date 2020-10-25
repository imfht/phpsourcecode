{tea:layout}
{tea:js js/tea.date.js}

<h3>添加数据</h3>

<form class="ui form" data-tea-action=".addDoc">
	<input type="hidden" name="serverId" value="{{server.id}}"/>
	<table class="ui table">
		<tr>
			<td class="title">键(KEY)</td>
			<td><input type="text" name="key" placeholder=""/></td>
		</tr>
		<tr>
			<td>超时时间(TTL)</td>
			<td>
				<div class="ui field">
					<select class="ui dropdown" name="ttl" ng-model="ttl" ng-init="ttl = '-1'">
						<option value="-1">不会超时</option>
						<option value="0">自定义</option>
					</select>
				</div>
				<div class="ui fields" ng-if="ttl == '0'">
					<div class="ui field">
						<input type="text" name="timeCount" size="5"/>
					</div>
					<div class="ui field">
						<select name="timeType" class="ui dropdown">
							<option value="year">年</option>
							<option value="month">月</option>
							<option value="week">周</option>
							<option value="day">天</option>
							<option value="hour">小时</option>
							<option value="minute">分钟</option>
							<option value="second">秒</option>
						</select>
					</div>
					<div class="ui field text">
						当前时间：{{now}}
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>数据类型</td>
			<td>
				<select name="type" class="ui dropdown">
					<option ng-repeat="type in types track by type.code" value="{{type.code}}">{{type.name}}({{type.code}})</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>值(VALUE)</td>
			<td>[在下一步中可以设置]</td>
		</tr>
	</table>

	<button type="submit" class="ui button primary">下一步</button>
</form>