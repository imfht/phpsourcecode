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
                    <div class="pull-left">
                        <label class="control-label" for="projectNameInput">搜索条件 :</label>
                        <input name="projectNameInput" id="projectNameInput" class="ipt form-control" data-toggle="tooltip" data-placement="bottom" title="搜索 企业名称 / 客户名称">
                        <button type="button" class="btn btn-primary" id="searchprojectName">搜索</button>
                    </div>
                    <div class="pull-right">
                        <a href="{:Url('orders/select_cusname')}" class="btn btn-primary">全部</a>
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
                    <th>企业名称</th>
                    <th>客户名称</th>
                    <th>联系电话</th>
                    <th>发货物流</th>
                    <th>收货地址</th>
                </tr>
                </thead>
                <tbody id="orderList" class="logistics-list">
                {volist name="list" id="vo" empty="$empty"}
                <tr id="row_{$vo.cus_id}">
                    <td class="qid">{$vo.cus_id}</td>
                    <td class="qiye">{$vo.cus_name}</td>
                    <td class="lxrmc">{$vo.cus_duty}</td>
                    <td class="lxrdh">{$vo.cus_moble}</td>
                    <td class="wlmc">{$vo.cus_log_id.log_name}</td>
                    <td class="shdz">{$vo.cus_prov}-{$vo.cus_city}-{$vo.cus_dist}</td>
                </tr>
                {/volist}
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6">
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
        $('[data-toggle="tooltip"]').tooltip(); //工具提示
        //选择
        $("[id^='row_']").on('click', function(){
            var $id = $(this).attr('id');
            if ($id !== null || $id !== '') {
                var qid = $("#"+$id).find('td.qid').text();
                var qiye = $("#"+$id).find('td.qiye').text();
                var lxrmc = $("#"+$id).find('td.lxrmc').text();
                var lxrdh = $("#"+$id).find('td.lxrdh').text();
                var wlmc = $("#"+$id).find('td.wlmc').text();
                var shdz = $("#"+$id).find('td.shdz').text();
                if (lxrmc=='') {
                    alert('没有收货人信息')
                }
                if (wlmc=='') {
                //    alert('没有物流信息')
					wlmc = '';
					//shdz = '';
                }
                //if (lxrdh!=='') {
                    bDialog.close({qid:qid,qiye:qiye,lxrmc:lxrmc,lxrdh:lxrdh,wlmc:wlmc,shdz:shdz});
                //}
            }
        });
        // 名称搜索
        $("#searchprojectName").on('click', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            if (NameInput !== null && NameInput !== '' && NameInput !== 'undefined') {
                window.location.href="{:Url('orders/select_cusname')}?q="+NameInput;
            }
        });
    });
</script>
</body>
</html>