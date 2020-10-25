<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="1200">
    <p slot="header">添加</p>
    <Row>
      <Col span="24">
      <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
        <FormItem label="分类：">
          <Select v-model="formData.category_id" filterable placeholder="请选择文章分类">
              <Option v-for="(item,key) in articleCategories" :value="item.id" :key="key">{{ item.name }} </Option>
          </Select>
        </FormItem>
        <FormItem label="标题：" prop="title">
          <Input v-model="formData.title"></Input>
        </FormItem>
        <FormItem label="封面：">
          <upload v-model="formData.cover_image" :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
        </FormItem>
        <FormItem label="是否启用：">
          <RadioGroup v-model="formData.enable">
            <Radio label="F">禁用</Radio>
            <Radio label="T">启用</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem label="关键词：" prop="keywords">
          <Input type="textarea" v-model="formData.keywords" placeholder="以英文逗号隔开"></Input>
          <input-helper text="以英文逗号隔开"></input-helper>
        </FormItem>
        <FormItem label="描述：" prop="description">
          <Input type="textarea" v-model="formData.descriptions" placeholder="请输入"></Input>
        </FormItem>
        <FormItem label="排序：">
          <Input v-model="formData.weight" placeholder="请输入"></Input>
        </FormItem>
        <FormItem label="置顶：">
          <Select size="small" style="width:20%" v-model="formData.top">
              <Option value="F">否</Option>
              <Option value="T">是</Option>
            </Select>
        </FormItem>
        <FormItem label="推荐：">
          <Select size="small" style="width:20%" v-model="formData.recommend">
              <Option value="F">否</Option>
              <Option value="T">是</Option>
            </Select>
        </FormItem>
        <p class="margin-top-10">
          <Icon type="eye"></Icon>&nbsp;&nbsp;公开度：&nbsp;<b>{{ Openness }}</b>
          <Button v-show="!editOpenness" size="small" type="text" @click="handleEditOpenness">修改</Button>
          <transition name="openness-con">
            <div v-show="editOpenness" class="publish-time-picker-con">
              <RadioGroup v-model="formData.access_type" vertical>
                <Radio label="PUB"> 公开</Radio>
                <Radio label="PWD"> 密码
                  <Input v-show="formData.access_type === 'PWD'" v-model="formData.access_value" style="width:50%" size="small" placeholder="请输入密码" />
                </Radio>
                <Radio label="PRI">私密</Radio>
              </RadioGroup>
              <div>
                <Button type="primary" @click="handleSaveOpenness">确认</Button>
              </div>
            </div>
          </transition>
        </p>

        <FormItem label="新建标签">
          <Input v-model="newTagName" search enter-button="新建" placeholder="标签名字" @on-search="addEditExcute" />
        </FormItem>
        <FormItem label="标签：">
          <Select v-model="formData.tags" multiple filterable placeholder="请选择文章标签">
                <Option v-for="item in articleTags" :value="item.id" :key="item.id">{{ item.name }} </Option>
            </Select>
        </FormItem>
        <FormItem label="文章内容：">
          <markdown-editor v-model="formData.content" :cache='true' />
        </FormItem>
      </Form>
      </Col>
    </Row>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="addExcute" :loading='saveLoading'>保存 </Button>
    </div>
    <div class="demo-spin-container" v-if='spinLoading === true'>
      <Spin fix>
        <Icon type="load-c" size=18 class="spin-icon-load"></Icon>
        <div>加载中...</div>
      </Spin>
    </div>
  </Modal>
</div>
</template>
<script>
import {
  add
} from '@/api/article'

import {
  addEdit,
  getTagList
} from '@/api/tag'

import Upload from '_c/common/upload'
import InputHelper from '_c/common/input-helper'
import MarkdownEditor from '_c/markdown'
export default {
  components: {
    Upload,
    InputHelper,
    MarkdownEditor
  },
  props: {
    articleCategories: {
      default: {}
    },
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      spinLoading: true,
      formData: {
        title: '',
        cover_image: {
          attachment_id: 0,
          url: '',
        },
        enable: 'F',
        keywords: '',
        descriptions: '',
        content: '',
        category_id: 0,
        weight: 20,
        top: 'F',
        recommend: 'F',
        access_type: 'PUB',
        access_value: '',
        tags: 0,
      },
      editOpenness: false,
      Openness: '公开',
      newTagName: '',
      articleTags: {},
      rules: {
        title: [{
          required: true,
          message: '请填写文章标题',
          trigger: 'blur'
        }],
      },
      imguploadConfig: {
        headers: {
          'Authorization': window.access_token
        },
        format: ['jpg', 'jpeg', 'png', 'gif'],
        max_size: 800, // 800KB
        upload_url: window.uploadUrl.uploadAdvertisement,
        file_name: 'file',
        multiple: false,
        file_num: 1,
        default_list: []
      },
    }
  },
  mounted() {
    this.spinLoading = false
    this.getTagListExcute()
  },
  methods: {
    getTagListExcute() {
      let t = this;
      getTagList().then(res => {
        t.articleTags = res.data;
      })
    },
    addExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          add(t.formData).then(res => {
            t.saveLoading = false
            t.modalShow = false
            t.$emit('on-add-modal-success')
            t.$emit('on-add-modal-hide')
            t.$Notice.success({
              title: res.message
            })
          }, function(error) {
            t.saveLoading = false;
          })
        }
      })
    },
    cancel() {
      this.modalShow = false
      this.$emit('on-add-modal-hide')
    },
    uploadChange(fileList, formatFileList) {},
    handleEditOpenness() {
      this.editOpenness = !this.editOpenness;
    },
    handleSaveOpenness() {
      var access_type = this.formData.access_type;
      if (this.passwordValidate()) {
        this.Openness = (access_type === 'PUB') ? '公开' : (access_type === 'PWD') ? '密码' : '私密';
        this.editOpenness = false;
      }
    },
    passwordValidate() {
      var access_type = this.formData.access_type;
      var access_value = this.formData.access_value;
      if (access_type === 'PWD') {
        var patt = /^[a-zA-Z0-9]{4,8}$/;
        if (!patt.test(access_value)) {
          this.$Notice.error({
            title: '出错了',
            desc: '密码只能是4到8位的数字与字母'
          });
          return false;
        }

      }
      return true;
    },
    addEditExcute() {
      let t = this;
      addEdit({
        name: t.newTagName
      }).then(res => {
        t.getTagListExcute()
        t.$Notice.success({
          title: res.message
        })
      })
    }
  }
}
</script>
