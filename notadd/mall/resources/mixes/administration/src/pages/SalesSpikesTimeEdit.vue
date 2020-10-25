<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                columns: [
                    {
                        align: 'center',
                        key: 'spikeName',
                        title: '秒杀时段名称',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'startTime',
                        title: '每日开始时间',
                        width: 200,
                    },
                    {
                        key: 'endTime',
                        title: '每日结束时间',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.edit(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '编辑'),
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
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                form: {
                    endTime: '',
                    name: '',
                    startTime: '',
                },
                list: [
                    {
                        endTime: '2017-2-02',
                        spikeName: '午夜场',
                        startTime: '2017-2-02',
                    },
                    {
                        endTime: '2017-2-02',
                        spikeName: '午夜场',
                        startTime: '2017-2-02',
                    },
                    {
                        endTime: '2017-2-02',
                        spikeName: '午夜场',
                        startTime: '2017-2-02',
                    },
                    {
                        endTime: '2017-2-02',
                        spikeName: '午夜场',
                        startTime: '2017-2-02',
                    },
                ],
                loading: false,
                rules: {
                    name: [
                        {
                            message: '秒杀时段名称不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
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
            remove(index) {
                this.list.splice(index, 1);
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
    <div class="mall-wrap">
        <div class="sales-spikes-time-edit">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>秒杀活动—时间段列表—秒杀时间段编辑</span>
            </div>
            <div class="spikes-information">
                <card :bordered="false">
                    <div class="prompt-box">
                        <p>提示</p>
                        <p>第一次新增秒杀时间段可对开始时间进行修改，以后默认为上一时间段结束时间的后一秒</p>
                        <p>秒杀时段名称将会显示在秒杀列表页的时间段内</p>
                        <p>编辑秒杀结束时段时不会影响到下一秒杀时段开始时间，结束时间不得小于当前时段开始时间，不得大于下一段结束时间</p>
                    </div>
                    <i-form :label-width="180" :model="form" ref="form" :rules="rules">
                        <row>
                            <i-col span="12">
                                <form-item label="秒杀时段名称" prop="name">
                                    <i-input v-model="form.name"></i-input>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="每日开始时间" prop="startTime">
                                    <time-picker type="time" placeholder="选择时间" v-model="form.startTime"></time-picker>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="每日结束时间" prop="endTime">
                                    <time-picker type="time" placeholder="选择时间" v-model="form.endTime"></time-picker>
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
    </div>
</template>
