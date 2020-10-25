module.exports = [{
		title: '开发指南',
		children: [
			'/zh-CN/guide/',
			'/zh-CN/guide/install',
			'/zh-CN/guide/structure',
			'/zh-CN/guide/configuration',
		]
	},
	'/zh-CN/container',
	{
		title: '请求处理',
		children: [
			'/zh-CN/process_request/',
			'/zh-CN/process_request/static',
			'/zh-CN/process_request/router',
			'/zh-CN/process_request/request',
			'/zh-CN/process_request/response'
		]
	},
	{
		title: '插件',
		children: [
			'/zh-CN/plugin/',
			'/zh-CN/plugin/on_worker_start'
		]
	},
	{
		title: '连接池',
		children: [
			'/zh-CN/connection/',
			'/zh-CN/connection/write_driver.md',
			'/zh-CN/connection/write_adapter.md'
		]
	},
	{
		title: '缓存',
		children: [
			'/zh-CN/cache/',
			'/zh-CN/cache/redis',
			'/zh-CN/cache/file',
			'/zh-CN/cache/yac',
			'/zh-CN/cache/custom',
		]
	}
];