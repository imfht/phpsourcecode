<script>
    export default{
        computed: {
            phoneNumber() {
                const str = '****';
                const phoneNum = this.userInfo.phone.substring(0, 3) + str +
                    this.userInfo.phone.substring(7, 11);
                return phoneNum;
            },
            privateMailbox() {
                const str = '****';
                const email = this.userInfo.email.substring(0, 3) + str +
                    this.userInfo.email.substring(10);
                return email;
            },
        },
        data() {
            return {
                userInfo: {
                    email: 'zhhu_123@163.com',
                    phone: '13429763663',
                },
            };
        },
    };
</script>
<template>
    <div class="account-security">
        <div class="address-title">
            <h4>账户安全</h4>
        </div>
        <div class="account-content account-list-model">
            <ul class="account-model">
                <li class="account-item clearfix">
                    <span class="span-icon"><i class="icon iconfont icon-icon-test"></i></span>
                    <span class="span-password">登录密码</span>
                    <span class="span-tip">为了您的账户安全，请您定期变更登录密码</span>
                    <span class="span-edit"><router-link to="/mall/user/password/edit">编辑</router-link></span>
                </li>
                <li class="account-item clearfix">
                    <span class="span-icon"><i class="icon iconfont icon-icon-test"></i></span>
                    <span class="span-password">邮箱验证</span>
                    <span class="span-tip">您的验证邮箱为：{{ privateMailbox }}</span>
                    <span class="span-edit"><router-link to="/mall/user/email/edit">编辑</router-link></span>
                </li>
                <li class="account-item clearfix">
                    <span class="span-icon"><i class="icon iconfont icon-tixing"></i></span>
                    <span class="span-password">手机验证</span>
                    <span class="span-tip">您验证的手机：{{ phoneNumber }} 若已丢失或停用，请立即更换</span>
                    <span class="span-edit"><router-link to="/mall/user/phone/edit">编辑</router-link></span>
                </li>
            </ul>
            <div class="security-tip">
                <p>安全服务提示</p>
                <p>1.确认您登录的是母婴商城网址http:/www.xbmy.com，注意防范进入钓鱼网站，不要轻信各种即时通讯工具发送的商品或支付链接，谨防网购诈骗。</p>
                <p>2.建议您安装杀毒软件，并定期更新操作系统等软件补丁，确保账户及交易安全。</p>
            </div>
        </div>
    </div>
</template>