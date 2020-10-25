function userAddrEditInit() {
    /* 表单验证 */
    $('#invoiceForm').validator({
        fields: {
            invoiceType: {
                rule: "checked;",
                msg: { checked: "至少选择一项" },
                tip: "",
                ok: "",
            },
            invoiceHead: {
                rule: "required;",
                msg: { required: "请输入您的发票抬头" },
                tip: "请输入您的发票抬头",
                ok: "",
            },
            invoiceCode: {
                rule: "required;",
                msg: { required: "请输入您的发票税号" },
                tip: "请输入您的发票税号",
                ok: "",
            },
            invoiceAddr: {
                rule: "required(#invoiceType-1:checked);",
                msg: { required: "请输入注册地址" },
                tip: "请输入注册地址",
                ok: "",
            },
            invoicePhoneNumber: {
                rule: "required(#invoiceType-1:checked);",
                msg: { required: "请输入注册电话" },
                tip: "请输入注册电话",
                ok: "",
            },
            invoiceBankName: {
                rule: "required(#invoiceType-1:checked);",
                msg: { required: "请输入开户银行" },
                tip: "请输入开户银行",
                ok: "",
            },
            invoiceBankNo: {
                rule: "required(#invoiceType-1:checked);",
                msg: { required: "请输入您的银行账户" },
                tip: "请输入您的银行账户",
                ok: "",
            }
        },
        valid: function (form) {
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', { icon: 16, time: 60000 });
            $.post(WST.U('home/invoices/' + ((params.id == 0) ? "add" : "edit")), params, function (data, textStatus) {
                layer.close(loading);
                var json = WST.toJson(data);
                if (json.status == '1') {
                    WST.msg(json.msg, { icon: 1 });
                    location.href = WST.U('home/invoices/invoicelist');
                } else {
                    WST.msg(json.msg, { icon: 2 });
                }
            });

        }

    });
}
function listQuery() {
    $.post(WST.U('home/invoices/pageQuery'), '', function (data, textStatus) {
        var json = WST.toJson(data);
        if (json && json.length>0) {
            var gettpl = document.getElementById('invoices').innerHTML;
            laytpl(gettpl).render(json, function (html) {
                $('#invoices_box').html(html);
            });
        } else {
            $('#invoices_box').empty();
        }
    });
}

function editInvoice(id) {
    location.href = WST.U('home/invoices/toEdit', 'id=' + id);
}
function toAdd(){
    var num = $('#invoices_box').children().size();
    if(num<20){
        location.href = WST.U('home/invoices/toEdit');
    }else{
        WST.msg('发票信息不能超过20条', { icon: 5 });
    }
}
function delInvoice(id, t) {
    WST.confirm({
        content: "您确定要删除该发票信息吗？", yes: function (tips) {
            var ll = layer.load('数据处理中，请稍候...');
            $.post(WST.U('Home/invoices/del'), { id: id }, function (data, textStatus) {
                layer.close(ll);
                layer.close(tips);
                var json = WST.toJson(data);
                if (json.status == '1') {
                    WST.msg('操作成功!', { icon: 1 }, function () {
                        listQuery();
                    });
                } else {
                    WST.msg('操作失败!', { icon: 5 });
                }
            });
        }
    });

}