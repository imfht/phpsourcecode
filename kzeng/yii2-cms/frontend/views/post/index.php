<div class="main">
    <div class="box">
        <!-- left -->
        <!-- left end -->
        <!-- right -->
            <div style="margin: 0 auto;">
                <div align="center" style="font-size:24px;color:#BF3904;font-weight: bold;margin-bottom:5px;">
                            <?= $post->title ?>
                </div>
                <div align="center" style="font-size:16px;">
                            <strong>发布时间：</strong><?= date('Y-m-d',$post->published_at);  ?>
                </div>
                <?= $post->content ?>
                <div style="clear:both;">

                </div>

            </div>
        <!-- right end -->
        <div class="clear"></div>
    </div>
</div>


    <script type="text/javascript">
    $(function(){
        $('table').eq(0).attr('style','margin:0 auto;');
        $('td > img').each(function(){
            $(this).hide();
        });
        $('.docTitleCls').hide();

        $('div').each(function(){
            // console.log($(this).attr('align'));
            if ($(this).attr('align') == 'left' || $(this).attr('align') == 'right') {
                $(this).attr('style','display:none;');
            }
        });
        $('td').each(function(){
            if ($(this).attr('height') == '28') {
                $(this).attr('style','display:none;');
            }
        });
    });
</script>


 <script type="text/javascript">
     $(function(){
        $('#hold_bottom').hide();
     });
 </script>
