			<div id="main">
				<div class="title">商店常规配置</div>
				<form action="{S_ADMIN_CONFIG}" method="post">
					<div class="module row1">
						精彩内容功能：
						<input type="radio" name="good" value="1" {GOOD_YES}/> 开启
						<input type="radio" name="good" value="0" {GOOD_NO}/> 关闭
					</div>
					<div class="module">
						精彩内容点击时间限制为 <input type="text" name="time_click" value="{TIME_CLICK}" size="4" maxlength="11" /> 秒
					</div>
					<div class="module row2">
						允许出售顶部和底部的广告：
						<input type="radio" name="ad" value="1" {AD_YES}/> 是
						<input type="radio" name="ad" value="0" {AD_NO}/> 否
					</div>
					<div class="module row1">
						顶部广告需要每天需要 <input type="text" name="top_ad" value="{TOP_AD}" size="3" maxlength="11" /> {POINTS_NAME}
					</div>
					<div class="module row2">
						底部广告需要每天需要 <input type="text" name="foot_ad" value="{FOOT_AD}" size="3" maxlength="11" /> {POINTS_NAME}
					</div>
					<div class="module row1">
						顶部最多可以放 <input type="text" name="max_top_ad" value="{MAX_TOP_AD}" size="3" maxlength="11" /> 个广告
					</div>
					<div class="module row2">
						底部最多可以放 <input type="text" name="max_foot_ad" value="{MAX_FOOT_AD}" size="3" maxlength="11" /> 个广告
					</div>
					<div class="module row1">
						投放广告最少需要 <input type="text" name="min_day" value="{MIN_DAY}" size="3" maxlength="3" /> 天，最多不能超过 <input type="text" name="max_day" value="{MAX_DAY}" size="3" maxlength="3"/> 天
					</div>
					<div class="module row2">
						更改用户名需要 <input type="text" name="buy_username" value="{BUY_USERNAME}" size="3" maxlength="11" /> {POINTS_NAME}
					</div>
					<div class="module row1">
						更改等级需要 <input type="text" name="buy_rank" value="{BUY_RANK}" size="3" maxlength="11" /> {POINTS_NAME}
					</div>
					<div class="module row2">
						更改用户名颜色需要 <input type="text" name="buy_namecolor" value="{BUY_NAMECOLOR}" size="3" maxlength="11" /> {POINTS_NAME}
					</div>
					<input type="submit" name="submit" value="保存配置" />
				</form>
				<div class="nav"><a href="{U_BACK}">返回上级</a> / <a href="{U_ADMIN}">后台首页</a> / <a href="{U_INDEX}">返回首页</a></div>
			</div>