<div class="jumbotron">
    <h1>{{title}}!</h1>
    <p>{{detail}}</p>
    <p><span id="second">5</span>秒后自动跳转&nbsp;&nbsp;<a  href="{{jmp_url}}" role="button">直接跳转</a> <p>
    <input type="hidden" id="jump_url" value="{{jmp_url}}">
    <p><a  class="btn btn-primary btn-lg" href="index.php" role="button">回到首页</a></p>
</div>

<script>
    (function(){
        var jm_url=$('#jump_url').val();
        var count_obj=$('#second');
        function count_down(count){
            if(count<1){
                location.href=jm_url;
            }
            else{
                count_obj.text(count);
                setTimeout(function(){
                    count_down(--count);
                },1000);
            }
        }
        count_down({{count_down}});

    })();

</script>