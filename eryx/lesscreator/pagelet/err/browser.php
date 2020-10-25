<style>
.err-brw {
    position: absolute;
    padding: 15px;
    width: 600px;
    border: 2px solid #ccc;
    background-color: #fff;
}
.err-brw td {
    padding: 10px 20px 10px 0;
}
.err-brw .imgs1 {
    width: 48px; height: 48px;
}
.err-brw .imgs0 {
    width: 24px; height: 24px; -webkit-filter: grayscale(0.3);
}
</style>
<div class="err-brw">
  <div class="">
    <div class="alert alert-error"><?php echo $this->T('browser-reject-desc')?></div>
    
    <p><?php echo $this->T('browser-reject-advice-desc')?></p>
    <table>
      <tr>
        <td><img src="/lesscreator/~/lessui/img/browser/chrome.png" class="imgs1" /></td>
        <td><strong>Google Chrome</strong></td>
        <td><a href="http://www.google.com/chrome/" target="_blank">http://www.google.com/chrome/</a></td>
      </tr>
      
    </table>
    
    <br />
    <div><?php echo $this->T('browser-dev-wait-desc')?> :-)</div>
    <table>
      <tr>
        <td><img src="/lesscreator/~/lessui/img/browser/safari.png" class="imgs0" /></td>
        <td>
            Apple Safari
            <!--<a href="http://www.apple.com/safari/" target="_blank">http://www.apple.com/safari/</a>-->
        </td>
        <td></td>
        <td><img src="/lesscreator/~/lessui/img/browser/firefox.png" class="imgs0" /></td>
        <td>
            Mozilla Firefox
            <!--<a href="http://www.mozilla.org/" target="_blank">http://www.mozilla.org/</a>-->
        </td>
      </tr>
    </table>
  </div>
</div>

<script>

$(window).resize(function() {
    _lc_err_brw_resize();
});

function _lc_err_brw_resize()
{
    var bh = $('body').height();
    var bw = $('body').width();

    if (bh < 300) {
        bh = 300;
    }
    if (bw < 600) {
        bw = 600;
    }

    var eh = $('.err-brw').height();
    var ew = $('.err-brw').width();

    $('.err-brw').css({
        "top" : ((bh - eh) / 3) + "px",
        "left": ((bw - ew) / 2) + "px"
    });
}

_lc_err_brw_resize();
</script>
