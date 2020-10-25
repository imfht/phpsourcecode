<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html dir="{S_CONTENT_DIRECTION}">
<head>
<link href="templates/GuildWarsAlliance/{T_HEAD_STYLESHEET}" rel="stylesheet" type="text/css" >
<link href="templates/GuildWarsAlliance/images/favicon.ico" rel="shortcut icon" >
<meta http-equiv="Content-Type" content="text/html; charset={S_CONTENT_ENCODING}" >
<meta http-equiv="Content-Style-Type" content="text/css" >
{META}
{NAV_LINKS}
<title>{SITENAME} :: {PAGE_TITLE}</title>
<!-- BEGIN switch_enable_pm_popup -->
<script language="Javascript" type="text/javascript">
<!--
	if ( {PRIVATE_MESSAGE_NEW_FLAG} )
	{
		window.open('{U_PRIVATEMSGS_POPUP}', '_phpbbprivmsg', 'HEIGHT=225,resizable=yes,WIDTH=400');;
	}
//-->
</script>
<!-- END switch_enable_pm_popup -->
</head>
<body topmargin="0">
<a name="top"></a>
<div class="center">
			<table border="0" width="760" cellspacing="0" cellpadding="0" class="content">
				<tr>
					<td>
						<table border="0" width="760" cellspacing="0" cellpadding="0">
							<tr>
								<td><a href="{U_INDEX}"><img border="0" src="templates/GuildWarsAlliance/images/banner/banner1.gif" alt="" height="99px" width="121"></a></td>
								<td><a href="{U_INDEX}"><img border="0" src="templates/GuildWarsAlliance/images/banner/banner2.gif" alt="" height="99px" width="117"></a></td>
								<td><a href="{U_INDEX}"><img border="0" src="templates/GuildWarsAlliance/images/banner/banner3.gif" alt="" height="99px" width="168"></a></td>
								<td><a href="{U_INDEX}"><img border="0" src="templates/GuildWarsAlliance/images/banner/banner4.gif" alt="" height="99px" width="122"></a></td>
								<td><a href="{U_INDEX}"><img border="0" src="templates/GuildWarsAlliance/images/banner/banner5.gif" alt="" height="99px" width="148"></a></td>
								<td><a href="{U_INDEX}"><img border="0" src="templates/GuildWarsAlliance/images/banner/banner6.gif" alt="" height="99px" width="84"></a></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
					
						<table border="0" class="border" width="758" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left">
									<table border="0" width="758" cellspacing="0" cellpadding="0">
										<tr>
											<td width="148" height="76" valign="top">
												<table border="0" height="76" width="148" cellspacing="0" cellpadding="0">
													<tr>
														<!-- BEGIN switch_user_logged_out -->
														<td valign="top" height="28">
															<a href="{U_LOGIN_LOGOUT}"><img border="0" src="templates/GuildWarsAlliance/images/lang_english/login.gif" width="117" height="28" alt="{L_LOGIN_LOGOUT}"></a></td>
														<!-- END switch_user_logged_out -->
														<!-- BEGIN switch_user_logged_in -->
														<td valign="top" height="28">
															<a href="{U_LOGIN_LOGOUT}"><img border="0" src="templates/GuildWarsAlliance/images/lang_english/logout.gif" width="117" height="28" alt="{L_LOGIN_LOGOUT}"></a></td>
														<!-- END switch_user_logged_in -->
														<td rowspan="2" class="log1" valign="top" width="31" height="76px"></td>
													</tr>
													<tr>
														<td class="log2" valign="top" width="117" height="48px"></td>
													</tr>
												</table>
											</td>
											<td valign="top">
											<table border="0" height="76" width="100" cellspacing="0" cellpadding="0">
												<tr>
													<td valign="top" height="12">
													<img border="0" src="templates/GuildWarsAlliance/images/menu_top.gif" alt="" width="610" height="12"></td>
												</tr>
												<tr>
													<td valign="middle" class="menubg">
													<span class="mainmenu">&nbsp;<a href="{U_FAQ}" class="mainmenu">{L_FAQ}</a>&nbsp; &nbsp;<a href="{U_SEARCH}" class="mainmenu">{L_SEARCH}</a>&nbsp; &nbsp;<a href="{U_MEMBERLIST}" class="mainmenu">{L_MEMBERLIST}</a>&nbsp; &nbsp;<a href="{U_GROUP_CP}" class="mainmenu">{L_USERGROUPS}</a>&nbsp; 
													<!-- BEGIN switch_user_logged_out -->
													&nbsp;<a href="{U_REGISTER}" class="mainmenu">{L_REGISTER}</a>&nbsp;
													<!-- END switch_user_logged_out -->
													&nbsp;<a href="{U_PROFILE}" class="mainmenu">{L_PROFILE}</a>&nbsp; <br /> &nbsp;<a href="{U_PRIVATEMSGS}" class="mainmenu">{PRIVATE_MESSAGE_INFO}</a></span>
													</td>
												</tr>
												<tr>
													<td valign="top" height="12">
													<img border="0" src="templates/GuildWarsAlliance/images/menu_bottom.gif" alt="" width="610" height="12"></td>
												</tr>
											</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
				<tr>
					<td class="bg2" height="104" valign="bottom">
								<table width="100%" cellspacing="0" cellpadding="2" border="0">
								  <tr> 
									<td valign="bottom"><span class="gensmall">
										<!-- BEGIN switch_user_logged_in -->
										{LAST_VISIT_DATE}<br />
										<!-- END switch_user_logged_in -->
										{CURRENT_TIME}<br /></span>
									</td>
									<td align="right" valign="bottom" class="gensmall">
										<!-- BEGIN switch_user_logged_in -->
										<a href="{U_SEARCH_NEW}" class="gensmall">{L_SEARCH_NEW}</a><br /><a href="{U_SEARCH_SELF}" class="gensmall">{L_SEARCH_SELF}</a><br />
										<!-- END switch_user_logged_in -->
										<a href="{U_SEARCH_UNANSWERED}" class="gensmall">{L_SEARCH_UNANSWERED}</a>
									</td>
								  </tr>
								</table>
					</td>
				</tr>
				<tr>
					<td valign="top">