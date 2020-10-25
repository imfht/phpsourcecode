<div class="mc clearfix">
    <link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/message.css?<?php echo VERHASH; ?>">
    <!-- Sidebar goes here-->
    <?php echo $this->getSidebar(array('lang' => $lang)); ?>
    
    <div class="mcr" id="mainer">
        <div class="page-list message-page-list">
            <div class="page-list-header page-list-header__title">
                <span class="fsl">提醒管理</span>
            </div>
            <div class="page-list-header page-list-header__query">
                <?php echo $this->getModulebar(array('lang' => $lang, 'tag' => 'notifyManage')); ?>
            </div>
            <div class="page-list-mainer clearfix">
                <div class="remain-msg">
                    <ul class="remain-msg--list" id="list_mainer">
                    </ul>
                </div>
            </div>
            <div class="page-list-footer">
                <div id="pagination" class="pull-right ajax-pagination"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/template" id="remind_tpl">
    <li>
        <div class="clearfix">
            <img src="<%= iconUrl %>" class="icon-module pull-left" alt="模块图标">
            <div class="pull-left remain-msg--detail">
                <p>
                    <a href="<%= url %>" target="_blank" title="<%= title %>">
                        <span class="remain-msg--title ellipsis"><%= title %></span>
                    </a>
                    <i class="o-msg-remainer mlm" data-toggle="tooltip" data-placement="top" data-original-title="提醒范围: <%= receiveuids %>"></i>
                </p>
                <p class="tcm mbm"><%= showtime %></p>
                <p class="remain-msg--desc ellipsis"><%= body %></p>
            </div>
            <label class="checkbox pull-right"><input type="checkbox" name="remind" value="<%= id %>"></label>
        </div>
        <div class="remain-msg--toolbar">
            <a href="javascript:;" title="编辑" class="cbtn o-edit" data-param='{"module": "<%= module %>", "node": "<%= node %>", "eventId": "<%= eventid %>", "id": "<%= id %>" }'></a>
            <a href="javascript:;" title="删除" class="cbtn o-trash" data-param='{"id": "<%= id %>" }'></a>
        </div>
    </li>
</script>
<script src='<?php echo $assetUrl; ?>/js/message.js?<?php echo VERHASH; ?>'></script>
<script src='<?php echo $assetUrl; ?>/js/message_manage.js?<?php echo VERHASH; ?>'></script>