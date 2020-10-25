       function fold(cid,_this){
            if($(_this).attr('fold')==0){
                $('tr[pid='+cid+']').css('display','');
                //findcds(cid)
                $(_this).attr('fold',1);
                $(_this).html('-');
            }
            else{
                findcd(cid);
                $(_this).attr('fold',0);
                $(_this).html('+');
            }
        }
        function findcd(id)
        {
            $('tr[pid='+id+']').each(function(){
                findcd($(this).attr('id'));
                $(this).css('display','none');
                var obj_a=$(this).find('a[fold=1]');
                obj_a.html('+');
                obj_a.attr('fold',0);
            });
        }
        /*
        function findcds(id)
        {
            $('tr[pid='+id+']').each(function(){
                var ids=$(this).attr('id');
                findcds(ids);
                $(this).css('display','');
            });
        }*/