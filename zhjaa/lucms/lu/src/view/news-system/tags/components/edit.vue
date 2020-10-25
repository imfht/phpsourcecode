<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">修改</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="标签名称" prop="name">
        <Input v-model="formData.name" placeholder="请输入"></Input>
      </FormItem>

    </Form>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="addEditExcute" :loading='saveLoading'>保存 </Button>
    </div>
  </Modal>
</div>
</template>
<script>
import {
  addEdit,
  getInfoById,
} from '@/api/tag'

export default {
  props: {
    modalId: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      formdataFinished: false,
      formData: {
        id: 0,
        name: ''
      },
      rules: {
        name: [{
          required: true,
          message: '请填写标签名称',
          trigger: 'blur'
        }]
      }
    }
  },
  mounted() {
    if (this.modalId > 0) {
      this.getInfoByIdExcute()
    }
  },
  methods: {
    getInfoByIdExcute() {
      let t = this
      getInfoById(t.modalId).then(res => {
        let resData = res.data
        t.formData = {
          id: resData.id,
          name: resData.name
        }
        t.formdataFinished = true
        t.spinLoading = false
      })
    },
    addEditExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          addEdit(t.formData).then(res => {
            t.saveLoading = false
            t.modalShow = false
            t.$emit('on-edit-modal-success')
            t.$emit('on-edit-modal-hide')
            t.$Notice.success({
              title: res.message
            })
          }, function(error) {
            t.saveLoading = false
          })
        }
      })
    },
    cancel() {
      this.modalShow = false
      this.$emit('on-edit-modal-hide')
    }
  }
}
</script>
