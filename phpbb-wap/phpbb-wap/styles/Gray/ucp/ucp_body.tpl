			<div id="main">
				<div class="ucp-nav-title center">
					<div class="left ucp-min-title"><a href="{U_UCP_MAIN}">主页</a></div>
					<div class="left ucp-min-title"><a href="{U_VIEWPROFILE}">个人档</a></div>
					<div class="left ucp-min-title"><a href="{U_GUESTBOOK}">留言</a></div>
					<div class="left ucp-min-title"><a href="{U_ALBUM}">相册</a></div>
					<div class="clear"></div>
				</div>
				<div class="ucp-box">
					<div class="ucp-main-box">
						<div class="ucp-box-line">
							<div style="padding-top: 5px;padding-left:5px;">{AVATAR_IMG}</div>
						</div>					
						<div class="ucp-box-line">
							<div class="left ucp-left-line">ＩＤ</div>
							<div class="left">
								{USER_ID} - <a href="{U_ADD_FRIEND}">加好友</a>
<!-- BEGIN manage -->
								 - <a href="{U_UCP_MANAGE}">管理</a>
<!-- END manage -->
							</div>
						</div>
						<div class="ucp-box-line">
							<div class="left ucp-left-line">用户名</div>
							<div class="left">{USERNAME} - <a href="{U_PM}">{IMG_PM}</a></div>
						</div>
						<div class="ucp-box-line">
							<div class="left ucp-left-line">等级</div>
							<div class="left">{USER_RANK}</div>
						</div>
<!-- BEGIN gender -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">性别</div>
							<div class="left">{GENDER}</div>
						</div>
<!-- END gender -->
<!-- BEGIN birthday -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">年龄</div>
							<div class="left">{USER_AGE}</div>
						</div>
						<div class="ucp-box-line">
							<div class="left ucp-left-line">生日</div>
							<div class="left">{BIRTHDAY}</div>
						</div>
						<div class="ucp-box-line">
							<div class="left ucp-left-line">星座</div>
							<div class="left">{ZODIAC}</div>
						</div>
<!-- END birthday -->
<!-- BEGIN signature -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">签名</div>
							<div class="left">{SIGNATURE}</div>
						</div>
<!-- END signature -->
					<div class="clear"></div>
					</div>
				</div>
				<div class="ucp-box">
					<div class="ucp-main-box">
<!-- BEGIN from -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">居住地</div>
							<div class="left">{LOCATION}</div>
						</div>
<!-- END from -->
<!-- BEGIN occ -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">职业</div>
							<div class="left">{OCCUPATION}</div>
						</div>
<!-- END occ -->
<!-- BEGIN interests -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">兴趣</div>
							<div class="left">{INTERESTS}</div>
						</div>
<!-- END interests -->

						<div class="clear"></div>
					</div>
				</div>
				<div class="ucp-box">
					<div class="ucp-main-box">
						<div class="ucp-box-line">
							<div class="left ucp-left-line">注册时间</div>
							<div class="left">{JOINED}</div>
						</div>
				
						<div class="ucp-box-line">
							<div class="left ucp-left-line">上次访问</div>
							<div class="left">{LASTVISIT}</div>
						</div>						

						<div class="clear"></div>
					</div>
				</div>


				<div class="ucp-box">
					<div class="ucp-main-box">
<!-- BEGIN qq -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">ＱＱ</div>
							<div class="left">{QQ}</div>
						</div>
<!-- END qq -->
<!-- BEGIN number -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">电话</div>
							<div class="left">{NUMBER}</div>
						</div>
<!-- END number -->
<!-- BEGIN email -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">E-mail</div>
							<div class="left">{EMAIL}</div>
						</div>
<!-- END email -->
<!-- BEGIN msn -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">MSN</div>
							<div class="left">{MSN}</div>
						</div>
<!-- END msn -->
<!-- BEGIN yim -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">雅虎通</div>
							<div class="left">{YIM}</div>
						</div>
<!-- END yim -->
<!-- BEGIN aim -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">AIM</div>
							<div class="left">{AIM}</div>
						</div>
<!-- END aim -->
<!-- BEGIN www -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">博客</div>
							<div class="left">{WWW}</div>
						</div>
<!-- END www -->
{GUESTBOOK}
						<div class="clear"></div>
					</div>
				</div>
				<div class="ucp-box">
					<div class="ucp-main-box">
						<div class="ucp-box-line">
							<div class="left ucp-left-line">{POINTS_NAME}</div>
							<div class="left">
								{MONEY}
<!-- BEGIN money -->
					-（<a href="{U_MONEY_SEND}">奖励</a>）
<!-- END money -->
							</div>
						</div>
<!-- BEGIN topic_collect -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">收藏帖</div>
							<div class="left"><a href="{topic_collect.U_VIEW}">{topic_collect.TOTAL}</a></div>
						</div>
<!-- END topic_collect -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">帖子</div>
							<div class="left"><a href="{U_SEARCH_USER}">{POSTS}</a></div>
						</div>
						<div class="ucp-box-line">
							<div class="left ucp-left-line">主题</div>
							<div class="left"><a href="{U_SEARCH_USER_TOPICS}">{TOPICS}</a></div>
						</div>
						<div class="ucp-box-line">
							<div class="left ucp-left-line">附件</div>
							<div class="left">{ATTACH}</div>
						</div>
				
<!-- BEGIN usergroup -->
						<div class="ucp-box-line">
							<div class="left ucp-left-line">小组</div>
							<div class="left">{USERGROUP}</div>
						</div>
<!-- END usergroup -->
						<div class="clear"></div>
					</div>
				</div>
				<div class="ucp-box">
					<div class="center">
						<a href="#main">{IMG_TOP}回顶部</a>
					</div>
				</div>
				<div class="nav"><a href="{U_INDEX}">返回上级</a> / <a href="{U_INDEX}">返回首页</a></div>	
			</div>