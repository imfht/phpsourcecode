<?php $cfg = array(
    'title'             => $lang->get('Albums', 'console.common') . ' &raquo; ' . $albumRow['album_name'] . ' &raquo; ' . $lang->get('Choose image'),
    'menu_active'       => 'attach',
    'sub_active'        => 'album',
    'baigoValidate'    => 'true',
    'baigoSubmit'       => 'true',
    'baigoCheckall'     => 'true',
    'baigoQuery'        => 'true',
    'baigoDialog'       => 'true',
    'imageAsync'        => 'true',
    'pathInclude'       => $path_tpl . 'include' . DS,
);

include($cfg['pathInclude'] . 'console_head' . GK_EXT_TPL); ?>

    <div class="d-flex justify-content-between">
        <nav class="nav mb-3">
            <a href="<?php echo $route_console; ?>album/" class="nav-link">
                <span class="fas fa-chevron-left"></span>
                <?php echo $lang->get('Back'); ?>
            </a>
            <a href="<?php echo $route_console; ?>attach/form/album/<?php echo $albumRow['album_id']; ?>/" class="nav-link">
                <span class="fas fa-cloud-upload-alt"></span>
                <?php echo $lang->get('Upload'); ?>
            </a>
        </nav>

        <form name="attach_search" id="attach_search" class="d-none d-lg-inline-block" action="<?php echo $route_console; ?>album_belong/index/id/<?php echo $albumRow['album_id']; ?>/">
            <div class="input-group mb-3">
                <input type="text" name="key" value="<?php echo $search['key']; ?>" placeholder="<?php echo $lang->get('Keyword'); ?>" class="form-control">
                <span class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">
                        <span class="fas fa-search"></span>
                    </button>
                </span>
                <span class="input-group-append">
                    <button class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" type="button" data-toggle="collapse" data-target="#bg-search-more" >
                        <span class="sr-only">Dropdown</span>
                    </button>
                </span>
            </div>
            <div class="collapse" id="bg-search-more">
                <div class="input-group mb-3">
                    <select name="box" class="custom-select">
                        <option value=""><?php echo $lang->get('All status'); ?></option>
                        <?php foreach ($box as $key=>$value) { ?>
                            <option <?php if ($search['box'] == $value) { ?>selected<?php } ?> value="<?php echo $value; ?>">
                                <?php echo $lang->get($value); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <?php if (!empty($search['key']) || !empty($search['box'])) { ?>
        <div class="mb-3 text-right">
            <?php if (!empty($search['key'])) { ?>
                <span class="badge badge-info">
                    <?php echo $lang->get('Keyword'); ?>:
                    <?php echo $search['key']; ?>
                </span>
            <?php }

            if (!empty($search['box'])) { ?>
                <span class="badge badge-info">
                    <?php echo $lang->get('Status'); ?>:
                    <?php echo $lang->get($search['box']); ?>
                </span>
            <?php } ?>

            <a href="<?php echo $route_console; ?>album_belong/index/id/<?php echo $search['id']; ?>/" class="badge badge-danger badge-pill">
                <span class="fas fa-times-circle"></span>
                <?php echo $lang->get('Reset'); ?>
            </a>
        </div>
    <?php } ?>

    <div class="card-group">
        <div class="card">
            <form name="attach_list_belong" id="attach_list_belong" action="<?php echo $route_console; ?>album_belong/remove/">
                <input type="hidden" name="<?php echo $token['name']; ?>" value="<?php echo $token['value']; ?>">
                <input type="hidden" name="album_id" value="<?php echo $albumRow['album_id']; ?>">

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-nowrap bg-td-xs">
                                    <div class="form-check">
                                        <input type="checkbox" name="chk_all_belong" id="chk_all_belong" data-parent="first" class="form-check-input">
                                        <label for="chk_all_belong" class="form-check-label">
                                            <small><?php echo $lang->get('ID'); ?></small>
                                        </label>
                                    </div>
                                </th>
                                <th>&nbsp;</th>
                                <th><?php echo $lang->get('Images in album'); ?></th>
                                <th class="d-none d-lg-table-cell bg-td-md text-right">
                                    <small><?php echo $lang->get('Status'); ?></small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attachRowsBelong as $key=>$value) { ?>
                                <tr class="bg-manage-tr<?php if ($albumRow['album_attach_id'] == $value['attach_id']) { ?>  table-success<?php } ?>">
                                    <td class="text-nowrap bg-td-xs">
                                        <div class="form-check">
                                            <input type="checkbox" name="attach_ids_belong[]" value="<?php echo $value['attach_id']; ?>" id="attach_id_belong_<?php echo $value['attach_id']; ?>" data-parent="chk_all_belong" data-validate="attach_ids_belong" class="form-check-input attach_id_belong">
                                            <label for="attach_id_belong_<?php echo $value['attach_id']; ?>" class="form-check-label">
                                                <small><?php echo $value['attach_id']; ?></small>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="bg-td-xs">
                                        <a href="<?php echo $route_console; ?>attach/show/id/<?php echo $value['attach_id']; ?>/">
                                            <img src="{:DIR_STATIC}image/loading.gif" data-src="<?php echo $value['thumb_default']; ?>" data-toggle="async" alt="<?php echo $value['attach_name']; ?>" class="img-fluid">
                                        </a>
                                    </td>
                                    <td>
                                        <a class="dropdown-toggle float-right d-block d-lg-none" data-toggle="collapse" href="#collapse-belong-<?php echo $value['attach_id']; ?>">
                                            <span class="sr-only">Dropdown</span>
                                        </a>
                                        <div class="mb-2 text-wrap text-break">
                                            <?php echo $value['attach_name']; ?>
                                        </div>
                                        <div class="bg-manage-menu">
                                            <div class="d-flex flex-wrap">
                                                <a href="<?php echo $route_console; ?>attach/show/id/<?php echo $value['attach_id']; ?>/" class="mr-2">
                                                    <span class="fas fa-eye"></span>
                                                    <?php echo $lang->get('Show'); ?>
                                                </a>
                                                <?php if ($albumRow['album_attach_id'] == $value['attach_id']) { ?>
                                                    <span class="mr-2">
                                                        <span class="fas fa-image"></span>
                                                        <?php echo $lang->get('Set as cover'); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <a href="javascript:void(0);" data-id="<?php echo $value['attach_id']; ?>" class="attach_cover mr-2">
                                                        <span class="fas fa-image"></span>
                                                        <?php echo $lang->get('Set as cover'); ?>
                                                    </a>
                                                <?php } ?>
                                                <a href="javascript:void(0);" data-id="<?php echo $value['attach_id']; ?>" class="text-danger attach_remove">
                                                    <span class="fas fa-times"></span>
                                                    <?php echo $lang->get('Remove'); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <dl class="row collapse mt-3 mb-0" id="collapse-belong-<?php echo $value['attach_id']; ?>">
                                            <dt class="col-3">
                                                <small><?php echo $lang->get('Status'); ?></small>
                                            </dt>
                                            <dd class="col-9">
                                                <?php if ($albumRow['album_attach_id'] == $value['attach_id']) { ?>
                                                    <span class="badge badge-info"><?php echo $lang->get('Cover'); ?></span>
                                                <?php }
                                                $str_status = $value['attach_box'];
                                                include($cfg['pathInclude'] . 'status_process' . GK_EXT_TPL); ?>
                                            </dd>
                                        </dl>
                                    </td>
                                    <td class="d-none d-lg-table-cell bg-td-md text-right">
                                        <?php if ($albumRow['album_attach_id'] == $value['attach_id']) { ?>
                                            <span class="badge badge-info"><?php echo $lang->get('Cover'); ?></span>
                                        <?php }
                                        $str_status = $value['attach_box'];
                                        include($cfg['pathInclude'] . 'status_process' . GK_EXT_TPL); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <small class="form-text" id="msg_attach_ids_belong"></small>
                    </div>

                    <div class="clearfix">
                        <div class="float-left">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-danger">
                                    <?php echo $lang->get('Remove'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="float-right">
                            <?php $pageRow = $pageRowBelong;
                            $pageParam = $pageParamBelong;
                            include($cfg['pathInclude'] . 'pagination' . GK_EXT_TPL); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card">
            <form name="attach_list" id="attach_list" action="<?php echo $route_console; ?>album_belong/choose/">
                <input type="hidden" name="<?php echo $token['name']; ?>" value="<?php echo $token['value']; ?>">
                <input type="hidden" name="album_id" value="<?php echo $albumRow['album_id']; ?>">

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-nowrap bg-td-xs">
                                    <div class="form-check">
                                        <input type="checkbox" name="chk_all" id="chk_all" data-parent="first" class="form-check-input">
                                        <label for="chk_all" class="form-check-label">
                                            <small><?php echo $lang->get('ID'); ?></small>
                                        </label>
                                    </div>
                                </th>
                                <th>&nbsp;</th>
                                <th><?php echo $lang->get('Pending images'); ?></th>
                                <th class="d-none d-lg-table-cell bg-td-md text-right">
                                    <small><?php echo $lang->get('Status'); ?></small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attachRows as $key=>$value) { ?>
                                <tr class="bg-manage-tr">
                                    <td class="text-nowrap bg-td-xs">
                                        <div class="form-check">
                                            <input type="checkbox" name="attach_ids[]" value="<?php echo $value['attach_id']; ?>" id="attach_id_<?php echo $value['attach_id']; ?>" data-parent="chk_all" data-validate="attach_ids" class="form-check-input attach_id">
                                            <label for="attach_id_<?php echo $value['attach_id']; ?>" class="form-check-label">
                                                <small><?php echo $value['attach_id']; ?></small>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="bg-td-xs">
                                        <a href="<?php echo $route_console; ?>attach/show/id/<?php echo $value['attach_id']; ?>/">
                                            <img src="{:DIR_STATIC}image/loading.gif" data-src="<?php echo $value['thumb_default']; ?>" data-toggle="async" alt="<?php echo $value['attach_name']; ?>" class="img-fluid">
                                        </a>
                                    </td>
                                    <td>
                                        <a class="dropdown-toggle float-right d-block d-lg-none" data-toggle="collapse" href="#td-collapse-<?php echo $value['attach_id']; ?>">
                                            <span class="sr-only">Dropdown</span>
                                        </a>
                                        <div class="mb-2 text-wrap text-break">
                                            <?php echo $value['attach_name']; ?>
                                        </div>
                                        <div class="bg-manage-menu">
                                            <div class="d-flex flex-wrap">
                                                <a href="<?php echo $route_console; ?>attach/show/id/<?php echo $value['attach_id']; ?>/" class="mr-2">
                                                    <span class="fas fa-eye"></span>
                                                    <?php echo $lang->get('Show'); ?>
                                                </a>
                                                <a href="javascript:void(0);" data-id="<?php echo $value['attach_id']; ?>" class="attach_choose">
                                                    <span class="fas fa-check"></span>
                                                    <?php echo $lang->get('Choose'); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <dl class="row collapse mt-3 mb-0" id="td-collapse-<?php echo $value['attach_id']; ?>">
                                            <dt class="col-3">
                                                <small><?php echo $lang->get('Status'); ?></small>
                                            </dt>
                                            <dd class="col-9">
                                                <?php $str_status = $value['attach_box'];
                                                include($cfg['pathInclude'] . 'status_process' . GK_EXT_TPL); ?>
                                            </dd>
                                        </dl>
                                    </td>
                                    <td class="d-none d-lg-table-cell bg-td-md text-right">
                                        <?php $str_status = $value['attach_box'];
                                        include($cfg['pathInclude'] . 'status_process' . GK_EXT_TPL); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <small class="form-text" id="msg_attach_ids"></small>
                    </div>

                    <div class="clearfix">
                        <div class="float-left">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $lang->get('Choose'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="float-right">
                            <?php $pageRow = $pageRowAlbum;
                            $pageParam = 'page';
                            include($cfg['pathInclude'] . 'pagination' . GK_EXT_TPL); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form name="album_cover" id="album_cover" action="<?php echo $route_console; ?>album/cover/">
        <input type="hidden" name="<?php echo $token['name']; ?>" value="<?php echo $token['value']; ?>">
        <input type="hidden" name="album_id" value="<?php echo $albumRow['album_id']; ?>">
        <input type="hidden" name="attach_id" id="attach_id" value="0">
    </form>

<?php include($cfg['pathInclude'] . 'console_foot' . GK_EXT_TPL); ?>

    <script type="text/javascript">
    var opts_dialog = {
        btn_text: {
            cancel: '<?php echo $lang->get('Cancel'); ?>',
            confirm: '<?php echo $lang->get('Confirm'); ?>'
        }
    };

    var opts_validate_belong = {
        rules: {
            attach_ids_belong: {
                checkbox: '1'
            }
        },
        attr_names: {
            attach_ids_belong: '<?php echo $lang->get('Attachment'); ?>'
        },
        type_msg: {
            checkbox: '<?php echo $lang->get('Choose at least one {:attr}'); ?>'
        },
        selector_types: {
            attach_ids_belong: 'validate'
        }
    };

    var opts_validate_list = {
        rules: {
            attach_ids: {
                checkbox: '1'
            }
        },
        attr_names: {
            attach_ids: '<?php echo $lang->get('Attachment'); ?>'
        },
        type_msg: {
            checkbox: '<?php echo $lang->get('Choose at least one {:attr}'); ?>'
        },
        selector_types: {
            attach_ids: 'validate'
        }
    };

    var opts_submit_list = {
        modal: {
            btn_text: {
                close: '<?php echo $lang->get('Close'); ?>',
                ok: '<?php echo $lang->get('OK'); ?>'
            }
        },
        msg_text: {
            submitting: '<?php echo $lang->get('Submitting'); ?>'
        }
    };

    $(document).ready(function(){
        var obj_dialog            = $.baigoDialog(opts_dialog);
        var obj_validate_belong   = $('#attach_list_belong').baigoValidate(opts_validate_belong);
        var obj_submit_belong     = $('#attach_list_belong').baigoSubmit(opts_submit_list);

        //console.log(obj_submit_belong);

        $('#attach_list_belong').submit(function(){
            if (obj_validate_belong.verify()) {
                obj_dialog.confirm('<?php echo $lang->get('Are you sure to remove?'); ?>', function(confirm_result){
                    if (confirm_result) {
                        obj_submit_belong.formSubmit();
                    }
                });
            }
        });


        $('#attach_list_belong').baigoCheckall();

        var obj_validate_list   = $('#attach_list').baigoValidate(opts_validate_list);
        var obj_submit_list     = $('#attach_list').baigoSubmit(opts_submit_list);

        //console.log(obj_submit_list);

        $('#attach_list').submit(function(){
            if (obj_validate_list.verify()) {
                obj_submit_list.formSubmit();
            }
        });


        $('.attach_cover').click(function(){
            var obj_submit_cover = $('#album_cover').baigoSubmit(opts_submit_list);
            var _attach_id = $(this).data('id');
            $('#attach_id').val(_attach_id);
            obj_submit_cover.formSubmit();
        });


        $('.attach_remove').click(function(){
            var _attach_id = $(this).data('id');
            $('.attach_id_belong').prop('checked', false);
            $('#attach_id_belong_' + _attach_id).prop('checked', true);
            $('#attach_list_belong').submit();
        });


        $('.attach_choose').click(function(){
            var _attach_id = $(this).data('id');
            $('.attach_id').prop('checked', false);
            $('#attach_id_' + _attach_id).prop('checked', true);
            $('#attach_list').submit();
        });

        $('#attach_list').baigoCheckall();

        var obj_query = $('#attach_search').baigoQuery();

        $('#attach_search').submit(function(){
            obj_query.formSubmit();
        });
    });
    </script>
<?php include($cfg['pathInclude'] . 'html_foot' . GK_EXT_TPL);