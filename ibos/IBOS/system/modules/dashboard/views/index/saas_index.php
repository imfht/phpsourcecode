<?php

use application\core\utils\Ibos;
use application\core\utils\StringUtil;

?>
<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/home.css?<?php echo VERHASH; ?>">
<div class="ct">
    <?php if (!empty($saasDetail['upgradeTip'])) :?>
        <div class="dashboard-update-tip"><?php echo $saasDetail['upgradeTip'];?></div>
    <?php endif; ?>
    <div class="clearfix">
        <h1 class="mt"><?php echo $unit['fullname']; ?></h1>
    </div>
    <div>
        <!-- 企业基本信息 start -->
        <div class="ctb saas-ctb basic-info <?php if ($appClosed): ?>card-flip<?php endif; ?>">
            <h2 class="st">企业基本信息</h2>
            <div class="clearfix">
                <section class="logo">
                    <h4><?php echo $lang['Enterprise logo'] ?></h4>
                    <div class="content">
                        <?php if (!empty($unit['logourl'])): ?>
                          <img src="<?php echo $unit['logourl']; ?>" alt="<?php echo $lang['Enterprise logo'] ?>" class="custom-logo"
                          id="upload_img">
                        <?php else: ?>
                            <div class="no-logo">未上传<?php echo $lang['Enterprise logo'] ?></div>
                        <?php endif; ?>
                    </div>
                </section>
                <section class="fullname">
                    <h4><?php echo $lang['Enterprise fullname'] ?></h4>
                    <div class="content">
                        <?php echo $unit['fullname']; ?>
                    </div>
                </section>
                <?php if (!empty($unit['phone'])): ?>
                    <section class="contact">
                        <h4><?php echo $lang['Phone'] ?></h4>
                        <div class="content">
                            <?php echo $unit['phone']; ?>
                        </div>
                    </section>
                <?php endif;?>
                <?php if (!empty($unit['address'])): ?>
                    <section class="address">
                        <h4><?php echo $lang['Address'] ?></h4>
                        <div class="content">
                            <?php echo $unit['address']; ?>
                        </div>
                    </section>
                <?php endif;?>
            </div>
        </div>
        <!-- 账户信息 start -->
        <div class="ctb saas-ctb">
          <h2 class="st">账户信息</h2>
          <div class="clearfix">
            <ul class="card-list">
                <li class="account-edition">
                    <h3>当前版本</h3>
                    <div class="col">
                    </div>
                    <div class="col">
                        <span class="name"><?php if ($saasDetail['version'] == 0) {
                                echo '试用版';
                            } else if ($saasDetail['version'] == 1) {
                                echo '企业版';
                            } else if ($saasDetail['version'] == 9) {
                                echo '免费版';
                            } else {
                                echo '试用版';
                            }?></span>
                        <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2355767033&site=qq&menu=yes">购买</a>
                    </div>
                </li>
                <li class="account-user">
                    <h3>已用人数</h3>
                    <div class="col">
                        <?php if ($saasDetail['existUser'] <= $saasDetail['alluser']): ?>
                            <div class="col">
                            </div>
                        <?php endif;?>
                        <span class="used-num" <?php if($saasDetail['existUser'] > $saasDetail['alluser']) :?>style="color: #ff432e"<?php endif; ?>><?php echo $saasDetail['existUser']; ?></span>
                        /
                        <span class="total-num"><?php echo $saasDetail['alluser']; ?></span>
                        人
                        <?php if ($saasDetail['existUser'] <= $saasDetail['alluser']): ?>
                        <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2355767033&site=qq&menu=yes">购买</a>
                        <?php endif; ?>
                    </div>
                    <?php if ($saasDetail['existUser'] > $saasDetail['alluser']): ?>
                        <div class="col">
                            <span class="desc">
                                超出的<?php echo $saasDetail['existUser']-$saasDetail['alluser'];?>人将无法登录
                            </span>
                            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2355767033&site=qq&menu=yes">购买</a>
                        </div>
                    <?php endif; ?>
                </li>
              <li class="account-space">
                <h3>已用空间</h3>
                <div class="col">
                  <span class="used-num"><?php echo $saasDetail['usedSpace']['size']; ?></span>
                  <span class="used-unit"><?php echo $saasDetail['usedSpace']['unit']; ?></span>
                  /
                  <span class="total-num"><?php echo $saasDetail['allspace']; ?></span>
                  <span class="total-unit">GB</span>
                </div>
                <div class="col">
                  <div class="account-progress-bar">
                    <?php  ?>
                    <div class="block" style="transform: translateX(<?php echo ((string)$saasDetail['precent']).'%'// TODO: use computed value ?>);">
                    </div>
                  </div>
                  <a class="expand" href="javascript:;" data-action="spaceUpgrade">扩容</a>
                </div>
              </li>
              <li class="account-time">
                <h3>剩余时长</h3>
                <div class="col">
                  <div class="rest">
                    <?php if ($saasDetail['isBeyond']): ?>
                        超过
                    <?php else:  ?>
                       剩余
                    <?php endif; ?>
                    <span class="days"><?php echo $saasDetail['restDay']; ?></span> 天
                  </div>
                </div>
                <div class="col">
                  <span class="expireDate"><?php echo implode('-', $saasDetail['expiretime']); ?>到期</span>
                  <a class="expand" href="javascript:;" data-action="timeUpgrade">续期</a>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <!-- 联系我们 start -->
        <div class="ctb saas-ctb">
            <h2 class="st"><?php echo $lang['Contact us']; ?></h2>
            <div id="securityTips"></div>
        </div>
    </div>
