<?php $this->extendTpl('layout.php'); ?>

<?php $this->blockStart('sidebar'); ?>
    <!-- block-sidebar -->
    <h2 class="widget-title">接口列表</h2>
    <ol id="apilist">
        <li><a href="#gklist"
               title="<?= $url_prefix ?>/gklist/?appid=26300001&uid=13824309408&time=1443437429&sign=e8782f88bbd9034279634ab073dcc80b">GK列表</a>
        </li>
        <li><a href="#initvoip"
               title="/initvoip/?appid=26300001&uid=13824309408&imei=59374046041881&time=1443437429&sign=56f2393fcff89457ef378ac22d0c9f43">注册用户</a>
        </li>
        <li><a href="#balance"
               title="<?= $url_prefix ?>/balance/?appid=26300001&uid=13824309408&time=1443437429&sign
               =e8782f88bbd9034279634ab073dcc80b">读取余额</a>
        </li>
        <li><a href="#query"
               title="<?= $url_prefix ?>/query/?appid=26300001&uid=13824309408&start=2015-06-01&stop=2015-10-10&time=1443437429&sign=caf2ead604f245c339ddef5db67800f7">查询话单</a>
        </li>
        <li><a href="#recharge"
               title="<?= $url_prefix ?>/recharge/?appid=26300001&uid=13824309408&amount=7.3&time=1443437429&sign=0eb85f69fb45bd8bd7f2dac136aba199">充值</a>
        </li>
    </ol>
    <!-- .block-sidebar -->
<?php $this->blockEnd(); ?>

<?php $this->blockStart('content'); ?>
    <!-- block-content -->
    <h3 class="legent">请求：</h3>
    <div class="entry-content">
        <pre id="url"></pre>
    </div>
    <h3 class="legent">返回：</h3>
    <div class="entry-content">
        <pre id="result"></pre>
    </div>
    <!-- .block-content -->
<?php $this->blockEnd(); ?>