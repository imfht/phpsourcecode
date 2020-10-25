<template lang="html">
    <div :class="`markdown ${fullscreen ? 'fullscreen' : ''} ${bordered ? 'border' : ''}`" ref="markdown" :style="{ width: width + (typeof width === 'string'?'':'px'), height: height + (typeof height === 'string'?'':'px') }">
        <!-- 头部工具栏 -->
        <ul class="markdown-toolbars" v-show="!preview">
            <li>
                <slot name="title"/>
            </li>
            <li v-if="tools.undo" name="撤销">
                <span class="iconfont icon-undo" @click="editor.undo()"></span>
            </li>
            <li v-if="tools.redo" name="重做" @click="redo">
                <span class="iconfont icon-redo"></span>
            </li>
            <li v-if="tools.strong" name="粗体">
                <span @click="insertStrong" class="iconfont icon-bold"></span>
            </li>
            <li v-if="tools.italic" name="斜体">
                <span @click="insertItalic" class="iconfont icon-italic"></span>
            </li>
            <li v-if="tools.overline" name="删除线">
                <span @click="insertOverline" class="iconfont icon-overline"></span>
            </li>
            <li v-if="tools.overline" name="下划线">
                <span @click="insertUnderline" class="iconfont icon-under-line"></span>
            </li>
            <li v-if="tools.h1" name="标题1">
                <span style="font-size: 16px;" @click="insertTitle(1)">h1</span>
            </li>
            <li v-if="tools.h2" name="标题2">
                <span style="font-size: 16px;" @click="insertTitle(2)">h2</span>
            </li>
            <li v-if="tools.h3" name="标题3">
                <span style="font-size: 16px;" @click="insertTitle(3)">h3</span>
            </li>
            <li v-if="tools.h4" name="标题4">
                <span style="font-size: 16px;" @click="insertTitle(4)">h4</span>
            </li>
            <li v-if="tools.h5" name="标题5">
                <span style="font-size: 16px;" @click="insertTitle(5)">h5</span>
            </li>
            <li v-if="tools.h6" name="标题6">
                <span style="font-size: 16px;" @click="insertTitle(6)">h6</span>
            </li>
            <li v-if="tools.hr" name="分割线">
                <span @click="insertLine" class="iconfont icon-horizontal"></span>
            </li>
            <li v-if="tools.quote" name="引用">
                <span style="font-size: 16px;" @click="insertQuote" class="iconfont icon-quote"></span>
            </li>
            <li v-if="tools.ul" name="无序列表">
                <span @click="insertUl" class="iconfont icon-ul"></span>
            </li>
            <li v-if="tools.ol" name="有序列表">
                <span @click="insertOl" class="iconfont icon-ol"></span>
            </li>
            <li v-if="tools.code" name="代码块">
                <span @click="insertCode" class="iconfont icon-code"></span>
            </li>
            <li v-if="tools.notChecked" name="未完成列表">
                <span @click="insertNotFinished" class="iconfont icon-checked-false"></span>
            </li>
            <li v-if="tools.checked" name="已完成列表">
                <span @click="insertFinished" class="iconfont icon-checked"></span>
            </li>
            <li v-if="tools.link" name="链接">
                <span @click="insertLink" class="iconfont icon-link"></span>
            </li>
            <li v-if="tools.image" name="图片">
                <span @click="insertImage" class="iconfont icon-img"></span>
            </li>
            <li v-if="tools.uploadImage" name="本地图片">
                <span @click="chooseImage" class="iconfont icon-upload-img"></span>
            </li>
            <li v-if="tools.custom_image" name="浏览图片">
                <span @click="onCustom('image-browse')" class="iconfont icon-img"></span>
            </li>
            <li v-if="tools.custom_uploadImage" name="上传图片">
                <span @click="onCustom('image-upload')" class="iconfont icon-upload-img"></span>
            </li>
            <li v-if="tools.custom_uploadFile" name="上传文件">
                <span @click="onCustom('file-upload')" class="icon-svg">
                    <svg t="1599285632421" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="45640" width="16" height="16"><path d="M127.519 892.879v-763.34h655.448V514.69h63.612V81.831c0-8.783-7.12-15.903-15.903-15.903H79.81c-8.783 0-15.903 7.12-15.903 15.903v858.757c0 8.783 7.12 15.903 15.903 15.903h493.993v-63.612H127.519z" fill="#999999" p-id="45641"></path><path d="M231.608 228.388h447.269V292H231.608zM231.608 384.409h447.269v63.612H231.608zM231.608 540.43h245.141v63.612H231.608zM231.608 696.451h245.141v63.612H231.608zM923.269 762.938L745.315 584.984c-3.545-3.545-8.34-5.074-12.966-4.596a15.931 15.931 0 0 0-9.848 4.616L544.586 762.918c-6.248 6.248-6.248 16.379 0 22.627l22.353 22.353c6.248 6.248 16.379 6.248 22.627 0l112.555-112.555v245.148c0 8.837 7.163 16 16 16h31.612c8.837 0 16-7.163 16-16V695.363l112.555 112.555c6.248 6.248 16.379 6.248 22.627 0l22.353-22.353c6.249-6.248 6.249-16.378 0.001-22.627z" fill="#999999" p-id="45642"></path></svg>
                </span>
            </li>
            <li v-if="tools.table" name="表格">
                <span @click="insertTable" class="iconfont icon-table"></span>
            </li>
            <li v-if="tools.theme" class="shift-theme" name="代码块主题">
                <div>
                    <span class="iconfont icon-theme" @click="themeSlideDown = !themeSlideDown"></span>
                    <ul :class="{ active: themeSlideDown }" @mouseleave="themeSlideDown = false">
                        <li @click="setThemes('light')">Light</li>
                        <li @click="setThemes('dark')">VS Code</li>
                        <li @click="setThemes('oneDark')">Atom OneDark</li>
                        <li @click="setThemes('gitHub')">GitHub</li>
                    </ul>
                </div>
            </li>
            <li name="导入文件" class="import-file" v-show="tools.importmd">
                <span class="iconfont icon-daoru" @click="importFile"></span>
                <input type="file" @change="importFile($event)" accept="text/markdown"/>
            </li>
            <li name="保存到本地" v-show="tools.exportmd">
                <span class="iconfont icon-download" @click="exportFile"></span>
            </li>
            <li v-if="tools.split && split" name="全屏编辑">
                <span @click="split = false" class="iconfont icon-md"></span>
            </li>
            <li v-if="tools.split && !split" name="分屏显示">
                <span @click="split = true" class="iconfont icon-group"></span>
            </li>
            <li v-if="tools.preview" name="预览">
                <span @click="preview = true" class="iconfont icon-preview"></span>
            </li>
            <li v-if="tools.clear" name="清空" @click="value = ''">
                <span class="iconfont icon-clear"></span>
            </li>
            <li v-if="tools.save" name="保存" @click="handleSave">
                <span class="iconfont icon-save"></span>
            </li>
            <li :name="scrolling ? '同步滚动:开' : '同步滚动:关'">
                <span @click="scrolling = !scrolling" v-show="scrolling" class="iconfont icon-on"></span>
                <span @click="scrolling = !scrolling" v-show="!scrolling" class="iconfont icon-off"></span>
            </li>
            <li  name="html转markdown" @click="onCustom('html2md')">
                <span style="width:auto;font-size:14px;padding:0 6px">HTML2MD</span>
            </li>
            <li class="empty"></li>
            <li v-if="tools.fullscreen && !fullscreen" name="全屏">
                <span @click="fullscreen = !fullscreen" class="iconfont icon-fullscreen"></span>
            </li>
            <li v-if="tools.fullscreen && fullscreen" name="退出全屏">
                <span @click="fullscreen = !fullscreen" class="iconfont icon-quite"></span>
            </li>
            <li v-if="tools.custom_fullscreen && !isCustomFullscreen" name="全屏">
                <span @click="onCustom('fullscreen')" class="iconfont icon-fullscreen"></span>
            </li>
            <li v-if="tools.custom_fullscreen && isCustomFullscreen" name="退出全屏">
                <span @click="onCustom('fullscreen')" class="iconfont icon-quite"></span>
            </li>
        </ul>
        <!-- 关闭预览按钮 -->
        <div
            class="close-preview"
            v-show="preview && !isPreview"
            @click="preview = false"
        >
            <span class="iconfont icon-close"></span>
        </div>
        <!-- 编辑器 -->
        <div
            class="markdown-content"
            :style="{ background: preview ? '#fff' : '' }"
        >
            <div
                class="codemirror"
                ref="codemirror"
                v-show="!preview"
                @mouseenter="mousescrollSide('left')"
            ></div>
            <div
                v-show="preview ? preview : split"
                :class="`markdown-preview ${'markdown-theme-' + themeName}`"
                ref="preview"
                @scroll="previewScroll"
                @mouseenter="mousescrollSide('right')"
            >
                <div v-html="html" ref="previewInner"></div>
            </div>
        </div>
        <!--    预览图片-->
        <div :class="['preview-img', previewImgModal ? 'active' : '']">
            <span
                class="close icon-close iconfont"
                @click="previewImgModal = false"
            ></span>
            <img :src="previewImgSrc" :class="[previewImgMode]" alt=""/>
        </div>
    </div>
</template>

<script>
    import markdown from './pro';

    export default markdown;
</script>

<style scoped  lang="less">
    @import "../../assets/font/iconfont.css";
    @import "../../assets/css/theme";
    @import "../../assets/css/light";
    @import "../../assets/css/dark";
    @import "../../assets/css/one-dark";
    @import "../../assets/css/github";
    @import "../../assets/css/index";
</style>