</div>
<?php
    $deadTime = $saasDetail['expiretime']['year'] . '年' . $saasDetail['expiretime']['month'] . '月' . $saasDetail['expiretime']['day'] . '日';
?>
<!-- 升级 -->
<div id="user_upgrade_dialog" style="width: 640px; display: none">
    <form action="javascript:;" method="post" class="form-horizontal form-compact license-info">
        <div class="upgrade-content">
            <div class="upgrade-box">
                <p class="upgrade-box-title">新增用户数<i class="glyphicon-question-sign mlm" id="user_tip"></i></p>
                <div type="text" class="dib w420 upgrade-slider" id="user_slider"></div>
                <div class="dib vam mlh w160">
                    <div class="input-group">
                        <span class="input-group-addon upgrade-btn" data-param="down">-</span>
                        <input type="text" class="xac upgrade-val" value="1">
                        <span class="input-group-addon upgrade-btn" data-param="up">+</span>
                        <span class="mls">个</span>
                    </div>
                </div>
            </div>
            <div class="upgrade-box">
                <p class="upgrade-box-title">到期时间</p>
                <p class="upgrade-box-alert"><?php echo $deadTime; ?></p>
            </div>
        </div>
        <div class="upgrade-footer">
            <div class="dib">
                <p class="lhl">合计:&nbsp;<span class="xco fsh suki-rmb">0.00</span>&nbsp;元</p>
                <p>此数据仅供参考，消费以实际情况为准，如有疑问请联系工作人员咨询</p>
            </div>
            <div class="dib span3 xac pull-right">
                <a target="_blank" class="btn btn-primary mtm" href="http://wpa.qq.com/msgrd?v=3&uin=2355767033&site=qq&menu=yes">马上联系购买</a>
            </div>
        </div>
    </form>
</div>
<!-- 扩容 -->
<div id="space_upgrade_dialog" style="width: 640px; display: none">
    <form action="javascript:;" method="post" class="form-horizontal form-compact license-info">
        <div class="upgrade-content">
            <div class="upgrade-box">
                <p class="upgrade-box-title">新增空间<i class="glyphicon-question-sign mlm" id="space_tip"></i></p>
                <div type="text" class="dib w420 upgrade-slider" id="space_slider"></div>
                <div class="dib vam mlh w160">
                    <div class="input-group">
                        <span class="input-group-addon upgrade-btn" data-param="down">-</span>
                        <input type="text" class="xac upgrade-val" value="1">
                        <span class="input-group-addon upgrade-btn" data-param="up">+</span>
                        <span class="mls">GB</span>
                    </div>
                </div>
            </div>
            <div class="upgrade-box">
                <p class="upgrade-box-title">到期时间</p>
                <p class="upgrade-box-alert"><?php echo $deadTime; ?></p>
            </div>
        </div>
        <div class="upgrade-footer">
            <div class="dib">
                <p class="lhl">合计:&nbsp;<span class="xco fsh suki-rmb">0.00</span>&nbsp;元</p>
                <p>此数据仅供参考，消费以实际情况为准，如有疑问请联系工作人员咨询</p>
            </div>
            <div class="dib span3 xac pull-right">
                <a target="_blank" class="btn btn-primary mtm" href="http://wpa.qq.com/msgrd?v=3&uin=2355767033&site=qq&menu=yes">马上联系购买</a>
            </div>
        </div>
    </form>
