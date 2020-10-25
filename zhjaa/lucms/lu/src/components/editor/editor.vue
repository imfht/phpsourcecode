<template>
<div class="w-e-text">
  <div id="toolbar" class="wang-editor-toolbar"> </div>
  <div :id="editorId" :class="uploadConfig.heightStyle"></div>
  <Collapse>
    <Panel name="1">
      预览
      <p slot="content" v-html='editorHtml'></p>
    </Panel>
    <Panel name="2">
      修改源代码
      <p slot="content">
        <Input v-model="editorHtml" type="textarea" :rows="6" />
        <Button size='small' type="success" icon="ios-create-outline" @click="editSourceCode">
          修改
        </Button>
      </p>
    </Panel>
  </Collapse>
</div>
</template>

<script>
import Editor from 'wangeditor'
import 'wangeditor/release/wangEditor.min.css'

import {
  oneOf
} from '@/libs/tools'
export default {
  name: 'Editor',
  props: {
    value: {
      type: String,
      default: ''
    },
    /**
     * 绑定的值的类型, enum: ['html', 'text']
     */
    valueType: {
      type: String,
      default: 'html',
      validator: (val) => {
        return oneOf(val, ['html', 'text'])
      }
    },
    /**
     * @description 设置change事件触发时间间隔
     */
    changeInterval: {
      type: Number,
      default: 200
    },
    /**
     * @description 是否开启本地存储
     */
    cache: {
      type: Boolean,
      default: true
    },
    uploadConfig: {
      type: Object,
      default: {
        headers: {
          'Authorization': window.access_token
        },
        wang_size: 1 * 1024 * 1024, // 1M
        uploadUrl: window.uploadUrl.uploadWang,
        params: {},
        max_length: 3,
        file_name: 'file',
        z_index: 10000,
        heightStyle: 'wang-editor-text-400'
      }
    }
  },
  data() {
    return {
      showHtml: true,
      editorHtml: '',
      _uid: window.access_token
    }
  },
  computed: {
    editorId() {
      return `editor${this._uid}`
    }
  },
  mounted() {
    this.editor = new Editor('#toolbar', `#${this.editorId}`)
    this.editor.customConfig.onchange = (html) => {
      let text = this.editor.txt.text()
      if (this.cache) localStorage.editorCache = html
      this.$emit('input', this.valueType === 'html' ? html : text)
      this.$emit('on-change', html, text)
      this.editorHtml = html
    }
    this.editor.customConfig.onchangeTimeout = this.changeInterval
    this.editor.customConfig.uploadImgServer = this.uploadConfig.uploadUrl // 上传图片到服务器
    this.editor.customConfig.uploadImgMaxSize = this.uploadConfig.wang_size
    this.editor.customConfig.uploadImgParams = this.uploadConfig.params
    this.editor.customConfig.uploadImgParams = this.uploadConfig.max_length
    this.editor.customConfig.uploadFileName = this.uploadConfig.file_name
    this.editor.customConfig.uploadImgHeaders = this.uploadConfig.headers
    this.editor.customConfig.zIndex = this.uploadConfig.z_index
    this.editor.customConfig.uploadImgHooks = {
      before: function(xhr, editor, files) {
        // 图片上传之前触发
        // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象，files 是选择的图片文件

        // 如果返回的结果是 {prevent: true, msg: 'xxxx'} 则表示用户放弃上传
        // return {
        //     prevent: true,
        //     msg: '放弃上传'
        // }
      },
      success: function(xhr, editor, result) {
        // 图片上传并返回结果，图片插入成功之后触发
        // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象，result 是服务器端返回的结果
      },
      fail: function(xhr, editor, result) {
        // 图片上传并返回结果，但图片插入错误时触发
        // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象，result 是服务器端返回的结果
      },
      error: function(xhr, editor) {
        // 图片上传出错时触发
        // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象
      },
      timeout: function(xhr, editor) {
        // 图片上传超时时触发
        // xhr 是 XMLHttpRequst 对象，editor 是编辑器对象
      },

      // 如果服务器端返回的不是 {errno:0, data: [...]} 这种格式，可使用该配置
      // （但是，服务器端返回的必须是一个 JSON 格式字符串！！！否则会报错）
      customInsert: function(insertImg, result, editor) {
        // 图片上传并返回结果，自定义插入图片的事件（而不是编辑器自动插入图片！！！）
        // insertImg 是插入图片的函数，editor 是编辑器对象，result 是服务器端返回的结果

        // 举例：假如上传图片成功后，服务器端返回的是 {url:'....'} 这种格式，即可这样插入图片：
        var url = result.data.url
        insertImg(url)

        // result 必须是一个 JSON 格式字符串！！！否则报错
      }
    }
    this.editor.customConfig.pasteTextHandle = function(content) {
      // content 即粘贴过来的内容（html 或 纯文本），可进行自定义处理然后返回
      return content
    }

    // create这个方法一定要在所有配置项之后调用
    this.editor.create()

    // 如果本地有存储加载本地存储内容
    let html = ''
    if (this.cache) {
      html = localStorage.editorCache
    } else {
      html = this.value
    }
    if (html) this.editor.txt.html(html)
    this.editorHtml = html
    let text = this.editor.txt.text()
    this.$emit('input', this.valueType === 'html' ? html : text)
    this.$emit('on-change', html, text)
  },
  methods: {
    editSourceCode() {
      let html = this.editorHtml
      this.editor.txt.html(html)
      let text = this.editor.txt.text()
      this.$emit('input', this.valueType === 'html' ? html : text)
      this.$emit('on-change', html, text)
      this.$Notice.success({
        title: '修改成功'
      })
    }
  }
}
</script>
