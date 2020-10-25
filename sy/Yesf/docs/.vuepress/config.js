module.exports = {
  base: "/",
  title: "Yesf",
  description: "Yesf docs",
  markdown: {
    lineNumbers: true,
    toc: {
      includeLevel: [1, 2, 3]
    }
  },
  plugins: [
    ['vuepress-plugin-baidu-google-analytics', {
      hm: '44b41bc63385a6b27e692272bb1fb393',
      ignore_hash: true
    }]
  ],
  locales: {
    '/': {
      lang: 'zh-CN',
      title: 'Yesf',
      description: 'Yesf文档'
    },
    '/en/': {
      lang: 'en-US',
      title: 'Yesf',
      description: 'Yesf docs'
    }
  },
  themeConfig: {
    repo: "sylingd/Yesf",
    docsDir: 'docs',
    editLinks: true,
    locales: {
      '/': {
        lang: 'zh-CN',
        selectText: '选择语言',
        label: '简体中文',
        editLinkText: '在 GitHub 上编辑此页',
        nav: [{
          text: '首页',
          link: '/'
        }, {
          text: '指南',
          link: '/zh-CN/guide/'
        }, {
          text: '镜像',
          items: [{
              text: '美国（由GitHub提供）',
              link: 'https://yesf.sylibs.com'
            },
            {
              text: '香港（由Gitee提供）',
              link: 'http://yesf-cn.sylibs.com'
            }
          ]
        }],
        sidebar: require('./list_zh-CN')
      },
      '/en/': {
        lang: 'en-US',
        selectText: 'Languages',
        label: 'English',
        editLinkText: 'Edit this page on GitHub',
        nav: [{
          text: 'Home',
          link: '/en/'
        }, {
          text: 'Guide',
          link: '/en/guide'
        }, {
          text: 'Mirrors',
          items: [{
              text: 'United States (Provided by GitHub)',
              link: 'https://yesf.sylibs.com'
            },
            {
              text: 'HongKong (Provided by Gitee)',
              link: 'http://yesf-cn.sylibs.com'
            }
          ]
        }],
        sidebar: require('./list_en')
      }
    }
  }
};