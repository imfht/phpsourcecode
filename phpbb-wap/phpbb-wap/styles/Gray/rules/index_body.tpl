			<div id="main">
				<div class="nav">
					<a href="./">首页</a>
<!-- BEGIN is_rules_body -->
					&gt; <a href="./rules.php">规则</a> &gt; {CAT_RULES}
<!-- END is_rules_body -->			
<!-- BEGIN is_body -->
					&gt; 规则
<!-- END is_body -->
				</div>
				<div class="title">{CAT_RULES}</div>
<!-- BEGIN rulesrow -->
				<div class="{rulesrow.ROW_CLASS}">
					<strong>{rulesrow.RULE_NAME}</strong>{rulesrow.RULE_MOD}{rulesrow.RULE_EDIT}{rulesrow.RULE_DELETE}<br />
					<p>{rulesrow.RULE_SUBJ}</p>
				</div>
<!-- END rulesrow -->
				<div class="title">规则</div>
<!-- BEGIN other_rule -->
					<div class="nav-title">
						<a href="{other_rule.U_RULE}">{other_rule.RULE_CAT}</a>{other_rule.EDIT_RULE}{other_rule.DELETE_RULE}
					</div>
<!-- END other_rule -->
					<div class="nav-title"><a href="{U_FAQ_RULES}">FAQ</a></div>
					
					<div class="center">
						<hr /><br />
<!-- BEGIN rule_auth -->
						<a href="{U_ADD_RULE}" class="button">新增规则</a>
<!-- END rule_auth -->
<!-- BEGIN rule_cat_auth -->
						<a href="{U_ADD_RULE_CAT}" class="button">新增分类</a>
<!-- END rule_cat_auth -->
					<br /><br />
					</div>
				</div>
			</div>