<template>
    <div>
        <div class="desc-editor-box">
            <div class="desc-editor-tool">
                <Button class="tool-button" size="small" @click="openFull">{{$L('全屏')}}</Button>
            </div>
            <div v-if="loadIng > 0" class="desc-editor-load">
                <WLoading/>
            </div>
            <div
                ref="myTextarea"
                class="desc-editor-content"
                :id="id"
                :placeholder="placeholder"
                v-html="content"
                @blur="handleBlur"></div>
            <ImgUpload
                ref="myUpload"
                class="desc-editor-upload"
                type="callback"
                :uploadIng.sync="uploadIng"
                @on-callback="editorImage"
                num="50"></ImgUpload>
        </div>
        <Spin fix v-if="uploadIng > 0">
            <Icon type="ios-loading" class="upload-control-spin-icon-load"></Icon>
            <div>{{$L('正在上传文件...')}}</div>
        </Spin>
        <Modal v-model="transfer" class="desc-editor-transfer" @on-visible-change="transferChange" footer-hide fullscreen transfer>
            <div slot="close">
                <Button type="primary" size="small">{{$L('完成')}}</Button>
            </div>
            <div class="desc-editor-transfer-body">
                <textarea :id="'T_' + id" :placeholder="placeholder">{{content}}</textarea>
            </div>
            <Spin fix v-if="uploadIng > 0">
                <Icon type="ios-loading" class="upload-control-spin-icon-load"></Icon>
                <div>{{$L('正在上传文件...')}}</div>
            </Spin>
        </Modal>
    </div>
</template>

<style lang="scss">
.desc-editor-box {
    .desc-editor-content {
        img {
            max-width: 100%;
            max-height: 100%;
        }
        &:before {
            left: 8px !important;
            color: #cccccc !important;
        }
    }
}
.desc-editor-transfer {
    background-color: #ffffff;
    .tox-toolbar {
        > div:last-child {
            > button:last-child {
                margin-right: 64px;
            }
        }
    }
    .ivu-modal-header {
        display: none;
    }
    .ivu-modal-close {
        top: 7px;
        z-index: 2;
    }
    .desc-editor-transfer-body {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
        textarea {
            opacity: 0;
        }
        .tox-tinymce {
            border: 0;
            .tox-statusbar {
                span.tox-statusbar__branding {
                    a {
                        display: none;
                    }
                }
            }
        }
    }
}
</style>
<style lang="scss" scoped>
.desc-editor-box {
    position: relative;
    &:hover {
        .desc-editor-tool {
            .tool-button {
                opacity: 0.9;
                &:hover {
                    opacity: 1;
                }
            }
        }
    }
    .desc-editor-tool {
        display: flex;
        flex-direction: row;
        align-items: center;
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 2;
        .tool-button {
            font-size: 12px;
            opacity: 0;
            transition: all 0.2s;
            margin-left: 5px;
            background-color: #ffffff;
        }
    }
    .desc-editor-load {
        position: absolute;
        right: 5px;
        bottom: 5px;
        z-index: 2;
        width: 16px;
        height: 16px;
    }
}
.desc-editor-content {
    position: relative;
    margin: 10px 0 6px;
    border: 2px solid transparent;
    padding: 5px 8px;
    color: #172b4d;
    line-height: 1.5;
    border-radius: 4px;
    min-height: 56px;
    max-height: 182px;
    background: rgba(9, 30, 66, 0.04);
    overflow: auto;
    &:focus {
        box-shadow: 0 0 0 2px rgba(45, 140, 240, .2);
    }
}
.desc-editor-upload {
    display: none;
    width: 0;
    height: 0;
    overflow: hidden;
}
</style>

<script>
import tinymce from 'tinymce/tinymce';
import ImgUpload from "../../../ImgUpload";

