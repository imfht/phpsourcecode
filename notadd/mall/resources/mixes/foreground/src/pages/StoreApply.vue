<script>
    import Datepicker from 'vuejs-datepicker';
    import cities from '../data/cities';

    export default {
        components: {
            Datepicker,
        },
        data() {
            const self = this;
            const reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
            const validatorPhone = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('手机号不能为空'));
                } else if (!reg.test(value)) {
                    callback(new Error('请输入正确手机号'));
                } else {
                    callback();
                }
            };
            return {
                agree: false,
                temps: ['入驻须知', '公司信息', '店铺信息', '入驻审核'],
                temp: 1,
                categories: [],
                data: cities,
                form: {
                    business_scope: '',
                    company_address: '',
                    company_capital: '',
                    company_employees: '',
                    company_locations: [],
                    company_name: '',
                    company_telephone: '',
                    contact_email: '',
                    contact_name: '',
                    contact_telephone: '',
                    license_addresses: [],
                    license_deadline: '',
                    license_begins: '',
                    license_images: [],
                    license_number: '',
                },
                formRule: {
                    business_scope: [
                        {
                            required: true,
                            message: '经营范围不能为空',
                            trigger: 'blur',
                        },
                    ],
                    company_address: [
                        {
                            required: true,
                            message: '公司地址不能为空',
                            type: 'string',
                            trigger: 'blur',
                        },
                    ],
                    company_capital: [
                        {
                            required: true,
                            message: '注册资金不能为空',
                            trigger: 'blur',
                        },
                        {
                            type: 'number',
                            message: '注册资金为数字',
                            trigger: 'blur',
                        },
                    ],
                    company_employees: [
                        {
                            required: true,
                            message: '员工数不能为空',
                            trigger: 'blur',
                        },
                        {
                            type: 'number',
                            message: '员工总数为数字',
                            trigger: 'blur',
                        },
                    ],
                    company_locations: [
                        {
                            required: true,
                            message: '请选择公司所在地',
                            type: 'array',
                            trigger: 'change',
                        },
                    ],
                    company_name: [
                        {
                            required: true,
                            message: '公司名不能为空',
                            trigger: 'blur',
                        },
                    ],
                    company_telephone: [
                        {
                            message: '公司电话为数字',
                            type: 'number',
                            trigger: 'blur',
                        },
                    ],
                    contact_email: [
                        {
                            required: true,
                            message: '邮箱不能为空',
                            trigger: 'blur',
                        },
                        {
                            type: 'email',
                            message: '邮箱格式不正确',
                            trigger: 'blur',
                        },
                    ],
                    contact_name: [
                        {
                            required: true,
                            message: '联系人不能为空',
                            trigger: 'blur',
                        },
                    ],
                    contact_telephone: [
                        {
                            required: true,
                            trigger: 'blur',
                            type: 'number',
                            validator: validatorPhone,
                        },
                    ],
                    license_addresses: [
                        {
                            required: true,
                            message: '请选择营业执照所在地',
                            type: 'array',
                            trigger: 'change',
                        },
                    ],
                    license_deadline: [
                        {
                            required: true,
                            type: 'date',
                            message: '请选择营业执照结束日期',
                            trigger: 'change',
                        },
                    ],
                    license_begins: [
                        {
                            required: true,
                            type: 'date',
                            message: '请选择营业执照开始日期',
                            trigger: 'change',
                        },
                    ],
                    license_images: [
                        {
                            required: true,
                            type: 'array',
                            min: 2,
                            message: '请上传营业执照电子版正反面两张',
                            trigger: 'change',
                        },
                    ],
                    license_number: [
                        {
                            required: true,
                            message: '营业执照号不能为空',
                            trigger: 'blur',
                        },
                    ],
                },
                longTime: false,
                options1: {
                    disabledDate(date) {
                        return date && date.valueOf() > Date.now();
                    },
                },
                options2: {
                    disabledDate(date) {
                        return date && date.valueOf() < self.getTimeBegin();
                    },
                },
                shopInfo: {
                    category: [],
                    store_account: '',
                    store_name: '',
                    type: '',
                },
                shopInfoRule: {
                    category: [
                        {
                            required: true,
                            message: '请选择主营类目',
                            type: 'array',
                            trigger: 'change',
                        },
                    ],
                    store_account: [
                        {
                            required: true,
                            message: '店铺账号不能为空',
                            trigger: 'blur',
                        },
                    ],
                    store_name: [
                        {
                            required: true,
                            message: '店铺名称不能为空',
                            trigger: 'blur',
                        },
                    ],
                    type: {
                        required: true,
                        message: '请选择城市',
                        trigger: 'change',
                    },
                },
                types: [
                    {
                        name: '母婴',
                    },
                ],
            };
        },
        methods: {
            getTimeBegin() {
                return Date.parse(this.form.license_begins);
            },
            handleSuccess() {},
            next() {
                const self = this;
                if (self.temp < 4) {
                    self.temp += 1;
                }
                if (self.temp === 4) {
//                    const form = self.form;
//                    form.category_id
//                        = form.category.length ? form.category[form.category.length - 1] : 0;
//                    form.license_address = form.license_addresses.join('/');
//                    form.company_location = form.company_locations.join('/');
//                    self.$http.post(`${window.api}/mall/store/apply`, form).then(() => {})
//                    .catch(() => {
//                        window.console.log('提交申请失败！');
//                    });
                }
            },
            prev() {
                this.temp -= 1;
            },
            imageSelected(e, arr) {
                const file = e.target.files[0];
                const image = {
                    content: '',
                    file1: file,
                };
                const reader = new global.FileReader();
                reader.onload = () => {
                    image.content = reader.result;
                };
                reader.readAsDataURL(file);
                arr.push(image);
            },
            deleteImg(arr, item) {
                arr.splice(arr.indexOf(item));
            },
        },
        mounted() {
            const self = this;
            self.$http.all([
                self.$http.post(`${window.api}/mall/store/product/category/list`),
                self.$http.post(`${window.api}/mall/store/type`),
            ]).then(self.$http.spread((category, type) => {
                self.categories = category.data.data.map(item => {
                    item.label = item.name;
                    item.value = item.id;
                    const children = item.children;
                    item.children = Object.keys(children).map(i => {
                        const sub = children[i];
                        sub.label = sub.name;
                        sub.value = sub.id;
                        return sub;
                    });
                    return item;
                });
                self.types = type.data.data;
            })).catch(() => {
                window.console.log('执行错误！');
            });
        },
    };
