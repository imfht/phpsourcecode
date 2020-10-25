
<template>
<div>
  <Row :gutter="24">
    <Col :xs="6" :lg="3">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
    </Col>
    <Col :xs="3" :lg="4" class="hidden-mobile">
    <Input icon="search" placeholder="请输入标题..." v-model="searchForm.title" />
    </Col>
    <Col :xs="3" :lg="3">
    <Select v-model="searchForm.enable" placeholder="是否启用">
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in tableStatus.enable" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="3" :lg="3" class="hidden-mobile">
    <Select v-model="searchForm.category_id" placeholder="分类" filterable>
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in articleCategories" :value="item.id" :key="item.id">{{ item.name }} </Option>
    </Select>
    </Col>
    <Col :xs="3" :lg="3">
    <Select v-model="searchForm.recommend" placeholder="推荐" filterable>
        <Option value="" key="">全部</Option>
        <Option v-for="(item,key) in tableStatus.recommend" :value="key" :key="key">{{ item }} </Option>
    </Select>
    </Col>
    <Col :xs="3" :lg="3">
    <Select v-model="searchForm.top" placeholder="置顶" filterable>
        <Option value="" key="">全部</Option>
        <Option v-for="(item,key) in tableStatus.top" :value="key" :key="key">{{ item }} </Option>
    </Select>
    </Col>
    <Col :xs="1" :lg="3">
    <Button type="primary" icon="ios-search" @click="getTableDataExcute(feeds.current_page)">Search</Button>
    </Col>
  </Row>
  <br>

  <Row>
    <div class="demo-spin-container" v-if="tableLoading">
      <Spin fix>
        <Icon type="load-c" size=18 class="spin-icon-load"></Icon>
        <div>加载中...</div>
      </Spin>
    </div>
    <Table border :columns="columns" :data="feeds.data" @on-sort-change='onSortChange'></Table>
    <div style="margin: 10px;overflow: hidden">
      <div style="float: right;">
        <Page :total="feeds.total" :current="feeds.current_page" :page-size="feeds.per_page" class="paging" show-elevator show-total show-sizer @on-change="handleOnPageChange" @on-page-size-change='onPageSizeChange'></Page>
      </div>
    </div>
  </Row>

  <show-info v-if='showInfoModal.show' :info='showInfoModal.info' @show-modal-close="showModalClose"></show-info>
  <add-component v-if='platformIsPc && addModal.show' @on-add-modal-success='getTableDataExcute(feeds.current_page)' @on-add-modal-hide="addModalHide" :article-categories='articleCategories' :article-tags='articleTagList'></add-component>
  <add-mobile-component v-if='!platformIsPc && addModal.show' @on-add-modal-success='getTableDataExcute(feeds.current_page)' @on-add-modal-hide="addModalHide" :article-categories='articleCategories' :article-tags='articleTagList'></add-mobile-component>
  <edit-component v-if="platformIsPc && editModal.show" :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute(feeds.current_page)' @on-edit-modal-hide="editModalHide" :article-categories='articleCategories' :article-tags='articleTagList'></edit-component>
  <edit-mobile-component v-if='!platformIsPc && editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute(feeds.current_page)' @on-edit-modal-hide="editModalHide" :article-categories='articleCategories' :article-tags='articleTagList'></edit-mobile-component>

</div>
</template>


<script>
import AddComponent from './components/add'
import AddMobileComponent from './components/add-mobile'
import EditComponent from './components/edit'
import EditMobileComponent from './components/edit-mobile'
import ShowInfo from './components/show-info'

import {
  getTableStatus,
  switchEnable
} from '@/api/common'

import {
  getTableData,
  getArticleCategories,
  destroy
} from '@/api/article'

