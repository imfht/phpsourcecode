<template>
    <Upload
        name="files"
        ref="upload"
        :action="actionUrl"
        :data="params"
        multiple
        :format="uploadFormat"
        :show-upload-list="false"
        :max-size="maxSize"
        :on-progress="handleProgress"
        :on-success="handleSuccess"
        :on-format-error="handleFormatError"
        :on-exceeded-size="handleMaxSize"
        :before-upload="handleBeforeUpload">
    </Upload>
</template>

<script>
export default {
    name: 'ChatLoad',
    props: {
        target: {
            default: ''
        },
        maxSize: {
            type: Number,
            default: 10240
        }
    },

    data() {
        return {
            uploadFormat: ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'esp', 'pdf', 'rar', 'zip', 'gz'],
            actionUrl: $A.apiUrl('chat/files/upload'),
            params: {
                username: this.target,
                token: $A.getToken(),
            }
        }
    },

    watch: {
        target(val) {
            this.$set(this.params, 'username', val);
        }
    },

    methods: {
        handleProgress(event, file) {
            //上传时
            if (typeof file.tempId === "undefined") {
                file.tempId = $A.randomString(8);
                this.$emit('on-progress', file);
            }
        },

        handleSuccess(res, file) {
            //上传完成
            if (res.ret === 1) {
                for (let key in res.data) {
                    if (res.data.hasOwnProperty(key)) {
                        file[key] = res.data[key];
                    }
                }
                this.$emit('on-success', file);
            } else {
                this.$Modal.warning({
                    title: this.$L('上传失败'),
                    content: this.$L('文件 % 上传失败，%', file.name, res.msg)
                });
                this.$emit('on-error', file);
                this.$refs.upload.fileList.pop();
            }
        },

        handleFormatError(file) {
            //上传类型错误
            this.$Modal.warning({
                title: this.$L('文件格式不正确'),
                content: this.$L('文件 % 格式不正确，仅支持上传：%', file.name, this.uploadFormat.join(','))
            });
        },

        handleMaxSize(file) {
            //上传大小错误
            this.$Modal.warning({
                title: this.$L('超出文件大小限制'),
                content: this.$L('文件 % 太大，不能超过%。', file.name, $A.bytesToSize(this.maxSize * 1024))
            });
        },

        handleBeforeUpload() {
            //上传前判断
            this.params = {
                username: this.target,
                token: $A.getToken(),
            };
            return true;
        },

        handleClick() {
            //手动上传
            if (this.handleBeforeUpload()) {
                this.$refs.upload.handleClick()
            }
        },

        upload(file) {
            //手动传file
            if (this.handleBeforeUpload()) {
                this.$refs.upload.upload(file);
            }
        },
    }
}
</script>
