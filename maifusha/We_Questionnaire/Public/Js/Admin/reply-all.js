$(function () {

    $(document).delegate('#all-reply', 'change', function () {
        var tobe = $(this).get(0).checked;

        $('#reply-table tbody input').each(function () {
            $(this).get(0).checked = tobe;
        });
    });


    $("#reply-table").dataTable({
        "iDisplayLength": 50,
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [0, 3, 6] },
        ],
        "oLanguage":{
            "sSearch": "搜索：",
            "sLengthMenu": "每页显示 _MENU_ 条记录",
            "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
            "sInfoFiltered": "(从 _MAX_ 条数据中检索)",
            "oPaginate": {
                "sPrevious": "前一页",
                "sNext": "后一页",
            },
            "sZeroRecords": "抱歉， 没有检索到数据",
            "sInfoEmpty": "没有数据",
        },
    });
});