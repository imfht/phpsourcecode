jQuery(function () {
    var cnt = $('.markdown_container').length;
    for (var i = 1; i <= cnt; i++) {
        editormd.markdownToHTML("markdown_view_" + i, {
            htmlDecode: "style,script,iframe",
            tocContainer: "#custom_toc_container_" + i,
            markdownSourceCode: false,
            emoji: false,
            taskList: true,
            tex: true,
            flowChart: true,
            sequenceDiagram: true,
            onload: function (div) {
                div.find('.sequence-diagram > svg, .flowchart > svg').each(function (i, o) {
                    try {
                        var width = $(o).attr('width'), height = $(o).attr('height');
                        o.setAttribute('viewBox', '0 0 ' + width + ' ' + height);
                    } catch (e) {
                    }
                });
            }
        });
    }
});