export default {
    name: 'DescEditor',
    components: {ImgUpload},
    props: {
        taskid: {
            default: ''
        },
        desc: {
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            loadIng: 0,
            uploadIng: 0,

            id: "tinymce_" + Math.round(Math.random() * 10000),
            content: '',
            submitContent: '',

            editor: null,
            editorT: null,
            cTinyMce: null,
            checkerTimeout: null,
            isTyping: false,

            transfer: false,
        };
    },
    mounted() {
        this.loadData((val) => {
            this.submitContent = val;
            this.content = val;
            this.init();
        });
    },
    beforeDestroy() {
        if (this.editor !== null) {
            this.editor.destroy();
        }
        if (this.editorT !== null) {
            this.editorT.destroy();
        }
    },
    watch: {
        desc() {
            this.loadData((val) => {
                this.submitContent = val;
                this.content = val;
                this.getEditor().setContent(val);
            });
        }
    },
    methods: {
        loadData(callback) {
            this.loadIng++;
            $A.apiAjax({
                url: 'project/task/desc',
                data: {
                    taskid: this.taskid,
                },
                complete: () => {
                    this.loadIng--;
                },
                success: (res) => {
                    if (res.ret === 1) {
                        callback(res.data.desc);
                    } else {
                        callback('');
                    }
                }
            });
        },

        init() {
            this.$nextTick(() => {
                tinymce.init(this.options(false));
            });
        },

        initTransfer() {
            this.$nextTick(() => {
                tinymce.init(this.options(true));
            });
        },

        options(isFull) {
            let toolbar;
            if (isFull) {
                toolbar = 'undo redo | styleselect | uploadImages | bold italic underline forecolor backcolor | alignleft aligncenter alignright | outdent indent | link image emoticons media codesample | preview screenload';
            } else {
                toolbar = false;
            }
            return {
                selector: (isFull ? '#T_' : '#') + this.id,
                base_url: $A.serverUrl('js/build'),
                auto_focus: false,
                language: "zh_CN",
                toolbar: toolbar,
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak imagetools',
                    'searchreplace visualblocks code',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons paste textcolor colorpicker imagetools codesample'
                ],
                save_onsavecallback: (e) => {
                    this.handleBlur(e);
                },
                menubar: isFull,
                inline: !isFull,
                inline_boundaries: false,
                paste_data_images: true,
                menu: {
                    view: {
                        title: 'View',
                        items: 'code | visualaid visualchars visualblocks | spellchecker | preview fullscreen screenload | showcomments'
                    },
                    insert: {
                        title: "Insert",
                        items: "image link media addcomment pageembed template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime | uploadImages browseImages"
                    }
                },
                codesample_languages: [
                    {text: "HTML/VUE/XML", value: "markup"},
                    {text: "JavaScript", value: "javascript"},
                    {text: "CSS", value: "css"},
                    {text: "PHP", value: "php"},
                    {text: "Ruby", value: "ruby"},
                    {text: "Python", value: "python"},
                    {text: "Java", value: "java"},
                    {text: "C", value: "c"},
                    {text: "C#", value: "csharp"},
                    {text: "C++", value: "cpp"}
                ],
                height: isFull ? '100%' : ($A.rightExists(this.height, '%') ? this.height : ($A.runNum(this.height) || 360)),
                resize: !isFull,
                convert_urls: false,
                toolbar_mode: 'sliding',
                toolbar_drawer: 'floating',
                setup: (editor) => {
                    editor.ui.registry.addMenuButton('uploadImages', {
                        text: this.$L('图片'),
                        tooltip: this.$L('上传/浏览 图片'),
                        fetch: (callback) => {
                            let items = [{
                                type: 'menuitem',
                                text: this.$L('上传图片'),
                                onAction: () => {
                                    this.$refs.myUpload.handleClick();
                                }
                            }, {
                                type: 'menuitem',
                                text: this.$L('浏览图片'),
                                onAction: () => {
                                    this.$refs.myUpload.browsePicture();
                                }
                            }];
                            callback(items);
                        }
                    });
                    editor.ui.registry.addMenuItem('uploadImages', {
                        text: this.$L('上传图片'),
                        onAction: () => {
                            this.$refs.myUpload.handleClick();
                        }
                    });
                    editor.ui.registry.addMenuItem('browseImages', {
                        text: this.$L('浏览图片'),
                        onAction: () => {
                            this.$refs.myUpload.browsePicture();
                        }
                    });
                    if (isFull) {
                        editor.ui.registry.addButton('screenload', {
                            icon: 'fullscreen',
                            tooltip: this.$L('退出全屏'),
                            onAction: () => {
                                this.closeFull();
                            }
                        });
                        editor.ui.registry.addMenuItem('screenload', {
                            text: this.$L('退出全屏'),
                            onAction: () => {
                                this.closeFull();
                            }
                        });
                        editor.on('Init', (e) => {
                            this.editorT = editor;
                            this.editorT.setContent(this.content);
                        });
                    } else {
                        editor.ui.registry.addButton('screenload', {
                            icon: 'fullscreen',
                            tooltip: this.$L('全屏'),
                            onAction: () => {
                                this.openFull();
                            }
                        });
                        editor.ui.registry.addMenuItem('screenload', {
                            text: this.$L('全屏'),
                            onAction: () => {
                                this.openFull();
                            }
                        });
                        editor.on('Init', (e) => {
                            this.editor = editor;
                            this.editor.setContent(this.content);
                            this.$emit('editorInit', this.editor);
                        });
                        editor.on('KeyUp', (e) => {
                            if (this.editor !== null) {
                                this.submitNewContent();
                            }
                        });
                        editor.on('Change', (e) => {
                            if (this.editor !== null) {
                                if (this.getContent() !== this.value) {
                                    this.submitNewContent();
                                }
                                this.$emit('editorChange', e);
                            }
                        });
                    }
                },
            };
        },

        openFull() {
            this.content = this.getContent();
            this.transfer = true;
            this.initTransfer();
        },

        closeFull() {
            this.content = this.getContent();
            this.editor.setContent(this.content);
            this.transfer = false;
            if (this.editorT != null) {
                this.editorT.destroy();
                this.editorT = null;
            }
        },

        transferChange(visible) {
            if (!visible) {
                this.$refs.myTextarea.focus();
                if (this.editorT != null) {
                    this.content = this.editorT.getContent();
                    this.editor.setContent(this.content);
                    this.editorT.destroy();
                    this.editorT = null;
                }
            }
        },

        getEditor() {
            return this.transfer ? this.editorT : this.editor;
        },

        getContent() {
            if (this.getEditor() === null) {
                return "";
            }
            return this.getEditor().getContent();
        },

        submitNewContent() {
            this.isTyping = true;
            if (this.checkerTimeout !== null) {
                clearTimeout(this.checkerTimeout);
            }
            this.checkerTimeout = setTimeout(() => {
                this.isTyping = false;
            }, 300);
        },

        insertContent(content) {
            if (this.getEditor() !== null) {
                this.getEditor().insertContent(content);
            } else {
                this.content += content;
            }
        },

        insertImage(src) {
            this.insertContent('<img src="' + src + '">');
        },

        editorImage(lists) {
            for (let i = 0; i < lists.length; i++) {
                let item = lists[i];
                if (typeof item === 'object' && typeof item.url === "string") {
                    this.insertImage(item.url);
                }
            }
        },

        handleBlur() {
            this.loadIng++;
            setTimeout(() => {
                this.handleSave();
                this.loadIng--;
            }, 300)
        },

        handleSave() {
            if (this.transfer) {
                return;
            }
            if (this.submitContent != this.getContent()) {
                const bakContent = this.submitContent;
                this.submitContent = this.getContent();
                //
                this.loadIng++;
                $A.apiAjax({
                    url: 'project/task/edit',
                    method: 'post',
                    data: {
                        act: 'desc',
                        taskid: this.taskid,
                        content: this.submitContent,
                    },
                    complete: () => {
                        this.loadIng--;
                    },
                    error: () => {
                        this.getEditor().setContent(bakContent);
                        alert(this.$L('网络繁忙，请稍后再试！'));
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            $A.triggerTaskInfoListener('desc', res.data);
                            $A.triggerTaskInfoChange(this.taskid);
                            this.$Message.success(res.msg);
                            this.$emit('save-success');
                        } else {
                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                            this.getEditor().setContent(bakContent);
                        }
                    }
                })
            }
        },
    }
}
</script>