export default {
  components: {
    AddComponent,
    AddMobileComponent,
    EditComponent,
    EditMobileComponent,
    ShowInfo
  },
  data() {
    return {
      searchForm: {
        order_by: 'id,desc'
      },
      tableLoading: false,
      tableStatus: {
        enable: [],
        top: [],
        recommend: [],
      },
      feeds: {
        data: [],
        total: 0,
        current_page: 1,
        per_page: 10
      },
      articleCategories: {},
      articleTagList: {},
      addModal: {
        show: false
      },
      editModal: {
        show: false,
        id: 0
      },
      showInfoModal: {
        show: false,
        info: ''
      },
      columns: [{
          title: 'ID',
          key: 'id',
          sortable: 'customer',
          minWidth: 50,
        },
        {
          title: '标题',
          key: 'title',
          minWidth: 150,
        },
        {
          title: '封面',
          minWidth: 150,
          render: (h, params) => {
            let t = this;
            if (params.row.cover_image.url) {
              return h('div', [
                h('img', {
                  attrs: {
                      src: params.row.cover_image.url,
                      class: 'fancybox',
                      href: params.row.cover_image.url,
                      title:'图片'
                  },
                  style: {
                    width: '40px',
                    height: '40px'
                  },
                }),
              ]);
            }
          }
        },
        {
          title: '分类',
          minWidth: 100,
          render: (h, params) => {
            return h('div',
              params.row.category.name
            )
          }
        },
        {
          title: '标签',
          minWidth: 100,
          render: (h, params) => {
            var tags = params.row.tags;
            var text = '';
            for (var key in tags) {
              if (key < 1) {
                text += tags[key].name
              } else {
                text += '、' + tags[key].name
              }
            }
            return h('div',
              text
            )
          }
        },
        {
          title: '置顶',
          minWidth: 100,
          render: (h, params) => {
            var row = params.row;
            var color = 'green';
            var text = '置顶';
            if (row.top === 'F') {
              text = '非置顶';
              color = 'default';
            }

            return h('div', [
              h('Tag', {
                props: {
                  color: color
                }
              }, text)
            ]);
          }
        },
        {
          title: '推荐',
          minWidth: 100,
          render: (h, params) => {
            var row = params.row;
            var color = 'green';
            var text = '推荐';
            if (row.recommend === 'F') {
              text = '非推荐';
              color = 'default';
            }

            return h('div', [
              h('Tag', {
                props: {
                  color: color
                }
              }, text)
            ]);
          }
        },
        {
          title: '启用状态',
          key: 'enable',
          minWidth: 100,
          render: (h, params) => {
            return h('i-switch', {
              props: {
                slot: 'open',
                type: 'primary',
                value: params.row.enable === 'T', //控制开关的打开或关闭状态，官网文档属性是value
              },
              on: {
                'on-change': (value) => {
                  this.switchEnableExcute(params.index)
                }
              }
            });
          }
        },
        {
          title: '创建时间',
          sortable: 'customer',
          key: 'created_at',
          minWidth: 150,
        },
        {
          title: '操作',
          minWidth: 200,
          render: (h, params) => {
            let t = this;
            var delete_btn = '';

            delete_btn = h('Poptip', {
              props: {
                confirm: true,
                title: '您确定要删除「' + params.row.title + '」？',
                transfer: true
              },
              on: {
                'on-ok': () => {
                  t.destroyExcute(params.row.id, params.index);
                }
              }
            }, [
              h('Button', {
                style: {
                  margin: '0 5px'
                },
                props: {
                  type: 'error',
                  size: 'small',
                  placement: 'top'
                }
              }, '删除'),
            ])
            return h('div', [
              h('Button', {
                style: {
                  margin: '0 5px'
                },
                props: {
                  type: 'primary',
                  size: 'small'
                },
                on: {
                  click: () => {
                    this.showInfoModal.show = true
                    this.showInfoModal.info = params.row
                  }
                }

              }, '详细'),
              h('Button', {
                props: {
                  type: 'success',
                  size: 'small'
                },
                on: {
                  click: () => {
                    this.editModal.show = true
                    this.editModal.id = params.row.id
                  }
                }

              }, '修改'),
              delete_btn
            ])
          }
        }
      ]
    }
  },
  mounted() {
    let t = this
    t.getTableStatusExcute('articles')
    t.getArticleCategoriesExcute()
    t.getTableDataExcute(t.feeds.current_page)
  },
  computed: {
    platformIsPc: function() {
      return this.globalPlatformType() == 'pc' ? true : false
    }
  },
  methods: {
    handleOnPageChange: function(to_page) {
      this.getTableDataExcute(to_page)
    },
    onPageSizeChange: function(per_page) {
      this.feeds.per_page = per_page
      this.getTableDataExcute(this.feeds.current_page)
    },
    getTableStatusExcute(params) {
      let t = this
      getTableStatus(params).then(res => {
        const response_data = res.data
        t.tableStatus.enable = response_data.enable
        t.tableStatus.recommend = response_data.recommend
        t.tableStatus.top = response_data.top
      })
    },
    getArticleCategoriesExcute() {
      let t = this
      getArticleCategories().then(res => {
        t.articleCategories = res.data;
      })
    },
    getTableDataExcute(to_page) {
      let t = this
      t.tableLoading = true
      t.feeds.current_page = to_page
      getTableData(to_page, t.feeds.per_page, t.searchForm).then(res => {
        t.feeds.data = res.data
        t.feeds.total = res.meta.total
        t.tableLoading = false
        t.globalFancybox()
      }, function(error) {
        t.tableLoading = false
      })
    },
    onSortChange: function(data) {
      const order = data.column.key + ',' + data.order
      this.searchForm.order_by = order
      this.getTableDataExcute(this.feeds.current_page)
    },
    switchEnableExcute(index) {
      let t = this
      let new_status = 'T'
      if (t.feeds.data[index].enable === 'T') {
        new_status = 'F'
      }
      switchEnable(t.feeds.data[index].id, 'articles', new_status).then(res => {
        t.feeds.data[index].enable = new_status
        t.$Notice.success({
          title: res.message
        })
      })
    },
    destroyExcute(id, key) {
      let t = this
      destroy(id).then(res => {
        t.feeds.data.splice(key, 1)
        t.$Notice.success({
          title: res.message
        })
      })
    },
    addBtn() {
      this.addModal.show = true
    },
    addModalHide() {
      this.addModal.show = false
    },
    editModalHide() {
      this.editModal.show = false
    },
    showModalClose() {
      this.showInfoModal.show = false
    }
  }
}
</script>
