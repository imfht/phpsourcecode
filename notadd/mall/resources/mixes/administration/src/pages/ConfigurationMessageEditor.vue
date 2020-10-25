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
                    remarks: '',
                },
                rules: {
                    remarks: [
                        {
                            message: '信息不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                },
                whetherGroup1: '否',
                whetherGroup2: '否',
                whetherGroup3: '否',
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
                self.$refs.activityValidate.validate(valid => {
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
        <div class="configuration-message-editor">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>商家消息模板—编辑</span>
            </div>
            <card :bordered="false" class="store storeBox">
                <div class="prompt-box">
                    <p>提示</p>
                    <p>平台可给商家提供站内信、短消息、邮件三种通知方式。平台可以选择开启一种或多种通知方式供商家选择。</p>
                    <p>开启强制接收后，商家不能取消该方式通知的接收。</p>
                    <p>短消息、邮件需要商家设置正确号码后才能正常接收。</p>
                    <p>编辑完成后请清理“商家消息模板”缓存。</p>
                </div>
                <div class="refund-process-content store-information">
                    <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                        <ul>
                            <li>
                                <div class="refund-application">
                                    <h5>站内信模块</h5>
                                    <div class="application-content refund-module">
                                        <row>
                                            <i-col span="12">
                                                <form-item label="站内信开关">
                                                    <i-switch size="large">
                                                        <span slot="open">开启</span>
                                                        <span slot="close">关闭</span>
                                                    </i-switch>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col span="12">
                                                <form-item label="强制接收">
                                                    <radio-group v-model="whetherGroup1">
                                                        <radio label="是"></radio>
                                                        <radio label="否"></radio>
                                                    </radio-group>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col span="12">
                                                <form-item label="消息内容">
                                                    <form-item class="remark-input">
                                                        <i-input :autosize="{minRows: 3,maxRows: 5}"
                                                                 v-model="form.remarks"
                                                                 type="textarea"></i-input>
                                                    </form-item>
                                                </form-item>
                                            </i-col>
                                        </row>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="refund-application">
                                    <h5>手机短信模块</h5>
                                    <div class="application-content refund-module">
                                        <row>
                                            <i-col span="12">
                                                <form-item label="站内信开关">
                                                    <i-switch size="large">
                                                        <span slot="open">开启</span>
                                                        <span slot="close">关闭</span>
                                                    </i-switch>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col span="12">
                                                <form-item label="强制接收">
                                                    <radio-group v-model="whetherGroup2">
                                                        <radio label="是"></radio>
                                                        <radio label="否"></radio>
                                                    </radio-group>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col span="12">
                                                <form-item label="消息内容">
                                                    <form-item class="remark-input">
                                                        <i-input :autosize="{minRows: 3,maxRows: 5}" type="textarea"
                                                                 v-model="form.remarks"></i-input>
                                                    </form-item>
                                                </form-item>
                                            </i-col>
                                        </row>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="refund-application">
                                    <h5>邮件模块</h5>
                                    <div class="application-content refund-module">
                                        <row>
                                            <i-col span="12">
                                                <form-item label="站内信开关">
                                                    <i-switch size="large">
                                                        <span slot="open">开启</span>
                                                        <span slot="close">关闭</span>
                                                    </i-switch>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col span="12">
                                                <form-item label="强制接收">
                                                    <radio-group v-model="whetherGroup3">
                                                        <radio label="是"></radio>
                                                        <radio label="否"></radio>
                                                    </radio-group>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col span="12">
                                                <form-item label="邮件标题">
                                                    <i-input></i-input>
                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col span="12">
                                                <form-item label="邮件内容">

                                                </form-item>
                                            </i-col>
                                        </row>
                                        <row>
                                            <i-col span="12">
                                                <form-item label="">
                                                    <i-button @click.native="submit" type="primary">
                                                        <span v-if="!loading">确认提交</span>
                                                        <span v-else>正在提交…</span>
                                                    </i-button>
                                                </form-item>
                                            </i-col>
                                        </row>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </i-form>
                </div>
            </card>
        </div>
    </div>
</template>