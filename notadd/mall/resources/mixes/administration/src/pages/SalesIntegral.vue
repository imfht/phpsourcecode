<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                form: {
                    enabled: '是',
                },
                loading: false,
            };
        },
        methods: {
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
    <div class="mall-wrap">
        <div class="sales-integral">
            <tabs value="name1">
                <tab-pane label="积分" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>控制积分在买家付款时能否抵付现金</p>
                        </div>
                        <i-form :label-width="200" :model="form" ref="form" :rules="rules">
                            <row>
                                <i-col span="12">
                                    <form-item label="是否抵现" prop="enabled">
                                        <radio-group v-model="form.enabled">
                                            <radio label="是"></radio>
                                            <radio label="否"></radio>
                                        </radio-group>
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
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>
