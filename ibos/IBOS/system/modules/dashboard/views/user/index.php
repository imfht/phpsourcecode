<?php

use application\core\utils\Env;
use application\core\utils\Ibos;
use application\modules\department\utils\Department as DepartmentUtil;
use application\modules\position\utils\Position as PositionUtil;
use application\modules\role\utils\Role as RoleUtil;
?>
<link rel="stylesheet" href="<?php echo STATICURL; ?>/js/lib/dataTable/css/jquery.dataTables_ibos.min.css?<?php echo VERHASH; ?>">
<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/organization.css?<?php echo VERHASH; ?>">
<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/organization_role.css?<?php echo VERHASH; ?>">
<div class="ct">
	<div class="clearfix">
		<h1 class="mt">通讯录管理＞部门与用户管理</h1>
	</div>
	<div>
		<!-- 部门信息 start -->
		<div class="ctb">
			<?php if (!$canwrite): ?>
				<div class="alert trick-tip">
	        <div class="trick-tip-title">
	            <i></i>
	            <strong>提示</strong>
	        </div>
	        <div class="trick-tip-content">
	            <ul>
	                <li><span>未安装通讯录套件，无法进行管理。<a href="<?php echo $contacturl;?>" target="_blank">马上安装</a></span></li>
	            </ul>
	        </div>
	      </div>
      <?php endif;?>
			<div class="mc clearfix">
				<div class="aside">
					<div class="fill-ss">
                        <a href="<?php echo $this->createUrl( 'department/add' ); ?>" class="btn btn-primary add-dept-btn" <?php if (!$canwrite):?>style="display: none" <?php endif;?>>新增部门</a>
					</div>
					<div class="ztree-wrap">
						<div>
							<ul class="ztree org-utree org-corporation-utree">
								<li class="level0">
									<span class="button level0 switch corporation"></span>
									<a href="javascript:;"  title="<?php echo $unit['fullname']; ?>" class="curSelectedNode" id="corp_unit">
										<span><?php echo $unit['fullname']; ?></span>
										<i class="o-org-ztree-edit pull-right opt-btn opt-edit-btn" title="设置公司信息"  id="edit_corporation"></i>
									</a>
								</li>
							</ul>
						</div>
						<ul id="utree" class="ztree org-utree">
						</ul>
					</div>
				</div>
				<div class="mcr">
					<div class="page-list">
						<div class="page-list-header">
							<div class="pull-left">
								<div class="btn-group">
									<button type="button" onclick="location.href = '<?php echo $this->createUrl( 'user/add' ) . "&deptid=" . Env::getRequest( 'deptid' ); ?>';" class="btn btn-primary" <?php if (!$canwrite):?>style="display: none" <?php endif;?>><?php echo $lang['Add user']; ?></button>
								</div>
                                <div class="btn-group mlm">
                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" <?php if (!$canwrite):?>disabled <?php endif;?>>
                                        同步人员
                                        <i class="caret"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?php echo $this->createUrl('wxsync/index');?>">企业微信同步</a></li>
                                        <li><a href="<?php echo $this->createUrl('cobinding/index');?>">酷办公同步</a></li>
                                    </ul>
                                </div>
								<div class="btn-group mlm">
									<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" <?php if (!$canwrite):?>disabled <?php endif;?>>
										切换人员状态
										<i class="caret"></i>
									</button>						
									<ul class="dropdown-menu" id="list_act">
										<li><a data-action="setUserStatus" data-param='{"op": "enabled"}' href="javascript:;"><?php echo $lang['Enable']; ?></a></li>
										<li><a data-action="setUserStatus" data-param='{"op": "disabled"}' href="javascript:;"><?php echo $lang['Disabled']; ?></a></li>
									</ul>
								</div>
								<div class="btn-group mlm">
									<button type="button" data-action="batchImport" class="btn" <?php if (!$canwrite):?>disabled <?php endif;?>><?php echo Ibos::lang('batch import'); ?></button>
								</div>
								<div class="btn-group mlm">
									<button type="button" data-action="exportUser" class="btn"><?php echo $lang['Export']; ?></button>
								</div>
								<div class="btn-group mlm">
									<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" <?php if (!$canwrite):?>disabled <?php endif;?>>
										更多<i class="caret"></i>
									</button>
									<ul class="dropdown-menu" id="list_act">
										<li><a data-action="checkRelationship" href="javascript:;">管理上下级关系</a></li>
										<li><a data-action="updateUserInfo" href="javascript:;">设置所在部门/岗位</a></li>
									</ul>
								</div>
							</div>
							<form method="post" action="javascript:;">
								<div class="search pull-right span3 mls">
									<input type="text" name="keyword" placeholder="<?php echo $lang['User search tip']; ?>" id="mn_search" nofocus>
									<a href="javascript:;">search</a>
								</div>
							</form>
							<div class="btn-group pull-right">
								<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
									在职<i class="caret"></i>
								</button>
								<ul class="dropdown-menu">
									<li>
										<a data-action="getStatusList" href="javascript:;" data-type="all"><?php echo $lang['All']; ?></a>
									</li>
									<li>
										<a data-action="getStatusList" href="javascript:;" data-type="enabled"><?php echo $lang['Enable']; ?></a>
									</li>
									<li>
										<a data-action="getStatusList" href="javascript:;" data-type="disabled">禁用</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="page-list-mainer">
							<table class="table table-striped table-hover org-user-table" id="org_user_table">
								<thead>
									<tr>
										<th width="20">
											<label class="checkbox">
												<input type="checkbox" data-name="user">
											</label>
										</th>
										<th width="40"></th>
										<th width="100"><?php echo $lang['Full name']; ?></th>
										<th><?php echo $lang['Department']; ?></th>
										<th>角色</th>
										<th>人员状态</th>
										<th>手机</th>
										<th width="60"><?php echo $lang['Operation']; ?></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="update_userinfo_dialog" style="display:none;">
    <div class="user-form-con">
        <form class="form-horizontal update-userinfo-form" id="update_userinfo_form">
            <ul class="nav nav-skid nav-skid-inverse" id="dept_post_tab">
                <li class="active">
                    <a href="#update_user_dept" data-toggle="tab">按部门</a>
                </li>
                <li>
                    <a href="#update_user_pos" data-toggle="tab">按岗位</a>
                </li>
            </ul>
            <div class="dialog-form-content tab-content">
                <div class="tab-pane active" id="update_user_dept">
                	<ul class="ztree" id="dept_tree"></ul>
                </div>
                <div class="tab-pane" id="update_user_pos">
                	<ul class="ztree" id="pos_tree"></ul>
                </div>
                <input type="hidden" name="deptid" value=""/>
                <input type="hidden" name="posid" value=""/>
                <input type="hidden" name="type" value="dept"/>
            </div>
        </form>
    </div>
</div>
<div id="update_userinfo_box"></div>
<script>
    Ibos.app.s({
        'canwrite': <?php echo $canwrite;?>,
        "auxiliaryId": [<?php echo $deptStr; ?>]
    });
</script>
<script src="<?php echo STATICURL; ?>/js/lib/dataTable/js/jquery.dataTables.js?<?php echo VERHASH; ?>"></script><script src="<?php echo STATICURL; ?>/js/lib/dataTable/plugins/input.js?<?php echo VERHASH; ?>"></script>
<script src='<?php echo $assetUrl; ?>/js/lang/zh-cn.js?<?php echo VERHASH; ?>'></script>
<script src='<?php echo STATICURL; ?>/js/lib/webuploader/webuploader.js?<?php echo VERHASH; ?>'></script>
<script src='<?php echo STATICURL; ?>/js/lib/webuploader/handlers.js?<?php echo VERHASH; ?>'></script>
<script src='<?php echo STATICURL; ?>/js/app/ibos.importData.js?<?php echo VERHASH; ?>'></script>
<script src='<?php echo $assetUrl; ?>/js/org_user_index.js?<?php echo VERHASH; ?>'></script>

