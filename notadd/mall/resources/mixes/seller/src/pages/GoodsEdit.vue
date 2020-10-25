<script>
    import injection from '../helpers/injection';
    import image1 from '../assets/images/adv.jpg';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                distribution: [
                    {
                        label: '333',
                        value: '1',
                    },
                    {
                        label: '444',
                        value: '2',
                    },
                ],
                goodsEdit: {
                    barCode: '',
                    commodityRelease: '',
                    costPrice: '',
                    distribution: '',
                    freightMoney: '',
                    freightStyle: '固定运费',
                    goodComment: '',
                    goodDiscount: '',
                    goodFeatured: '',
                    goodInventory: '',
                    goodOrigin: '',
                    goodPlace: [],
                    goodPrice: '',
                    goodStyle: '',
                    logo: '',
                    marketPrice: '',
                    name: '蘑菇空气净化器台灯创意USB充电小台灯学生护眼灯可爱卧室床头灯',
                    packageType: [],
                    price: '',
                    remarks: '',
                    selectVersion: [],
                    sellerNum: '',
                    sellPoint: '原价258 特价9元F码抢购！仅限10个！全球首款净化空气的台灯，上班看书的同时释放负离子，' +
                    '远离城市喧嚣，仿佛置身于大自然的感觉，让你身心健康',
                    shopStyle: [],
                    shopStyleList: [
                        {
                            shopStyle: [
                                {
                                    children: [
                                        {
                                            label: '童车童床',
                                            value: '童车童床',
                                        },
                                        {
                                            label: '营养辅食',
                                            value: '营养辅食',
                                        },
                                        {
                                            label: '尿裤湿巾',
                                            value: '尿裤湿巾',
                                        },
                                    ],
                                    label: '个护化妆',
                                    value: '个护化妆',
                                },
                                {
                                    children: [
                                        {
                                            label: '服饰寝居',
                                            value: '服饰寝居',
                                        },
                                        {
                                            label: '营养辅食',
                                            value: '营养辅食',
                                        },
                                        {
                                            label: '尿裤湿巾',
                                            value: '尿裤湿巾',
                                        },
                                    ],
                                    label: '家用电器',
                                    value: '家用电器',
                                },
                            ],
                        },
                    ],
                    supplier: '',
                    type: '数码办公>时尚影音>智能设备',
                    vatInvoice: '否',
                    warnValue: '',
                },
                goodComment: [
                    {
                        label: '好',
                        value: '1',
                    },
                    {
                        label: '一般',
                        value: '2',
                    },
                ],
                goodOrigin: [
                    {
                        label: '南',
                        value: '1',
                    },
                    {
                        label: '北',
                        value: '2',
                    },
                ],
                goodPlace: [
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '耀县',
                                        value: '1',
                                    },
                                    {
                                        label: '王益区',
                                        value: '2',
                                    },
                                    {
                                        label: '印台区',
                                        value: '3',
                                    },
                                ],
                                label: '铜川市',
                                value: '1',
                            },
                            {
                                label: '咸阳市',
                                value: '2',
                            },
                            {
                                label: '宝鸡市',
                                value: '3',
                            },
                        ],
                        label: '陕西',
                        value: '1',
                    },
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '婴儿推车1',
                                        value: '婴儿推车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                ],
                                label: '营养辅食',
                                value: '营养辅食',
                            },
                            {
                                children: [
                                    {
                                        label: '车1',
                                        value: '车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                ],
                                label: '尿裤湿巾',
                                value: '尿裤湿巾',
                            },
                        ],
                        label: '北京市',
                        value: '2',
                    },
                ],
                goodStyle: [
                    {
                        label: '华为',
                        value: '1',
                    },
                    {
                        label: '苹果',
                        value: '2',
                    },
                ],
                goodSupplier: [
                    {
                        label: '新增分类1',
                        value: '1',
                    },
                    {
                        label: '新增分类2',
                        value: '2',
                    },
                ],
                inventoryColumns: [
                    {
                        align: 'center',
                        key: 'type',
                        title: '套餐类型',
                    },
                    {
                        align: 'center',
                        key: 'version',
                        title: '选择版本',
                    },
                    {
                        align: 'center',
                        key: 'goodPrice',
                        render(h) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                },
                                style: {
                                    width: '64px',
                                },
                            });
                        },
                        title: '商品价格（元）',
                    },
                    {
                        align: 'center',
                        key: 'marketPrice',
                        render(h) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                },
                                style: {
                                    width: '64px',
                                },
                            });
                        },
                        title: '市场价（元）',
                    },
                    {
                        align: 'center',
                        key: 'stock',
                        render(h) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                },
                                style: {
                                    width: '64px',
                                },
                            });
                        },
                        title: '库存',
                    },
                    {
                        align: 'center',
                        key: 'warnValue',
                        render(h) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                },
                                style: {
                                    width: '64px',
                                },
                            });
                        },
                        title: '预警值',
                    },
                    {
                        align: 'center',
                        key: 'sellNumber',
                        render(h) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                },
                                style: {
                                    width: '124px',
                                },
                            });
                        },
                        title: '商家货号',
                        width: 160,
                    },
                    {
                        align: 'center',
                        key: 'barCode',
                        render(h) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                },
                                style: {
                                    width: '124px',
                                },
                            });
                        },
                        title: '商品条形码',
                        width: 160,
                    },
                ],
                inventoryData: [
                    {
                        type: '官方标配',
                        version: '256G',
                    },
                    {
                        type: '官方标配',
                        version: '256G',
                    },
                    {
                        type: '官方标配',
                        version: '256G',
                    },
                    {
                        type: '官方标配',
                        version: '256G',
                    },
                    {
                        type: '官方标配',
                        version: '256G',
                    },
                ],
                isEditPicture: false,
                isEditText: false,
                isPcPicture: false,
                list: {},
                loading: false,
                packageType: ['官方标配', '官方标配2', '套餐'],
                pictureGroup: [
                    {
                        color: '黑色',
                        logoList: [
                            {
                                img: image1,
                            },
                            {
                                img: image1,
                            },
                            {
                                img: image1,
                            },
                        ],
                        pictureSpace: [
                            {
                                img: image1,
                            },
                            {
                                img: image1,
                            },
                            {
                                img: image1,
                            },
                        ],
                    },
                    {
                        color: '无色',
                        logoList: [
                            {
                                img: image1,
                            },
                            {
                                img: image1,
                            },
                        ],
                        pictureSpace: [
                            {
                                img: image1,
                            },
                            {
                                img: image1,
                            },
                        ],
                    },
                ],
                priceList: [
                    {
                        label: '100-200',
                        value: '1',
                    },
                    {
                        label: '400-600',
                        value: '2',
                    },
                ],
                requests: [
                    {
                        content: '（1）手机详情总体大小：图片+文字，图片不超过20张，文字不超过5000字；' +
                        '建议：所有图片都是本宝贝相关的图片',
                        name: '1.基本要求',
                    },
                    {
                        content: '（1）建议使用宽度480~620像素、高度小于等于960像素的图片；' +
                        '（2）格式为：jpg、jepg、gif、png举例：可以上传一张宽度为480，高度为960像素，格式为jpg的图片',
                        name: '2.图片大小要求',
                    },
                    {
                        content: '（1）每次插入文字不能超过500个字，标点、特殊字符按照一个字计算；' +
                        '（2）请手动输入文字，不要复制粘网页上的文字，防止出现乱码；' +
                        '（3）以下特殊字符"<",">,"/",""等会被替换为空 ',
                        name: '3.文字要求',
                    },
                ],
                ruleValidate: {
                    goodInventory: [
                        {
                            message: '商品库存不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    goodPrice: [
                        {
                            message: '商品价格不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    logo: [
                        {
                            message: '商品图片不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    marketPrice: [
                        {
                            message: '市场价格不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    name: [
                        {
                            message: '商品名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                selectVersion: ['256GB', '128GB', '64GB', '32GB'],
                self: this,
                spaceExit: false,
            };
        },
        methods: {
            addAlbumPicture() {
                const self = this;
                self.isPcPicture = !self.isPcPicture;
            },
            addCategory() {
                this.goodsEdit.shopStyleList.push(
                    {
                        shopStyle: [
                            {
                                children: [
                                    {
                                        label: '童车童床',
                                        value: '童车童床',
                                    },
                                    {
                                        label: '营养辅食',
                                        value: '营养辅食',
                                    },
                                    {
                                        label: '尿裤湿巾',
                                        value: '尿裤湿巾',
                                    },
                                ],
                                label: '个护化妆',
                                value: '个护化妆',
                            },
                            {
                                children: [
                                    {
                                        label: '服饰寝居',
                                        value: '服饰寝居',
                                    },
                                    {
                                        label: '营养辅食',
                                        value: '营养辅食',
                                    },
                                    {
                                        label: '尿裤湿巾',
                                        value: '尿裤湿巾',
                                    },
                                ],
                                label: '家用电器',
                                value: '家用电器',
                            },
                        ],
                    },
                );
            },
            addPicture() {
                const self = this;
                self.isEditPicture = !self.isEditPicture;
            },
            addText() {
                const self = this;
                self.isEditText = !self.isEditText;
            },
            closeAlbum() {
                const self = this;
                self.isEditPicture = false;
            },
            closePcAlbum() {
                const self = this;
                self.isPcPicture = false;
            },
            editInformation() {
                const self = this;
                self.$router.push({
                    path: 'edit/category',
                });
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            pictureClose() {
                this.spaceExit = false;
            },
            pictureSelect(group, index) {
                console.log(group);
                console.log(index);
                const self = this;
                self.pictureGroup.forEach(item => {
                    self.list = item;
                });
                this.spaceExit = true;
                self.list.pictureSpace = true;
            },
            removeAlbum(group, index) {
                group.logoList.splice(index, 1);
            },
            removeLogo() {
                this.goodsEdit.logo = '';
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.goodsEdit.validate(valid => {
                    if (valid) {
                        self.$Message.success('提交成功!');
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
            uploadBefore() {
                injection.loading.start();
            },
            uploadError(error, data) {
                const self = this;
                injection.loading.error();
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
                injection.loading.finish();
                const self = this;
                let index;
                self.pictureGroup.forEach((item, i) => {
                    if (item.color === data.data.color) {
                        self.list = item;
                        index = i;
                    }
                });
                if (self.list.logoList.length < 5) {
                    self.$notice.open({
                        title: data.message,
                    });
                    self.pictureGroup[index].logoList.push({
                        img: data.data.path,
                    });
                } else {
                    self.$notice.open({
                        title: '每类最多展示五张图片',
                    });
                }
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="goods-edit">
            <div class="store-refund-process">
                <div class="edit-link-title">
                    <i-button type="text" @click.native="goBack">
                        <icon type="chevron-left"></icon>
                    </i-button>
                    <span>商品列表-编辑商品</span>
                </div>
                <div>
                    <card :bordered="false">
                        <tabs type="card">
                            <tab-pane label="编辑商品">
                                <i-form ref="goodsEdit" :model="goodsEdit" :rules="ruleValidate" :label-width="200">
                                    <div class="library-application">
                                        <h5>商品基本信息</h5>
                                        <div class="application-content refund-module">
                                            <row>
                                                <i-col span="16">
                                                    <form-item label="商品分类">
                                                        {{ goodsEdit.type }}&nbsp; &nbsp;&nbsp;
                                                        <i-button type="ghost" @click.native="editInformation">编辑</i-button>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="12">
                                                    <form-item label="商品名称" prop="name">
                                                        <i-input v-model="goodsEdit.name"></i-input>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="12">
                                                    <form-item label="商品卖点">
                                                        <i-input v-model="goodsEdit.sellPoint" type="textarea"
                                                                 :autosize="{minRows: 3,maxRows: 5}"></i-input>
                                                        <p class="tip">商品卖点最长不超过140个汉字</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="商品价格" prop="goodPrice">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.goodPrice"></i-input>
                                                            </i-col>
                                                            <i-col span="2">元</i-col>
                                                        </row>
                                                        <p class="tip">价格必须是大于等于0.01的数字，且不能高于市场价，
                                                            此价格为商品实际销售价格，如果存在规格，该价格显示最低价格</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="市场价格" prop="marketPrice">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.marketPrice"></i-input>
                                                            </i-col>
                                                            <i-col span="2">元</i-col>
                                                        </row>
                                                        <p class="tip">价格必须是大于等于0.01的数字，此价格仅为市场参考售价，
                                                            请根据实际情况认真填写</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="成本价格">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.costPrice"></i-input>
                                                            </i-col>
                                                            <i-col span="2">元</i-col>
                                                        </row>
                                                        <p class="tip">价格必须是大于等于0.01的数字，此价格为商户对所销售的商品实际
                                                            成本价格进行备注记录，不会在前台销售页面中显示</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="折扣">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.goodDiscount"></i-input>
                                                            </i-col>
                                                            <i-col span="2">%</i-col>
                                                        </row>
                                                        <p class="tip">价格必须是大于等于0.01的数字，此价格为商户对所销售的商品实
                                                            际成本价格进行备注记录，不会在前台销售页面中显示</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="24">
                                                    <form-item label="套餐类型">
                                                        <checkbox-group v-model="goodsEdit.packageType">
                                                            <checkbox :label="item" v-for="item in packageType">
                                                                <span class="type-style">{{ item }}</span>
                                                            </checkbox>
                                                            <i-button class="add-type-btn">添加规格值</i-button>
                                                        </checkbox-group>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="24">
                                                    <form-item label="选择版本">
                                                        <checkbox-group v-model="goodsEdit.selectVersion">
                                                            <checkbox :label="item" v-for="item in selectVersion">
                                                                <span class="type-style">{{ item }}</span>
                                                            </checkbox>
                                                            <i-button class="add-type-btn">添加规格值</i-button>
                                                        </checkbox-group>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="23">
                                                    <form-item label="库存配置" class="stock-module">
                                                        <i-table class="goods-table"
                                                                 :columns="inventoryColumns"
                                                                 :context="self"
                                                                 :data="inventoryData"
                                                                 highlight-row>
                                                        </i-table>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="商品库存" prop="goodInventory">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.goodInventory"></i-input>
                                                            </i-col>
                                                        </row>
                                                        <p class="tip">商品库存数量必须为0~999999999之间的整数，若启用了库存配置，
                                                            则系统自动计算商品的总数，此处无需买家填写</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="库存预警值">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.warnValue"></i-input>
                                                            </i-col>
                                                        </row>
                                                        <p class="tip">设置最低库存预警值。当库存低于预警值时商家中心商品列表页库
                                                            存列红字提醒，请填写0~255的数字，0为不预警</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="商家货号">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.sellerNum"></i-input>
                                                            </i-col>
                                                        </row>
                                                        <p class="tip">商家货号是指商家管理商品的编号，买家不可见，最多可输入20个字符，
                                                            支持输入中文、字母、数字、_、/、-和小数点</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="商品条形码">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.barCode"></i-input>
                                                            </i-col>
                                                        </row>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="24">
                                                    <form-item label="商品图片" prop="logo">
                                                        <div class="image-preview" v-if="goodsEdit.logo">
                                                            <img :src="goodsEdit.logo">
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
                                                                v-if="goodsEdit.logo === '' || goodsEdit.logo === null">
                                                        </upload>
                                                        <p class="tip">上传商品默认主图，如多规格值时将默认使用该图或分规格上传各规格主图；
                                                            支持jpg、gif、png格式上传或从图片空间中选择，建议使用尺寸800x800像素以上、
                                                            大小不超过1M的正方形图片，上传后的图片将会自动保存在图片空间的默认分类中
                                                        </p>
                                                        <i-button type="ghost">从图片空间上传</i-button>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                        </div>
                                    </div>
                                    <div class="library-application">
                                        <h5>商品详情描述</h5>
                                        <div class="application-content">
                                            <row>
                                                <i-col span="10">
                                                    <form-item label="商品品牌">
                                                        <i-select v-model="goodsEdit.goodStyle">
                                                            <i-option v-for="item in goodStyle" :value="item.value"
                                                                      :key="item">{{ item.label }}</i-option>
                                                        </i-select>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="24">
                                                    <form-item label="商品属性" class="good-edit-style">
                                                        <row>
                                                            <i-col span="6">
                                                                <span class="style-title">价格区间</span>
                                                                <i-select v-model="goodsEdit.price">
                                                                    <i-option v-for="item in priceList" :value="item.value"
                                                                              :key="item">{{ item.label }}</i-option>
                                                                </i-select>
                                                            </i-col>
                                                            <i-col span="6">
                                                                <span class="style-title">口碑</span>
                                                                <i-select v-model="goodsEdit.goodComment">
                                                                    <i-option v-for="item in goodComment" :value="item.value"
                                                                              :key="item">{{ item.label }}</i-option>
                                                                </i-select>
                                                            </i-col>
                                                            <i-col span="6">
                                                                <span class="style-title">区域配送</span>
                                                                <i-select v-model="goodsEdit.distribution">
                                                                    <i-option v-for="item in distribution" :value="item.value"
                                                                              :key="item">{{ item.label }}</i-option>
                                                                </i-select>
                                                            </i-col>
                                                            <i-col span="6">
                                                                <span class="style-title">产地</span>
                                                                <i-select v-model="goodsEdit.goodOrigin">
                                                                    <i-option v-for="item in goodOrigin" :value="item.value"
                                                                              :key="item">{{ item.label }}</i-option>
                                                                </i-select>
                                                            </i-col>
                                                        </row>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="24">
                                                    <form-item label="商品描述" class="remark-input">
                                                        <row>
                                                            <i-col span="24">
                                                                <div>
                                                                    <tabs type="card">
                                                                        <tab-pane label="电脑端" class="pc-module-content">
                                                                            <row>
                                                                                <i-col span="18">
                                                                                    <div class="edit-content-area">

                                                                                    </div>
                                                                                </i-col>
                                                                                <i-col span="6"></i-col>
                                                                            </row>
                                                                            <i-button class="close-album"
                                                                                      @click.native="addAlbumPicture"
                                                                                      type="ghost">插入相册图片</i-button>
                                                                            <div class="picture-edit-area" v-if="isPcPicture">
                                                                                <i-button class="close-album"
                                                                                          @click.native="closePcAlbum"
                                                                                          type="ghost">关闭相册</i-button>
                                                                                <p>用户相册>全部图片</p>
                                                                                <div class="picture-content">
                                                                                    <row>
                                                                                        <i-col span="4" v-for="img in [1,2,3,4,5,6,7,8,9]">
                                                                                            <img src="../assets/images/adv.jpg" alt="">
                                                                                        </i-col>
                                                                                    </row>
                                                                                    <div class="page">
                                                                                        <page :total="100" show-elevator></page>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </tab-pane>
                                                                        <tab-pane label="手机端" class="mobile-module-content">
                                                                            <row>
                                                                                <i-col span="10" class="request-col-border">
                                                                                    <div class="pro-des">
                                                                                        <div class="pro-bg">
                                                                                    <span>图片总数不得超过20张，
                                                                                        文字不得超过500字</span>
                                                                                        </div>
                                                                                        <div class="pro-bg2">
                                                                                            <i-button @click.native="addPicture"
                                                                                                      type="ghost">插入图片</i-button>
                                                                                            <i-button @click.native="addText"
                                                                                                      type="ghost">添加文字</i-button>
                                                                                        </div>
                                                                                        <div class="pro-content"></div>
                                                                                    </div>
                                                                                    <div class="text-edit-area" v-if="isEditText">
                                                                                        <span>还可以输入500字</span><br>
                                                                                        <i-input  type="textarea"
                                                                                                  v-model="goodsEdit.remarks"
                                                                                                  :rows="6"></i-input>
                                                                                        <i-button type="ghost">确认</i-button>
                                                                                        <i-button type="ghost">提交</i-button>
                                                                                    </div>
                                                                                    <div class="picture-edit-area"
                                                                                         v-if="isEditPicture">
                                                                                        <i-button type="ghost" class="close-album"
                                                                                                  @click.native="closeAlbum">
                                                                                            关闭相册</i-button>
                                                                                        <p>用户相册>全部图片</p>
                                                                                        <div class="picture-content">
                                                                                            <row>
                                                                                                <i-col span="4" v-for="img in [1,2,3,4,5,6,7,8,9]">
                                                                                                    <img src="../assets/images/adv.jpg" alt="">
                                                                                                </i-col>
                                                                                            </row>
                                                                                            <div class="page">
                                                                                                <page :total="100" show-elevator></page>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </i-col>
                                                                                <i-col span="14" class="request-col-line">
                                                                                    <row>
                                                                                        <i-col span="14">
                                                                                            <ul class="request">
                                                                                                <li v-for="item in requests">
                                                                                                    <p>{{item.name}}</p>
                                                                                                    <span>{{item.content}}</span>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </i-col>
                                                                                        <i-col span="10"></i-col>
                                                                                    </row>

                                                                                </i-col>
                                                                            </row>
                                                                        </tab-pane>
                                                                    </tabs>
                                                                </div>
                                                            </i-col>
                                                        </row>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                        </div>
                                    </div>
                                    <div class="library-application">
                                        <h5>商品物流信息</h5>
                                        <div class="application-content refund-module">
                                            <row>
                                                <i-col span="12">
                                                    <form-item label="所在地">
                                                        <cascader :data="goodPlace" trigger="hover"
                                                                  v-model="goodsEdit.goodPlace"></Cascader>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="freight-style-module">
                                                <i-col span="16">
                                                    <form-item label="运费">
                                                        <row class="freight-style">
                                                            <i-col span="4">
                                                                <i-input v-model="goodsEdit.freightMoney"></i-input>
                                                            </i-col>
                                                            <i-col span="2">元</i-col>
                                                        </row>
                                                        <p class="tip">运费设置为0元，前台商品将显示为免运费</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                        </div>
                                    </div>
                                    <div class="library-application">
                                        <h5>发票信息</h5>
                                        <div class="application-content refund-module">
                                            <row>
                                                <i-col span="16">
                                                    <form-item label="是否开增值税发票">
                                                        <radio-group v-model="goodsEdit.vatInvoice">
                                                            <radio label="是"></radio>
                                                            <radio label="否"></radio>
                                                        </radio-group>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                        </div>
                                    </div>
                                    <div class="library-application">
                                        <h5>其他信息</h5>
                                        <div class="application-content refund-module">
                                            <row>
                                                <i-col span="20">
                                                    <form-item label="本店分类">
                                                        <cascader class="shop-edit-classification"
                                                                  change-on-select
                                                                  :data="type.shopStyle"
                                                                  v-for="type in goodsEdit.shopStyleList"
                                                                  v-model="goodsEdit.shopStyle"></Cascader>
                                                        <i-button type="ghost" @click.native="addCategory">新增分类</i-button>
                                                        <p class="tip">商品可以从属于店铺的多个分类下，店铺分类可以由“商家中心>店铺>
                                                            店铺分类”中自定义</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row class="data-picker-module">
                                                <i-col span="16">
                                                    <form-item label="商品发布">
                                                        <radio-group v-model="goodsEdit.commodityRelease">
                                                            <radio label="fabu">
                                                                <span>立即发布</span>
                                                            </radio>
                                                            <radio label="time">
                                                                <span>发布时间</span>
                                                                <date-picker type="date" placeholder="选择日期"></date-picker>
                                                            </radio>
                                                            <radio label="nofabu">
                                                                <span>暂不发布</span>
                                                            </radio>
                                                        </radio-group>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="16">
                                                    <form-item label="商品推荐">
                                                        <radio-group v-model="goodsEdit.goodFeatured">
                                                            <radio label="是"></radio>
                                                            <radio label="否"></radio>
                                                        </radio-group>
                                                        <p class="tip">被推荐的商品会显示在店铺首页</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                            <row>
                                                <i-col span="12">
                                                    <form-item label="供货商">
                                                        <i-select v-model="goodsEdit.supplier">
                                                            <i-option v-for="item in goodSupplier" :value="item.value"
                                                                      :key="item">{{ item.label }}</i-option>
                                                        </i-select>
                                                        <p class="tip">可以选择商品的供货商</p>
                                                    </form-item>
                                                </i-col>
                                            </row>
                                        </div>
                                    </div>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="">
                                                <i-button :loading="loading" @click.native="submit" type="primary">
                                                    <span v-if="!loading">确认提交</span>
                                                    <span v-else>正在提交…</span>
                                                </i-button>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </i-form>
                            </tab-pane>
                            <tab-pane label="编辑图片">
                                <div class="prompt-box">
                                    <p>提示</p>
                                    <p>每组图片的第一张图片默认为主图，每类最多展示5张图片</p>
                                </div>
                                <div class="picture-group" v-for="(group, index) in pictureGroup">
                                    <h5>颜色：{{ group.color }}</h5>
                                    <div>
                                        <div class="image-preview" v-for="(item, index) in group.logoList">
                                            <img :src="item.img">
                                            <i-button type="text" @click.native="removeAlbum(group, index)">
                                                <icon type="trash-a"></icon>
                                            </i-button>
                                        </div>
                                        <div style="margin-top: 16px">
                                            <upload :action="action"
                                                    :before-upload="uploadBefore"
                                                    :data="{
                                                        color: group.color,
                                                    }"
                                                    :format="['jpg','jpeg','png']"
                                                    :headers="{
                                                        Authorization: `Bearer ${$store.state.token.access_token}`
                                                    }"
                                                    multiple
                                                    :max-size="2048"
                                                    :on-error="uploadError"
                                                    :on-format-error="uploadFormatError"
                                                    :on-success="uploadSuccess"
                                                    ref="upload"
                                                    :show-upload-list="false">
                                                <i-button type="ghost">图片上传</i-button>
                                            </upload>
                                            <i-button type="ghost" v-if="!spaceExit"
                                                      @click.native="pictureSelect(group, index)">从图片空间选择</i-button>
                                            <i-button type="ghost" v-if="spaceExit"
                                                      @click.native="pictureClose(group, index)">关闭图片空间</i-button>
                                            <div v-if="spaceExit" class="picture-space">
                                                <h5>图片空间</h5>
                                                <span>
                                                    <img :src="space.img" alt=""
                                                         v-for="space in group.pictureSpace">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tab-pane>
                        </tabs>
                    </card>
                </div>
            </div>
        </div>
    </div>
</template>
