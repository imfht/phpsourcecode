import Main from '@/view/main'
import parentView from '@/components/parent-view'

export default[
  {
    path: '/login',
    name: 'login',
    meta: {
      title: 'Login - 登录',
      hideInMenu: true
    },
    component: () => import ('@/view/login/login.vue')
  }, {
    path: '/',
    name: '_home',
    redirect: '/home',
    component: Main,
    meta: {
      hideInMenu: true,
      notCache: true
    },
    children: [
      {
        path: '/home',
        name: 'home',
        meta: {
          hideInMenu: true,
          title: '首页',
          notCache: true
        },
        component: () => import ('@/view/single-page/home')
      }
    ]
  }, {
    path: '/privileges',
    name: 'privileges',
    component: Main,
    meta: {
      icon: 'ios-key',
      title: '权限管理',
      access: ['Founder']
    },
    children: [
      {
        path: '/permission-list',
        name: 'permission-list',
        meta: {
          title: '权限列表'
        },
        component: () => import ('@/view/privileges/permissions/list.vue')
      }, {
        path: '/role-list',
        name: 'role-list',
        meta: {
          title: '角色列表',
          // href: 'https://lison16.github.io/iview-admin-doc/#/'
        },
        component: () => import ('@/view/privileges/roles/list.vue')
      }, {
        path: '/administrator-list',
        name: 'administrator-list',
        meta: {
          title: '用户列表'
        },
        component: () => import ('@/view/privileges/users/list.vue')
      }
    ]
  }, {
    path: '/news-system',
    name: 'news-system',
    component: Main,
    meta: {
      title: '新闻系统',
      icon: 'ios-cog'
    },
    children: [
      {
        path: '/advertisement-positions',
        name: 'advertisement-positions',
        meta: {
          title: '广告位'
        },
        component: () => import ('@/view/news-system/advertisement-positions/list.vue')
      }, {
        path: 'advertisement-list',
        name: 'advertisement-list',
        meta: {
          title: '广告列表'
        },
        component: () => import ('@/view/news-system/advertisements/list.vue')
      }, {
        path: '/category-list',
        name: 'category-list',
        meta: {
          title: '分类管理'
        },
        component: () => import ('@/view/news-system/categories/list.vue')
      }, {
        path: '/tag-list',
        name: 'tag-list',
        meta: {
          title: '标签管理'
        },
        component: () => import ('@/view/news-system/tags/list.vue')
      }, {
        path: '/article-list',
        name: 'article-list',
        meta: {
          title: '文章管理'
        },
        component: () => import ('@/view/news-system/articles/list.vue')
      }, {
        path: '/carousel-list',
        name: 'carousel-list',
        meta: {
          title: '抡播图'
        },
        component: () => import ('@/view/news-system/carousels/list.vue')
      }
    ]
  }, {
    path: '/resources',
    name: 'resources',
    component: Main,
    meta: {
      title: '资源管理',
      icon: 'ios-keypad-outline'
    },
    children: [
      {
        path: '/attachments',
        name: 'attachments',
        meta: {
          title: '附件列表'
        },
        component: () => import ('@/view/resources/attachments/list.vue')
      }, {
        path: '/config-item-list',
        name: 'config-item-list',
        meta: {
          title: '系统配置项'
        },
        component: () => import ('@/view/resources/systems/config-item-list.vue')
      }
    ]
  }, {
    path: '/security',
    name: 'security',
    meta: {
      icon: 'ios-build',
      title: '安全管理'
    },
    component: Main,
    children: [
      {
        path: '/system-logs',
        name: 'system-logs',
        meta: {
          title: '系统日志'
        },
        component: () => import ('@/view/security/logs/list.vue')
      }, {
        path: '/ip-filters',
        name: 'ip-filters',
        meta: {
          title: 'ip 过滤'
        },
        component: () => import ('@/view/security/ip_filters/list.vue')
      }, {
        path: '/app-versions',
        name: 'app-versions',
        meta: {
          title: 'app 版本控制'
        },
        component: () => import ('@/view/security/app-versions/list.vue')
      }, {
        path: '/system-versions',
        name: 'system-versions',
        meta: {
          title: 'lucms 版本更新日志'
        },
        component: () => import ('@/view/system-version')
      }
    ]
  }, {
    path: '/messages',
    name: 'messages',
    component: Main,
    meta: {
      title: '消息中心',
      icon: 'ios-analytics'
    },
    children: [
      {
        path: '/admin-messages',
        name: 'admin-messages',
        meta: {
          title: '后台消息'
        },
        component: () => import ('@/view/messages/admin-messages/list.vue')
      }, {
        path: '/api-messages',
        name: 'api-messages',
        meta: {
          title: 'api 消息'
        },
        component: () => import ('@/view/messages/api-messages/list.vue')
      }
    ]
  }, {
    path: '/401',
    name: 'error_401',
    meta: {
      hideInMenu: true
    },
    component: () => import ('@/view/error-page/401.vue')
  }, {
    path: '/500',
    name: 'error_500',
    meta: {
      hideInMenu: true
    },
    component: () => import ('@/view/error-page/500.vue')
  }, {
    path: '*',
    name: 'error_404',
    meta: {
      hideInMenu: true
    },
    component: () => import ('@/view/error-page/404.vue')
  }
]
