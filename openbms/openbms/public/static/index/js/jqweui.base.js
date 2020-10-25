// 通用提交
$('.ajax-submit').on('click', function() {
    var than  = $(this);
    var form  = $(this).parents('form');
    $.showLoading();
    than.attr('disabled', true);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        dataType: 'json',
        data: form.serialize(),
        success: function(result) {
            $.hideLoading();
            if (result.code === 1 && result.url != '') {
                $.toast(result.msg);
                setTimeout(function() {
                    location.href = result.url;
                }, 1000);
            } else {
                $.toast(result.msg, 'forbidden');
                than.attr('disabled', false);
            }
        },
        error: function (xhr, state, errorThrown) {
            layer.close(index);
            layer.msg(state + '：' + errorThrown);
        }
    });
    return false;
});

// 通用删除
$('.ajax-delete').on('click', function() {
    var url = $(this).attr('href');
    $.confirm("确定删除？", function() {
        $.showLoading();
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(result) {
                $.hideLoading();
                if (result.code === 1 && result.url != '') {
                    $.toast(result.msg);
                    setTimeout(function() {
                        location.href = result.url;
                    }, 1000);
                } else {
                    $.toast(result.msg, 'forbidden');
                }
            },
            error: function (xhr, state, errorThrown) {
                layer.close(index);
                layer.msg(state + '：' + errorThrown);
            }
        });
    });
    return false;
});
