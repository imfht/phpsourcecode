<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                adjust: false,
                designForm: {
                    select: [],
                },
                editForm: {
                    bottom: '',
                    left: '',
                    height: '',
                    width: '',
                },
                loading: false,
                selectList: [
                    {
                        name: '收货人',
                    },
                    {
                        name: '收货人地址',
                    },
                    {
                        name: '收货人手机',
                    },
                    {
                        name: '备注信息',
                    },
                    {
                        name: '发货人',
                    },
                    {
                        name: '发货人地区',
                    },
                    {
                        name: '发货人公司',
                    },
                ],
            };
        },
        methods: {
            edit() {
                this.adjust = true;
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.designForm.validate(valid => {
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
            submitEdit() {
                const self = this;
                self.loading = true;
                self.$refs.editForm.validate(valid => {
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
        <div class="order-waybill-design">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>自建模版-设计模版</span>
            </div>
            <card :bordered="false">
                <i-form ref="designForm" :model="designForm" :rules="ruleValidate" :label-width="180">
                    <row>
                        <i-col span="20">
                            <form-item label="选择打印项">
                                <checkbox-group v-model="designForm.select">
                                    <checkbox :label="item.name" v-for="item in selectList">
                                        <span>{{ item.name }}</span>
                                        <icon type="edit" @click.native="edit(item)"></icon>
                                    </checkbox>
                                </checkbox-group>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="20">
                            <form-item label="打印项偏移校正">
                                <div class="correction-box">

                                </div>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="20">
                            <form-item>
                                <i-button :loading="loading" type="primary" @click.native="submit">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
                <modal
                        v-model="adjust"
                        title="微调" class="upload-picture-modal adjust-template-design">
                    <div>
                        <i-form ref="editForm" :model="editForm" :rules="editValidate" :label-width="100">
                            <form-item label="左偏移量">
                                <row>
                                    <i-col span="4">
                                        <i-input v-model="editForm.left"></i-input>
                                    </i-col>
                                    <i-col span="2">mm</i-col>
                                </row>
                            </form-item>
                            <form-item label="下偏移量">
                                <row>
                                    <i-col span="4">
                                        <i-input v-model="editForm.bottom"></i-input>
                                    </i-col>
                                    <i-col span="2">mm</i-col>
                                </row>
                            </form-item>
                            <form-item label="宽">
                                <row>
                                    <i-col span="4">
                                        <i-input v-model="editForm.width"></i-input>
                                    </i-col>
                                    <i-col span="2">mm</i-col>
                                </row>
                            </form-item>
                            <form-item label="高">
                                <row>
                                    <i-col span="4">
                                        <i-input v-model="editForm.height"></i-input>
                                    </i-col>
                                    <i-col span="2">mm</i-col>
                                </row>
                            </form-item>
                            <row>
                                <i-col span="20">
                                    <form-item>
                                        <i-button :loading="loading" type="primary" @click.native="submitEdit">
                                            <span v-if="!loading">确认提交</span>
                                            <span v-else>正在提交…</span>
                                        </i-button>
                                    </form-item>
                                </i-col>
                            </row>
                        </i-form>
                    </div>
                </modal>
            </card>
        </div>
    </div>
</template>