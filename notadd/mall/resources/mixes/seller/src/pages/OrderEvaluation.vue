<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                evaluation: [
                    {
                        assess: '西收到了，做工精细，款式设计的时尚大气，实物和图片没区别，和买家描述的一致，买家态度很好，' +
                        '物流也很给力。很愉快的一次购物体验。东西收到了，做工精细，款式设计的时尚大气，实物和图片没区别，和 ' +
                        '买家描述的一致，买家态度很好，物流也很给力。很愉快的一次购物体验',
                        buyer: 'ibenchu',
                        createTime: '2017-6-9',
                        name: '发过乐上LEXON lexon薄款手提公文',
                        valueDisabled: 4,
                    },
                    {
                        assess: '西收到了，做工精细，款式设计的时尚大气，实物和图片没区别，和买家描述的一致，买家态度很好，' +
                        '物流也很给力。很愉快的一次购物体验。东西收到了，做工精细，款式设计的时尚大气，实物和图片没区别，和 ' +
                        '买家描述的一致，买家态度很好，物流也很给力。很愉快的一次购物体验',
                        buyer: 'ibenchu',
                        createTime: '2017-6-9',
                        name: '发过乐上LEXON lexon薄款手提公文',
                        valueDisabled: 4,
                    },
                ],
                loading: false,
                managementSearch: '',
                message: false,
                replayMessage: {
                    assess: '西收到了，做工精细，款式设计的时尚大气，实物和图片没区别，和买家描述的一致，买家态度很好，' +
                    '物流也很给力。很愉快的一次购物体验。东西收到了，做工精细，款式设计的时尚大气，实物和图片没区别，和 ' +
                    '买家描述的一致，买家态度很好，物流也很给力。很愉快的一次购物体验',
                    message: '',
                },
                searchList: [
                    {
                        label: '商品名称',
                        value: '1',
                    },
                    {
                        label: '买家名称',
                        value: '2',
                    },
                ],
            };
        },
        methods: {
            replay() {
                this.message = true;
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.replayMessage.validate(valid => {
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
        <div class="order-evaluation">
            <tabs value="name1">
                <tab-pane label="买家评论" name="name1">
                    <card :bordered="false">
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <i-select v-model="managementSearch" slot="prepend" style="width: 100px;">
                                                    <i-option v-for="item in searchList"
                                                              :value="item.value">{{ item.label }}</i-option>
                                                </i-select>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <table class="order-table">
                            <thead>
                            <tr class="thead-border">
                                <th>评价信息</th>
                                <th>操作</th>
                            </tr>
                            <tr class="space-bg">
                                <th colspan="2"></th>
                            </tr>
                            </thead>
                            <tbody v-for="(item, index) in evaluation">
                            <tr>
                                <td>
                                    商品：{{ item.name }}&nbsp;&nbsp;
                                    评分：<rate disabled v-model="item.valueDisabled"></rate>&nbsp;&nbsp;
                                    买家：{{ item.buyer }}&nbsp;&nbsp;
                                    时间：{{ item.createTime }}&nbsp;&nbsp;
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>买家评价：{{ item.assess }}</td>
                                <td>
                                    <i-button type="ghost" size="small" @click="replay(item)">回复</i-button>
                                </td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="2"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                    <modal
                            v-model="message"
                            title="回复评价" class="upload-picture-modal seller-order-modal">
                        <div>
                            <i-form ref="replayMessage" :model="replayMessage" :rules="ruleValidate" :label-width="100">
                                <row>
                                    <i-col span="22">
                                        <form-item label="评价内容">
                                            {{ replayMessage.assess }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="22">
                                        <form-item label="回复内容">
                                            <i-input type="textarea" :autosize="{minRows: 3,maxRows: 5}"
                                                    v-model="replayMessage.message"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="22">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submit">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-form>
                        </div>
                    </modal>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>