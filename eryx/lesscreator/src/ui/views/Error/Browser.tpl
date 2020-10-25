<style>
body {
    width: 100%;
    background-color: #eee;
    font-family: Helvetica, Arial, "Liberation Sans", sans-serif;
    font-size: 14px;
    line-height: 140%;
}
.err-alert-danger {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}
.err-brw {
    padding: 20px;
    width: 600px;
    margin: 40px auto;
    border: 1px solid #ccc;
    background-color: #fff;
    border-radius: 4px;
}
.err-brw td {
    padding: 10px 20px 10px 0;
}
.err-brw .imgs1 {
    width: 48px; height: 48px; 
}
.err-brw .imgs0 {
    width: 24px; height: 24px; -webkit-filter: grayscale(0.5);
}
.err-footer {
    text-align: center;
    margin: 0 auto;
}
</style>

<div class="err-brw">
  <div class="">
    <div class="err-alert-danger">{{T . "browser-reject-desc"}}</div>
    
    <p>{{T . "browser-reject-advice-desc"}}</p>
    <table>
      <tr>
        <td><img src="/lesscreator/~/lessui/img/browser/chrome.png" class="imgs1" /></td>
        <td><strong>Google Chrome</strong></td>
        <td><a href="http://www.google.com/chrome/" target="_blank">http://www.google.com/chrome/</a></td>
      </tr>
    </table>

    <br />
    <div>{{T . "browser-dev-wait-desc"}} :-)</div>
    <table>
      <tr>
        <td><img src="/lesscreator/~/lessui/img/browser/safari.png" class="imgs0" /></td>
        <td>
            Apple Safari
            <!-- <a href="http://www.apple.com/safari/" target="_blank">http://www.apple.com/safari/</a> -->
        </td>
        <td></td>
        <td><img src="/lesscreator/~/lessui/img/browser/firefox.png" class="imgs0" /></td>
        <td>
            Mozilla Firefox
            <!-- <a href="http://www.mozilla.org/" target="_blank">http://www.mozilla.org/</a> -->
        </td>
      </tr>
    </table>
    
  </div>

</div>

<div class="err-footer">
    &copy; 2014 <a href="http://lessos.com" target="_blank">lessOS.com</a>
</div>
