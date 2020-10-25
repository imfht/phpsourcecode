---
title: Worker进程启动（workerStart）
lang: zh-CN
---

# Worker进程启动（workerStart）

此事件为Swoole事件封装。当Swoole的worker（包括taskWorker）启动时将会被调用。这时候，可以进行一些基本的初始化操作。

### 传入参数

* bool $taskworker 是否为taskworker

* int $worker_id Worker ID

### 返回

无需返回