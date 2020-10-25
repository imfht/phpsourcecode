<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                classification: [
                    {
                        label: '分类1',
                        value: '1',
                    },
                    {
                        label: '分类2',
                        value: '2',
                    },
                ],
                companyPlace: [
                    {
                        label: '上海市',
                        value: '1',
                    },
                    {
                        label: '北京市',
                        value: '2',
                    },
                ],
                defaultList: [],
                imgName: '',
                level: [
                    {
                        label: '等级1',
                        value: '1',
                    },
                    {
                        label: '等级2',
                        value: '2',
                    },
                ],
                licensePlace: [
                    {
                        label: '上海市',
                        value: '1',
                    },
                    {
                        label: '北京市',
                        value: '2',
                    },
                ],
                loading: false,
                options2: {
                    disabledDate(date) {
                        return date && date.valueOf() < Date.now();
                    },
                },
                province: [
                    {
                        label: '上海市',
                        value: '1',
                    },
                    {
                        label: '北京市',
                        value: '2',
                    },
                ],
                rules: {
                    storeName: [
                        {
                            message: '名称不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                },
                form: {
                    account: 'hjhjkhjk',
                    classification: '',
                    companyName: '',
                    companyPlace: '',
                    company_detail_name: '',
                    company_email: '',
                    company_person_num: '',
                    company_phone: '',
                    contact_name: '',
                    contact_phone: '',
                    createTime: '2016-12-23',
                    level: '',
                    licensePlace: '',
                    logo: '',
                    province: '',
                    register_money: '',
                    storeAddress: '',
                    storeName: '',
                    switch1: true,
                },
                uploadList: [],
                visible: false,
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            removeLogo() {
                this.form.logo = '';
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        window.console.log(valid);
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
            uploadBefore() {
                this.$loading.start();
            },
            uploadError(error, data) {
                const self = this;
                self.$loading.error();
                if (typeof data.message === 'object') {
                    for (const p in data.message) {
                        self.$notice.error({
                            title: data.message[p],
                        });
                    }
                } else {
                    self.$notice.error({
                        title: data.message,
                    });
                }
            },
            uploadFormatError(file) {
                this.$notice.warning({
                    title: '文件格式不正确',
                    desc: `文件 ${file.name} 格式不正确`,
                });
            },
            uploadSuccess(data) {
                const self = this;
                self.$loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.form.logo = data.data.path;
            },
        },
    };
</script>
<template>
	<div class="mall-wrap">
		<div class="store-edit">
			<div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
				<span>店铺管理—店铺编辑</span>
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
                                        <i-input v-model="form.storeName"></i-input>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="公司名称">
                                        <i-input v-model="form.companyName"></i-input>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="所在地区">
                                        <i-select placeholder="请选择" v-model="form.province">
                                            <i-option v-for="item in province" :value="item.value"
                                                      :key="item">{{ item.label }}</i-option>
                                        </i-select>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="店铺地址">
                                        <i-input v-model="form.storeAddress"></i-input>
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
                                        <i-select v-model="form.classification" placeholder="请选择">
                                            <i-option v-for="item in classification" :value="item.value"
                                                      :key="item">{{ item.label }}</i-option>
                                        </i-select>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="所属等级">
                                        <i-select v-model="form.level" placeholder="请选择">
                                            <i-option v-for="item in level" :value="item.value"
                                                      :key="item">{{ item.label }}</i-option>
                                        </i-select>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12" class="data-picker-input picker-input">
                                    <form-item label="有效期至">
                                        <date-picker type="date" :options="options2"
                                                     placeholder="选择日期"></date-picker>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="状态"  class="switch-status">
                                        <i-switch size="large" v-model="form.switch1">
                                            <span slot="open">开启</span>
                                            <span slot="close">关闭</span>
                                        </i-switch>
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
                                                                <i-input v-model="storeDetail.companyName"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="公司所在地">
                                                                <i-select placeholder="请选择"
                                                                          v-model="storeDetail.companyPlace">
                                                                    <i-option :key="item"
                                                                              :value="item.value"
                                                                              v-for="item in companyPlace">
                                                                        {{ item.label }}
                                                                    </i-option>
                                                                </i-select>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="公司详细地址" class="company-name">
                                                                <i-input v-model="storeDetail.company_detail_name"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="10">
                                                            <form-item label="公司电话">
                                                                <i-input v-model="storeDetail.company_phone"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                        <i-col span="10">
                                                            <form-item label="电子邮箱">
                                                                <i-input v-model="storeDetail.company_email"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="10">
                                                            <form-item label="员工总数">
                                                                <i-input v-model="storeDetail.company_person_num"
                                                                         class="input-param"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                        <i-col span="10">
                                                            <form-item label="注册资金">
                                                                <i-input v-model="storeDetail.register_money"
                                                                         class="input-param"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="10">
                                                            <form-item label="联系人姓名">
                                                                <i-input v-model="storeDetail.contact_name"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                        <i-col span="10">
                                                            <form-item label="联系人电话">
                                                                <i-input v-model="storeDetail.contact_phone"></i-input>
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
                                                                <i-input v-model="storeDetail.license"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="营业执照所在地">
                                                                <i-select placeholder="请选择"
                                                                          v-model="storeDetail.licensePlace">
                                                                    <i-option :value="item.value"
                                                                              :key="item"
                                                                              v-for="item in licensePlace">
                                                                        {{ item.label }}
                                                                    </i-option>
                                                                </i-select>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="18" class="data-picker-input">
                                                            <form-item label="营业执照有效期">
                                                                <row>
                                                                    <i-col span="8">
                                                                        <date-picker type="date" :options="options1"
                                                                                     placeholder="选择日期"></date-picker>
                                                                    </i-col>
                                                                    <i-col span="2" style="text-align: center">-</i-col>
                                                                    <i-col span="8">
                                                                        <date-picker type="date" :options="options2"
                                                                                     placeholder="选择日期"></date-picker>
                                                                    </i-col>
                                                                </row>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="法定经营范围">
                                                                <i-input v-model="storeDetail.license"></i-input>
                                                            </form-item>
                                                        </i-col>
                                                    </row>
                                                </li>
                                                <li>
                                                    <row>
                                                        <i-col span="12">
                                                            <form-item label="营业执照电子版" prop="logo">
                                                                <div class="image-preview" v-if="storeDetail.logo">
                                                                    <img :src="storeDetail.logo">
                                                                    <icon type="close" @click.native="removeLogo"></icon>
                                                                </div>
                                                                <upload :action="action"
                                                                        :before-upload="uploadBefore"
                                                                        :format="['jpg','jpeg','png']"
                                                                        :headers="{
                                                                            Authorization: `Bearer ${$store.state.token.access_token}`
                                                                        }"
                                                                        :max-size="2048"
                                                                        :on-error="uploadError"
                                                                        :on-format-error="uploadFormatError"
                                                                        :on-success="uploadSuccess"
                                                                        ref="upload"
                                                                        :show-upload-list="false"
                                                                        v-if="storeDetail.logo === '' || storeDetail.logo === null">
                                                                </upload>
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
                        <div class="submit-btn">
                            <i-button :loading="loading" type="primary" @click.native="submit">
                                <span v-if="!loading">确认提交</span>
                                <span v-else>正在提交…</span>
                            </i-button>
                        </div>
                    </i-form>
                </card>
			</div>
		</div>
	</div>
</template>