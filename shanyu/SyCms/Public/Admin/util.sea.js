define(function (require,exports,module){
    exports.checkAll = function (all,one){
        if(all == undefined) all=".CheckAll";
        if(one == undefined) one=".CheckOne";

        $(all).click(function(){
            $(one).prop('checked',this.checked);
        });
        $(one).click(function(){
            var checkbox=$(one);
            checkbox.each(function(i){
                if(!this.checked){
                    $(all).prop('checked',false);
                    return false;
                }else{
                    $(all).prop("checked", true);
                }
            });
        });
    }
});