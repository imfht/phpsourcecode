# 共用接口
- sign-in 用户登录
- sign-out 用户退出
- refresh token刷新

# app专用接口 和 web 前台专用接口
- get-all-data (改get-task-list-by-group-id) 获取task列表和该用户相对应的作答情况，和用户的groupId有关
- get-all-data-by-type (改get-task-list-by-group-id-and-type) 获取task列表和该用户相对应的作答情况，和用户的groupId有关,只取某一种task类型的（用于单列表刷新）
- get-task-content 根据taskId获取对应task的一组问题
- submit-task (改add-task-report)task完成后，用于提交用户task的report

# web 后台专用接口
- get-all-users 获取用户信息列表，分页显示和简单查找功能
- get-user-detail 查看学生的详细情况，比如完成任务情况
- update-user 根据userId来更新用户信息

- get-all-tasks 获取所有任务列表，分页显示和简单查找功能
- publish-task 发布任务
- add-task 增加任务
- del-task 删除任务
- update-task 修改任务，发布之后不能被修改

- get-all-questions 获取所有问题的列表，分页显示和简单查找功能
- add-question 增加问题
- del-question 删除问题
- update-question 修改问题

- get-all-reports 获取所有的task报告，分页显示和简单查找功能
- update-report-score 给report进行打分，只对总结报告有效

