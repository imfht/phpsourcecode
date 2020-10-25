<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                loading: false,
                navigate: {
                    address: '',
                    isShow: '',
                    name: '',
                    open: '',
                    sort: '',
                },
                ruleValidate: {
                    name: [
                        {
                            message: '导航名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.navigate.validate(valid => {
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
        <div class="shop-navigate-edit">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>导航列表-编辑导航</span>
            </div>
            <card :bordered="false">
                <i-form ref="navigate" :model="navigate" :rules="ruleValidate" :label-width="180">
                    <row>
                        <i-col span="14">
                            <form-item label="导航名称" prop="name">
                                <i-input v-model="navigate.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item label="是否显示">
                                <radio-group v-model="navigate.isShow">
                                    <radio label="是"></radio>
                                    <radio label="否"></radio>
                                </radio-group>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item label="链接地址">
                                <i-input v-model="navigate.address"></i-input>
                                <p class="tip">如果是本站的网址，可缩写为与商城根目录相对地址，如index.php；
                                    其他情况请填写包含http://的完整URL地址</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="8">
                            <form-item label="排序">
                                <i-input v-model="navigate.sort"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
                            <form-item label="新窗口打开">
                                <radio-group v-model="navigate.open">
                                    <radio label="是"></radio>
                                    <radio label="否"></radio>
                                </radio-group>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="14">
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
