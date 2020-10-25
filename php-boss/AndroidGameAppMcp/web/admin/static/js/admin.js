/**
 * 
 */

var gameselect = function(elid){
	$(elid).select2({
        placeholder:'所属游戏',
        multiple:false,
        allowClear: true,
        minimumInputLength: 1,
        ajax:{
            url:'/game/api/game-select-search',
            type:'GET',
            dataType:'json',
            data:function(term,page){
                return {
                    q:term,
                    page_limit: 10,
                };
            },
            results:function(data,page){                
                return {results:data.game_list};
                //return {results:[{id:'a',text:'a'},{id:'b',text:'b'}]};
            }
        },
        initSelection: function(element, callback) {
            var id=$(element).val();
            if (id!=="") {
                $.ajax({
                    url:'/game/api/game-select-init',
                    type:'GET',
                    dataType:'json',
                    data:{id:id},                    
                }).done(function(data) { callback(data); });
            }
        },
        //formatResult:formatOperator
    });
};