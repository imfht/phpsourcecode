<template>
    <div class="userimg-container" @click="onClick">
        <template v-if="this.isJson(info)">
            <img v-if="isShowImg(userImg)&&!imgError" class="userimg-container-img" :src="userImg" @error="loadError"/>
            <div v-else class="userimg-container-box" :style="textStyle">
                <div class="usertext-container-text">{{userName}}</div>
            </div>
        </template>
    </div>
</template>

<style lang="scss" scoped>
    .userimg-container {
        display: inline-block;
        max-width: 100%;
        max-height: 100%;
        position: relative;
        overflow: hidden;
        .userimg-container-img {
            width: 100%;
            height: 100%;
            display: table;
        }
        .userimg-container-box {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 400;
            color: #ffffff;

            .usertext-container-text {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
    }
</style>
<script>
    export default {
        name: 'UserImg',
        props: {
            info: {
                default: {} //{username, nickname, userimg}
            },
            size: {

            }
        },
        data() {
            return {
                imgError: false,
                againUrl: '',
            }
        },
        computed: {
            textStyle() {
                const style = {
                    backgroundColor: '#A0D7F1'
                };
                const {size} = this;
                if (size) {
                    style.fontSize = /^\d+$/.test(size) ? (size + 'px') : size;
                }
                const {username} = this.info;
                if (username) {
                    let bgColor = '';
                    for (let i = 0; i < username.length; i++) {
                        bgColor += parseInt(username[i].charCodeAt(0), 10).toString(16);
                    }
                    style.backgroundColor = '#' + bgColor.slice(1, 4);
                }
                return style;
            },

            userName() {
                if (!this.isJson(this.info)) {
                    return '';
                }
                let name = this.info.send_username || this.info.username;
                if (this.info.nickname && !this.isEmojiPrefix(this.info.nickname)) {
                    name = this.info.nickname;
                }
                return (name + " ").substring(0, 1).toLocaleUpperCase();
            },

            userImg() {
                if (!this.isJson(this.info)) {
                    return '';
                }
                if (this.againUrl) {
                    return this.againUrl;
                }
                return this.info.send_userimg || this.info.userimg;
            },
        },
        methods: {
            isJson(obj) {
                return typeof (obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && typeof obj.length == "undefined";
            },

            isShowImg(url) {
                return !!(url && !$A.rightExists(url, 'images/other/avatar.png'));
            },

            isEmojiPrefix(text) {
                return /^[\uD800-\uDBFF][\uDC00-\uDFFF]/.test(text);
            },

            loadError() {
                if (!this.againUrl) {
                    if ($A.rightExists(this.userImg, 'images/other/system-message.png')) {
                        this.againUrl = $A.fillUrl('images/other/system-message.png');
                        return;
                    } else if ($A.rightExists(this.userImg, 'images/other/group.png')) {
                        this.againUrl = $A.fillUrl('images/other/group.png');
                        return;
                    }
                }
                this.imgError = true
            },

            onClick(e) {
                this.$emit('click', e);
            }
        }
    }
</script>
