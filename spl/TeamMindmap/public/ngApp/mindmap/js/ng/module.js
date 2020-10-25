/**
 * Created by rockyren on 14/12/22.
 */
define(['angular'],
  function(angular){
    var mindmapModule = angular.module('TeamMindmap.mindmap', []);

    mindmapModule.constant('mindmapModuleBaseUrl', 'ngApp/mindmap/');

    return mindmapModule;

});