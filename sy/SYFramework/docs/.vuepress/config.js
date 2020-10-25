module.exports = {
  base: "/",
  title: "SYFramework",
  description: "SYFramework docs",
  markdown: {
    lineNumbers: true,
    toc: {
      includeLevel: [1, 2, 3]
    }
  },
  locales: {
    '/': {
      lang: 'zh-CN',
      title: 'SYFramework',
      description: 'SYFramework文档'
    }
  },
  themeConfig: {
    repo: "sylingd/SYFramework",
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
          link: '/zh-CN/'
        }],
        sidebar: []
      }
    }
  }
};