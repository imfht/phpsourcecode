<template>
    <div class="user-view-inline">
        <Tooltip
            :disabled="loadIng"
            :delay="delay"
            :transfer="transfer"
            :placement="placement"
            maxWidth="auto"
            @on-popper-show="getUserData(30)">
            <div class="user-view-info">
                <UserImg v-if="showimg" class="user-view-img" :info="userInfo" :style="imgStyle"/>
                <div v-if="showname" class="user-view-name">{{nickname || username}}</div>
            </div>
            <div slot="content" style="white-space:normal">
                <div v-if="!showname">{{$L('昵称')}}: {{nickname || '-'}}</div>
                <div>{{$L('用户名')}}: {{username}}</div>
                <div>{{$L('职位/职称')}}: {{profession || '-'}}</div>
            </div>
        </Tooltip>
    </div>
</template>

<style lang="scss">
    .user-view-inline {
        .ivu-tooltip {
            max-width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            .ivu-tooltip-rel {
                max-width: 100%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                .user-view-info {
                    .user-view-img {
                        .usertext-container-text {
                            transform: scale(0.86);
                        }
                    }
                }
            }
        }
    }
</style>
<style lang="scss" scoped>
    .user-view-inline {
        display: inline-block;
        max-width: 100%;
        .user-view-info {
            display: flex;
            align-items: center;
            .user-view-img {
                width: 16px;
                height: 16px;
                font-size: 12px;
                line-height: 16px;
                border-radius: 50%;
                margin-right: 3px;
            }
            .user-view-title {
                flex: 1;
                line-height: 1.2;
            }
        }
    }
</style>

<script>
    export default {
        name: 'UserView',
        props: {
            username: {
                default: ''
            },
            delay: {
                type: Number,
                default: 600
            },
            transfer: {
                type: Boolean,
                default: true
            },
            placement: {
                default: 'bottom'
            },
            showimg: {
                type: Boolean,
                default: false
            },
            imgsize: {

            },
            imgfontsize: {

            },
            showname: {
                type: Boolean,
                default: true
            },
            info: {
                default: null
            },
        },
        data() {
            return {
                loadIng: true,

                nickname: null,
                userimg: '',
                profession: ''
            }
        },
        mounted() {
            this.getUserData(300);
        },
        watch: {
            username() {
                this.getUserData(300);
            },
            info: {
                handler() {
                    this.upInfo()
                },
                deep: true
            }
        },
        computed: {
            userInfo() {
                const {username, nickname, userimg} = this;
                return {username, nickname, userimg}
            },
            imgStyle() {
                const {imgsize, imgfontsize} = this;
                const myStyle = {};
                if (imgsize) {
                    const size = /^\d+$/.test(imgsize) ? (imgsize + 'px') : imgsize;
                    myStyle.width = size;
                    myStyle.height = size;
                    myStyle.lineHeight = size;
                }
                if (imgfontsize) {
                    myStyle.fontSize = /^\d+$/.test(imgfontsize) ? (imgfontsize + 'px') : imgfontsize;
                }
                return myStyle;
            }
        },
        methods: {
            isJson(obj) {
                return typeof (obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && typeof obj.length == "undefined";
            },

            upInfo() {
                if (this.isJson(this.info)) {
                    this.$set(this.info, 'nickname', this.nickname);
                    this.$set(this.info, 'userimg', this.userimg);
                }
            },

            getUserData(cacheTime) {
                $A.getUserBasic(this.username, (data, success) => {
                    if (success) {
                        this.nickname = data.nickname;
                        this.userimg = data.userimg;
                        this.profession = data.profession;
                    } else {
                        this.nickname = '';
                        this.userimg = '';
                        this.profession = '';
                    }
                    this.loadIng = false;
                    this.$emit("on-result", data);
                    this.upInfo();
                }, cacheTime);
            }
        }
    }
</script>
