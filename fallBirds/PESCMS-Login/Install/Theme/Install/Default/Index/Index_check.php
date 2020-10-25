<div class="am-form-group">
    <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">PHP版本:</label>
    <div class="am-u-sm-10">
        <button type="button" id="version" class="am-btn am-btn-<?= $version == true ? 'success' : 'danger' ?>"><?= $pdo == true ? phpversion() : '请升级PHP版本' ?></button>
        <?php if ($version == false): ?>
            <a href="http://php.net/downloads.php" class="install_tips" target="_blank"><i class="am-icon-frown-o"></i> PESCMS Login必须运行于PHP5.4或更高级的版本，请升级版本</a>
        <?php endif; ?>
    </div>
</div>

<div class="am-form-group">
    <label for="doc-ipt-3" class="am-u-sm-2 am-form-label">PDO 扩展:</label>
    <div class="am-u-sm-10">
        <button type="button" id="pdo" class="am-btn am-btn-<?= $pdo == true ? 'success' : 'danger' ?>"><?= $pdo == true ? '支持' : '不支持' ?></button>
        <?php if ($pdo == false): ?>
            <a href="http://php.net/manual/zh/book.pdo.php" class="install_tips" target="_blank"><i class="am-icon-frown-o"></i> PESCMS Login依靠pdo_mysql进行链接数据库，否则程序无法安装!</a>
        <?php endif; ?>
    </div>
</div>