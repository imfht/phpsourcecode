<script>
//    import ColorPicker from './colorPicker.vue';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        components: {
//            ColorPicker,
        },
        data() {
            const self = this;
            return {
                color: '',
                firstType: [
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '婴儿推车',
                                        value: '婴儿推车',
                                    },
                                    {
                                        label: '自行车',
                                        value: '自行车',
                                    },
                                    {
                                        label: '婴儿推车',
                                        value: '婴儿推车',
                                    },
                                    {
                                        label: '电动车',
                                        value: '电动车',
                                    },
                                    {
                                        label: '安全座椅',
                                        value: '安全座椅',
                                    },
                                ],
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
                                children: [
                                    {
                                        label: '婴儿推车1',
                                        value: '婴儿推车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                    {
                                        label: '婴儿推车3',
                                        value: '婴儿推车3',
                                    },
                                    {
                                        label: '电动车',
                                        value: '电动车',
                                    },
                                    {
                                        label: '安全座椅4',
                                        value: '安全座椅4',
                                    },
                                ],
                                label: '服饰寝居',
                                value: '服饰寝居',
                            },
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
                        label: '家用电器',
                        value: '家用电器',
                    },
                ],
                goodsColorColumns: [
                    {
                        align: 'center',
                        key: 'sort',
                        render(h, data) {
                            return h('input-number', {
                                props: {
                                    max: 10,
                                    min: 1,
                                    value: data.index + 1,
                                },
                                style: {
                                    width: '64px',
                                },
                            });
                        },
                        title: '排序',
                        width: 150,
                    },
                    {
                        key: 'standardName',
                        render(h) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                },
                                style: {
                                    width: '269px',
                                },
                            });
                        },
                        title: '规格值名称',
                        width: 400,
                    },
                    {
                        key: 'color',
//                        render(h) {
//                            return h('i-input', {
//                                props: {
//                                    type: 'ghost',
//                                },
//                                style: {
//                                    width: '124px',
//                                },
//                            });
//                        },
                        render(h) {
                            return h('color-picker', {
                                props: {
                                    value: self.color,
                                },
                            });
                        },
                        title: '颜色色块',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.removeColorGoods(data.index);
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '删除');
                        },
                        title: '操作',
                        width: 150,
                    },
                ],
                goodsColorData: [
                    {
                        sortNum: '',
                    },
                ],
                goodsSizeColumns: [
                    {
                        align: 'center',
                        key: 'sort',
                        render(h, data) {
                            return h('input-number', {
                                props: {
                                    max: 10,
                                    min: 1,
                                    value: data.index + 1,
                                },
                                style: {
                                    width: '64px',
                                },
                            });
                        },
                        title: '排序',
                        width: 150,
                    },
                    {
                        key: 'standardName',
                        render(h) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                },
                                style: {
                                    width: '268px',
                                },
                            });
                        },
                        title: '规格值名称',
                        width: 400,
                    },
                    {
                        key: 'size',
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
                        title: '尺码大小',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.removeSizeGoods(data.index);
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '删除');
                        },
                        title: '操作',
                        width: 150,
                    },
                ],
                goodsSizeData: [
                    {
                        sortNum: '',
                    },
                ],
                goodsStyle: {
                    firstType: [],
                },
                loading: false,
                self: this,
            };
        },
        methods: {
            addColorStandard() {
                this.goodsColorData.push(
                    {
                        sortNum: '',
                    },
                );
            },
            addSizeStandard() {
                this.goodsSizeData.push(
                    {
                        sortNum: '',
                    },
                );
            },
            removeColorGoods(index) {
                this.goodsColorData.splice(index, 1);
            },
            removeSizeGoods(index) {
                this.goodsSizeData.splice(index, 1);
            },
            submitColor() {
                const self = this;
                self.loading = true;
                self.$refs.colorForm.validate(valid => {
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
            submitSize() {
                const self = this;
                self.loading = true;
                self.$refs.sizeForm.validate(valid => {
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
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="goods-standard">
            <tabs value="name1">
                <tab-pane label="商品规格" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.选择店铺经营的商品分类，以读取平台绑定的商品分类-规格类型，如分类："服装","规格","颜色",
                                "尺码'等；当商品分类具有"颜色"规格时，可选择色块加以标识 </p>
                            <p>2.添加所属规格下的规格值，已有规格值可以删除，新增未保存的规格值可以移除；新增的规格值必须填写，
                                否则该行数据不会被更新或保存</p>
                            <p>3.可通过排序0~255改变规格值显示顺序；在发布商品是勾选已绑定的商品规格，还可对规格值进行"别名"
                                修改操作，但不会影响规格值默认名称的设定</p>
                        </div>
                        <div class="goods-list">
                            <row>
                                <i-col span="3">选择商品分类</i-col>
                                <i-col span="6">
                                    <cascader :data="firstType"
                                              trigger="hover"
                                              v-model="goodsStyle.firstType"></cascader>
                                </i-col>
                            </row>
                            <tabs type="card" class="goods-standard-module">
                                <tab-pane label="编辑颜色规格">
                                    <i-form :label-width="45" ref="colorForm" :model="colorForm">
                                        <div class="btn-group">
                                            <i-button type="ghost" class="float-right"
                                                      @click="addColorStandard">添加规格值</i-button>
                                        </div>
                                        <i-table class="goods-table"
                                                 :columns="goodsColorColumns"
                                                 :context="self"
                                                 :data="goodsColorData"
                                                 ref="goodsColorList"
                                                 highlight-row>
                                        </i-table>
                                        <row>
                                            <i-col span="20">
                                                <form-item>
                                                    <i-button :loading="loading" type="primary" @click.native="submitColor">
                                                        <span v-if="!loading">确认提交</span>
                                                        <span v-else>正在提交…</span>
                                                    </i-button>
                                                </form-item>
                                            </i-col>
                                        </row>
                                    </i-form>
                                </tab-pane>
                                <tab-pane label="编辑尺码规格">
                                    <i-form :label-width="45" ref="sizeForm" :model="sizeForm">
                                        <div class="btn-group">
                                            <i-button type="ghost" class="float-right"
                                                      @click="addSizeStandard">添加规格值</i-button>
                                        </div>
                                        <i-table class="goods-table"
                                                 :columns="goodsSizeColumns"
                                                 :context="self"
                                                 :data="goodsSizeData"
                                                 ref="goodsSizeList"
                                                 highlight-row>
                                        </i-table>
                                        <row>
                                            <i-col span="20">
                                                <form-item>
                                                    <i-button :loading="loading" type="primary" @click.native="submitSize">
                                                        <span v-if="!loading">确认提交</span>
                                                        <span v-else>正在提交…</span>
                                                    </i-button>
                                                </form-item>
                                            </i-col>
                                        </row>
                                    </i-form>
                                </tab-pane>
                            </tabs>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>