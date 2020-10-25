/**
 * Created by rockyren on 15/5/18.
 */

define(['noteJS/module'], function(module){
  module.factory('GroupService', ['ResourceService', function(ResourceService){
    return {
      accessor: ResourceService.resourceFactory({
        resourceName: 'group'
      })
    }
  }]);

  module.factory('NotebookService', ['ResourceService', function(ResourceService){
    return {
      accessor: ResourceService.nestedResourceFactory({
        resourceName: 'notebook',
        parentResourceName: 'group'
      })
    }
  }]);


  module.factory('NoteService', ['ResourceService', function(ResourceService){
    return {
      accessor: ResourceService.nestedResourceFactory({
        resourceName: 'note',
        parentResourceName: 'group|notebook'
      }),
      /**
       * 根据类型设置的笔记accessor的parentId
       * stateParams: {type:'all'|'group'|'notebook',  typeId:number或者不传}
       */
      setNotesParentByType: function(stateParams){
        var type = stateParams.type;
        switch(type){
          case 'all':
            this.accessor.setParentId({});
            break;
          case 'group':
            this.accessor.setParentId({
              'groupId': stateParams.typeId
            });
            break;
          case 'notebook':
            this.accessor.setParentId({
              'notebookId': stateParams.typeId
            });
            break;
        }
      }
    }
  }]);


  module.factory('CatalogueService', ['ResourceService', function(ResourceService){
    return {
      accessor: ResourceService.resourceFactory({
        resourceName: 'catalogue'
      })
    }
  }]);

});