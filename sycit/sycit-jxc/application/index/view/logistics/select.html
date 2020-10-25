<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header-model"}
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="sub-button-line marginTop10">
                <form class="pull-left marginBottom10 form-inline">
                    <div class="form-group">
                        <label class="control-label" for="projectNameInput">物流名称 :</label>
                        <input name="projectNameInput" id="projectNameInput" class="ipt form-control">
                        <button type="button" class="btn btn-primary" id="searchprojectName">搜索</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>物流名称</th>
                    <th>物流电话</th>
                    <th>物流传真</th>
                    <th>物流地址</th>
                </tr>
                </thead>
                <tbody id="orderList" class="logistics-list">
                {volist name="data" id="vo" empty="$empty"}
                <tr id="row_{$vo.log_id}">
                    <td class="logid">{$vo.log_id}</td>
                    <td class="logname">{$vo.log_name}</td>
                    <td>{$vo.log_phone}</td>
                    <td>{$vo.log_fax}</td>
                    <td>{$vo.log_address}</td>
                </tr>
                {/volist}
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
                        <div class="pull-right page-box">
                            {$page}
                        </div>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        //选择
        $("[id^='row_']").on('click', function(){
            var $id = $(this).attr('id');
            if ($id !== null || $id !== '') {
                var logid = $("#"+$id).find('td.logid').text();
                var logname = $("#"+$id).find('td.logname').text();
                if (logid!=='' && logname!=='') {
                    bDialog.close({logid:logid,logname:logname});
                }
            }
        });

        // 名称搜索
        $("#searchprojectName").on('click', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            if (NameInput !== null && NameInput !== '' && NameInput !== 'undefined') {
                window.location.href="{:Url('logistics/select')}?q="+NameInput;
            }
        });
    });
</script>
</body>
</html>