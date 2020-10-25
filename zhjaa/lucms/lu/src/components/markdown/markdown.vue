<template>
<div class="markdown-wrapper">
  <textarea ref="editor"></textarea>
</div>
</template>


<script>
import Simplemde from 'simplemde'
import 'simplemde/dist/simplemde.min.css'
require('./js/inline-attachment')
require('./js/codemirror-4.inline-attachment.js')

export default {
  name: 'MarkdownEditor',
  props: {
    value: {
      type: String,
      default: ''
    },
    options: {
      type: Object,
      default: () => {
        return {
          spellChecker: false,
          autosave: {
            enabled: true,
            delay: 2000,
            unique_id: "article_content_text"
          },
          forceSync: true,
          toolbar: [
            "bold", "italic", "heading", "|", "quote", "code", "table",
            "horizontal-rule", "unordered-list", "ordered-list", "|",
            "link", "image", "|", "side-by-side", 'fullscreen', "|",
            {
              name: "guide",
              action: function customFunction(editor) {
                var win = window.open('https://github.com/riku/Markdown-Syntax-CN/blob/master/syntax.md', '_blank');
                if (win) {
                  //Browser has allowed it to be opened
                  win.focus();
                } else {
                  //Browser has blocked it
                  alert('Please allow popups for this website');
                }
              },
              className: "fa fa-info-circle",
              title: "Markdown 语法！",
            },
            /*
            {
              name: "publish",
              action: function customFunction(editor) {
                $('#articleFormCommitBtn').click();
              },
              className: "fa fa-paper-plane",
              title: "发布文章",
            }
            */
          ],
        }
      }
    },
    cache: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      editor: null
    }
  },
  methods: {
    addEvents() {
      this.editor.codemirror.on('change', () => {
        let value = this.editor.value()
        /*
        if (this.cache) localStorage.markdownContent = value
        */
        this.$emit('input', value)
        this.$emit('on-change', value)
      })
      this.editor.codemirror.on('focus', () => {
        this.$emit('on-focus', this.editor.value())
      })
      this.editor.codemirror.on('blur', () => {
        this.$emit('on-blur', this.editor.value())
      })
      let raw = ''
      if (this.cache) {
        raw = this.editor.value()
      } else {
        raw = this.value
        this.editor.value(raw)
      }
      this.$emit('input', raw)
    },
  },
  mounted() {
    this.editor = new Simplemde(Object.assign(this.options, {
      element: this.$refs.editor,
      autosave: {
        enabled: this.cache,
        delay: 2000,
        unique_id: "article_content_text"
      },
    }))

    inlineAttachment.editors.codemirror4.attach(this.editor.codemirror, {
      uploadUrl: window.uploadUrl.uploadWang,
      progressText: '![uploading file...]()',
      urlText: '![]({filename})',
      errorText: 'Error uploading file',
      jsonFieldName: 'load',
      uploadFieldName: 'file',
      extraParams: {},
      extraHeaders: {
        'Authorization': window.access_token
      },
      onFileUploadResponse: function(xhr) {
        var result = JSON.parse(xhr.responseText),
          filename = result.data.url;

        if (result && filename) {
          var newValue;
          if (typeof this.settings.urlText === 'function') {
            newValue = this.settings.urlText.call(this, filename, result.data.url);
          } else {
            newValue = this.settings.urlText.replace(this.filenameTag, filename);
          }
          var text = this.editor.getValue().replace(this.lastValue, newValue);
          this.editor.setValue(text);
          this.settings.onFileUploaded.call(this, filename);
        }
        return false;
      },
      onFileUploadError: function(data) {
        this.$Notice.error({
          title: '出错了',
          desc: data
        })
      }
    });
    /**
     * 事件列表为Codemirror编辑器的事件，更多事件类型，请参考：
     * https://codemirror.net/doc/manual.html#events
     */
    this.addEvents()
    /*
    let content = localStorage.markdownContent
    if (content) this.editor.value(content)
    */
  }
}
</script>

<style lang="less">
.markdown-wrapper {
    .editor-toolbar.fullscreen {
        z-index: 9999;
    }
    .CodeMirror-fullscreen {
        z-index: 9999;
    }
    .CodeMirror-fullscreen ~ .editor-preview-side {
        z-index: 9999;
    }
}
</style>
