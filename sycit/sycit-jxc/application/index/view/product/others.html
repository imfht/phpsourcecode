<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
</head>
<body>
{// 引入顶部导航文件}
{include file="public/topbar"}

<div class="viewFramework-body viewFramework-sidebar-full">
    {// 引入左侧导航文件}
    {include file="public/sidebar"}
    <!-- 主体内容 开始 -->
    <div class="viewFramework-product">
        <!-- 中间导航 开始 viewFramework-product-col-1-->
        <!-- 中间导航 结束 -->
        <div class="viewFramework-product-body">
            <div class="console-container">
                <!--内容开始-->
                <div class="row syc-bg-fff">
                    <div class="col-lg-12 syc-border-bs">
                        <div class="console-title">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                                <a href="javascript:history.go(-1);" class="btn btn-default">
                                    <span class="icon-goback"></span><span>返回</span>
                                </a>
                            </div>
                            <div class="pull-right">
                                <a href="javascript:window.location.reload();" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="marginTop15"></div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="portlet light-syc bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <span class="caption-subject bold uppercase font-green-haze">属性单价</span>
                                    <span class="caption-helper">包边线单价设定和墙厚单价</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table class="table syc-table border">
                                    <thead>
                                    <tr>
                                        <th colspan="3"><b>包边线</b></th>
                                        <th colspan="3"><b>墙厚度</b></th>
                                        <th rowspan="2">操作</th>
                                    </tr>
                                    <tr>
                                        <th>包边分类</th>
                                        <th>包边名称</th>
                                        <th>包边金额</th>
                                        <th>基础墙厚</th>
                                        <th>递增墙厚</th>
                                        <th>递增金额</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {volist name="$baobian" id="vo"}
                                    <tr>
                                        <td>{$vo.bname}</td>
                                        <td>{$vo.bval}</td>
                                        <td>￥<input type="text" class="syc-input w40 text-center" id="bamo_{$vo.bid}" value="{$vo.bamo}">元</td>
                                        <td><input type="text" class="syc-input w40 text-center" id="qhjc_{$vo.bid}" value="{$vo.qhjc}">mm</td>
                                        <td><input type="text" class="syc-input w40 text-center" id="qhdz_{$vo.bid}" value="{$vo.qhdz}">mm</td>
                                        <td>￥<input type="text" class="syc-input w40 text-center" id="qhdzamo_{$vo.bid}" value="{$vo.qhdzamo}">元</td>
                                        <td><a href="javascript:;" onclick="editBaobianAmo('{$vo.bid}')">修改</a></td>
                                    </tr>
                                    {/volist}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--内容结束-->
            </div>
        </div>
    </div>
</div>

{// 引入底部公共JS文件}
{include file="public/footer"}
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-product").addClass("sidebar-nav-active"); // 大分类
        $("#product-others").addClass("active"); // 小分类

        // 名称搜索
        $("#searchprojectName").on('click', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            if (NameInput !== null && NameInput !== '' && NameInput !== 'undefined') {
                window.location.href="{:Url('product/color')}?q="+NameInput;
            }
        });

        // 使用prop实现全选和反选
        $("#ckSelectAll").on('click', function () {
            $("input[name='ckbox[]']").prop("checked", $(this).prop("checked"));
        });

        // 获取选中元素删除操作
        $("#DelAllAttr").on('click', function () {
            if(confirm("是否删除所选？")){
                // 获取所有选中的项并把选中项的文本组成一个字符串
                var valArr = new Array;
                $("input[name='ckbox[]']:checked").each(function(i){
                    valArr[i] = $(this).val();
                });
                if (valArr.length !== 0 && valArr !== null && valArr !== '') {
                    var data={name:'delallattr',pid:valArr.join(',')};
                    $.sycToAjax("{:Url('product/color_delete')}", data);
                };
            };
            return false;
        });
    });
    //单价操作
    function editBaobianAmo(e) {
        if(confirm("是否修改金额？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var r = /^\+?[0-9]*$/;　　//整数（正整数 + 0）
                //var amo = {};
                var bamo = $("#bamo_"+e).val();
                var qhjc = $("#qhjc_"+e).val();
                var qhdz = $("#qhdz_"+e).val();
                var qhdzamo = $("#qhdzamo_"+e).val();
                if (bamo < 0 || r.test(bamo) == false || qhjc < 0 || r.test(qhjc) == false || qhdz < 0 || r.test(qhdz) == false || qhdzamo < 0 || r.test(qhdzamo) == false) {
                    alert('不能小于0或空');
                    return false;
                }
                var options={pid:e,bamo:bamo,qhjc:qhjc,qhdz:qhdz,qhdzamo:qhdzamo};
                $.ajax({
                    url: '{:Url(\'product/others_edit\')}',
                    type: 'POST', //GET
                    async: true,    //或false,是否异步
                    data: options,
                    timeout: 5000,    //超时时间
                    dataType: 'json',    //返回的数据格式：json/xml/html/script/jsonp/text

                    success: function (result) {
                        if (result.code == '1') {
                            //
                            toastr.success(result.msg);
                        } else {
                            toastr.warning(result.msg);
                        }
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    },
                });
            }
        };
        return false;
    }
</script>
</body>
</html>