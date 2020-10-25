jQuery.noConflict();

function invOnSwitch(n){
    //先全部移除
    $('#invoiceType1').removeClass('ui-icon-unchecked-s wst-active ui-icon-checked-s');
    $('#invoiceType2').removeClass('ui-icon-unchecked-s wst-active ui-icon-checked-s');

    if (n == 0) {
        $('#invoiceType1').addClass('ui-icon-checked-s wst-active');
        $('#invoiceType2').addClass('ui-icon-unchecked-s');
        $('.interData').hide();
        $('#invoiceType').val(0);
    } else {
        $('#invoiceType2').addClass('ui-icon-checked-s wst-active');
        $('#invoiceType1').addClass('ui-icon-unchecked-s');
        $('.interData').show();
        $('#invoiceType').val(1);
    }

}


function switchPage(n) {
    //1编辑 2新增
    if (n == 1) {
        $('.invo-js1').hide();
        $('.invo-js2').show();
        $("#invoId").val(0);
        $('.interData').hide();
        $('#header-title').text('新增发票');
    } else if (n == 2) {
        $('.invo-js1').hide();
        $('.invo-js2').show();
        $("#invoId").val(0);
        $('#invoiceType2').removeAttr('checked')
        $('#invoiceType1').attr('checked', 'checked')
        $('#header-title').text('新增发票');
        setNullInput('add')
    } else {
        $('.invo-js2').hide();
        $('.invo-js1').show();
    }
}



//清空不必要的元素
function setNullInput(type = 'edit') {
    //新增数据时判断是否是普通发票
    if (type == 'add') {
        $("#invoiceHead").val("");
        $("#invoiceCode").val("");
    }
    $("#invoiceAddr").val("");
    $("#invoicePhoneNumber").val("");
    $("#invoiceBankName").val("");
    $("#invoiceBankNo").val("");
}


//新增或保存
$("#saveInvoice").click(function () {
    var checkValue = $('#invoiceType').val();
    if (checkValue == '0') {
        //将不需要的数据清除
        setNullInput();
    }

    var params = getParams();
    if ($("#invoId").val() != 0) {
        
        params.id = $("#invoId").val();
    }

    delete(params.invoId);
    AddInvo(params, ($("#invoId").val() == 0) ? 'add' : 'edit');
  
});

//新增数据
function AddInvo(params, type = 'add') {
    $.post(WST.U('mobile/invoices/' + ((type == 'add') ? 'add' : 'edit')), params, function (data, textStatus) {
        var json = WST.toJson(data);
        if (json.status == 1) {
            WST.msg(((type == 'add') ? '新增' : '修改') + '发票成功', 'success');
            setTimeout(function () {
                location.href = WST.U('mobile/invoices/listquery');
            }, 1500);

        } else {
            WST.msg(json.msg);
        }
    });
}

//编辑时修改数据
function getInvoice(id, invoiceType) {
    switchPage(1);
    $('#header-title').text('编辑发票');
    $("#invoId").val(id);
    invOnSwitch(invoiceType)
    $.post(WST.U('mobile/invoices/get'), {'id': id}, function (data, textStatus) {
        var json = WST.toJson(data);
        if (json.status == 1) {
            var data = json.data;
            $("#invoiceHead").val(data.invoiceHead);
            $("#invoiceCode").val(data.invoiceCode);
            $("#invoiceAddr").val(data.invoiceAddr);
            $("#invoicePhoneNumber").val(data.invoicePhoneNumber);
            $("#invoiceBankName").val(data.invoiceBankName);
            $("#invoiceBankNo").val(data.invoiceBankNo);
        } else {
            WST.msg('系统错误', 'error');
        }
    });
}

function editInvoice(id) {
    location.href = WST.U('home/invoices/toEdit', 'id=' + id);
}

function delInvoice(id) {
    WST.dialog('确定删除吗？','del('+id+')');
}

function del(id) {
    $.post(WST.U('mobile/invoices/del'), { id: id }, function (data, textStatus) {
        var json = WST.toJson(data);
        WST.msg(json.msg,'success');
        setTimeout(function () {
            location.href = WST.U('mobile/invoices/listquery');
        }, 1500);
    });
}


//获取form的name数据
function getParams() {
    var data = {};

    var params = $('form').serializeArray();    //根据name值获取value
    $.each(params, function () {
        data [this.name] = this.value;
    });

    return data;
}