<?php
echo '<p><strong>' . __( 'backupwordpress存储备份文件在哪里？', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( '备份存储在您的服务器 <code>/wp-content/backups</code>, 您可以更改目录。', 'backupwordpress' ). '</p>' .

     '<p>' . __( '注意：默认情况下backupwordpress备份在你的站点根目录的一切，以及你的数据库，包括任何非WordPress文件夹，这意味着你的备份目录可能较大。', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( '如果我想要回我的网站的另一个目标吗？', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( 'BackUpWordPress 支持备份到 Dropbox, Google Drive, Amazon S3, Rackspace, Azure, DreamObjects and FTP/SFTP.看看这里: <a href="http://bwp.hmn.md/?utm_source=wordpress-org&utm_medium=plugin-page&utm_campaign=freeplugin" title="BackUpWordPress Homepage" target="_blank">https://bwp.hmn.md</a>', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( '我如何从备份恢复我的网站？', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( '你需要下载最新的备份文件，可以通过点击备份页或通过FTP下载<密码> < /代码>。<密码> < /解压缩代码>文件并上传所有文件到你的服务器重写你的网站。然后你可以使用你的主机数据库管理工具导入数据库（可能是<代码> phpMyAdmin < /代码>）。', 'backupwordpress' ) . '</p>' .

     '<p>' . __( '看到这个指南详情 - <a href="https://bwp.hmn.md/support-center/restore-backup/" title="Go to support center" target="_blank">How to restore from backup</a>.', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( 'Does BackUpWordPress back up the backups directory?', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( 'No.', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( '我没有通过电子邮件接收我的备份？', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( 'ON划词翻译ON实时翻译

大多数的服务器上的电子邮件附件的文件大小限制，它的一般约10MB。如果备份文件在限制它不会被附加到电子邮件，相反，你应该收到一封电子邮件的链接下载备份，如果你不能接受，那么你可能是在您的服务器上的邮件的问题，您需要联系您的主机商。', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( '默认的存储备份是多少？', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( 'backupwordpress储存最后10个默认备份。', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( '备份需要花费多长时间？', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( '除非你的网站是非常大的（多字节）这只需要几分钟来执行备份，如果你的备份已经运行超过一个小时，这是安全的假设，出了问题，尝试去激活和重新激活插件，如果它继续发生，与支持。', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( '如果我得到WP cron错误信息，我该怎么办？', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( 'The issue is that your <code>wp-cron.php</code> is not returning a <code>200</code> response when hit with a HTTP request originating from your own server, it could be several things, in most cases, it\'s an issue with the server / site.', 'backupwordpress' ) . '</p>' .

     '<p>' . __( '有些事情你可以测试来确认这是问题。', 'backupwordpress' ) . '</p>' .

     '<ul><li>' . __( 'Are scheduled posts working? (They use wp-cron as well ). ', 'backupwordpress' ) . '</li>' .

     '<li>' . __( 'Are you hosted on Heart Internet? (wp-cron may not be supported by Heart Internet, see below for work-around).', 'backupwordpress' ) . '</li>' .

     '<li>' . __( '如果您单击“手动备份工作的呢？', 'backupwordpress' ) . '</li>' .

     '<li>' . __( 'Try adding <code>define( \'ALTERNATE_WP_CRON\', true );</code> to your <code>wp-config.php</code>, do automatic backups work?', 'backupwordpress' ) . '</li>' .

     '<li>' . __( 'Is your site private (I.E. is it behind some kind of authentication, maintenance plugin, .htaccess) if so wp-cron won\'t work until you remove it, if you are and you temporarily remove the authentication, do backups start working?', 'backupwordpress' ) . '</li></ul>' .

     '<p>' . __( '报告的结果对我们的支持团队的进一步的帮助。要做到这一点，或者使你的管理员控制台支持（推荐），或电子邮件support@hmn.md', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( 'How to get BackUpWordPress working in Heart Internet', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( 'The script to be entered into the Heart Internet cPanel is: <code>/usr/bin/php5 /home/sites/yourdomain.com/public_html/wp-cron.php</code> (note the space between php5 and the location of the file). The file <code>wp-cron.php</code> <code>chmod</code> must be set to <code>711</code>.', 'backupwordpress' ) . '</p>' .

     '<p><strong>' . __( 'My backups seem to be failing?', 'backupwordpress' ) . '</strong></p>' .

     '<p>' . __( '如果您的备份失败-这是常见的可用资源的缺乏导致在您的服务器。建立这个排除一些[ ]或整个uploades文件夹的最简单的方法，来运行一个备份一个如果成功。如果是这样，我们知道这可能是一个服务器的问题。如果不是，报告结果给我们的支持团队的更多帮助。要做到这一点，或者使你的管理员控制台支持（推荐），或电子邮件support@hmn.md', 'backupwordpress' ) . '</p>';
