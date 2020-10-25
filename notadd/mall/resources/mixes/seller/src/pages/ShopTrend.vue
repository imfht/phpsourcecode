<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                loading: false,
                self: this,
                settingColumns: [
                    {
                        align: 'center',
                        title: '自动',
                        type: 'selection',
                        width: 100,
                    },
                    {
                        key: 'introText',
                        render() {
                            return `<p>{{ row.type }}</p>
                                     <i-input v-model="row.text" type="textarea"
                                     :autosize="{minRows: 3,maxRows: 5}"></i-input>`;
                        },
                        title: '文字内容',
                    },
                    {
                        key: 'defaultStatus',
                        title: '系统默认状态',
                    },
                ],
                settingData: [
                    {
                        defaultStatus: '亲，我家又上新宝贝了<br>' +
                        '亲，为您推荐一款本店新上宝贝<br>' +
                        '亲，我家又上新宝贝了！快来逛逛看更多吧',
                        text: '',
                        type: '新品发布',
                    },
                    {
                        defaultStatus: '省心又省钱，活动促销中<br>' +
                        '限时折扣，玩得就是心跳<br>' +
                        '只买对的，不买贵的，宝贝限时折扣中<br>' +
                        '宝贝限时折扣中，性价比超高哟<br>' +
                        '折扣限时，不买不死心哇〜',
                        text: '',
                        type: '限时秒杀',
                    },
                    {
                        defaultStatus: '亲，我家又上新宝贝了<br>' +
                        '亲，为您推荐一款本店新上宝贝<br>' +
                        '亲，我家又上新宝贝了！快来逛逛看更多吧',
                        text: '',
                        type: '满减活动',
                    },
                ],
                shop: {
                    intro: '',
                    logo: '',
                },
                shopValidate: {
                    logo: [
                        {
                            message: '图片不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
            };
        },
        methods: {
            removeLogo() {
                this.shop.logo = '';
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.shop.validate(valid => {
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
                self.shop.logo = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="shop-trend">
            <tabs value="name1">
                <tab-pane label="发布动态" name="name1">
                    <card :bordered="false">
                        <i-form ref="shop" :model="shop" :rules="shopValidate" :label-width="180">
                            <row>
                                <i-col span="20">
                                    <form-item label="店铺图片" prop="logo">
                                        <div class="image-preview" v-if="shop.logo">
                                            <img :src="shop.logo">
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
                                                v-if="shop.logo === '' || shop.logo === null">
                                        </upload>
                                        <i-button class="first-btn" type="ghost">从图片空间选择</i-button>
                                        <p class="tip">您可使用图片上传或从图片空间选择两种方式。支持JPG，JPEG，GIF，
                                            PNG格式图片，建议上传大小4MB内的图片，上传图片将会自动保存在用户图片空间
                                            的默认分类中</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="16">
                                    <form-item label="文字内容">
                                        <i-input v-model="shop.intro" type="textarea"
                                                 :autosize="{minRows: 3,maxRows: 5}"></i-input>
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
                </tab-pane>
                <tab-pane label="动态设置" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.系统默认自动发布动态内容;去掉勾选，系统将不再自动发送相应的动态</p>
                            <p>2.可自行编辑修改各类型动态的消息，留空为系统默认设定的动态消息文字</p>
                        </div>
                        <i-form ref="setting" :model="setting" :rules="ruleValidate" :label-width="120">
                            <i-table :columns="settingColumns"
                                     :context="self"
                                     :data="settingData"
                                     highlight-row>
                            </i-table>
                            <row>
                                <i-col span="14">
                                    <form-item>
                                        <i-button :loading="loading" type="primary" class="setting-btn"
                                                  @click.native="submitSetting">
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