</script>
<template>
    <div class="businessmen-box">
        <div class="shop_banner" v-if="temp==1">
            <div class="shop_icon1 shop_icon" @click="next">
                <div class="shop_icon2 shop_icon">
                    <div class="shop_icon3 shop_icon">我要开店</div>
                </div>
            </div>
        </div>
        <div class="container businessmen" v-if="temp != 1">
            <div class="step-box" v-if="temp >= 3">
                <ul class="clearfix row">
                    <li class="clearfix pull-left col-md-3.5" v-for="(item, index) in temps"
                        :class="{ active: temp >= index+2 }">
                        <ul class="clearfix cricle-box pull-left" v-if="index!=0">
                            <li class="cricle pull-left" v-for="i in 17"></li>
                        </ul>
                        <div class="step pull-left">
                            <span class="step-list">{{ index + 1 }}</span>
                            <p class="modify-margin">{{ item }}</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="content_box">
                <div class="progress_02" v-if="temp===2">
                    <h4 class="text-center">入驻协议</h4>
                    <div>
                        <h5>商家入驻要求</h5>
                        <ol>
                            <li>1.请确保您的企业营业执照、组织机构代码证、银行开户许可证、税务登记证、一般纳税人资格证均有效；</li>
                            <li>2.请确保您所拥有的品牌有效或已获得相关授权；</li>
                            <li>3.请确保您所售商品已取得国家规定的相关行业资质。</li>
                        </ol>
                    </div>
                    <label class="agree">
                        <input type="checkbox" v-model="agree">
                        <span></span>
                        我已阅读并同意以上协议
                    </label>
                    <div class="col-md-offset-5 col-md-1 col-sm-1">
                        <button class="btn btn-default next-btn btn-info" :disabled="!agree" @click="next">确认入驻店铺</button>
                    </div>
                </div>
                <div class="progress_03" v-if="temp===3">
                    <div>
                        <ol>
                            <li>1.公司类信息需填项较多，建议先查看公司信息注意事项再进行填写；</li>
                            <li>2.公司信息请严格按照相关证件信息进行确认填写；</li>
                            <li>3.以下所需要上传电子版资质仅支持JPG、GIF、PNG格式的图片，大小不超过1M，且必须加盖企业彩色公章。</li>
                        </ol>
                    </div>
                    <i-form class="form-horizontal" ref="applyForm" :model="form" :rules="formRule">
                        <h4>公司及联系人信息</h4>
                        <form-item class="form-group clearfix" prop="company_name" label="公司名称">
                            <i-input v-model="form.company_name"></i-input>
                        </form-item>
                        <form-item class="form-group clearfix" prop="company_locations" label="公司所在地">
                            <cascader :data="data" v-model="form.company_locations"></cascader>
                        </form-item>
                        <form-item class="form-group clearfix" prop="company_address" label="公司详细地址">
                            <i-input v-model="form.company_address"
                                     type="textarea"
                                     :autosize="{minRows: 3,maxRows: 5}">
                            </i-input>
                        </form-item>
                        <form-item class="form-group clearfix" prop="company_telephone" label="公司电话">
                            <i-input v-model="form.company_telephone"></i-input>
                        </form-item>
                        <form-item class="form-group clearfix" prop="company_employees" label="员工总数">
                            <i-input v-model="form.company_employees"></i-input>人
                        </form-item>
                        <form-item class="form-group clearfix" prop="company_capital" label="注册资金">
                            <i-input v-model="form.company_capital"></i-input><span class="after-text">万元</span>
                        </form-item>
                        <form-item class="form-group clearfix" prop="contact_name" label="联系人姓名">
                            <i-input v-model="form.contact_name"></i-input>
                        </form-item>
                        <form-item class="form-group clearfix" prop="contact_telephone" label="联系人电话">
                            <i-input v-model="form.contact_telephone"></i-input>
                        </form-item>
                        <form-item class="form-group clearfix" prop="contact_email" label="电子邮箱">
                            <i-input v-model="form.contact_email"></i-input>
                        </form-item>
                        <h4>营业执照信息</h4>
                        <form-item class="form-group clearfix" prop="license_number" label="营业执照号">
                            <i-input v-model="form.license_number"></i-input>
                        </form-item>
                        <form-item class="form-group clearfix" prop="license_addresses" label="营业执照所在地">
                            <cascader :data="data" v-model="form.license_addresses"></cascader>
                        </form-item>
                        <form-item class="form-group clearfix date_div" label="营业期限">
                            <form-item prop="license_begins">
                                <date-picker :disabled="longTime"
                                             :options="options1"
                                             placeholder="选择日期"
                                             type="date"
                                             v-model="form.license_begins">
                                </date-picker>
                            </form-item>
                            <span class="pull-left connect"></span>
                            <form-item prop="license_deadline">
                                <date-picker :disabled="longTime"
                                             :options="options2"
                                              placeholder="选择日期"
                                              type="date"
                                              v-model="form.license_deadline">
                                </date-picker>
                            </form-item>
                            <form-item>
                                <label class="ivu-checkbox-wrapper ivu-checkbox-group-item default-edit">
                                    <span class="ivu-checkbox">
                                        <input
                                            type="checkbox"
                                            class="ivu-checkbox-input"
                                            v-model="longTime"
                                        >
                                        <span class="ivu-checkbox-inner"></span>
                                    </span>
                                    <span>长期</span>
                                </label>
                            </form-item>
                        </form-item>
                        <form-item class="form-group clearfix col-flex" prop="business_scope" label="法定经营范围">
                            <i-input v-model="form.business_scope"
                                     type="textarea"
                                     :autosize="{minRows: 3,maxRows: 5}">
                            </i-input>
                            <p class="p_first">请与营业执照或企业信息公示网的经营范围保持一致</p>
                        </form-item>
                        <form-item class="form-group clearfix col-flex" prop="business_scope" label="营业执照电子版">
                            <ul class="real-imgs clearfix">
                                <li v-for="img in form.license_images">
                                    <img :src="img"/>
                                    <div class="cover">
                                        <i class="icon iconfont icon-icon_shanchu"
                                           @click="deleteImg(form.license_images,img)"> </i>
                                    </div>
                                </li>
                                <li class="diamond-upload-file"
                                    v-if="form.license_images.length<2">
                                    <div class="icon iconfont icon-tupian"></div>
                                    <upload
                                        ref="upload"
                                        :format="['jpg','jpeg','png']"
                                        :on-success="handleSuccess"
                                        action="//jsonplaceholder.typicode.com/posts/">
                                    </upload>
                                </li>
                            </ul>
                            <p class="p_first">营业执照复印件需加盖公司红章扫描上传，若营业执照上未体现注册资本、经营范围，请在营业执照后另行上传企业信息公示网上的截图。</p>
                            <p class="p_prompt">图片尺寸请确保800px*800px以上，文件大小1MB以内，支持JPG、GIF、PNG格式，最多可上传2张</p>
                        </form-item>
                    </i-form>
                    <div class="form-group clearfix">
                            <div class="col-md-offset-4 col-sm-offset-4 col-xs-offset-4 col-md-1 col-sm-1 col-xs-1">
                                <button class="btn btn-default prev-btn" @click="prev">上一步</button>
                            </div>
                            <div class="col-md-1 col-sm-1 col-xs-1">
                                <button type="submit" class="col-md-offset-11 col-sm-offset-11 col-xs-offset-11 btn btn-info next-btn" @click="next">
                                    下一步，完善公司信息
                                </button>
                            </div>
                        </div>
                    </div>
                <div class="progress_06" v-if="temp===4">
                    <div class="info_item form-horizontal operating">
                        <h4>店铺信息</h4>
                        <i-form  class="form-horizontal" ref="shopInfo" :model="shopInfo" :rules="shopInfoRule">
                            <form-item class="form-group clearfix" prop="type" label="所属分类">
                                <i-select v-model="shopInfo.type"  style="width:174px">
                                    <i-option v-for="(type,index) in types"
                                              :value="type.name"
                                              :key="index">
                                        {{ type.name }}
                                    </i-option>
                                </i-select>
                            </form-item>
                            <form-item class="form-group clearfix" prop="category" label="主营类目">
                                <cascader :data="categories" v-model="shopInfo.category"></cascader>
                            </form-item>
                            <form-item class="form-group clearfix" prop="store_name" label="店铺名称">
                                <i-input v-model="shopInfo.store_name"></i-input>
                            </form-item>
                            <form-item class="form-group clearfix" prop="store_account" label="店铺账号">
                                <i-input v-model="shopInfo.store_account"></i-input>
                            </form-item>
                        </i-form>
                        <div class="form-group btn_div">
                            <div class="col-md-offset-4 col-md-1">
                                <button class="btn btn-default prev-btn" @click="prev">上一步</button>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="col-md-offset-11 btn btn-info next-btn" @click="next">
                                    提交申请
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="progress_07 settled_audit_box" v-if="temp===5">
                    <p>如果您对提交的信息有疑问，可以
                        <router-link to="/"><u>点击修改公司信息</u></router-link>&nbsp;&nbsp;&nbsp;
                        <router-link to="/"><u>点击修改店铺信息</u></router-link>
                    </p>
                    <div class="info_item form-horizontal">
                        <h4>入驻状态</h4>
                        <div class="row settled_audit">
                            <div class="col-sm-1 control-label">待审核</div>
                            <div class="col-sm-9">
                                等待审核人员进行审核。如对已提交信息有疑问，您可以自主撤销此次入驻申请。
                            </div>
                            <div class="col-sm-2 text-right">
                                <button class="btn btn-default prev-btn">撤销申请</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>