</div>
<!-- 续期 -->
<div id="time_upgrade_dialog" style="width: 640px; display: none">
    <form action="javascript:;" method="post" class="form-horizontal form-compact license-info <?php echo $saasDetail['discount']['favorable'] == 0 ? 'upgrade-no-discount' : ''; ?>">
        <?php if ($saasDetail['discount']['tips']) : ?>
            <div class="card-box-tip">
                <i class="o-tip-alert"></i>
                <span class="dib"><?php echo $saasDetail['discount']['tips']; ?></span>
            </div>
        <?php endif; ?>
        <div class="upgrade-content">
            <div class="upgrade-box">
                <p class="upgrade-box-title">总用户数<i class="glyphicon-question-sign mlm" id="time_tip"></i></p>
                <div type="text" class="dib w420 upgrade-slider" id="time_slider"></div>
                <div class="dib vam mlh w160">
                    <div class="input-group">
                        <span class="input-group-addon upgrade-btn" data-param="down">-</span>
                        <input type="text" class="xac upgrade-val" value="5">
                        <span class="input-group-addon upgrade-btn" data-param="up">+</span>
                        <span class="mls">个</span>
                    </div>
                </div>
            </div>
            <div class="upgrade-box">
                <p class="upgrade-box-title">使用时长</p>
                <ul class="btn-group cycle-btn-group"></ul>
            </div>
        </div>
        <div class="upgrade-footer">
            <div class="dib">
                <p class="lhl">合计:&nbsp;<span class="xco fsh suki-rmb">0.00</span>&nbsp;元</p>
                <p>此数据仅供参考，消费以实际情况为准，如有疑问请联系工作人员咨询</p>
            </div>
            <div class="dib span3 xac pull-right">
                <a target="_blank" class="btn btn-primary mtm" href="http://wpa.qq.com/msgrd?v=3&uin=2355767033&site=qq&menu=yes">马上联系购买</a>
            </div>
        </div>
    </form>
</div>
<!--<script type="text/javascript" src="https://wpa.b.qq.com/cgi/wpa.php"></script>-->
<script>
    var _ib = _ib || [];
    _ib.push(['authkey', '<?php echo Ibos::app()->setting->get('config/security/authkey'); ?>']);
    _ib.push(['datasize', '<?php echo $dataSize . $dataUnit; ?>']);
    _ib.push(['system', '<?php echo $sys['operating_system']; ?>']);
    _ib.push(['type', 'dashboardlogin']);
    (function () {
        var ib = document.createElement('script');
        ib.type = 'text/javascript';
        ib.async = true;
        ib.src = 'https://www.ibos.com.cn/Public/static/ib.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ib, s);
    })();
</script>
<script>
    Ibos.app.s({
        "installTime": <?php echo $installDate; ?>,
        "nowTime": <?php echo TIMESTAMP; ?>,
        "assetUrl": '<?php echo $assetUrl; ?>',
        "engine": 'SAAS',
        "leftDays": '<?php echo $saasDetail["restDay"]; ?>',
        "univalenceuser": '<?php echo $saasDetail["discount"]["univalenceuser"]; ?>',
        "univalencespace": '<?php echo $saasDetail["discount"]["univalencespace"]; ?>',
        "onediscount": '<?php echo $saasDetail["discount"]["onediscount"]; ?>',
        "twodiscount": '<?php echo $saasDetail["discount"]["twodiscount"]; ?>',
        "threediscount": '<?php echo $saasDetail["discount"]["threediscount"]; ?>',
    });
</script>
<script type="text/template" id="expect_time_tmpl">
    <% for (var i = 0, len = data.length; i < len; i++) { %>
        <% var item = data[i]; %>
        <li type="button" class="posr btn" data-action="expectTime" data-param='{"text": "<%= item['text']%>", "active": "<%= item['active']%>", "discount": "<%= item['discount']%>"}'>
            <span><%= item['text'] %></span>
            <input type="radio" name="expect_time" class="dn" value="<%= item['value'] %>"/>
            <% if (item['discount'] != 1) { %>
                <p class="upgrade-discount"><%= item['discount'].slice(2) %>折</p>
            <% } %>
        </li>
    <% } %>
</script>
<script src='<?php echo STATICURL; ?>/js/lib/formValidator/formValidator.packaged.js?<?php echo VERHASH; ?>'></script>
<script src="<?php echo $assetUrl; ?>/js/db_index.js?<?php echo VERHASH; ?>"></script>
<script src="https://www.sobot.com/chat/frame/js/entrance.js?sysNum=6d4e2bfba189486d96e24f4e82458563"
        class="zhiCustomBtn" id="zhichiScript" data-args="manual=true"></script>
<script type="text/javascript">
    // 初始化客服组件
    var zhiManager = (getzhiSDKInstance());
    //再调用load方法
    zhiManager.on("load", function() {
        zhiManager.initBtnDOM();
        zhiManager.set('userinfo', {
            partnerId: G.uid,
            uname: userObj.text + '-' + G.shortname,
            tel: userObj.phone,
            realname: userObj.text,
            remark: remarkInfo
        });
        // 设置组件弹窗颜色
        zhiManager.set('color',dialogColor);
    });
    zhiManager.load();
</script>
