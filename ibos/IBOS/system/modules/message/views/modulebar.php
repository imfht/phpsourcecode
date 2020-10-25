<form action="#" id="module_query_form" method="post">
    <input type="hidden" value="<?php echo $tag?>" id="tag"/>
    <div class="module-query" id="module_list_wrap">
        <div class="control-group">
            <label class="control-label xwb"><?php echo $lang['Belongs to module']; ?></label>
            <div class="controls clearfix">
                <ul class="module-query--list" id="module_list">
                    <li class="active">
                        <a href="javascript:;" data-action="toggleQueryParam" data-param='{"type": "module", "value": ""}'><?php echo $lang['There is no limit']; ?></a>
                    </li>
                    <?php foreach ($modulelist as $key => $val) {?>
                    <li class="">
                        <a href="javascript:;" data-action="toggleQueryParam" data-param='{"type": "module", "value": "<?php echo $key;?>"}'><?php echo $val;?></a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <?php if ($tag == 'notifyManage'): ?>
        <div class="clearfix">
            <div class="pull-left">
                <label class="checkbox btn"><input type="checkbox" data-name="remind"></label>
                <button type="button" class="btn btn-primary mls add-btn"><?php echo $lang['Add alarm notify']; ?></button>
                <button type="button" class="btn mls multi-btn"><?php echo $lang['Delete']; ?></button>
            </div>
            <a class="module-query--toggle" data-action="toggleModuleList"><?php echo $lang['Select module']; ?></a>
            <div class="search pull-right">
                <input type="text" placeholder="<?php echo $lang['Search notily content']; ?>" id="notify_manage_search" name="keyword" nofocus="">
                <a href="javascript:;">search</a>
            </div>
        </div>
    <?php endif; ?>
</form>
