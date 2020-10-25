<script>
    import image1 from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                form: {
                    columns: [
                        {
                            key: 'name',
                            render(h, data) {
                                return h('div', {
                                    class: {
                                        'goods-name-row': true,
                                    },
                                }, [
                                    h('div', {
                                        class: {
                                            img: true,
                                        },
                                    }, [
                                        h('img', {
                                            domProps: {
                                                src: data.row.image,
                                            },
                                        }),
                                    ]),
                                    h('div', {
                                        class: {
                                            'right-text': true,
                                        },
                                    }, [
                                        h('p', data.row.name),
                                    ]),
                                ]);
                            },
                            title: '商品名称',
                        },
                        {
                            align: 'center',
                            key: 'pre_price',
                            title: '原价',
                            width: 180,
                        },
                        {
                            align: 'center',
                            key: 'price',
                            render(h, data) {
                                return h('i-input', {
                                    props: {
                                        type: 'ghost',
                                        value: data.row.price,
                                    },
                                });
                            },
                            title: '优惠价格',
                            width: 180,
                        },
                        {
                            align: 'center',
                            key: 'action',
                            render(h, data) {
                                return h('div', [
                                    h('i-button', {
                                        on: {
                                            click() {
                                                self.remove(data.index);
                                            },
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '删除'),
                                ]);
                            },
                            title: '操作',
                            width: 180,
                        },
                    ],
                    endTime: '',
                    list: [
                        {
                            image: image1,
                            name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                            pre_price: '￥388.00',
                            price: '388.00',
                        },
                        {
                            image: image1,
                            name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                            pre_price: '￥388.00',
                            price: '388.00',
                        },
                        {
                            image: image1,
                            name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                            pre_price: '￥388.00',
                            price: '388.00',
                        },
                    ],
                    name: '',
                    remarks: '',
                    standard: [
                        {
                            amount: '',
                            fullPrice: '',
                        },
                    ],
                    startTime: '',
                },
                loading: false,
                rules: {
                    name: [
                        {
                            message: '活动名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    standard: [
                        {
                            message: '单笔订单不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    startTime: [
                        {
                            message: '开始时间不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
            };
        },
        methods: {
            addStandard() {
                this.form.standard.push({
                    amount: '',
                    fullPrice: '',
                });
            },
            deleteStandard(index) {
                this.form.standard.splice(index, 1);
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            remove(index) {
                this.form.list.splice(index, 1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
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
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="sales-fulldown-create">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>活动列表-添加活动</span>
            </div>
            <card :bordered="false">
                <div class="prompt-box">
                    <p>提示</p>
                    <p>满减活动包括店铺所有商品，活动时间不能喝以后活动重叠</p>
                    <p>每个满减活动最多可以设置三个价格级别，点击新增级别按钮可以增加新的级别，价格级别应该由低到高</p>
                </div>
                <i-form :label-width="180" :model="form" ref="form" :rules="rules">
                    <row>
                        <i-col span="12">
                            <form-item label="活动名称" prop="name">
                                <i-input v-model="form.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="开始时间" prop="startTime">
                                <time-picker type="time" placeholder="选择日期" v-model="form.startTime"></time-picker>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="结束时间" prop="endTime">
                                <time-picker type="time" placeholder="选择日期" v-model="form.endTime"></time-picker>
                            </form-item>
                        </i-col>
                    </row>
                    <form-item label="活动规则" prop="standard">
                        <row class="active-standard" v-for="(item, index) in form.standard">
                            <i-col span="3" class="price-width">单笔订单满</i-col>
                            <i-col span="2" class="input-width">
                                <i-input v-model="item.amount"></i-input>
                            </i-col>
                            <i-col span="3" class="price-width"> 元, 立减现金</i-col>
                            <i-col span="2" class="input-width">
                                <i-input v-model="item.fullPrice"></i-input>
                            </i-col>
                            <i-col span="1">元</i-col>
                            <i-col span="12">
                                <i-button @click.native="deleteStandard(index)" v-if="index !== 0"
                                          class="delete-color" type="ghost">刪除</i-button>
                            </i-col>
                        </row>
                    </form-item>
                    <form-item>
                        <i-button @click.native="addStandard"  class="button-style"
                                  type="ghost">添加规则</i-button>
                        <p class="tip">设置当单笔订单满足金额时（必填选项），减免金额（选填）；留空为不做减免金额处理，
                            系统最多支持设置三组等级规则</p>
                    </form-item>
                    <row>
                        <i-col span="16">
                            <form-item label="备注" prop="remarks">
                                <i-input :autosize="{minRows: 4,maxRows: 5}" type="textarea"
                                v-model="form.remarks"></i-input>
                                <p class="tip">活动备注最多为100个字符</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item>
                                <i-button :loading="loading" type="primary" @click.native="submit">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </card>
        </div>
    </div>
</template>
