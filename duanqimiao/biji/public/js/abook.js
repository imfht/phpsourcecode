/**
 * Created by dell on 2016/8/11.
 */
jQuery( document ).ready(function( $ ) {

    $('#abook').click(function () {
        $('#book').toggle();
    });

    $('#asearch').click(function(){
       $('#search').toggle();
    });

    $('#search_btn').click(function(){
        $.ajax({
            type: "GET",
            url: "/book/",
            data:{'search_book':$('input[name=search_book]').val()},
            success:function(data){
                var html = "";
                //第一个each:index为booksObj values为Object
                $.each(data,function(index,values){
                    //第二个each:index为所有匹配到的笔记本个数 value为Object
                    $.each(values,function(index,value){
                        //第三个each:index为字段名title val为每个字段对应的title值
                        $.each(value,function(index,val){
                             html +=
                            "<form method='GET' action='/biji/'><div class='book_list' ><button type='submit'  class='book_list_btn'>"+val+"</button></div><input type='hidden' name='search_title' value='"+val+"'/></form>";

                        });
                    });
                });
                $('#book_list').html(html);
            }
        });
    });
});