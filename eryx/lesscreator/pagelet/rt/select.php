<style>

.kyexic .title {
    margin: 0; padding: 2px 0; font-weight: bold; font-size: 16px; line-height: 100%; color: #333333;
}

.kyexic .rtlogo {
    width: 80px; height: 40px;
}
.kyexic .rtset {
    width: 20px; height; 20px;
}
.kyexic .desc {
    margin: 0; padding: 0; color: #999999; line-height: 100%;
}
.kyexic :hover {
    background-color: #d9edf7;
}
.kyexic > table {
    width: 100%;
}
.kyexic > table td {
    padding: 5px;
}
.kyexic tr.line {
    border-top: 1px solid #ccc;
}
.kyexic .imggray {
    -webkit-filter: grayscale(1);
}
.kyexic .indev {
    color: #dc4437; font-weight: normal;
}
</style>


<a class="kyexic" href="#rt/nginx-set">
<table>
    <tr>
        <td width="100px"><img class="rtlogo" src="/lesscreator/static/img/rt/nginx_200.png" /></td>
        <td >
            <div class="title">WebServer (Nginx)</div>
            <div class="desc"><?php echo $this->T('High Performance http server')?></div>
        </td>
        <td align="right">
            <img class="rtset" src="/lesscreator/static/img/for-test/setting2-128.png" />
        </td>
    </tr>
</table>
</a>

<a class="kyexic" href="#rt/php-set">
<table>
    <tr class="line">
        <td width="100px"><img class="rtlogo" src="/lesscreator/static/img/rt/php_200.png" /></td>
        <td >
            <div class="title">PHP</div>
            <div class="desc">PHP Runtime Environment. php-fpm, php-cli, ...</div>
        </td>
        <td align="right">
            <img class="rtset" src="/lesscreator/static/img/for-test/setting2-128.png" />
        </td>
    </tr>
</table>
</a>

<a class="kyexic" href="#rt/go-set">
<table>
    <tr class="line">
        <td width="100px"><img class="rtlogo" src="/lesscreator/static/img/rt/go_200.png" /></td>
        <td >
            <div class="title">Go <span class="indev"></span></div>
            <div class="desc">Go Runtime Environment</div>
        </td>
        <td align="right">
            <img class="rtset" src="/lesscreator/static/img/for-test/setting2-128.png" />
        </td>
    </tr>
</table>
</a>

<a class="kyexic" href="#rt/nodejs-set">
<table>
    <tr class="line">
        <td width="100px"><img class="rtlogo" src="/lesscreator/static/img/rt/nodejs_200.png" /></td>
        <td >
            <div class="title">NodeJS <span class="indev"></span></div>
            <div class="desc">NodeJS Runtime Environment</div>
        </td>
        <td align="right">
            <img class="rtset" src="/lesscreator/static/img/for-test/setting2-128.png" />
        </td>
    </tr>
</table>
</a>

<a class="kyexic" href="#rt/java-set">
<table>
    <tr class="line">
        <td width="100px"><img class="rtlogo imggray" src="/lesscreator/static/img/rt/java_200.png" /></td>
        <td >
            <div class="title">Java <span class="indev">(<?php echo $this->T('Upcoming, later supported')?>)</span></div>
            <div class="desc">Java Runtime Environment</div>
        </td>
        <td align="right">
            <img class="rtset imggray" src="/lesscreator/static/img/for-test/setting2-128.png" />
        </td>
    </tr>
</table>
</a>

<a class="kyexic" href="#rt/python-set">
<table>
    <tr class="line">
        <td width="100px"><img class="rtlogo imggray" src="/lesscreator/static/img/rt/python_200.png" /></td>
        <td >
            <div class="title">Python <span class="indev">(<?php echo $this->T('Upcoming, later supported')?>)</span></div>
            <div class="desc">Python Runtime Environment</div>
        </td>
        <td align="right">
            <img class="rtset imggray" src="/lesscreator/static/img/for-test/setting2-128.png" />
        </td>
    </tr>
</table>
</a>

<script>
if (lessModalPrevId() != null) {
    lessModalButtonAdd("qc7cbv", "<?php echo $this->T('Back')?>", "lessModalPrev()", "pull-left h5c-marginl0");
}

lessModalButtonAdd("y4l8t6", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

$(".kyexic").click(function(){
        
    var uri = $(this).attr('href').substr(1);

    var title = "";
    switch (uri) {
    case "rt/nginx-set":
        title = "<?php echo $this->T('Setting')?> Nginx"
        break;
    case "rt/php-set":
        title = "<?php echo $this->T('Setting')?> PHP";
        break;
    case "rt/go-set":
        title = "<?php echo $this->T('Setting')?> Go";
        break;
    case "rt/nodejs-set":
        title = "<?php echo $this->T('Setting')?> NodeJS";
        break;
    default:
        return;
    }
    
    uri += "?proj=" + lessSession.Get("ProjPath");
    lessModalOpen("/lesscreator/"+ uri, 1, 600, 400, title, null);
});

</script>
