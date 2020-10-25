<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                loading: false,
                form: {
                    account: 'hjhjkhjk',
                    classification: '所属分类',
                    companyLimitData: '2019-12-31',
                    companyName: '公司名称',
                    companyPlace: '公司所在地',
                    company_detail_name: '公司详细地址',
                    company_email: '电子邮箱',
                    company_person_num: '员工总数',
                    company_phone: '公司电话',
                    contact_name: '联系人姓名',
                    contact_phone: '公司电话',
                    createTime: '2016-12-23',
                    level: '所属等级',
                    license: '营业执照号',
                    licenseArea: '法定经营范围',
                    licensePlace: '营业执照所在地',
                    limitData: '2018-02-21',
                    logo: '营业执照电子版',
                    province: '所在地区',
                    register_money: '注册资金',
                    storeAddress: '店铺地址',
                    storeName: '店铺名称',
                    switch1: '开启',
                },
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="store-look">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>店铺管理—查看</span>
            </div>
            <div class="store-information">
                <card :bordered="false">
                    <p slot="title">店铺信息</p>
                    <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                        <div class="basic-information">
                            <row>
                                <i-col span="12">
                                    <form-item label="店主账号">
                                        {{ form.account }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="店铺名称" prop="storeName">
                                        {{ form.storeName }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="公司名称">
                                        {{ form.companyName }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="所在地区">
                                        {{ form.province }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="店铺地址">
                                        {{ form.storeAddress }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="开店时间">
                                        {{ form.createTime }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="所属分类">
                                        {{ form.classification }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="所属等级">
                                        {{ form.level }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12" class="data-picker-input picker-input">
                                    <form-item label="有效期至">
                                        {{ form.limitData }}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="状态"  class="switch-status">
                                        {{ form.switch1 }}
                                    </form-item>
                                </i-col>
                            </row>
                        </div>
                    </i-form>
                </card>
                <card :bordered="false">
                    <p slot="title">注册信息</p>
                    <i-form ref="storeDetail" :model="storeDetail" :rules="rules" :label-width="200">
                        <div class="register-information">
                            <div class="register-content">
                                <div class="company-information border-color">
                                    <div>
                                        <h5>公司及联系人信息</h5>
                                        <div class="company-content">
                                            <ul>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="公司名称">
                                                                {{ storeDetail.companyName }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="公司所在地">
                                                                {{ storeDetail.companyPlace }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="公司详细地址" class="company-name">
                                                                {{ storeDetail.company_detail_name }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="10">
                                                            <form-item label="公司电话">
                                                               {{ storeDetail.company_phone }}
                                                            </form-item>
                                                        </i-col>
                                                        <i-col span="10">
                                                            <form-item label="电子邮箱">
                                                                {{ storeDetail.company_email }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="10">
                                                            <form-item label="员工总数">
                                                                {{ storeDetail.company_person_num }}
                                                            </form-item>
                                                        </i-col>
                                                        <i-col span="10">
                                                            <form-item label="注册资金">
                                                                {{ storeDetail.register_money }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="10">
                                                            <form-item label="联系人姓名">
                                                                {{ storeDetail.contact_name }}
                                                            </form-item>
                                                        </i-col>
                                                        <i-col span="10">
                                                            <form-item label="联系人电话">
                                                                {{ storeDetail.contact_phone }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="license-information border-color">
                                    <div>
                                        <h5>营业执照信息（副本）</h5>
                                        <div class="license-content">
                                            <ul>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="营业执照号">
                                                                {{ storeDetail.license }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="营业执照所在地">
                                                                {{ storeDetail.licensePlace }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="18" class="data-picker-input">
                                                            <form-item label="营业执照有效期">
                                                                {{ storeDetail.companyLimitData }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="法定经营范围">
                                                                {{ storeDetail.licenseArea }}
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="营业执照电子版" prop="logo">
                                                                <img :src="storeDetail.logo" alt="">
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </i-form>
                </card>
            </div>
        </div>
    </div>
</template>