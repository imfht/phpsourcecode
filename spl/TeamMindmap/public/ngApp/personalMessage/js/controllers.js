/**
 * Created by spatra on 14-12-12.
 */

define(['personalMessageJS/module'], function(personalMessageModule){

  personalMessageModule.controller('MessageCreatingController', ['$scope', 'projectList', 'MessageService',
    function($scope, projectList, MessageService){

      /**
       * 初始化当前作用域中使用到的变量
       */
      $scope.resetScope = function(){
        $scope.receiverSet = {};  //收信人的集合
        $scope.addMixed = null;   //添加收信人的添加申请字段
        $scope.message = {};      //待发送的消息
        $scope.method = 1;        //默认的添加收信人的方法
        $scope.sending = false; //是否处于发送状态
      };
      $scope.resetScope();
      $scope.projectList = projectList;   //用户的参与的和创建的项目列表
      $scope.sendMethods = MessageService.getSendMethods();   //得到添加收信人的方法

      //当前切换添加方法时，清空添加申请字段
      $scope.$watch('method', function(newValue, oldValue){
        if( newValue !== oldValue ){
         $scope.addMixed = null;
        }
      });


      /**
       * 添加接收者
       */
      $scope.addReceiver = function(){
        MessageService.addReceiver($scope.method, $scope.receiverSet, $scope.addMixed);
        $scope.addMixed = null;
      };

      /**
       * 移除接收者
       * @param receiverId 待移除接收者的用户id
       */
      $scope.removeReceiver = function(receiverId){
        delete $scope.receiverSet[ receiverId ];
      };

      /**
       * 发送私信
       */
      $scope.sendMessage = function(){
        $scope.message.receiver_id = [];

        for(var receiver in $scope.receiverSet ){

          if( $scope.receiverSet.hasOwnProperty(receiver) ){
            $scope.message.receiver_id.push(receiver);
          }
        }

        $scope.sending = true;
        var waitingOpts = {
          title: '私信正在发送中...'
        };

        $scope.$emit('message:wait', waitingOpts);

        MessageService.accessor.store($scope.message)
          .success(function(){
            waitingOpts.show({
              type: 'success',
              title: '私信发送成功'
            });
            $scope.resetScope();
            $scope.$emit('unread:update');
          })
          .error(function(data){
            waitingOpts.show({
              type: 'error',
              msg: data.error
            });
            console.error(data);
          });
      };
  }]);

  /**
   * 用于显示私信列表的控制器
   */
  personalMessageModule.controller('MessageListController', ['$scope', '$state', 'MessageService', 'messageList', 'ClassHelperService', '$rootScope', 'PaginationService',
    function($scope, $state, MessageService, messageList, ClassHelperService, $rootScope, PaginationService){
      /**
       * 请求私信
       * @param isSent
       */
      $scope.loading = false;

      //一开始设置为收件箱
      $scope.isSent = false;
      $scope.messageShowList = messageList.data;
      $scope.filterOption = {
        option: 'received'
      };

      //当前页数改变时调用的函数: 请求currentPage页的数据

      $scope.pagination = PaginationService.createPagination('number', {
        currentPage: messageList.current_page,
        itemsPerPage: messageList.per_page,
        totalItems: messageList.total,
        resourceList: $scope.messageShowList,
        resourceGetMethod: PaginationService.convertMethodToNormalFun(MessageService.accessor, 'get'),
        getResourceOps: $scope.filterOption
      });
      $scope.pagination.init();


      $scope.$watch('isSent', function(newValue, oldValue){
        if(!ClassHelperService.objectEquals(newValue, oldValue)) {
          $scope.filterOption.option = $scope.isSent ? 'sent' : 'received';
          $scope.pagination.update({resetCurrentPage: true});
        }
      }, true);

      //查看具体的信息内容
      $scope.goToShow = function(currentMessageInfo) {

        //如果私信状态为未读，则设置为已读
        if(! currentMessageInfo.read ) {
          MessageService.accessor.update(currentMessageInfo.id,{read: 'true'})
            .error(function(data){
              console.error(data);
              $scope.$emit('message:error', {
                msg: '更改私信的已读状态失败'
              });
            })
            .success(function(){
              currentMessageInfo.read = true;
              $rootScope.$emit('unread:update');  //更改未读的统计信息

            });
        }

        //设置当前的具体的私信，用于后续显示具体的私信
        currentMessageInfo.isSent = $scope.isSent;
        MessageService.currentMessageInfo = currentMessageInfo;
        //跳转到具体的私信页
        $state.go('personal.message.list.show');
      };




    }]);

  /**
   * 用户显示具体私信的控制器
   */
  personalMessageModule.controller('MessageShowController',['$scope', '$state', 'currentMessageInfo',
    function($scope, $state, currentMessageInfo){
      if(currentMessageInfo) {

        $scope.messageInfo = currentMessageInfo;
      }else{
        //如果没有当前的具体的信息，则跳转回私信列表
        $state.go('personal.message.list');
      }
  }]);

});