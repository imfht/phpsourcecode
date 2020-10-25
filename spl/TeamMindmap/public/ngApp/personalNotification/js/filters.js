/**
 * Created by spatra on 14-12-12.
 */

define(['personalNotificationJS/module'], function(personalNotificationModule){

  /**
   * 对通知按所属的项目进行过滤，如果传递进来的项目id为0， 则返回所有的通知，
   * 否则返回相应项目的通知.
   */
  personalNotificationModule.filter('project', function(){

    return function(projects, projectId){
      if( projectId ){
        var rtn = [];

        projects.forEach(function(item){
          if( item.source_id === projectId ){
            rtn.push(item);
          }
        });

        return rtn;
      }
      else{
        return projects;
      }
    };
  });// End fo --> project

});