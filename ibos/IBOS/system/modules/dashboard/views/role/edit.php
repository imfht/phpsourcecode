<?php ?>
<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/organization.css?<?php echo VERHASH; ?>">
<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/organization_role.css?<?php echo VERHASH; ?>">
<div class="ct">
    <div class="clearfix">
        <h1 class="mt">前台管理权限＞编辑角色</h1>
    </div>
    <div>
        <!-- 部门信息 start -->
        <div class="ctb">
            <div>
                <div class="btn-group mb">
                    <a href="javascript:;" class="btn active">权限设置</a>
                    <a href="<?php echo $this->createUrl('role/edit', array('op' => 'member', 'id' => $id)); ?>"
                       class="btn">角色成员管理</a>
                </div>
                <div class="limit-info-wrap">
                    <form action="<?php echo $this->createUrl('role/edit'); ?>" method="post" id="position_edit_form">
                        <div class="page-list-header clearfix">
                            <div class="pull-left">
                                <span class="xwb">角色名称</span>
                                <span class="xcr">*</span>
                                <input class="role-name mls" type="text" name="rolename" id="role_name"
                                       value="<?php echo $role['rolename']; ?>"/>
                            </div>
                        </div>
                        <div>
                            <div class="limit-setup-tip">
                                <img src="<?php echo $assetUrl; ?>/image/illustrate.png">
                            </div>
                            <div class="org-limit org-limit-setup posr" id="limit_setup">
                                <?php if ($isInstallCrm): ?>
                                    <div class="org-limit-box">
                                        <div class="org-limit-header">
                                            <button type="button" class="btn btn-small j-limit-checkall pull-right"
                                                    data-node="cateCheckbox" data-id="<?php echo base64_encode('CRM');?>">全选
                                            </button>
                                            <h4 class="curp active" data-node="toggle">
                                                <i class="caret"></i>CRM
                                            </h4>
                                        </div>
                                        <div class="org-limit-body">
                                            <div class="org-limit-entry">
                                                <label class="checkbox dib">
                                                    <input type="checkbox" data-id="<?php echo base64_encode('CRM');?>"
                                                           data-node="modCheckbox"
                                                           data-pid="<?php echo base64_encode('CRM');?>">
                                                    <?php echo $lang['CRM authority']; ?>
                                                </label>
                                                <span class="tcm opt-tip">(点击图标进行权限切换)</span>
                                            </div>
                                            <div class="bdbs">
                                                <table id="CRM_limit_table"
                                                       class="table table-striped table-thbt table-row-condensed table-hover mbz">
                                                    <thead>
                                                    <tr>
                                                        <th><?php echo $lang['CRM module']; ?></th>
                                                        <th width="60" class="xac">
                                                            <div class="dib curp" data-node="batchVtc" data-toggle="tooltip" data-title="整列设置"
                                                                 data-placement="top">
                                        <span class="privilege-level"><i
                                                class="setting"></i></span><?php echo $lang['read']; ?>
                                                            </div>
                                                        </th>
                                                        <th width="60" class="xac">
                                                            <div class="dib curp" data-node="batchVtc" data-toggle="tooltip" data-title="整列设置"
                                                                 data-placement="top">
                                        <span class="privilege-level"><i
                                                class="setting"></i></span><?php echo $lang['create']; ?>
                                                            </div>
                                                        </th>
                                                        <th width="60" class="xac">
                                                            <div class="dib curp" data-node="batchVtc" data-toggle="tooltip" data-title="整列设置"
                                                                 data-placement="top">
                                        <span class="privilege-level"><i
                                                class="setting"></i></span><?php echo $lang['edit']; ?>
                                                            </div>
                                                        </th>
                                                        <th width="60" class="xac">
                                                            <div class="dib curp" data-node="batchVtc" data-toggle="tooltip" data-title="整列设置"
                                                                 data-placement="top">
                                        <span class="privilege-level"><i
                                                class="setting"></i></span><?php echo $lang['Delete']; ?>
                                                            </div>
                                                        </th>
                                                        <th width="60" class="xac">
                                                            <div class="dib curp" data-node="batchVtc" data-toggle="tooltip" data-title="整列设置"
                                                                 data-placement="top">
                                        <span class="privilege-level"><i
                                                class="setting"></i></span><?php echo $lang['assign']; ?>
                                                            </div>
                                                        </th>
                                                        <th width="60" class="xac">
                                                            <div class="dib curp" data-node="batchVtc" data-toggle="tooltip" data-title="整列设置"
                                                                 data-placement="top">
                                        <span class="privilege-level"><i
                                                class="setting"></i></span><?php echo $lang['share']; ?>
                                                            </div>
                                                        </th>
                                                        <th width="60" class="xac">
                                                            <div class="dib curp" data-node="batchVtc" data-toggle="tooltip" data-title="整列设置"
                                                                 data-placement="top">
                                        <span class="privilege-level"><i
                                                class="setting"></i></span><?php echo $lang['export']; ?>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($crmAuthItem as $item): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="dib curp" data-node="batchHoz" data-toggle="tooltip"
                                                                     data-title="整行设置"
                                                                     data-placement="bottom">
                                            <span class="privilege-level"><i
                                                    class="setting"></i></span><?php echo $item['name']; ?>
                                                                </div>
                                                            </td>
                                                            <input type="hidden" name="nodes[<?php echo $item['id']; ?>]" value="data">
                                                            <td class="xac">
                                                                <input type="hidden"
                                                                       name="data-privilege[<?php echo $item['id']; ?>][<?php echo $item['node']['index']['id']; ?>]"
                                                                       data-toggle="privilegeLevel"
                                                                       value="<?php echo isset($crmRelated[$item['key']]['index']) ? $crmRelated[$item['key']]['index'] : '0'; ?>">
                                                            </td>
                                                            <td class="xac">
                                                                <?php if (isset($item['node']['add'])): ?>
                                                                    <input type="hidden"
                                                                           name="data-privilege[<?php echo $item['id']; ?>][<?php echo $item['node']['add']['id']; ?>]"
                                                                           data-toggle="privilegeLevel"
                                                                           value="<?php echo isset($crmRelated[$item['key']]['add']) ? $crmRelated[$item['key']]['add'] : '0'; ?>">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="xac">
                                                                <?php if (isset($item['node']['edit'])): ?>
                                                                    <input type="hidden"
                                                                           name="data-privilege[<?php echo $item['id']; ?>][<?php echo $item['node']['edit']['id']; ?>]"
                                                                           data-toggle="privilegeLevel"
                                                                           value="<?php echo isset($crmRelated[$item['key']]['edit']) ? $crmRelated[$item['key']]['edit'] : '0'; ?>">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="xac">
                                                                <?php if (isset($item['node']['del'])): ?>
                                                                    <input type="hidden"
                                                                           name="data-privilege[<?php echo $item['id']; ?>][<?php echo $item['node']['del']['id']; ?>]"
                                                                           data-toggle="privilegeLevel"
                                                                           value="<?php echo isset($crmRelated[$item['key']]['del']) ? $crmRelated[$item['key']]['del'] : '0'; ?>">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="xac">
                                                                <?php if (isset($item['node']['assign'])): ?>
                                                                    <input type="hidden"
                                                                           name="data-privilege[<?php echo $item['id']; ?>][<?php echo $item['node']['assign']['id']; ?>]"
                                                                           data-toggle="privilegeLevel"
                                                                           value="<?php echo isset($crmRelated[$item['key']]['assign']) ? $crmRelated[$item['key']]['assign'] : '0'; ?>">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="xac">
                                                                <?php if (isset($item['node']['share'])): ?>
                                                                    <input type="hidden"
                                                                           name="data-privilege[<?php echo $item['id']; ?>][<?php echo $item['node']['share']['id']; ?>]"
                                                                           data-toggle="privilegeLevel"
                                                                           value="<?php echo isset($crmRelated[$item['key']]['share']) ? $crmRelated[$item['key']]['share'] : '0'; ?>">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="xac">
                                                                <?php if (isset($item['node']['export'])): ?>
                                                                    <?php $exportCheck = isset($crmRelated[$item['key']]['export']);?>
                                                                    <div class="posr <?php if ($exportCheck): ?>active<?php endif; ?>">
                                                                        <label class="checkbox">
                                                                            <input <?php if ($exportCheck): ?>checked<?php endif; ?>
                                                                                   type="checkbox"
                                                                                   name="nodes[<?php echo $item['node']['export']['id'];?>]"
                                                                                   value="<?php echo $item['node']['export']['id'];?>"
                                                                                   data-node="funcCheckbox"
                                                                                   data-pid="<?php echo base64_encode('CRM');?>">
                                                                        </label>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <div class="org-limit-body">
                                                    <?php foreach ($crmAdvanced as $key => $item):?>
                                                        <div class="org-limit-entry">
                                                            <label class="checkbox">
                                                                <input type="checkbox" data-id="<?php echo $key;?>"
                                                                       data-node="modCheckbox"
                                                                       data-pid="<?php echo base64_encode('CRM');?>">
                                                                <?php echo $item['groupName'];?>
                                                            </label>
                                                        </div>
                                                        <div class="fill-nn">
                                                            <ul class="org-limit-list clearfix">
                                                                <?php foreach ($item['node'] as $nIndex => $node):?>
                                                                    <?php
                                                                    $isData = $node['type'] === 'data';
                                                                    $crmSet = isset($related[$node['module']][$node['key']]);
                                                                    ?>
                                                                    <li <?php if ($isData): ?>class="org-limit-privilege-wrap"<?php endif; ?>>
                                                                        <div class="posr <?php if ($crmSet): ?>active<?php endif; ?>">
                                                                            <label class="checkbox">
                                                                                <input
                                                                                    <?php if ($crmSet): ?>checked<?php endif; ?>
                                                                                    type="checkbox"
                                                                                    name="nodes[<?php echo $node['id']; ?>]"
                                                                                    value="<?php echo $isData ? 'data' : $node['id']; ?>"
                                                                                    data-node="funcCheckbox"
                                                                                    data-pid="<?php echo $key;?>"
                                                                                >
                                                                                <span class="mlft">
                                                                                        <?php echo $node['name']; ?>
                                                                                    </span>
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                <?php endforeach;?>
                                                            </ul>
                                                        </div>
                                                    <?php endforeach;?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;?>
                                <!-- 认证项输出 begin -->
                                <?php foreach ($authItem as $key => $auth) : ?>
                                    <div class="org-limit-box">
                                        <div class="org-limit-header">
                                            <button type="button" class="btn btn-small j-limit-checkall pull-right"
                                                    data-node="cateCheckbox" data-id="<?php echo $key; ?>">全选
                                            </button>
                                            <h4 class="curp active" data-node="toggle">
                                                <i class="caret"></i>
                                                <?php echo $auth['category']; ?>
                                            </h4>
                                        </div>
                                        <div class="org-limit-body">
                                            <?php if (isset($auth['group'])): ?>
                                                <?php foreach ($auth['group'] as $gKey => $group) : ?>
                                                    <div class="org-limit-entry">
                                                        <label class="checkbox">
                                                            <input type="checkbox" data-id="<?php echo $gKey; ?>"
                                                                   data-node="modCheckbox"
                                                                   data-pid="<?php echo $key; ?>">
                                                            <?php echo $group['groupName']; ?>
                                                        </label>
                                                    </div>
                                                    <div class="fill-nn">
                                                        <ul class="org-limit-list clearfix">
                                                            <?php foreach ($group['node'] as $nIndex => $node): ?>
                                                                <?php
                                                                $isData = $node['type'] === 'data';
                                                                $checked = isset($related[$node['module']][$node['key']]);
                                                                ?>
                                                                <li <?php if ($isData): ?>class="org-limit-privilege-wrap"<?php endif; ?>>
                                                                    <div
                                                                        class="posr <?php if ($checked): ?>active<?php endif; ?>">
                                                                        <label class="checkbox">
                                                                            <input
                                                                                <?php if ($checked): ?>checked<?php endif; ?>
                                                                                type="checkbox"
                                                                                name="nodes[<?php echo $node['id']; ?>]"
                                                                                value="<?php echo $isData ? 'data' : $node['id']; ?>"
                                                                                data-node="funcCheckbox"
                                                                                data-pid="<?php echo $gKey; ?>">
                                                                            <span
                                                                                class="mlft"><?php echo $node['name']; ?></span>
                                                                        </label>
                                                                        <?php if ($isData): ?>
                                                                            <div class="org-limit-privilege">
                                                                                <?php foreach ($node['node'] as $dIndex => $data): ?>
                                                                                    <?php $checked = isset($related[$data['module']][$data['key']][$data['node']]); ?>
                                                                                    <input
                                                                                        value="<?php echo $checked ? $related[$data['module']][$data['key']][$data['node']] : ''; ?>"
                                                                                        <?php if ($checked): ?>checked<?php endif; ?>
                                                                                        name="data-privilege[<?php echo $node['id']; ?>][<?php echo $data['id']; ?>]"
                                                                                        type="text"
                                                                                        data-text="<?php echo $data['name']; ?>"
                                                                                        data-toggle="privilegeLevel">
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="fill-nn">
                                                    <ul class="org-limit-list clearfix">
                                                        <?php foreach ($auth['node'] as $nIndex => $node): ?>
                                                            <?php
                                                            $isData = $node['type'] === 'data';
                                                            $checked = isset($related[$node['module']][$node['key']]);
                                                            ?>
                                                            <li <?php if ($isData): ?>class="org-limit-privilege-wrap"<?php endif; ?>>
                                                                <div
                                                                    class="posr <?php if ($checked): ?>active<?php endif; ?>">
                                                                    <label class="checkbox">
                                                                        <input
                                                                            <?php if ($checked): ?>checked<?php endif; ?>
                                                                            type="checkbox"
                                                                            name="nodes[<?php echo $node['id']; ?>]"
                                                                            value="<?php echo $isData ? 'data' : $node['id']; ?>"
                                                                            data-pid="<?php echo $key; ?>">
                                                                        <span
                                                                            class="mlft"><?php echo $node['name']; ?></span>
                                                                    </label>
                                                                    <?php if ($isData): ?>
                                                                        <div class="org-limit-privilege">
                                                                            <?php foreach ($node['node'] as $dIndex => $data): ?>
                                                                                <?php $checked = isset($related[$data['module']][$data['key']][$data['node']]); ?>
                                                                                <input
                                                                                    value="<?php echo $checked ? $related[$data['module']][$data['key']][$data['node']] : ''; ?>"
                                                                                    <?php if ($checked): ?>checked<?php endif; ?>
                                                                                    type="text"
                                                                                    name="data-privilege[<?php echo $node['id']; ?>][<?php echo $data['id']; ?>]"
                                                                                    data-text="<?php echo $data['name']; ?>"
                                                                                    data-toggle="privilegeLevel">
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="fill-nn">
                                <button type="submit" class="btn btn-large btn-primary">提交</button>
                            </div>
                            <input type="hidden" name="posSubmit" value="1"/>
                            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src='<?php echo STATICURL; ?>/js/lib/formValidator/formValidator.packaged.js?<?php echo VERHASH; ?>'></script>
<script src='<?php echo $assetUrl; ?>/js/organization.js?<?php echo VERHASH; ?>'></script>
<script src='<?php echo $assetUrl; ?>/js/org_position_edit.js?<?php echo VERHASH; ?>'></script>
<script type="text/javascript">
    $(".privilege-level").tooltip({left: "-15px"});
    $('[data-toggle="tooltip"]').tooltip();
</script>
