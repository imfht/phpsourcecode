// 通用提交
$('.ajax-submit').on('click', function() {
    var than  = $(this);
    var form  = $(this).parents('form');
    var index = layer.msg('提交中，请稍候', {
        icon: 16,
        time: false,
        shade: 0.3
    });
    than.attr('disabled', true);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        dataType: 'json',
        data: form.serialize(),
        success: function(result) {
            if (result.code === 1 && result.url != '') {
                setTimeout(function() {
                    location.href = result.url;
                }, 1000);
            } else {
                than.attr('disabled', false);
            }
            layer.close(index);
            layer.msg(result.msg);
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
    layer.confirm('确定删除？', {
        icon: 3,
        title: '提示'
    }, function(index) {
        var index = layer.msg('删除中，请稍候', {
            icon: 16,
            time: false,
            shade: 0.3
        });
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(result) {
                if (result.code === 1 && result.url != '') {
                    setTimeout(function() {
                        location.href = result.url;
                    }, 1000);
                }
                layer.close(index);
                layer.msg(result.msg);
            },
            error: function (xhr, state, errorThrown) {
                layer.close(index);
                layer.msg(state + '：' + errorThrown);
            }
        });
    });
    return false;
});
