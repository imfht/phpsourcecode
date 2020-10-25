$(function() {
    "use strict";

    $(document).on('click','.fc-day', function() {
        //alert('day cell click!');
        var date = $(this).attr('data-date');

        $.get('create',{'date':date},function(data){
            $('#modal').modal('show')
                .find('#modalContent')
                .html(data);
        });
        
    });

    //Make the dashboard widgets sortable Using jquery UI
    $(".connectedSortable").sortable({
        placeholder: "sort-highlight",
        connectWith: ".connectedSortable",
        handle: ".box-header, .nav-tabs",
        forcePlaceholderSize: true,
        zIndex: 999999
    }).disableSelection();
    $(".connectedSortable .box-header, .connectedSortable .nav-tabs-custom").css("cursor", "move");
})