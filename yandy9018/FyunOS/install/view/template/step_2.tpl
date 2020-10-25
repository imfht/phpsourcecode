<?php echo $header; ?>
<h1 style="background: url('view/image/installation.png') no-repeat;">第二步 - 安装环境检测</h1>
<div style="width: 100%; display: inline-block;">
  <div style="float: left; width: 569px;">
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <p>1. 请修改你的php.ini配置以适应以下安装需求.</p>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 15px;">
        <table width="100%">
          <tr>
            <th width="35%" align="left"><b>PHP环境参数</b></th>
            <th width="25%" align="left"><b>当前设置</b></th>
            <th width="25%" align="left"><b>必须设置</b></th>
            <th width="15%" align="center"><b>状态</b></th>
          </tr>
          <tr>
            <td>PHP Version:</td>
            <td><?php echo phpversion(); ?></td>
            <td>5.0+</td>
            <td align="center"><?php echo (phpversion() >= '5.0') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
          <tr>
            <td>Register Globals:</td>
            <td><?php echo (ini_get('register_globals')) ? 'On' : 'Off'; ?></td>
            <td>Off</td>
            <td align="center"><?php echo (!ini_get('register_globals')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
          <tr>
            <td>Magic Quotes GPC:</td>
            <td><?php echo (ini_get('magic_quotes_gpc')) ? 'On' : 'Off'; ?></td>
            <td>Off</td>
            <td align="center"><?php echo (!ini_get('magic_quotes_gpc')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
          <tr>
            <td>File Uploads:</td>
            <td><?php echo (ini_get('file_uploads')) ? 'On' : 'Off'; ?></td>
            <td>On</td>
            <td align="center"><?php echo (ini_get('file_uploads')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
          <tr>
            <td>Session Auto Start:</td>
            <td><?php echo (ini_get('session_auto_start')) ? 'On' : 'Off'; ?></td>
            <td>Off</td>
            <td align="center"><?php echo (!ini_get('session_auto_start')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
        </table>
      </div>
      <p>2. 请确认安装环境是否包含以下扩展.</p>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 15px;">
        <table width="100%">
          <tr>
            <th width="35%" align="left"><b>扩展</b></th>
             <th width="25%" align="left"><b>当前设置</b></th>
            <th width="25%" align="left"><b>必须设置</b></th>
            <th width="15%" align="center"><b>状态</b></th>
          </tr>
          <tr>
            <td>MySQL:</td>
            <td><?php echo extension_loaded('mysql') ? 'On' : 'Off'; ?></td>
            <td>On</td>
            <td align="center"><?php echo extension_loaded('mysql') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
          <tr>
            <td>GD:</td>
            <td><?php echo extension_loaded('gd') ? 'On' : 'Off'; ?></td>
            <td>On</td>
            <td align="center"><?php echo extension_loaded('gd') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
          <tr>
            <td>cURL:</td>
            <td><?php echo extension_loaded('curl') ? 'On' : 'Off'; ?></td>
            <td>On</td>
            <td align="center"><?php echo extension_loaded('curl') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
          <tr>
            <td>ZIP:</td>
            <td><?php echo extension_loaded('zlib') ? 'On' : 'Off'; ?></td>
            <td>On</td>
            <td align="center"><?php echo extension_loaded('zlib') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
          </tr>
        </table>
      </div>
      <p>3. 请确保以下文件拥有读写权限.</p>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 15px;">
        <table width="100%">
          <tr>
            <th align="left"><b>文件</b></th>
            <th width="15%" align="left"><b>状态</b></th>
          </tr>
          <tr>
            <td><?php echo $config_catalog; ?></td>
            <td><?php echo is_writable($config_catalog) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>
          <tr>
            <td><?php echo $config_admin; ?></td>
            <td><?php echo is_writable($config_admin) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>
        </table>
      </div>
      <p>4. 请确保以下文件目录配置了合理的读写权限.</p>
      <div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 15px;">
        <table width="100%">
          <tr>
            <th align="left"><b>目录</b></th>
            <th width="15%" align="left"><b>状态</b></th>
          </tr>
          <tr>
            <td><?php echo $cache . '/'; ?></td>
            <td><?php echo is_writable($cache) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>
          <tr>
            <td><?php echo $logs . '/'; ?></td>
            <td><?php echo is_writable($logs) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>
          <tr>
            <td><?php echo $image . '/'; ?></td>
            <td><?php echo is_writable($image) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>
          <tr>
            <td><?php echo $image_cache . '/'; ?></td>
            <td><?php echo is_writable($image_cache) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>
          <tr>
            <td><?php echo $image_data . '/'; ?></td>
            <td><?php echo is_writable($image_data) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>          
          <tr>
            <td><?php echo $download . '/'; ?></td>
            <td><?php echo is_writable($download) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>
        </table>
      </div>
      <div style="text-align: right;"><a onclick="document.getElementById('form').submit()" class="button"><span class="button_left button_continue"></span><span class="button_middle">继续</span><span class="button_right"></span></a></div>
    </form>
  </div>
  <div style="float: right; width: 205px; height: 400px; padding: 10px; color: #663300; border: 1px solid #FFE0CC; background: #FFF5CC;">
     <ul>
      <li>开源协议</li>
      <li><b>安装环境检测</b></li>
      <li>配置</li>
      <li>完成</li>
    </ul>
  </div>
</div>
<?php echo $footer; ?>