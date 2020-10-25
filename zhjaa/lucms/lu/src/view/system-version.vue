<template>
<div class="iview-time-line">
  <Row :gutter="20">
    <i-col span="12">
      <Card shadow>
        <p slot="title">
          <Icon type="ios-repeat"></Icon>
          Lucms 版本更新记录
        </p>
        <a href="#" slot="extra" @click.prevent="pushNewVersion">
          <Icon type="ios-paper-plane-outline"></Icon>
          发布新版本
        </a>
        <div v-if="versionList">
          <Timeline>
            <TimelineItem v-for="(item,key) in versionList">
              <a :href="item.download_url" class="time" target="_blank">V{{ item.version }}</a>
              <br/>
              <h4>{{ item.title }}</h4>
              <Icon type="md-time" />{{ item.created_at}}
              <br/>
              <p class="content">
                <parse :content="item.content.raw"></parse>
              </p>
            </TimelineItem>
          </Timeline>

          <div style="margin: 10px;overflow: hidden">
            <div >
              <Page simple :total="feeds.total" :current="feeds.current_page" :page-size="feeds.per_page" class="paging"  @on-change="handleOnPageChange" @on-page-size-change='onPageSizeChange'></Page>
            </div>
          </div>
        </div>
        <p class="text-center" v-else>没有版本相关信息...</p>
      </Card>
    </i-col>
  </Row>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false fullscreen>
    <p slot="header">发布新版本</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="版本标题：" prop="title">
        <Input v-model="formData.title" placeholder="请输入版本标题"></Input>
      </FormItem>
      <FormItem label="版本号：" prop="version">
        <Input v-model="formData.version" placeholder="请输入版本号"></Input>
        <input-helper text="如：1.0.0"></input-helper>
      </FormItem>
      <FormItem label="下载链接：">
        <Input v-model="formData.download_url" placeholder="请输入下载链接"></Input>
      </FormItem>
      <FormItem label="版本描述：">
        <markdown-editor v-model="formData.content" :value="formData.content" :cache='true' />
      </FormItem>
    </Form>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="newVersionExcute" :loading='saveLoading'>保存 </Button>
    </div>
  </Modal>
</div>
</template>

<script>
import {
  getVersionList,
  newVersion
} from '@/api/version'

import MarkdownEditor from '_c/markdown'
import Parse from '_c/common/parse'
import InputHelper from '_c/common/input-helper'

export default {
  components: {
    MarkdownEditor,
    InputHelper,
    Parse
  },
  data() {
    return {
      feeds: {
        data: [],
        total: 0,
        current_page: 1,
        per_page: 10
      },
      versionList: [],
      tableLoading: false,
      saveLoading: false,
      modalShow: false,
      formData: {
        title: '',
        version: '',
      },
      rules: {
        title: [{
          required: true,
          message: '请填写版本标题',
          trigger: 'blur'
        }],
        version: [{
          required: true,
          message: '请填写版本号',
          trigger: 'blur'
        }],
        content: [{
          required: true,
          message: '请填写内容',
          trigger: 'blur'
        }],
      },
    }
  },
  mounted() {
    this.getVersionListExcute()
  },
  methods: {
    handleOnPageChange: function(to_page) {
      this.getTableDataExcute(to_page)
    },
    onPageSizeChange: function(per_page) {
      this.feeds.per_page = per_page
      this.getTableDataExcute(this.feeds.current_page)
    },
    pushNewVersion() {
      this.modalShow = true
    },
    getVersionListExcute() {
      let t = this
      t.tableLoading = true
      getVersionList().then(res => {
        t.versionList = res.data
        t.tableLoading = false
      })
    },
    cancel() {
      this.saveLoading = false
      this.modalShow = false
    },
    newVersionExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          newVersion(t.formData).then(res => {
            t.saveLoading = false
            t.modalShow = false
            t.$Notice.success({
              title: res.message
            })
            t.getVersionListExcute()
          }, function(error) {
            t.saveLoading = false;
          })
        }
      })
    }

  }
}
</script>
